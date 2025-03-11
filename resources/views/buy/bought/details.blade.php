@extends('layouts.app')

@section('content')
@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
    // Show the notification using the data from the session
    $(document).ready(function(){
        showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
    });
</script>
@endif

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
                            <h4 class="card-title">جزییات فورم خریداری
                                <span class="pull-left">
                                    <a href="{{ url('boughtList') }}">
                                        <button class="btn mybtn bg-default">برگشت به لیست</button>
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
                                            <td> حساب پرداخت کننده: {{ $boughtItems->first()->account->name ?? '' }}</td>
                                            <td>   واحد پولی: {{ $boughtItems->first()->currency->name ?? '' }}</td>
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
                                                    <th>فروشنده</th>
                                                    <th> جنس </th>
                                                    <th>تعداد خرید</th>
                                                    <th>واحد</th>
                                                    <th>قیمت فی واحد</th>
                                                    <th>قیمت مجموعی</th>
                                                    <th>تخفیف</th>
                                                    <th>ترانسپورت</th>
                                                    <th>تاریخ انقضا</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($boughtItemDetails as $key => $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail->accountRelation->name ?? ' ' }}</td>
                                                <td>{{ $detail->preListRelation->name ?? ' ' }}</td>
                                                
                                                <td>
                                                    @php
                                                        echo number_format($detail->amount);
                                                    @endphp
                                                </td>
                                                
                                                <td>{{ $detail->unitRelation->name }}</td>

                                                <td>
                                                    @php
                                                        echo number_format($detail->bought_up);
                                                    @endphp
                                                </td>

                                                <td>
                                                    @php
                                                        echo  number_format($detail->total);
                                                    @endphp
                                                </td>

                                                <td>
                                                    @php
                                                        echo  number_format($detail->discount);
                                                    @endphp
                                                </td>

                                                <td>
                                                    @php
                                                        echo number_format($detail->transport);
                                                    @endphp
                                                </td>
                                                <td>
                                                    {{  $detail->expire_date }}
                                                </td>
                                            </tr>
                                        @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                    <table class="table table-bordered new" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                        <tr>
                                            <td>مجموع پول &nbsp; </td>
                                            <td>
                                                @php
                                                    echo  number_format($boughtItems->first()->total_price);
                                                @endphp
                                            </td>
                                            <td> تخفیف </td>
                                            <td> 
                                                @php
                                                    echo   
                                                    number_format($boughtItems->first()->discount);
                                                @endphp
                                            </td>
                                            <td> مصارف ترانسپورت </td>
                                            <td> @php
                                                    echo 
                                                    number_format($boughtItems->first()->trans_spend);
                                                @endphp
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> قابل پرداخت</td>
                                            <td> @php
                                                    echo 
                                                    number_format($boughtItems->first()->payable);
                                                 @endphp
                                            </td>
                                            <td> پرداخت فعلی</td>
                                            <td> @php
                                                    echo  number_format($boughtItems->first()->cur_pay);
                                                    @endphp
                                            </td>
                                            <td> باقی </td>
                                            <td>
                                                @php
                                                    echo 
                                                    number_format($boughtItems->first()->remained);
                                                @endphp
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>نوت</td>
                                            <td colspan="5">{{$boughtItems->first()->note}}</td>
                                        </tr>
                                    </table>
                                </div>


                                <div class=" visible-print" style="width:100%;margin: 35px 0px; overflow:hidden; height: 24px;color:#000"> ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ </div>

                                <div class="container col-md-12 col-sm-12 col-xs-12 visible-print" id="print_area">
                                 <p class="d-none">تاریخ چاپ‌ : {{ now()->format('Y-m-d') }}</p>
                                    <table style="width:100%">
                                       <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="2">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>تاریخ ثبت : {{ $boughtItems->first()->idate ?? '' }}</td>
                                            <td>نمبر بل : {{ 'BUY_' . ($boughtItems->first()->billno ?? '') }}</td>
                                        </tr>
                                        <tr>
                                            <td> حساب پرداخت کننده: {{ $boughtItems->first()->account->name ?? '' }}</td>
                                            <td>   کاربر : {{ $boughtItems->first()->iby ?? '' }}</td>
                                        </tr>
                                    </table>
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>شماره</th>
                                                    <th>فروشنده</th>
                                                    <th> جنس </th>
                                                    <th>تعداد خرید</th>
                                                    <th>واحد</th>
                                                    <th>قیمت فی واحد</th>
                                                    <th>قیمت مجموعی</th>
                                                    <th>تخفیف</th>
                                                    <th>ترانسپورت</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($boughtItemDetails as $key => $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail->accountRelation->name ?? ' ' }}</td>
                                                <td>{{ $detail->preListRelation->name ?? ' ' }}</td>
                                                
                                                <td>
                                                    @php
                                                        echo  number_format($detail->amount);
                                                    @endphp
                                                </td>
                                                
                                                <td>{{ $detail->unitRelation->name }}</td>

                                                <td>
                                                    @php
                                                        echo number_format($detail->bought_up);
                                                    @endphp
                                                </td>

                                                <td>
                                                    @php
                                                        echo  number_format($detail->total);
                                                    @endphp
                                                </td>

                                                <td>
                                                    @php
                                                        echo  number_format($detail->discount);
                                                    @endphp
                                                </td>

                                                <td>
                                                    @php
                                                        echo  number_format($detail->transport);
                                                    @endphp
                                                </td>
                                            </tr>
                                        @endforeach

                                                <tr>
                                                    <td colspan="5" rowspan="7" style="padding: 40px;">
                                                        <div class="col-md-12" style="border:2px dotted #999; min-height:80px;background-color:#f8f8f8;border-top-right-radius:10px; border-bottom-left-radius:10px; padding: 10px;">
                                                            نوت : {{ $orgbios[0]->note_for_print }}
                                                        </div>
                                                         <div class="col-md-12 m-t-20">
                                                              <br>
                                                             <strong>
                                                                 <h3>مهر و امضا ---------------------</h3>
                                                             </strong>
                                                         </div>
                                                    </td>
                                                    <td colspan="2" class="price-section">مجموع بل</td>
                                                    <td colspan="2" class="price-section">
                                                       
                                                        @php
                                                           echo  number_format($boughtItems->first()->total_price);
                                                        @endphp

                                                        {{ $boughtItems->first()->currency->name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="price-section">  تخفیف </td>
                                                    <td colspan="2" class="price-section">
                                                        @php
                                                           echo 
                                                           number_format($boughtItems->first()->trans_spend);
                                                        @endphp
                                                        {{ $boughtItems->first()->currency->name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="price-section">  قابل پرداخت </td>
                                                    <td colspan="2" class="price-section">
                                                        @php echo number_format($boughtItems->first()->cur_pay);
                                                        @endphp
                                                        {{ $boughtItems->first()->currency->name ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section"> پرداخت فعلی  </td>
                                                    <td colspan="2" class="price-section">
                                                         @php
                                                           echo 
                                                           number_format($boughtItems->first()->payable) ;
                                                         @endphp
                                                         {{ $boughtItems->first()->currency->name ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section">  باقی  </td>
                                                    <td colspan="2" class="price-section">
                                                          @php
                                                           echo  number_format($boughtItems->first()->remained);
                                                          @endphp
                                                          {{ $boughtItems->first()->currency->name ?? '' }}
                                                    </td>
                                                </tr>

                                                <!-- <tr>
                                                    <td colspan="2" class="price-section"> بقایای سابقه   </td>
                                                    <td colspan="2" class="price-section">
                                                        ???
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="final-total">  مجموع عمومی   </td>
                                                    <td colspan="2" class="final-total">
                                                        ???
                                                    </td>
                                                </tr> -->
                                                
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
                                            
                                    <!-- edit button -->
                                    @if($boughtItems->first()->is_cleared == 0)
                                    <a href="{{ route('boughtList.edit', $boughtItems->first()->times) }}"   class="hidden-print">
                                        <button class="btn btn-primary btn-sm m-r-10">
                                        <i class="fas fa-pen"></i>  ویرایش 
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

<script>
function showNotification(message, type = 'info', from = 'top', align = 'left', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> پیام </span>';
    
    if (style === "withicon") {
        content.icon = 'fa fa-bell';
    } else {
        content.icon = 'none';
    }
    content.url = '#';
    content.target = '_blank';

    $.notify(content, {
        type: type, // Default, Primary, Secondary, Info, Success, Warning, Danger
        placement: {
            from: from, // top, bottom
            align: align // right, center, left
        },
        time: 500
    });
}
</script>

@endsection
