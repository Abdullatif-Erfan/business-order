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
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                           
                                <div class="container col-md-12 col-sm-12 col-xs-12" style="padding: 10px 10px;">
                                   
                                    <table style="width:100%">
                                         <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="5">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> {{__('sales.customer')}} : {{ $warehouseSales->first()->accountRelation->name ?? '' }}</td>
                                            <td> {{__('common.unit')}}: {{ $warehouseSales->first()->currencyRelation->name ?? '' }}</td>
                                            <td> {{__('common.sales_date')}} : {{ $warehouseSales->first()->idate ?? '' }}</td>
                                            <td> {{__('common.bill')}} : {{ 'SALES_' . ($warehouseSales->first()->billno ?? '') }}</td>
                                            <td> {{__('common.factor')}} : {{ ($warehouseSales->first()->factor ?? '') }}</td>
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
                                                    <th>  {{__('sales.profit')}}</th>
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
                                            <td> {{  number_format($warehouseSales->first()->total,2) }} </td>
                                            <td> {{__('buy.cur_pay')}}</td>
                                            <td> {{ number_format($warehouseSales->first()->cur_pay,2)  }} </td>
                                            <td> {{__('buy.remained')}} </td>
                                            <td> {{  number_format($warehouseSales->first()->remained,2) }} </td>
                                        </tr>
                                        <tr>
                                            <td> {{__('buy.note')}} </td>
                                            <td colspan="3">{{$warehouseSales->first()->note}} </td>
                                        </tr>
                                    </table>
                                </div>


  
                                <!-- buttons -->
                                <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    
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
