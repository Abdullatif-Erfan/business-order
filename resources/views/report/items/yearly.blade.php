@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                        <div class="card-header" style="padding:10px">
                           <a href="{{ route('reports.home') }}">
                               <button class="btn btn-sm pull-left"><i class="fas fa-arrow-left"></i></button>
                           </a>
                           <button class="printBtn m-l-40" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                           <h4 class="card-title">  {{__('reports.yearly_report')}} </h4>
                        </div>

                        <div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->                    
                            
                            <!-- filter area -->
                            <div class="col-md-12 col-sm-12 col-xs-12 filter_cover m-t-10 m-b-5" id="filterArea">
                                <form action="{{ route('reports.yearly') }}" method="POST">
                                    @csrf
                                    <div class="row">

                                        <!-- Currency Selection -->
                                        <div class="col-md-10 col-sm-10 col-xs-6">
                                            <select class="form-control select2" 
                                                style="width: 100%; border:none !important; background-color:#ddd;" 
                                                name="currency_id">
                                                <option value="{{ $data['currency_id'] }}"> {{ $data['currency_name'] }}</option>
                                                <option value=""> --- {{__('common.currency')}} --- </option>
                                                @foreach($data['currency'] as $key => $val)
                                                    <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-md-2 col-sm-2 col-xs-6">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-6">
                                                    <button type="submit" id="btn-filter" class="btn btn-info2 form-control btn-sm" style="border-left: 4px solid #fca505;">
                                                        <i class="fa fa-search" style="font-size:12px;color:#ee70c9 !important;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                            <!-- / filter area -->

                            <!-- table -->
                            <div class="table_responsive" style="padding:5px; min-width: 800px" id="print_area">
                                <table class="table table-bordered table-striped dataTable my_table" style="width:100%">
                                    <thead>
                                       <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="10">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="10">
                                                <center>   {{__('reports.yearly_report')}}   </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th rowspan="3"><center>  {{__('reports.year')}}  </center></th>
                                        </tr>
                                        <tr>
                                            <th colspan="3"> <center>  {{__('reports.buy')}} </center> </th>
                                            <th colspan="4"> <center>   {{__('reports.sales')}}  </center> </th>
                                        </tr>
                                        <tr>
                                        <th style="border-top: 1px solid #fff !important;"><center>  
                                                {{__('reports.buy')}}</center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>  
                                            {{__('reports.bought_paid')}} </center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center> 
                                            {{__('reports.buy_low')}} </center></th>
                                        
                                        <th style="border-top: 1px solid #fff !important;"><center>
                                        {{__('reports.sales')}} </center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>
                                        {{__('reports.sales_income')}}</center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>
                                        {{__('reports.sales_talab')}} </center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>
                                         {{__('reports.sales_profit')}}</center></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                          @php
                                                // Initialize totals
                                                $totalSalesPayable = 0;
                                                $totalSalesCurPay = 0;
                                                $totalSalesRemained = 0;
                                                $totalSalesProfit = 0;
                                                $totalBoughtPayable = 0;
                                                $totalBoughtCurPay = 0;
                                                $totalBoughtRemained = 0;
                                            @endphp
                                            @foreach($yearlyReport as $row)
                                            @php
                                                  
                                                   $salesPayable = $row->total_sales_payable ?? 0;
                                                   $salesCurPay = $row->total_sales_curpay ?? 0;
                                                   $salesRemained = $row->total_sales_remained ?? 0;
                                                   $salesProfit = $row->total_sales_profit ?? 0;
                                                   $boughtPayable = $row->total_bought_payable ?? 0;
                                                   $boughtCurPay = $row->total_bought_curpay ?? 0;
                                                   $boughtRemained = $row->total_bought_remained ?? 0;

                                                   // Sum up values
                                                   $totalSalesPayable += $salesPayable;
                                                   $totalSalesCurPay += $salesCurPay;
                                                   $totalSalesRemained += $salesRemained;
                                                   $totalSalesProfit += $salesProfit;
                                                   $totalBoughtPayable += $boughtPayable;
                                                   $totalBoughtCurPay += $boughtCurPay;
                                                   $totalBoughtRemained += $boughtRemained;
                                               @endphp
                                                <tr>
                                                    <td>{{$row->year}} </td>
                                                    <td>{{ number_format($row->total_bought_payable,2) }}</td>
                                                    <td>{{ number_format($row->total_bought_curpay,2) }}</td>
                                                    <td>{{ number_format($row->total_bought_remained,2) }}</td>
                                                    <td>{{ number_format($row->total_sales_payable,2) }}</td>
                                                    <td>{{ number_format($row->total_sales_curpay,2) }}</td>
                                                    <td>{{ number_format($row->total_sales_remained,2) }}</td>
                                                    <td>{{ number_format($row->total_sales_profit,2) }}</td>
                                                </tr>
                                            @endforeach
                                    </tbody>
                                    <tfoot>
                                            <tr style="background-color:#fff8d9">
                                                <td><strong>{{__('reports.total')}}</strong></td>
                                                <td><strong>{{ number_format($totalBoughtPayable,2) }}</strong></td>
                                                <td><strong>{{ number_format($totalBoughtCurPay,2) }}</strong></td>
                                                <td><strong>{{ number_format($totalBoughtRemained,2) }}</strong></td>
                                                <td><strong>{{ number_format($totalSalesPayable,2) }}</strong></td>
                                                <td><strong>{{ number_format($totalSalesCurPay,2) }}</strong></td>
                                                <td><strong>{{ number_format($totalSalesRemained,2) }}</strong></td>
                                                <td><strong>{{ number_format($totalSalesProfit,2) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                </table>
                            </div>
                            <!-- /table -->

                        </div> <!-- / card-body -->
                    </div> 
                </div>
            </div>  
        </div>
    </div>
</div>

@endsection
