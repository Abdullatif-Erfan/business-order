@extends('layouts.app')

@section('content')
@php
  $not_col_for_print = $saved_with_tax ? "4":"3"; 
  $total_cols = $saved_with_tax ? "3":"2"; 

   // Customer Balance Calculations
   $customerLoans = $customer_balance['loans'] ?? 0;        // Company owes customer (طلبات)
   $customerTalabat = $customer_balance['talabat'] ?? 0;    // Customer owes company (قرض)
  
  // Net balance: positive = company owes customer, negative = customer owes company
  // بیلانس مشتری تابحال به شمول این فروشات
  $netCustomerBalance = $customerLoans - $customerTalabat;
  
  // Current bill remained
  $currentRemained = $warehouseSales->first()->remained ?? 0;
  
  // Previous balance (customer's balance before this bill)
  // بیلانس گذشته بدون این فروشات
  $previousBalance = $netCustomerBalance - $currentRemained;
  
  // Total due including previous balance
  $totalDue = $netCustomerBalance + $currentRemained;
  $paymentsTotal = 0;
    // $prevAndCurBalance = $customer_balance->loans - $customer_balance->talabat;
    // $prevBaqi = $warehouseSales->first()->remained - $prevAndCurBalance;
    // $finalBalance =   $prevAndCurBalance + $prevBaqi; 

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
                                    <a href="{{ route('sales.index') }}">
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
                                 <p class="">{{__('common.print_date')}}‌ : {{ $todaysDate ?? '' }}</p>
                                    <table style="width:100%">
                                       <tr class="" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="2">
                                               <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> {{__('common.sales_date')}}‌ : {{ $warehouseSales->first()->idate ?? '' }}</td>
                                            <td> {{__('sales.billno')}}‌ : {{ 'SALES_' . ($warehouseSales->first()->billno ?? '') }}
                                                <br />
                                                 {{__('common.factor')}} : {{ ($warehouseSales->first()->factor ?? '') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> {{__('sales.customer')}}‌ : {{ $warehouseSales->first()->accountRelation->name ?? '' }}</td>
                                            <td> {{__('common.user')}}‌ : {{ $warehouseSales->first()->user_name ?? '' }}</td>
                                        </tr>
                                    </table>

                                    
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                              <thead>
                                                <tr>
                                                    <th>  {{__('common.number')}}   </th>
                                                    <th>  {{__('sales.item')}}      </th>
                                                    <th>  {{__('buy.sold_amount')}} </th>
                                                    <th>  {{__('sales.unit')}}</th>
                                                    @if($saved_with_tax) 
                                                    <th>  {{__('buy.sales_tax_percentage')}} </th>
                                                    <th>  {{__('buy.sell_tax_price')}} </th>
                                                    @endif
                                                    <th>  {{__('common.unit_price')}}</th>
                                                    <th>  {{__('common.total_price')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach($salesDetails as $key => $detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->preListRelation->name ?? ' '}}</td>
                                                    <td> {{ $detail->amount  }} </td>
                                                    <td>{{ $detail->unitRelation->name }}</td>
                                                     @if($saved_with_tax) 
                                                    <td> % {{ $detail->sell_tax_per }} </td>
                                                    <td> {{  number_format($detail->sell_tax_price,2) }} </td>
                                                    @endif
                                                    <td>{{ number_format($detail->sell_up,2) }}</td>
                                                    <td>{{ number_format($detail->total,2) }} </td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="{{ $not_col_for_print }}" rowspan="8" style="padding: 40px;">
                                                        <div class="col-md-12" style="border:2px dotted #999; min-height:80px;background-color:#f8f8f8;border-top-right-radius:10px; border-bottom-left-radius:10px; padding: 10px;">
                                                            {{__('buy.note')}} :  {{ $orgbios[0]->note_for_print }}
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
                                                        {{ number_format($warehouseSales->first()->total,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">  {{__('buy.cur_pay_yet')}}  </td>
                                                    <td  class="price-section">
                                                        {{ number_format($warehouseSales->first()->cur_pay,2) }}
                                                         {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">  {{__('buy.remained')}}  </td>
                                                    <td  class="price-section">
                                                        {{ number_format($warehouseSales->first()->remained,2) }}
                                                         {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <!-- باقیات گذشته -->
                                               
                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">  {{__('buy.old_remained')}}  </td>
                                                    <td  class="price-section">
                                                         {{ number_format($previousBalance,2) }}
                                                         {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                            
                                                <!-- بیلانس فعلی -->
                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">  {{__('buy.balance')}}  </td>
                                                    <td  class="price-section">
                                                       {{ number_format($netCustomerBalance, 2) }}
                                                         {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>


                                    <p style="margin-bottom:20px; padding-bottom:20px;"></p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                              <thead>
                                                <tr style="width:100%; background-color:#fff !important;color:#000 !important;">
                                                    <td colspan="7">{{__('common.bill_payments')}}</td>
                                                </tr>
                                                <tr>
                                                    <th>  {{__('common.number')}}   </th>
                                                    <th>  {{__('sales.billno')}}      </th>
                                                    <th>  {{__('common.total_payment')}} </th>
                                                    <th>  {{__('common.date')}}</th>
                                                    <th class="hidden-print">  {{__('common.journal_code')}}</th>
                                                    <th>  {{__('common.note')}}</th>
                                                    <th>  {{__('common.user')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach($salesBillPayments as $key => $pay)
                                               @php 
                                                 $paymentsTotal += $pay->amount;
                                               @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ 'SALES_'.$pay->billno ?? ' '}}</td>
                                                    <td>{{ number_format($pay->amount,2)  }}</td>
                                                    <td>{{ $pay->payment_date }}</td>
                                                    <td class="hidden-print">{{ $pay->journal_code }}</td>
                                                    <td>{{ $pay->note }} </td>
                                                    <td>{{ $pay->user_name }} </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="background-color:#eee;">
                                                    <td colspan="2">{{__('common.total')}}</td>
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
