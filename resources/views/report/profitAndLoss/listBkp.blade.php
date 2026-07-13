@extends('layouts.app')
@section('content')

<style>
.custom_badge{
    background-color: transparent !important;
    border-radius: 50px;
    margin-right: auto;
    line-height: 1;
    padding: 2px 10px;
    vertical-align: middle;
    font-weight: 400;
    font-size: 11px;
    border: 1px solid #ddd;
}
.custom_badge_warning {
    color: #8a6d3b; /* Bootstrap warning text color */
    border-color: #8a6d3b;
}
.custom_badge_info {
    color: #31708f; /* Bootstrap info text color */
    border-color: #31708f;
}

</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 mt-3">
                    <div class="card">
                        <div class="card-header" style="padding:10px">
                            <h4 class="card-title"> {{__('reports.profit_and_loss_title')}} 
                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                            </h4>
                        </div>

                        <div class="card-body">
                            
                    <!-- panel -->
                    <div class="col-md-12"  id="print_area">
                        <div class="panel-group" id="accordion">
                            <div class="col-xs-12">
                                <div class="row">

                                    <img src="{{ asset($orgbios[0]->header)  }}" alt="navbar brand" class="navbar-brand visible-print" style="width: 100% !important;">

                                     <!-- Income Seciont -->
                                    <div class="col-md-6 col-sm-12 col-x-12">
                                     
                                        <div class="panel-heading m-t-10" style="background-color:#f0eded">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseIncomes" class="">
                                                    <strong> {{__('reports.income_section')}} </strong>   
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseIncomes" class="panel-collapse collapse in" style="height: auto;">
                                                <div class="panel-body" id="body">       

                                                @php
                                                    $currencies = $transactionSummary ?? [];
                                                    $currencyCount = count($currencies);
                                                    $baseCurrency = collect($currencies)->where('is_base', 1)->first();
                                                    $totalConvertedIncome = collect($currencies)->sum('converted_total_income');
                                                    $totalConvertedSoldIncome = collect($currencies)->sum('converted_total_sold');
                                                    $totalConvertedExpense = collect($currencies)->sum('converted_total_expense');
                                                    $totalConvertedSalary = collect($currencies)->sum('converted_total_salary');
                                                    $totalConverted‌‌Bought = collect($currencies)->sum('converted_total_bought');
                                                    $totalConvertedCacheIn = collect($currencies)->sum('converted_total_cache_in');
                                                    $totalConvertedCacheOut = collect($currencies)->sum('converted_total_cache_out');
                                                    $finalTotalCache = $totalConvertedCacheIn - $totalConvertedCacheOut;
                                                @endphp


                                                  @php
                                                    $total_talabat = $talabat ?? [];
                                                    $total_talabat_count = count($total_talabat);
                                                    $talabatBaseCurrency = collect($total_talabat)->where('is_base', 1)->first();
                                                    $totalConvertedTalabat = collect($total_talabat)->sum('converted_total_talab');
                                                @endphp

                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th rowspan="{{ $total_talabat_count + 1 }}" style="width:90px !important;">
                                                        {{__('reports.talabat')}}</th>
                                                        <td style="width:130px !important;color:{{$talabatBaseCurrency['color']}}">{{ $talabatBaseCurrency['currency_name'] ?? 'N/A' }}:</td>
                                                        <td style="color:{{$talabatBaseCurrency['color']}}">
                                                        {{ number_format($talabatBaseCurrency['total_talabat'],2) ?? 'N/A' }}</td>
                                                    </tr>
                                                    
                                                    @foreach ($total_talabat as $talab)
                                                        @if (!$talab['is_base']) 
                                                            <tr>
                                                                <td style="color:{{$talab['color']}}">{{ $talab['currency_name'] }} :</td>
                                                                <td style="color:{{$talab['color']}}">
                                                                 <span class="custom_badge custom_badge_info">
                                                                {{ number_format($talab['total_talabat'],2 ?? 0) }}
                                                                 </span> &nbsp; =   
                                                                    {{ number_format($talab['converted_total_talab'],2 ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <tr>
                                                        <td>  <strong> {{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> {{ number_format($totalConvertedTalabat,2) }} {{ $talabatBaseCurrency['symbols'] ?? 'N/A' }} </strong></td>
                                                    </tr>
                                                </table>



                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th rowspan="{{ $currencyCount + 1 }}" style="width:90px !important;">
                                                        {{__('reports.diff_income')}}</th>
                                                        <td style="width:130px !important;color:{{$baseCurrency['color']}}">{{ $baseCurrency['currency_name'] ?? 'N/A' }}:</td>
                                                        <td style="color:{{$baseCurrency['color']}}">{{ number_format($baseCurrency['total_income']) ?? 'N/A' }}</td>
                                                    </tr>
                                                    
                                                    @foreach ($currencies as $currency)
                                                        @if (!$currency['is_base']) 
                                                            <tr>
                                                                <td style="color:{{$currency['color']}}">{{ $currency['currency_name'] }} :</td>
                                                                <td style="color:{{$currency['color']}}">
                                                                 <span class="custom_badge custom_badge_info">
                                                                {{ number_format($currency['total_income'],2 ?? 0) }}
                                                                 </span> &nbsp; =   
                                                                    {{ number_format($currency['converted_total_income'],2 ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <tr>
                                                        <td>  <strong> {{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> {{ number_format($totalConvertedIncome,2) }} {{ $baseCurrency['symbols'] ?? 'N/A' }} </strong></td>
                                                    </tr>
                                                </table>



                                                <!-- مفاد خالص فروشات -->
                                                @php
                                                    $currencies3 = $salesProfit ?? [];
                                                    $currencyCount3 = count($currencies3);
                                                    $baseCurrency3 = collect($currencies3)->where('is_base', 1)->first();
                                                    $totalConvertedTotalProfit = collect($currencies3)->sum('converted_total_profit');
                                                @endphp

                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th rowspan="{{ $currencyCount3 + 1 }}" style="width:90px !important;">  
                                                       {{__('reports.sales_net_income')}}</th>
                                                        <td style="width:130px !important; color:{{ $baseCurrency3['color'] ?? '' }}">
                                                            {{ $baseCurrency3['currency_name'] ?? 'N/A' }}:
                                                        </td>
                                                        <td style="color:{{ $baseCurrency3['color'] ?? '' }}">
                                                            {{ number_format($baseCurrency3['total_profit'],2 ?? 0) }}
                                                        </td>
                                                    </tr>
                                                    
                                                    @foreach ($currencies3 as $currency)
                                                        @if (!$currency['is_base']) 
                                                            <tr>
                                                                <td style="color:{{ $currency['color'] ?? '' }}">{{ $currency['currency_name'] }} :</td>
                                                                <td style="color:{{ $currency['color'] ?? '' }}">
                                                                    <span class="custom_badge custom_badge_info">
                                                                        {{ number_format($currency['total_profit'],2 ?? 0) }}
                                                                    </span> &nbsp; =   
                                                                    {{ number_format($currency['converted_total_profit'],2 ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    

                                                    <tr>
                                                        <td> <strong>{{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> {{ number_format($totalConvertedTotalProfit,2) }} {{ $baseCurrency3['symbols'] ?? 'N/A' }} </strong></td>
                                                    </tr>
                                                </table>

                                                    <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th rowspan="{{ $currencyCount + 1 }}" style="width:90px !important;">
                                                        {{__('reports.sales_income_and_profit')}} </th>
                                                        <td style="width:130px !important;color:{{$baseCurrency['color']}}">{{ $baseCurrency['currency_name'] ?? 'N/A' }}:</td>
                                                        <td style="color:{{$baseCurrency['color']}}">{{ number_format($baseCurrency['total_sold'],2) ?? 'N/A' }}</td>
                                                    </tr>
                                                    
                                                    @foreach ($currencies as $currency)
                                                        @if (!$currency['is_base']) 
                                                            <tr>
                                                                <td style="color:{{$currency['color']}}">{{ $currency['currency_name'] }} :</td>
                                                                <td style="color:{{$currency['color']}}">
                                                                 <span class="custom_badge custom_badge_info">
                                                                {{ number_format($currency['total_sold'],2 ?? 0) }}
                                                                 </span> &nbsp; =   
                                                                    {{ number_format($currency['converted_total_sold'],2 ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <tr>
                                                        <td>  <strong>{{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> {{ number_format($totalConvertedSoldIncome,2) }} {{ $baseCurrency['symbols'] ?? 'N/A' }} </strong></td>
                                                    </tr>
                                                </table>
                                                

                                                </div>
                                            </div>

                                    </div>
                                    <!-- / Income Section  -->


                                    <!-- Expense Section -->
                                    <div class="col-md-6 col-sm-12 col-x-12">
                                     
                                        <div class="panel-heading m-t-10" style="background-color:#f0eded">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseExpense" class="">
                                                    <strong>{{__('reports.expense_section')}}</strong>   
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseExpense" class="panel-collapse collapse in" style="height: auto;">
                                                <div class="panel-body" id="body">       
                                                @php
                                                    $total_loans_count = count($total_talabat);
                                                    $loansBaseCurrency = collect($total_talabat)->where('is_base', 1)->first();
                                                    $totalConvertedLoan = collect($total_talabat)->sum('converted_total_loan');
                                                @endphp
                                                <!-- قرضه -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th rowspan="{{ $currencyCount + 1 }}" style="width:90px !important;">قرضه </th>
                                                        <td style="width:130px !important;color:{{$loansBaseCurrency['color']}}">{{ $loansBaseCurrency['currency_name'] ?? 'N/A' }}:</td>
                                                        <td style="color:{{$loansBaseCurrency['color']}}">{{ number_format($loansBaseCurrency['total_loan'],2) ?? 'N/A' }}</td>
                                                    </tr>
                                                    
                                                    @foreach ($talabat as $loans)
                                                        @if (!$loans['is_base']) 
                                                            <tr>
                                                                <td style="color:{{$loans['color']}}">{{ $loans['currency_name'] }} :</td>
                                                                <td style="color:{{$loans['color']}}">
                                                                 <span class="custom_badge custom_badge_info">
                                                                {{ number_format($loans['total_loan'],2 ?? 0) }}
                                                                 </span> &nbsp; =   
                                                                    {{ number_format($loans['converted_total_loan'],2 ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <tr>
                                                        <td>  <strong>{{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> {{ number_format($totalConvertedLoan,2) }} {{ $baseCurrency['symbols'] ?? 'N/A' }} </strong></td>
                                                    </tr>
                                                </table>
                                                
                                                <!-- مصارف متفرقه -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th rowspan="{{ $currencyCount + 1 }}" style="width:90px !important;">
                                                        {{__('reports.diff_expense')}} </th>
                                                        <td style="width:130px !important;color:{{$baseCurrency['color']}}">{{ $baseCurrency['currency_name'] ?? 'N/A' }}:</td>
                                                        <td style="color:{{$baseCurrency['color']}}">{{ number_format($baseCurrency['total_expense'],2) ?? 'N/A' }}</td>
                                                    </tr>
                                                    
                                                    @foreach ($currencies as $currency)
                                                        @if (!$currency['is_base']) 
                                                            <tr>
                                                                <td style="color:{{$currency['color']}}">{{ $currency['currency_name'] }} :</td>
                                                                <td style="color:{{$currency['color']}}">
                                                                 <span class="custom_badge custom_badge_info">
                                                                {{ number_format($currency['total_expense'],2 ?? 0) }}
                                                                 </span> &nbsp; =   
                                                                    {{ number_format($currency['converted_total_expense'],2 ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <tr>
                                                        <td>  <strong>{{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> {{ number_format($totalConvertedExpense,2) }} {{ $baseCurrency['symbols'] ?? 'N/A' }} </strong></td>
                                                    </tr>
                                                </table>


                                                <!-- معاشات کارمندان -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th rowspan="{{ $currencyCount + 1 }}" style="width:90px !important;"> 
                                                        {{__('reports.emp_salaries')}} </th>
                                                        <td style="width:130px !important;color:{{$baseCurrency['color']}}">{{ $baseCurrency['currency_name'] ?? 'N/A' }}:</td>
                                                        <td style="color:{{$baseCurrency['color']}}">{{ number_format($baseCurrency['total_salary'],2) ?? 'N/A' }}</td>
                                                    </tr>
                                                    
                                                    @foreach ($currencies as $currency)
                                                        @if (!$currency['is_base']) 
                                                            <tr>
                                                                <td style="color:{{$currency['color']}}">{{ $currency['currency_name'] }} :</td>
                                                                <td style="color:{{$currency['color']}}">
                                                                 <span class="custom_badge custom_badge_info">
                                                                {{ number_format($currency['total_salary'],2 ?? 0) }}
                                                                 </span> &nbsp; =   
                                                                    {{ number_format($currency['converted_total_salary'],2 ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <tr>
                                                        <td>  <strong>{{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> {{ number_format($totalConvertedSalary,2) }} {{ $baseCurrency['symbols'] ?? 'N/A' }} </strong></td>
                                                    </tr>
                                                </table>


                                              <!-- خرید + ترانسپورت  -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th rowspan="{{ $currencyCount + 1 }}" style="width:90px !important;"> 
                                                        {{__('reports.buy_and_transport')}} </th>
                                                        <td style="width:130px !important;color:{{$baseCurrency['color']}}">{{ $baseCurrency['currency_name'] ?? 'N/A' }}:</td>
                                                        <td style="color:{{$baseCurrency['color']}}">{{ number_format($baseCurrency['total_bought'],2) ?? 'N/A' }}</td>
                                                    </tr>
                                                    
                                                    @foreach ($currencies as $currency)
                                                        @if (!$currency['is_base']) 
                                                            <tr>
                                                                <td style="color:{{$currency['color']}}">{{ $currency['currency_name'] }} :</td>
                                                                <td style="color:{{$currency['color']}}">
                                                                 <span class="custom_badge custom_badge_info">
                                                                {{ number_format($currency['total_bought'],2 ?? 0) }}
                                                                 </span> &nbsp; =   
                                                                    {{ number_format($currency['converted_total_bought'],2 ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <tr>
                                                        <td>  <strong>{{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> {{ number_format($totalConverted‌‌Bought,2) }} {{ $baseCurrency['symbols'] ?? 'N/A' }} </strong></td>
                                                    </tr>
                                                </table>




                                                </div>
                                            </div>

                                    </div>
                                    <!-- /Expense Section -->

                              

                                    <!-- General Section -->
                                    <div class="col-md-12">
                                     
                                        <div class="panel-heading m-t-10" style="background-color:#f0eded">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseGeneral" class="">
                                                    <strong> {{__('reports.general_section')}} </strong>   
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseGeneral" class="panel-collapse collapse in" style="height: auto;">
                                                <div class="panel-body" id="body">      


                                                @php
                                                    $currencies2 = $warehouseValue ?? [];
                                                    $currencyCount2 = count($currencies2);
                                                    $baseCurrency2 = collect($currencies2)->where('is_base', 1)->first();
                                                    $totalConvertedValue = collect($currencies2)->sum('converted_total_warehouse_value');
                                                @endphp

                                                <!-- موجودی گدام -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th rowspan="{{ $currencyCount2 + 1 }}" style="width:200px !important;"> 
                                                        {{__('reports.stock_availability')}} </th>
                                                        <td style="width:130px !important; color:{{ $baseCurrency2['color'] ?? '' }}">
                                                            {{ $baseCurrency2['currency_name'] ?? 'N/A' }}:
                                                        </td>
                                                        <td style="color:{{ $baseCurrency2['color'] ?? '' }}">
                                                            {{ number_format($baseCurrency2['total_warehouse_value'],2 ?? 0) }}
                                                        </td>
                                                    </tr>
                                                    
                                                    @foreach ($currencies2 as $currency)
                                                        @if (!$currency['is_base']) 
                                                            <tr>
                                                                <td style="color:{{ $currency['color'] ?? '' }}">{{ $currency['currency_name'] }} :</td>
                                                                <td style="color:{{ $currency['color'] ?? '' }}">
                                                                    <span class="custom_badge custom_badge_info">
                                                                        {{ number_format($currency['total_warehouse_value'],2 ?? 0) }}
                                                                    </span> &nbsp; =   
                                                                    {{ number_format($currency['converted_total_warehouse_value'],2 ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    

                                                    <tr>
                                                        <td> <strong>{{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> {{ number_format($totalConvertedValue,2) }} {{ $baseCurrency2['symbols'] ?? 'N/A' }} </strong></td>
                                                    </tr>
                                                </table>


                                                <!-- پول نقد شرکت -->
                                                <table class="table table-bordered" style="width:100%"> 
                                                    <tr>
                                                        <th rowspan="{{ $currencyCount + 1 }}" style="width:200px !important;"> 
                                                        {{__('reports.company_cache')}} </th>
                                                        <td style="width:130px !important;color:{{$baseCurrency['color'] ?? ''}}">
                                                            {{ $baseCurrency['currency_name'] ?? 'N/A' }}:
                                                        </td>
                                                        <td style="color:{{$baseCurrency['color'] ?? ''}}">
                                                        {{ 
                                                            isset($baseCurrency['total_cache_in'], $baseCurrency['total_cache_out']) 
                                                            ? number_format(($baseCurrency['total_cache_in'] ?? 0) - ($baseCurrency['total_cache_out'] ?? 0),2) 
                                                            : 'N/A' }}
                                                        </td>
                                                    </tr>

                                                    @foreach ($currencies as $currency)
                                                        @if (!$currency['is_base'])
                                                            @php
                                                                $total_cache_in = $currency['total_cache_in'] ?? 0;
                                                                $total_cache_out = $currency['total_cache_out'] ?? 0;
                                                                $converted_cache_in = $currency['converted_total_cache_in'] ?? 0;
                                                                $converted_cache_out = $currency['converted_total_cache_out'] ?? 0;
                                                            @endphp 
                                                            <tr>
                                                                <td style="color:{{$currency['color'] ?? ''}}">{{ $currency['currency_name'] }} :</td>
                                                                <td style="color:{{$currency['color'] ?? ''}}">
                                                                    <span class="custom_badge custom_badge_info">
                                                                        {{ number_format($total_cache_in - $total_cache_out,2) }}
                                                                    </span> &nbsp; =   
                                                                    {{ number_format($converted_cache_in - $converted_cache_out,2) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <tr>
                                                        <td><strong>{{__('reports.total_price')}} : </strong></td>
                                                        <td><strong> 
                                                            {{ number_format($finalTotalCache,2) }} 
                                                            {{ $baseCurrency['symbols'] ?? 'N/A' }} 
                                                        </strong></td>
                                                    </tr>
                                                </table>

                                                <!-- total_assets = total_warehouse_value + total_cache_income(recieved-paid) + total_talabat - (total_warhouse_wastage + total_loan) -->

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:200px !important;font-weight:bolder">
                                                                 {{__('reports.company_capital')}} </th>
                                                                  <td style="width:70px !important;"> {{__('reports.afn')}} : </td>
                                                                  <td> <strong> 
                                                                  
                                                                  @php
                                                                        $final_capital = $totalConvertedValue
                                                                                    + $finalTotalCache
                                                                                    + ($totalConvertedTalabat - $totalConvertedLoan);
                                                                    @endphp
                                                                     {{ 
                                                                       number_format($final_capital,2);
                                                                      }}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                  <td> {{__('reports.formula')}} : </td>
                                                                  <td> 
                                                                    <span class="custom_badge custom_badge_info">{{__('reports.stock_availability')}} </span>
                                                                     +
                                                                     <span class="custom_badge custom_badge_info">
                                                                      {{__('reports.total_company_cache')}}</span>
                                                                     +
                                                                     <span class="custom_badge custom_badge_info">
                                                                     {{__('reports.talabat')}}</span> 
                                                                     - 
                                                                    <span class="custom_badge custom_badge_warning">
                                                                    {{__('reports.loan')}} </span>
                                                                    
                                                                   </td>
                                                            </tr>
                                                    </table>



                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:200px !important;font-weight:bolder" >
                                                                {{__('reports.company_net_income')}} 
                                                                </th>
                                                                  <td style="width:70px !important;">{{__('reports.afn')}} : </td>
                                                                  <td>   <strong>
                                                                        @php 
                                                                            $total_income = $totalConvertedIncome + $totalConvertedTotalProfit;

                                                                            $total_expense = $totalConvertedSalary + $totalConvertedExpense;
                                                                        @endphp
                                                                        {{ 
                                                                            number_format($total_income - $total_expense,2)
                                                                         }}
                                                                            </strong> </td>
                                                            </tr>
                                                            <tr>
                                                                  <td>{{__('reports.formula')}} : </td>
                                                                  <td> 
                                                                    <span class="custom_badge custom_badge_info"> 
                                                                    {{__('reports.diff_expense')}} </span>
                                                                     +
                                                                     <span class="custom_badge custom_badge_info"> 
                                                                     {{__('reports.emp_salaries')}} </span>
                                                                     -
                                                                     <span class="custom_badge custom_badge_info">
                                                                     {{__('reports.diff_income')}}</span> 
                                                                     + 
                                                                    <span class="custom_badge custom_badge_warning">
                                                                    {{__('reports.sales_net_income')}} 
                                                                    </span>
                                                                    
                                                                   </td>
                                                            </tr>
                                                    </table>


                                                    <hr>
                                                    <table class="table table-bordered"  style="width:100%">
                                                      <thead>
                                                        <tr>
                                                          <td colspan="8"><h3> {{__('reports.participants')}}  </h3></td>
                                                        </tr>
                                                        <tr>
                                                            <th> {{__('common.number')}}</th>
                                                            <th>{{__('reports.account')}}</th>
                                                            <th>{{ __('settings.percentage') }}</th>
                                                            <th>{{__('reports.capital_based_percentage')}} </th>
                                                            <th>{{__('reports.talabat')}}</th>
                                                            <th>{{__('reports.loan')}}</th>
                                                            <th>{{__('reports.balance')}} </th>
                                                            <th>{{__('reports.specify')}}</th>
                                                        </tr>
                                                      </thead>
                                                      <tbody>
                                                      @foreach($participant_accounts as $index => $row)
                                                      @php
                                                            $capital = ($row->percent / 100) * $final_capital;
                                                            $total_talabat = $row->loan_paid + $row->cache_paid;
                                                            $total_loan = $row->loan_recieved + $row->cache_recieved;
                                                            $balance = $capital + $total_talabat - $total_loan;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $row->name }}</td>
                                                            <td>{{ $row->percent }} % </td>
                                                            <td>{{ number_format($capital, 2) }}</td>
                                                            <td>{{ number_format($total_talabat,2) }}</td>
                                                            <td>{{ number_format($total_loan,2) }}</td>
                                                            <td>{{ number_format($balance, 2) }}</td>
                                                            <td> {{ $balance == 0 ? __('reports.clear') : ($balance < 0 ? __('reports.baqi') : __('reports.talab')) }}</td>
                                                        </tr>
                                                        @endforeach
                                                      </tbody>
                                                    </table>


                                                </div>
                                            </div>

                                    </div>
                                    <!-- /Expense Section -->


                                </div>
                            </div>
                         </div>
                      </div>
                    </div> <!-- End card-body -->
                    </div> <!-- End main card -->
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
