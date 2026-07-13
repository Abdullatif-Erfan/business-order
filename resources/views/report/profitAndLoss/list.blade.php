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
                        <div class="panel-group" id="accordion2">
                            <div class="col-xs-12">
                                <div class="row">

                                   

                                    <!-- General Section -->
                                    <div class="col-md-12">
                                     
                                        <div class="panel-heading m-t-10 hidden-print" style="background-color:#f0eded">
                                            <h4 class="panel-title">
                                                <strong> 
                                                    <span class="custom_badge custom_badge_info">{{__('reports.diff_expense')}}</span>
                                                    +
                                                    <span class="custom_badge custom_badge_info">{{__('reports.emp_salaries')}}</span>
                                                    -
                                                    <span class="custom_badge custom_badge_warning">{{__('reports.sales_net_income')}}</span>
                                                </strong>   
                                            </h4>
                                        </div>
                                        <div id="collapseGeneral" class="panel-collapse collapse in" style="height: auto;">
                                            <div class="panel-body" id="body">

                                                <!-- Stock Availability -->
                                                <table class="table table-bordered" style="width:100%">
                                                     <tr>
                                                        <td colspan="3"> <img src="{{ asset($orgbios[0]->header ?? '') }}" alt="navbar brand" class="navbar-brand visible-print" style="width: 100% !important;"></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width:200px !important;">{{__('reports.sales_net_income')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($salesProfit->total_profit ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>
                                                 <!-- Diff Expense -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:200px !important;">{{__('reports.diff_expense')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($transactionSummary->total_expense ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Employee Salaries -->
                                                <table class="table table-bordered" style="width:100%">
                                                    <tr>
                                                        <th style="width:200px !important;">{{__('reports.emp_salaries')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($transactionSummary->total_salary ?? 0, 2) }}</strong></td>
                                                    </tr>
                                                </table>

                                                <!-- Company Cash -->
                                              @php
                                                    $totalIncome = ($transactionSummary->total_income ?? 0) + ($salesProfit->total_profit ?? 0);
                                                    $totalExpense = ($transactionSummary->total_salary ?? 0) + ($transactionSummary->total_expense ?? 0);
                                                    $finalNetIncome = $totalIncome - $totalExpense;
                                                @endphp
                                                <table class="table table-bordered" style="width:100%"> 
                                                    <tr>
                                                        <th style="width:200px !important;">{{__('reports.company_net_profit')}}</th>
                                                        <td style="width:130px !important;"><strong>{{__('reports.afn')}}:</strong></td>
                                                        <td><strong>{{ number_format($finalNetIncome, 2) }}</strong></td>
                                                    </tr>
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