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

<script>
    window.onload = function() {
        print_page_with_image();
    };
</script>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">جزییات فورم فروشات
                                <span class="pull-left">
                                    <a href="{{ route('sales.index') }}">
                                        <button class="btn mybtn bg-default">برگشت به لیست</button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                           
                                <div class="container col-md-12 col-sm-12 col-xs-12" style="padding: 10px 10px;">
                                

                                <div class="container col-md-12 col-sm-12 col-xs-12 " id="print_area">
                                 <p>تاریخ چاپ‌ : {{ now()->format('Y-m-d') }}</p>
                                    <table style="width:100%">
                                       <tr  style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="2">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>تاریخ ثبت : {{ $warehouseSales->first()->ifull_date ?? '' }}</td>
                                            <td>نمبر بل : {{ 'SALES_' . ($warehouseSales->first()->billno ?? '') }}</td>
                                        </tr>
                                        <tr>
                                            <td> مشتری : {{ $warehouseSales->first()->accountRelation->name ?? '' }}</td>
                                            <td> کاربر : {{ $warehouseSales->first()->iby ?? '' }}</td>
                                        </tr>
                                    </table>
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>شماره</th>
                                                    <th> جنس </th>
                                                    <th>تعداد فروش</th>
                                                    <th>واحد</th>
                                                    <th>قیمت فی واحد</th>
                                                    <th>تخفیف</th>
                                                    <th>قیمت مجموعی</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($salesDetails as $key => $detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->preListRelation->name ?? ' '}}</td>
                                                    <td>{{ number_format($detail->amount) }} </td>
                                                    <td>{{ $detail->unitRelation->name }}</td>
                                                    <td>{{ number_format($detail->sell_up) }} </td>
                                                    <td>{{ number_format($detail->discount) }} </td>
                                                    <td>{{ number_format($detail->total) }}</td>
                                                </tr>
                                                  @endforeach
                                                <tr>
                                                    <td colspan="4" rowspan="8" style="padding: 40px;">
                                                        <div class="col-md-12" style="border:2px dotted #999; min-height:80px;background-color:#f8f8f8;border-top-right-radius:10px; border-bottom-left-radius:10px; padding: 10px;">
                                                            نوت :  {{ $orgbios[0]->note_for_print }}
                                                        </div>
                                                         <div class="col-md-12 m-t-20">
                                                              <br>
                                                             <strong>
                                                                 <h3>مهر و امضا ---------------------</h3>
                                                             </strong>
                                                         </div>
                                                    </td>
                                                    <td colspan="2" class="price-section">مجموع بل</td>
                                                    <td class="price-section">
                                                        {{ number_format($warehouseSales->first()->total_price) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="price-section">  تخفیف </td>
                                                    <td  class="price-section">
                                                         {{  number_format($warehouseSales->first()->total_discount) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="price-section">  قابل پرداخت </td>
                                                    <td class="price-section">
                                                         {{ number_format($warehouseSales->first()->payable) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section"> پرداخت فعلی  </td>
                                                    <td  class="price-section">
                                                        {{ number_format($warehouseSales->first()->cur_pay) }}
                                                         {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                  @php
                                                    $prev_baqi  =  $customer_balance['loans'] - $warehouseSales->first()->remained;
                                                    $prev_talab =  $customer_balance['talabat'];
                                                    $balance =  $customer_balance['loans'] -  $customer_balance['talabat'];
                                                  @endphp
                                                    <td colspan="2" class="price-section">  باقی  </td>
                                                    <td  class="price-section">
                                                          {{  number_format($warehouseSales->first()->remained) }}
                                                          {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section">بقایای سابقه</td>
                                                    <td class="price-section">
                                                        {{ number_format($prev_baqi) }} 
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section">طلبات سابقه</td>
                                                    <td class="price-section">
                                                        {{ number_format($prev_talab) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="final-total">مبلغ مجموعی قابل پرداخت</td>
                                                    <td class="final-total">
                                                        {{ number_format($balance) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>

                                                
                                            </tbody>
                                        </table>
                                    </div>


                               


                                </div>

                                <!-- buttons -->
                                <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    
                                    <!-- print button -->
                                    <button onclick="print_page_with_image()" class="btn btn-success btn-sm btn-border m-r-10 hidden-print" >
                                    <i class="fas fa-print"></i>  چاپ  بل 
                                    </button>
                                            
                                      

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
