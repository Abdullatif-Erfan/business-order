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
    color: #8a6d3b;
    border-color: #8a6d3b;
}
.custom_badge_info {
    color: #31708f;
    border-color: #31708f;
}
.custom_badge_success {
    color: #3c763d;
    border-color: #3c763d;
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
                    <div class="col-md-12" id="print_area">
                        <div class="panel-group" id="accordion">
                            <div class="col-xs-12">
                                <div class="row">

                                    <img src="{{ asset($orgbios[0]->header ?? '') }}" alt="navbar brand" class="navbar-brand visible-print" style="width: 100% !important;">

                                    <!-- Income Section -->
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

                                                <!-- Talabat -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:120px !important;">{{__('reports.talabat')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($talabat->total_talabat ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Diff Income -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:120px !important;">{{__('reports.diff_income')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($transactionSummary->total_income ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Sales Net Income -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:120px !important;">{{__('reports.sales_net_income')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($salesProfit->total_profit ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Sales Income and Profit -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:120px !important;">{{__('reports.sales_income_and_profit')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($transactionSummary->total_sold ?? 0, 2) }}</strong></td>
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

                                                <!-- Loan -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:120px !important;">{{__('reports.loan')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($talabat->total_loan ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Diff Expense -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:120px !important;">{{__('reports.diff_expense')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($transactionSummary->total_expense ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Employee Salaries -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:120px !important;">{{__('reports.emp_salaries')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($transactionSummary->total_salary ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Buy and Transport -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:120px !important;">{{__('reports.buy_and_transport')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($transactionSummary->total_bought ?? 0, 2) }}</strong></td>
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

                                                <!-- Stock Availability -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:200px !important;">{{__('reports.stock_availability')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($warehouseValue->total_warehouse_value ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Company Cash -->
                                                @php
                                                    $totalCacheIn = $transactionSummary->total_cache_in ?? 0;
                                                    $totalCacheOut = $transactionSummary->total_cache_out ?? 0;
                                                    $totalCompanyCache = $totalCacheIn - $totalCacheOut;
                                                @endphp
                                                <table class="table table-bordered" style="width:100%"> 
                                                    <tr>
                                                        <th style="width:200px !important;">{{__('reports.company_cache')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($totalCompanyCache, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Company Capital -->
                                                @php
                                                    $talabatTotal = $talabat->total_talabat ?? 0;
                                                    $loanTotal = $talabat->total_loan ?? 0;
                                                    $warehouseTotal = $warehouseValue->total_warehouse_value ?? 0;
                                                    
                                                    $finalCapital = $warehouseTotal + $totalCompanyCache + ($talabatTotal - $loanTotal);
                                                @endphp
                                                <table class="table table-bordered" style="width:100%">
                                                   
                                                    <tr>
                                                        <th style="width:200px !important;font-weight:bolder">{{__('reports.company_capital')}}</th>
                                                        <td style="width:70px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($finalCapital, 2) }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>{{__('reports.formula')}}:</td>
                                                        <td>
                                                            <span class="custom_badge custom_badge_info">{{__('reports.stock_availability')}}</span>
                                                            +
                                                            <span class="custom_badge custom_badge_info">{{__('reports.total_company_cache')}}</span>
                                                            +
                                                            <span class="custom_badge custom_badge_info">{{__('reports.talabat')}}</span>
                                                            -
                                                            <span class="custom_badge custom_badge_warning">{{__('reports.loan')}}</span>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- Company Net Income -->
                                                @php
                                                    $totalIncome = ($transactionSummary->total_income ?? 0) + ($salesProfit->total_profit ?? 0);
                                                    $totalExpense = ($transactionSummary->total_salary ?? 0) + ($transactionSummary->total_expense ?? 0);
                                                    $netIncome = $totalIncome - $totalExpense;
                                                @endphp
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:200px !important;font-weight:bolder">{{__('reports.company_net_income')}}</th>
                                                        <td style="width:70px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($netIncome, 2) }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>{{__('reports.formula')}}:</td>
                                                        <td>
                                                            <span class="custom_badge custom_badge_info">{{__('reports.diff_expense')}}</span>
                                                            +
                                                            <span class="custom_badge custom_badge_info">{{__('reports.emp_salaries')}}</span>
                                                            -
                                                            <span class="custom_badge custom_badge_info">{{__('reports.diff_income')}}</span>
                                                            +
                                                            <span class="custom_badge custom_badge_warning">{{__('reports.sales_net_income')}}</span>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <hr>

                                                <!-- Participants -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="8"><h3>{{__('reports.participants')}}</h3></td>
                                                        </tr>
                                                        <tr>
                                                            <th>{{__('common.number')}}</th>
                                                            <th>{{__('reports.account')}}</th>
                                                            <th>{{ __('settings.percentage') }}</th>
                                                            <th>{{__('reports.capital_based_percentage')}}</th>
                                                            <th>{{__('reports.talabat')}}</th>
                                                            <th>{{__('reports.loan')}}</th>
                                                            <th>{{__('reports.balance')}}</th>
                                                            <th>{{__('reports.specify')}}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($participant_accounts as $index => $row)
                                                            @php
                                                                $capital = ($row->percent / 100) * $finalCapital;
                                                                $totalTalabat = ($row->loan_paid ?? 0) + ($row->cache_paid ?? 0);
                                                                $totalLoan = ($row->loan_recieved ?? 0) + ($row->cache_recieved ?? 0);
                                                                $balance = $capital + $totalTalabat - $totalLoan;
                                                                
                                                                $status = $balance == 0 ? 'clear' : ($balance < 0 ? 'baqi' : 'talab');
                                                                $statusLabel = $balance == 0 ? __('reports.clear') : ($balance < 0 ? __('reports.baqi') : __('reports.talab'));
                                                                $statusClass = $balance == 0 ? 'custom_badge_success' : ($balance < 0 ? 'custom_badge_warning' : 'custom_badge_info');
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $row->name }}</td>
                                                                <td>{{ $row->percent }} %</td>
                                                                <td>{{ number_format($capital, 2) }}</td>
                                                                <td>{{ number_format($totalTalabat, 2) }}</td>
                                                                <td>{{ number_format($totalLoan, 2) }}</td>
                                                                <td>{{ number_format($balance, 2) }}</td>
                                                                <td><span class="custom_badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="8" class="text-center">{{__('common.no_data_found')}}</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- /General Section -->

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