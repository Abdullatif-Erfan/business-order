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
                           <h4 class="card-title"> گزارش ماهانه </h4>
                        </div>

                        <div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->                    
                            
                            <!-- filter area -->
                            <div class="col-md-12 col-sm-12 col-xs-12 filter_cover m-t-10 m-b-5" id="filterArea">
                                <form action="{{ route('reports.monthly') }}" method="POST">
                                    @csrf
                                    <div class="row">

                                        <!-- Currency Selection -->
                                        <div class="col-md-5 col-sm-6 col-xs-6">
                                            <select class="form-control select2" 
                                                style="width: 100%; border:none !important; background-color:#ddd;" 
                                                name="currency_id">
                                                <option value="{{ $data['currency_id'] }}"> {{ $data['currency_name'] }}</option>
                                                <option value=""> --- انتخاب واحد پولی --- </option>
                                                @foreach($data['currency'] as $key => $val)
                                                    <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Year Selection -->
                                        <div class="col-md-5 col-sm-6 col-xs-6">
                                            <select class="form-control mt-1 mb-1"
                                                style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="year">
                                                <option value="{{ $data['year'] }}">{{ $data['year'] }}</option>
                                                <option value="">-- انتخاب سال --</option>
                                                @for($i = 1400; $i <= 1440; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-md-2 col-sm-6 col-xs-6">
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
                                                <center> گزارش ماهانه  </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th rowspan="3"><center> ماه </center></th>
                                        </tr>
                                        <tr>
                                            <th> <center>  گدام </center> </th>
                                            <th colspan="4"> <center>  فروشات </center> </th>
                                            <th colspan="4"> <center>  خرید </center> </th>
                                        </tr>
                                        <tr>
                                        <th style="border-top: 1px solid #fff !important;"><center>ورودی گدام</center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>فروشات </center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>دریافت فروشات</center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>طلب فروشات</center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>مفاد فروشات</center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>  خرید</center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>  پرداخت خرید </center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>  قرض خرید </center></th>
                                        <th style="border-top: 1px solid #fff !important;"><center>   ترانسپورت </center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @php
                                                // Initialize totals
                                                $totalWarehouseValue = 0;
                                                $totalSalesPayable = 0;
                                                $totalSalesCurPay = 0;
                                                $totalSalesRemained = 0;
                                                $totalSalesProfit = 0;
                                                $totalBoughtPayable = 0;
                                                $totalBoughtCurPay = 0;
                                                $totalBoughtRemained = 0;
                                                $totalBoughtTransport = 0;
                                            @endphp
                                            @foreach($monthlyReport as $row)

                                                 @php
                                                   

                                                    // Calculate values for totals
                                                    $warehouseValue = ($row->total_warehouse_value - $row->total_warehouse_wastage) ?? 0;
                                                    $salesPayable = $row->total_sales_payable ?? 0;
                                                    $salesCurPay = $row->total_sales_curpay ?? 0;
                                                    $salesRemained = $row->total_sales_remained ?? 0;
                                                    $salesProfit = $row->total_sales_profit ?? 0;
                                                    $boughtPayable = $row->total_bought_payable ?? 0;
                                                    $boughtCurPay = $row->total_bought_curpay ?? 0;
                                                    $boughtRemained = $row->total_bought_remained ?? 0;
                                                    $boughtTransport = $row->total_bought_transport ?? 0;

                                                    // Sum up values
                                                    $totalWarehouseValue += $warehouseValue;
                                                    $totalSalesPayable += $salesPayable;
                                                    $totalSalesCurPay += $salesCurPay;
                                                    $totalSalesRemained += $salesRemained;
                                                    $totalSalesProfit += $salesProfit;
                                                    $totalBoughtPayable += $boughtPayable;
                                                    $totalBoughtCurPay += $boughtCurPay;
                                                    $totalBoughtRemained += $boughtRemained;
                                                    $totalBoughtTransport += $boughtTransport;
                                                @endphp
                                                <tr>
                                                    <td>{{$row->month}} </td>
                                                    <td>{{ number_format($row->total_warehouse_value - $row->total_warehouse_wastage) }}</td>
                                                    <td>{{ number_format($row->total_sales_payable) }}</td>
                                                    <td>{{ number_format($row->total_sales_curpay) }}</td>
                                                    <td>{{ number_format($row->total_sales_remained) }}</td>
                                                    <td>{{ number_format($row->total_sales_profit) }}</td>
                                                    <td>{{ number_format($row->total_bought_payable) }}</td>
                                                    <td>{{ number_format($row->total_bought_curpay) }}</td>
                                                    <td>{{ number_format($row->total_bought_remained) }}</td>
                                                    <td>{{ number_format($row->total_bought_transport) }}</td>
                                                </tr>
                                            @endforeach
                                    </tbody>
                                    <tfoot>
                                            <tr style="background-color:#fff8d9">
                                                <td><strong>مجموع</strong></td>
                                                <td><strong>{{ number_format($totalWarehouseValue) }}</strong></td>
                                                <td><strong>{{ number_format($totalSalesPayable) }}</strong></td>
                                                <td><strong>{{ number_format($totalSalesCurPay) }}</strong></td>
                                                <td><strong>{{ number_format($totalSalesRemained) }}</strong></td>
                                                <td><strong>{{ number_format($totalSalesProfit) }}</strong></td>
                                                <td><strong>{{ number_format($totalBoughtPayable) }}</strong></td>
                                                <td><strong>{{ number_format($totalBoughtCurPay) }}</strong></td>
                                                <td><strong>{{ number_format($totalBoughtRemained) }}</strong></td>
                                                <td><strong>{{ number_format($totalBoughtTransport) }}</strong></td>
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
