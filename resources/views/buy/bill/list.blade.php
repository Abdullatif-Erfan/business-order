@extends('layouts.app')

@section('content')
@php
    // Calculate column counts based on tax
    $not_col_for_print = $saved_with_tax ? "6" : "3"; 
    $total_cols = $saved_with_tax ? "2" : "2";
    $paymentsTotal = 0;
    $currencySymbol = $boughtItems->first()->currencyRelation->symbols ?? '';
    $currencyName = $boughtItems->first()->currencyRelation->name ?? '';
@endphp


<style>
.price-section {
    background-color: #f6f6f6;
}
.final-total{
    background-color:#436fa7;
    color: #fff;
    font-size: 20px;
    font-weight:bolder;
}
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title"> {{__('sales.sales_details_title')}}
                                <span class="pull-left">
                                    <a href="{{ route('boughtList.index') }}">
                                        <button class="btn mybtn bg-default">{{__('common.back')}}</button>
                                    </a>
                                </span>

                                <button onclick="print_page_with_image()" class="pull-left btn btn-success btn-sm btn-border m-l-10 hidden-print" >
                                   <i class="fas fa-print"></i>    {{__('sales.print_bill')}} 
                                </button>       
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                    
                                <div class="container col-md-12 col-sm-12 col-xs-12" id="print_area">
                                  <!-- Header -->
                                    <p class="">{{__('common.print_date')}} : {{ $todaysDate ?? '' }}</p>
                                    <table style="width:100%">
                                        <tr style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="2">
                                                <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('common.save_date')}} : {{ $boughtItems->first()->idate ?? '' }}</td>
                                            <td>{{__('common.bill')}} : {{ 'BUY_' . ($boughtItems->first()->billno ?? '') }}
                                                <br />{{__('common.factor')}} : {{ ($boughtItems->first()->factor ?? '') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('order.supplier_name')}} : {{ $boughtItems->first()->customerRelation->name ?? '' }}</td>
                                            <td>{{__('common.user')}} : {{ $boughtItems->first()->user_name ?? '' }}</td>
                                        </tr>
                                    </table>

                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />

                                     <!-- Items Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                              <thead>
                                                <tr>
                                                    <th style="width:5%">{{__('common.number')}}</th>
                                                    <th style="width:15%">{{__('buy.item')}}</th>
                                                    <th style="width:10%">{{__('buy.bought_amount')}}</th>
                                                    <th style="width:10%">{{__('common.unit')}}</th>
                                                    <th style="width:15%">{{__('common.unit_price')}}</th>
                                                    @if($saved_with_tax)
                                                    <th style="width:10%">{{__('buy.buy_tax_percentage_s')}}</th>
                                                    <th style="width:10%">{{__('buy.buy_tax_price_s')}}</th>
                                                    <th style="width:10%">{{__('buy.buy_up_vat')}}</th>
                                                    @endif
                                                    <th style="width:15%">{{__('common.total_price')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach($boughtItemDetails as $key => $detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->preListRelation->name ?? '-' }}</td>
                                                    <td>{{ number_format($detail->amount, 2) }}</td>
                                                    <td>{{ $detail->unitRelation->name ?? '-' }}</td>
                                                    @if($saved_with_tax)
                                                    <td>{{ number_format($detail->buy_up_vat ?? $detail->buy_up, 2) }}</td>
                                                    <td>% {{ $detail->buy_tax_per ?? 0 }}</td>
                                                    <td>{{ number_format($detail->buy_tax_price ?? 0, 2) }}</td>
                                                    <td>{{ number_format($detail->buy_up_vat ?? $detail->buy_up, 2) }}</td>
                                                    @else
                                                    <td>{{ number_format($detail->buy_up, 2) }}</td>
                                                    @endif
                                                    <td>{{ number_format($detail->total_vat ?? $detail->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="{{ $not_col_for_print }}" rowspan="8" style="padding: 40px;">
                                                        <div class="col-md-12" style="border:2px dotted #999; min-height:80px;background-color:#f8f8f8;border-top-right-radius:10px; border-bottom-left-radius:10px; padding: 10px;">
                                                            ...
                                                        </div>
                                                         <div class="col-md-12 m-t-20">
                                                              <br>
                                                             <strong>
                                                                 <h3>{{__('sales.stamp')}} ---------------------</h3>
                                                             </strong>
                                                         </div>
                                                    </td>
                                                    <td colspan="{{ $total_cols }}" class="price-section"> {{__('buy.total_bill_price')}} </td>
                                                    <td class="price-section">
                                                        {{ number_format($boughtItems->first()->total,2) }}
                                                        {{ $currencySymbol }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">  {{__('buy.cur_pay_yet')}}  </td>
                                                    <td  class="price-section">
                                                        {{ number_format($boughtItems->first()->cur_pay,2) }}
                                                         {{ $currencySymbol }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">  {{__('buy.remained')}}  </td>
                                                    <td  class="price-section">
                                                        {{ number_format($boughtItems->first()->remained,2) }}
                                                         {{ $currencySymbol }}
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- salesBillPayments -->
                                    <p style="margin-bottom:20px; padding-bottom:20px;"></p>
                                        <div class="table-responsive">
                                            <table class="table table-bordered new" style="width:100%">
                                                <thead>
                                                    <tr style="width:100%; background-color:#fff !important;color:#000 !important;">
                                                        <td colspan="7">{{__('common.bill_payments')}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>  {{__('common.number')}}   </th>
                                                        <th>  {{__('buy.billno')}}      </th>
                                                        <th>  {{__('common.account_payer')}}</th>
                                                        <th>  {{__('common.total_payment')}} </th>
                                                        <th>  {{__('common.date')}}</th>
                                                        <th class="hidden-print">  {{__('common.journal_code')}}</th>
                                                        <th>  {{__('common.note')}}</th>
                                                        <th>  {{__('common.user')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($boughtBillPayments as $key => $pay)
                                                @php 
                                                    $paymentsTotal += $pay->cur_pay;
                                                @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ 'BUY_'.$pay->billno ?? ' '}}</td>
                                                        <td>{{ $pay->account->name ?? '' }}</td>
                                                        <td>{{ number_format($pay->cur_pay,2)  }}</td>
                                                        <td>{{ $pay->payment_date }}</td>
                                                        <td class="hidden-print">{{ $pay->journal_code }}</td>
                                                        <td>{{ $pay->note }} </td>
                                                        <td>{{ $pay->user_name }} </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr style="background-color:#eee;">
                                                        <td colspan="3">{{__('common.total')}}</td>
                                                        <td>{{ number_format($paymentsTotal, 2) }}</td>
                                                        <td></td>
                                                        <td  class="hidden-print"></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <div class="col-md-12 m-t-20">
                                            <br>
                                            <strong>
                                                <h3>{{__('sales.sign_and_stamp')}} ---------------------</h3>
                                            </strong>
                                        </div>

                                    </div>

                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
