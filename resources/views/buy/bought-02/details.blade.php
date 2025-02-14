@extends('layouts.app')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">جزییات فورم خریداری
                                <span class="pull-left">
                                    <a href="{{ url('boughtList') }}">
                                        <button class="btn mybtn bg-default">برگشت به لیست</button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;" id="print_area">
                           
                                <div class="container col-md-12 col-sm-12 col-xs-12" style="padding: 10px 10px;">
                                    <p class="d-none">تاریخ چاپ‌ : {{ now()->format('Y-m-d') }}</p>
                                    <table style="width:100%">
                                         <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="3">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>نام فروشنده: {{ $boughtItems->first()->customer_account_name ?? '' }}</td>
                                            <td>تاریخ ثبت : {{ $boughtItems->first()->idate ?? '' }}</td>
                                            <td>نمبر بل : {{ 'BUY_' . ($boughtItems->first()->billno ?? '') }}</td>
                                        </tr>
                                    </table>
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>شماره</th>
                                                    <th>نوع فورم خریداری</th>
                                                    <th>تعداد خرید</th>
                                                    <th>واحد</th>
                                                    <th>قیمت فی واحد</th>
                                                    <th>قیمت مجموعی</th>
                                                    <th>تاریخ انقضا</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($boughtItemDetails as $key => $detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->pre_list_name }}</td>
                                                    <td>{{ $detail->amount }}</td>
                                                    <td>{{ $detail->unit_name }}</td>
                                                    <td>{{ $detail->bought_up }}</td>
                                                    <td>{{ $detail->total }}</td>
                                                    <td>{{ $detail->expire_date }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <table class="table table-bordered new" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                        <tr>
                                            <td>مجموع پول</td>
                                            <td>{{ $boughtItems->first()->total_price ?? '' }}</td>
                                            <td>تخفیف</td>
                                            <td>{{ $boughtItems->first()->discount ?? '' }}</td>
                                            <td>قابل پرداخت</td>
                                            <td>{{ $boughtItems->first()->payable ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td>پرداخت فعلی</td>
                                            <td>{{ $boughtItems->first()->cur_pay ?? '' }}</td>
                                            <td>باقی</td>
                                            <td>{{ $boughtItems->first()->remained ?? '' }}</td>
                                            <td>حساب پرداخت کننده</td>
                                            <td>{{ $boughtItems->first()->account_name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td>واحد پولی</td>
                                            <td>{{ $boughtItems->first()->currency_name ?? '' }}</td>
                                            <td>مصارف ترانسپورت</td>
                                            <td>{{ $boughtItems->first()->trans_spend ?? '' }}</td>
                                            <td></td><td></td>
                                        </tr>
                                        <tr>
                                            <td>تفصیلات</td>
                                            <td colspan="5">{{ $boughtItems->first()->note ?? '' }}</td>
                                        </tr>
                                    </table>
                                </div>


                                <div class=" visible-print" style="width:100%;margin: 35px 0px; overflow:hidden; height: 24px;color:#000"> ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ </div>

                                <div class="container col-md-12 col-sm-12 col-xs-12 visible-print">
                                    <table style="width:100%">
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="3">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>نام فروشنده: {{ $boughtItems->first()->customer_account_name ?? '' }}</td>
                                            <td>تاریخ ثبت : {{ $boughtItems->first()->idate ?? '' }}</td>
                                            <td>نمبر بل : {{ 'BUY_' . ($boughtItems->first()->billno ?? '') }}</td>
                                        </tr>
                                    </table>
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>شماره</th>
                                                    <th>نوع فورم خریداری</th>
                                                    <th>تعداد خرید</th>
                                                    <th>واحد</th>
                                                    <th>قیمت فی واحد</th>
                                                    <th>قیمت مجموعی</th>
                                                    <th>تاریخ انقضا</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($boughtItemDetails as $key => $detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->pre_list_name }}</td>
                                                    <td>{{ $detail->amount }}</td>
                                                    <td>{{ $detail->unit_name }}</td>
                                                    <td>{{ $detail->bought_up }}</td>
                                                    <td>{{ $detail->total }}</td>
                                                    <td>{{ $detail->expire_date }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <table class="table table-bordered new" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                        <tr>
                                            <td>مجموع پول</td>
                                            <td>{{ $boughtItems->first()->total_price ?? '' }}</td>
                                            <td>تخفیف</td>
                                            <td>{{ $boughtItems->first()->discount ?? '' }}</td>
                                            <td>قابل پرداخت</td>
                                            <td>{{ $boughtItems->first()->payable ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td>پرداخت فعلی</td>
                                            <td>{{ $boughtItems->first()->cur_pay ?? '' }}</td>
                                            <td>باقی</td>
                                            <td>{{ $boughtItems->first()->remained ?? '' }}</td>
                                            <td>حساب پرداخت کننده</td>
                                            <td>{{ $boughtItems->first()->account_name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td>واحد پولی</td>
                                            <td>{{ $boughtItems->first()->currency_name ?? '' }}</td>
                                            <td>مصارف ترانسپورت</td>
                                            <td>{{ $boughtItems->first()->trans_spend ?? '' }}</td>
                                            <td></td><td></td>
                                        </tr>
                                        <tr>
                                            <td>تفصیلات</td>
                                            <td colspan="5">{{ $boughtItems->first()->note ?? '' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- buttons -->
                                <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    
                                    <!-- print button -->
                                    <button onclick="print_page()" class="btn btn-success btn-sm btn-border m-r-10 hidden-print" >
                                    <i class="fas fa-print"></i>  چاپ  بل 
                                    </button>
                                            
                                    <!-- edit button -->
                                    <a href="{{ route('boughtList.edit', $boughtItems->first()->btimes) }}"   class="hidden-print">
                                        <button class="btn btn-primary btn-sm m-r-10">
                                        <i class="fas fa-pen"></i>  ویرایش 
                                        </button>
                                    </a>

                                       @if(auth()->user()->hasAccess('buy','delete_records'))
                                       <a href="{{ route('boughtList.destroy', $boughtItems->first()->btimes) }}"  onClick="return doConfirm();" class="hidden-print">
                                            <button class="btn btn-danger btn-sm m-r-10">
                                            <i class="fas fa-trash error "></i>  حذف 
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
