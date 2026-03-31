@extends('layouts.app')

@section('content')


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
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                           
                                <div class="container col-md-12 col-sm-12 col-xs-12" style="padding: 10px 10px;">
                                   
                                    <table style="width:100%">
                                         <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="4">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> {{__('sales.customer')}} : {{ $warehouseSales->first()->accountRelation->name ?? '' }}</td>
                                            <td> {{__('common.unit')}}: {{ $warehouseSales->first()->currencyRelation->name ?? '' }}</td>
                                            <td> {{__('common.save_date')}} : {{ $warehouseSales->first()->ifull_date ?? '' }}</td>
                                            <td> {{__('common.bill')}} : {{ 'SALES_' . ($warehouseSales->first()->billno ?? '') }}</td>
                                        </tr>
                                    </table>
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th> {{__('common.number')}} </th>
                                                    <th>  {{__('sales.item')}} </th>
                                                    <th> {{__('buy.sold_amount')}} </th>
                                                    <th>  {{__('sales.customer')}}</th>
                                                    <th> {{__('common.unit_price')}}</th>
                                                    <th> {{__('common.discount')}} </th>
                                                    <th> {{__('sales.profit')}}</th>
                                                    <th> {{__('common.total_price')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($salesDetails as $key => $detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->preListRelation->name ?? ' '}}</td>
                                                    <td> {{ number_format($detail->amount,2)  }} </td>
                                                    <td>{{ $detail->unitRelation->name }}</td>
                                                    <td>{{ number_format($detail->sell_up,2) }}</td>
                                                    <td>{{ number_format($detail->discount,2) }} </td>
                                                    <td>{{ number_format($detail->profit,2) }} </td>
                                                    <td>{{ number_format($detail->total,2) }} </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <table class="table table-bordered new" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                        <tr>
                                            <td> {{__('common.total_price')}} &nbsp; </td>
                                            <td>{{  number_format($warehouseSales->first()->total_price,2) }}</td>
                                            <td> {{__('sales.total_discount')}} </td>
                                            <td> {{ number_format($warehouseSales->first()->total_discount,2) }} </td>
                                            <td> {{__('buy.payable')}} </td>
                                            <td> {{ number_format($warehouseSales->first()->payable,2) }}</td>
                                        </tr>
                                        <tr>
                                        <td> {{__('buy.cur_pay')}} </td>
                                            <td> {{ number_format($warehouseSales->first()->cur_pay,2)  }} </td>
                                            <td> {{__('buy.remained')}} </td>
                                            <td>{{  number_format($warehouseSales->first()->remained,2) }}</td>
                                            <td>{{__('buy.note')}}</td>
                                            <td>{{$warehouseSales->first()->note}}</td>
                                        </tr>
                                    </table>
                                </div>


                                <div class="visible-print" style="width:100%;margin: 35px 0px; overflow:hidden; height: 24px;color:#000"> ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ </div>

                                <div class="container col-md-12 col-sm-12 col-xs-12 visible-print" id="print_area">
                                 <p class="d-none">{{__('common.print_date')}}‌ : {{ now()->format('Y-m-d') }}</p>
                                    <table style="width:100%">
                                       <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="2">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> {{__('common.save_date')}}‌ : {{ $warehouseSales->first()->ifull_date ?? '' }}</td>
                                            <td> {{__('common.bill')}}‌ : {{ 'SALES_' . ($warehouseSales->first()->billno ?? '') }}</td>
                                        </tr>
                                        <tr>
                                            <td> {{__('sales.customer')}}‌ : {{ $warehouseSales->first()->accountRelation->name ?? '' }}</td>
                                            <td> {{__('common.user')}}‌ : {{ $warehouseSales->first()->iby ?? '' }}</td>
                                        </tr>
                                    </table>
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('common.number')}}</th>
                                                    <th>{{__('sales.item')}} </th>
                                                    <th>{{__('sales.sales_amount')}}</th>
                                                    <th>{{__('common.unit')}}</th>
                                                    <th>{{__('common.unit_price')}}</th>
                                                    <th>{{__('common.discount')}}</th>
                                                    <th>{{__('common.total_price')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($salesDetails as $key => $detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->preListRelation->name ?? ' '}}</td>
                                                    <td>{{ number_format($detail->amount,2) }} </td>
                                                    <td>{{ $detail->unitRelation->name }}</td>
                                                    <td>{{ number_format($detail->sell_up,2) }} </td>
                                                    <td>{{ number_format($detail->discount,2) }} </td>
                                                    <td>{{ number_format($detail->total,2) }}</td>
                                                </tr>
                                                  @endforeach
                                                <tr>
                                                    <td colspan="4" rowspan="8" style="padding: 40px;">
                                                        <div class="col-md-12" style="border:2px dotted #999; min-height:80px;background-color:#f8f8f8;border-top-right-radius:10px; border-bottom-left-radius:10px; padding: 10px;">
                                                            {{__('buy.note')}} :  {{ $orgbios[0]->note_for_print }}
                                                        </div>
                                                         <div class="col-md-12 m-t-20">
                                                              <br>
                                                             <strong>
                                                                 <h3>{{__('sales.sign_and_stamp')}} ---------------------</h3>
                                                             </strong>
                                                         </div>
                                                    </td>
                                                    <td colspan="2" class="price-section"> {{__('buy.total_bill_price')}} </td>
                                                    <td class="price-section">
                                                        {{ number_format($warehouseSales->first()->total_price,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="price-section">  {{__('common.discount')}} </td>
                                                    <td  class="price-section">
                                                         {{  number_format($warehouseSales->first()->total_discount,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="price-section">  {{__('buy.payable')}} </td>
                                                    <td class="price-section">
                                                         {{ number_format($warehouseSales->first()->payable,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section">  {{__('buy.cur_pay')}}  </td>
                                                    <td  class="price-section">
                                                        {{ number_format($warehouseSales->first()->cur_pay,2) }}
                                                         {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                  @php
                                                    $prev_baqi  =  $customer_balance['loans'] - $warehouseSales->first()->remained;
                                                    $prev_talab =  $customer_balance['talabat'];
                                                    $balance =  $customer_balance['loans'] -  $customer_balance['talabat'];
                                                  @endphp
                                                    <td colspan="2" class="price-section">  {{__('buy.remained')}}  </td>
                                                    <td  class="price-section">
                                                          {{  number_format($warehouseSales->first()->remained,2) }}
                                                          {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section">{{__('buy.old_remained')}}</td>
                                                    <td class="price-section">
                                                        {{ number_format($prev_baqi,2) }} 
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section"> {{__('buy.old_talabat')}}</td>
                                                    <td class="price-section">
                                                        {{ number_format($prev_talab,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="final-total"> {{__('buy.total_payable')}} </td>
                                                    <td class="final-total">
                                                        {{ number_format($balance,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>




                                    <!-- پارچه گدام -->
                                    <div class="visible-print" style="width:100%;margin: 35px 0px; overflow:hidden; height: 24px;color:#000"> ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ </div>
                                        <div class="container col-md-12 col-sm-12 col-xs-12 visible-print" id="print_area2">
                                        <p class="d-none"> {{__('common.print_data')}} : {{ now()->format('Y-m-d') }}</p>
                                            <table style="width:100%">
                                            <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                                    <td colspan="2">
                                                    <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('common.save_data')}} : {{ $warehouseSales->first()->ifull_date ?? '' }}</td>
                                                    <td>{{__('common.bill')}} : {{ 'SALES_' . ($warehouseSales->first()->billno ?? '') }}</td>
                                                </tr>
                                                <tr>
                                                    <td> {{__('sales.customer')}} : {{ $warehouseSales->first()->accountRelation->name ?? '' }}</td>
                                                    <td> {{__('common.user')}} : {{ $warehouseSales->first()->iby ?? '' }}</td>
                                                </tr>
                                            </table>
                                            <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                            <div class="table-responsive">
                                                <table class="table table-bordered new" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>{{__('common.number')}}</th>
                                                            <th> {{__('sales.item')}} </th>
                                                            <th> {{__('sales.sales_amount')}}</th>
                                                            <th>{{__('common.unit')}}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($salesDetails as $key => $detail)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $detail->preListRelation->name ?? ' '}}</td>
                                                            <td>{{ number_format($detail->amount,2) }} </td>
                                                            <td>{{ $detail->unitRelation->name }}</td>
                                                        </tr>
                                                        @endforeach

                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                               



                                <!-- buttons -->
                                <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    
                                    <!-- print button -->
                                    <button onclick="print_page_with_image()" class="btn btn-success btn-sm btn-border m-r-10 hidden-print" >
                                    <i class="fas fa-print"></i>    {{__('sales.print_bill')}} 
                                    </button>


                                    <button onclick="print_page_with_image(2)" class="btn btn-success btn-sm m-r-10 hidden-print" >
                                    <i class="fas fa-print"></i> {{__('sales.warehouse_bill')}}   
                                    </button>
                                            
                                    <!-- edit button -->
                                    @if($warehouseSales->first()->is_cleared == 0)
                                    <a href="{{ route('sales.edit', $warehouseSales->first()->billno) }}"   class="hidden-print">
                                        <button class="btn btn-primary btn-sm m-r-10">
                                        <i class="fas fa-pen"></i>  {{__('common.edit')}} 
                                        </button>
                                    </a>
                                    @endif
                                      

                                    </div>
                                </div>
                                <!-- /buttons -->

                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
