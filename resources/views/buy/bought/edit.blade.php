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

@php 
    $grandTotal = 0; 
    $grandDiscount = 0;
    $grandTransport =0;
    $payable =0;
    $remained =0;
    $curPay=0;
    $count = 0;
@endphp

<style>
.tableTdPadding td {
    padding: 10px !important;
}
</style>
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">ویرایش فورم خریداری
                                <span class="pull-left">
                                    <a href="{{ url('boughtList') }}">
                                        <button class="btn mybtn bg-default">برگشت به لیست</button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">


                            <div class="col-md-12">
                                @if ($errors->any())
                                <div class="col-md-12 m-t-10">
                                   <div class="row">
                                      <div class="alert alert-danger col-12">
                                         <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                   </div>
                                </div>
                                @endif
                            </div>

                            <form action="{{ route('boughtList.update') }}"  method="POST">
                             @csrf
                             <input type="hidden" name="times" value="{{ $boughtItems->first()->times }}">
                             
                                <div class="form-body" style="padding: 0px 0px 15px !important;" id="print_area">
                            
                                    <div class="container col-md-12 col-sm-12 col-xs-12" style="padding: 10px 10px;">
                                        <p class="d-none">تاریخ چاپ‌ : {{ now()->format('Y-m-d') }}</p>
                                        

                                        <table style="width:100%">
                                            <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                                <td colspan="4">
                                                <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td> حساب پرداخت کننده: 
                                                    <select name="account_id" class="form-control select2" required>
                                                        @foreach($ownBanks as $account)
                                                            <option value="{{ $account->id }}" {{ $boughtItems->first()->account_id == $account->id ? 'selected' : '' }}>
                                                                {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>   واحد پولی: 
                                                <select name="currency_id" class="form-control select2" required>
                                                        @foreach($currencies as $currency)
                                                            <option value="{{ $currency->id }}" {{ $boughtItems->first()->currency_id == $currency->id ? 'selected' : '' }}>
                                                                {{ $currency->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    </td>
                                                <td>تاریخ ثبت : {{ $boughtItems->first()->idate ?? '' }}</td>
                                                <td>نمبر بل : <input type="text" class="form-control" name="billno" required value="{{ $boughtItems->first()->billno ?? '' }}" readonly></td>
                                            </tr>
                                        </table>
                                        <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                        <div class="table-responsive">
                                            <table class="table table-bordered new" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>شماره</th>
                                                        <th width="150">فروشنده</th>
                                                        <th width="150"> جنس </th>
                                                        <th width="100">واحد</th> 
                                                        <th width="100">تعداد خرید</th>
                                                        <th width="100">قیمت فی واحد</th>
                                                        <th width="100"> مجموع</th>
                                                        <th width="100">تخفیف</th>
                                                        <th width="100">ترانسپورت</th>
                                                        <th width="150">تاریخ انقضا</th>
                                                        <th class="hidden-print"> ویرایش</th>
                                                        <th class="hidden-print">حذف</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($boughtItemDetails as $key => $detail)
                                                    @php
                                                        $grandTotal += $detail->amount * $detail->bought_up;
                                                        $grandDiscount += $detail->discount;
                                                        $grandTransport += $detail->transport;
                                                        $payable = $grandTotal - $grandDiscount;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{$detail->accountRelation->name}} </td>
                                                        <td>{{ $detail->preListRelation->name }}</td>
                                                        <td>{{ $detail->unitRelation->name }} </td>
                                                        <td>{{ $detail->amount }} </td>
                                                        <td>{{ number_format($detail->bought_up,2) }}</td>
                                                        <td>{{ number_format($detail->total,2) }}</td>
                                                        <td>{{ number_format($detail->discount,2) }} </td>
                                                        <td>{{ number_format($detail->transport,2) }}</td>
                                                        <td>{{ $detail->expire_date }} </td>
                                                    
                                                        <td class="hidden-print"><i class="fas fa-pen-square font-20" onclick="updateThisRecord({{ $detail->id }})" ></i></td>
                                                        <td class="hidden-print"><i class="fas fa-trash-alt danger font-20"  onclick="deleteThisRecord({{ $detail->id }})"></i></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <table class="table table-bordered new tableTdPadding" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                            <tr>
                                                <td>مجموع پول &nbsp; </td>
                                                <td><input type="number" step="0.01"  class="form-control" name="total_price" id="total_price" required readonly
                                                value="{{ $grandTotal }}" ></td>
                                                <td> تخفیف </td>
                                                <td><input type="number" step="0.01"  class="form-control" name="total_discount" required
                                                value="{{ $grandDiscount }}" readonly oninput="updatePayAble(this.value)" ></td>
                                                <td> مصارف ترانسپورت </td>
                                                <td><input type="number" step="0.01" readonly class="form-control" name="trans_spend" required
                                                value="{{ $grandTransport }}" ></td>
                                            </tr>
                                            <tr>
                                                <td> قابل پرداخت</td>
                                                <td><input type="number" readonly step="0.01"  class="form-control" name="payable" id="payable" required
                                                value="{{ $payable }}" ></td>
                                                <td> پرداخت فعلی</td>
                                                <td><input type="number" step="0.01"  class="form-control" name="cur_pay" required
                                                value="{{ $boughtItems->first()->cur_pay ?? '' }}" oninput="updateRemain(this.value)" ></td>
                                                <td> باقی </td>
                                                <td><input type="number" step="0.01" readonly class="form-control" name="remained" id="remained" required
                                                value="{{ $payable - $boughtItems->first()->cur_pay }}" ></td>
                                            </tr>
                                        </table>


                                    </div>


                                    <!-- buttons -->
                                    <div class="col-md-8 col-sm-8 col-xs-12 m-t-10 m-b-10">
                                    <div class="row">
                                        <div class="col-md-8">
                                             <!-- edit button -->
                                             <button type="submit" id="submit_button" class="btn btn-primary btn-sm m-r-10 form-control">
                                            <i class="fas fa-pen"></i>  ثبت 
                                            </button>
                                           <!-- /buttons -->
                                        </div>
                                        <div class="col-md-4">
                                          
                                            @if(auth()->user()->hasAccess('buy','delete_records'))
                                               @if($boughtItemDetails->count() == 0)
                                              <a href="{{ route('boughtList.destroy', $boughtItems->first()->billno) }}"  
                                                onClick="return doConfirm();" class="hidden-print">
                                                    <button type="button" class="btn btn-danger btn-sm m-r-10">
                                                    <i class="fas fa-trash error "></i>  حذف 
                                                    </button>
                                                </a>
                                                   @else
                                                   <button type="button" class="btn btn-danger btn-sm m-r-10" onclick="alert('لطفا لیست بالا را دانه دانه حذف نمایید بعدا حذف کلی نمایید ')">
                                                       <i class="fas fa-trash error "></i>  حذف 
                                                    </button>
                                                    @endif
                                                @endif


                                        </div>

                                        
                                       
                                        </div>
                                    </div>

                                </div>  
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:800px !important">
            <form action="{{ route('boughtList.updateItemAndWarehouseItems')}}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"> ویرایش </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="ModalContent"></div>
                <div id="loading" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin font-20"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" >ثبت</button>
            </div>
            </form>
        </div>
    </div>
</div>


<!-- delete modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:800px !important">
            <form action="{{ route('boughtList.deleteSingleItem')}}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"> حذف </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="deleteModalContent"></div>
                <div id="loading_delete" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin font-20"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="delete_button" >تایید</button>
            </div>
            </form>
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

    function updatePayAble(discount)
    {
        var total_price = parseFloat($('#total_price').val());
        var result = total_price - parseFloat(discount);
        $('#payable').val(result).toFixed(2);
    }

    function updateRemain(cur_pay)
    {
        var payable = parseFloat($('#payable').val());
        var result = payable - parseFloat(cur_pay);
        if(result < 0) {
            alert('مبلغ پرداخت نادرست میباشد');
            $('#submit_button').fadeOut(1);
        } else {
          $('#submit_button').fadeIn(1);
          $('#remained').val(result).toFixed(2);
        }
    }


    function updateThisRecord(id)
    {
        $('#editModal').modal('show');
        $('#loading').show();
        $.ajax({
            url: `/boughtList/getSingleRecordForEdit/${id}`,
            type: 'GET',
            success: (result) => {
                $('#ModalContent').html(result);
                $('#loading').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    }

    
    function deleteThisRecord(boughtItemId)
    {
        $('#deleteModal').modal('show');
        $('#loading_delete').show();
        $.ajax({
            url: `/boughtList/getWarehouseListForDelete/${boughtItemId}`,
            type: 'GET',
            success: (result) => {
                $('#deleteModalContent').html(result);
                $('#loading_delete').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_delete').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    }
</script>
@endsection

