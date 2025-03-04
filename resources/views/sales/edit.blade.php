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
                            <h4 class="card-title">جزییات فورم فروشات
                               <span class="pull-left">
                                    <a href="{{ route('sales.index') }}">
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


                            <form action="{{ route('sales.update') }}"  method="POST">
                             @csrf
                             <input type="hidden" name="billno" value="{{ $warehouseSales->first()->billno }}">
                             <input type="hidden" name="todays_date" value="{{ $warehouseSales->first()->short_date ?? '' }}">
                             <input type="hidden" name="branch_id" value="{{ $warehouseSales->first()->branch_id ?? '' }}">
                             <input type="hidden" name="times" value="{{ $warehouseSales->first()->times ?? '' }}">


                               <div class="form-body" style="padding: 0px 0px 15px !important;">
                                <div class="container col-md-12 col-sm-12 col-xs-12" style="padding: 10px 10px;">
                                   
                                    <table style="width:100%">
                                         <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="4">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                                <td> حساب  مشتری: 
                                                    <select name="customer_account_id" class="form-control select2" required>
                                                        @foreach($customers as $account)
                                                            <option value="{{ $account->id }}" {{ $warehouseSales->first()->customer_account_id == $account->id ? 'selected' : '' }}>
                                                                {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>   واحد پولی: 
                                                <select name="currency_id" class="form-control select2" required>
                                                        @foreach($currencies as $currency)
                                                            <option value="{{ $currency->id }}" {{ $warehouseSales->first()->currency_id == $currency->id ? 'selected' : '' }}>
                                                                {{ $currency->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    </td>
                                                <td>تاریخ ثبت : {{ $warehouseSales->first()->ifull_date ?? '' }}</td>
                                                <td>نمبر بل : <input type="text" class="form-control" name="billno" required value="{{ $warehouseSales->first()->billno ?? '' }}" readonly></td>
                                            </tr>
                                    </table>
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
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
                                                    <th class="hidden-print"> ویرایش</th>
                                                    <th class="hidden-print">حذف</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($salesDetails as $key => $detail)
                                                @php
                                                    $grandTotal += $detail->amount * $detail->sell_up;
                                                    $grandDiscount += $detail->discount;
                                                    $payable = $grandTotal - $grandDiscount;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->preListRelation->name ?? ' '}}</td>
                                                    <td>{{ number_format($detail->amount) }}</td>
                                                    <td>{{ $detail->unitRelation->name }}</td>
                                                    <td>{{ number_format($detail->sell_up) }}</td>
                                                    <td>{{ number_format($detail->discount) }}</td>
                                                    <td>{{ number_format($detail->total) }}</td>
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
                                            <td> دریافت کننده </td>
                                            <td>
                                                <select name="from_account_id" class="form-control select2" required>
                                                    @foreach($ownBanks as $account)
                                                        <option value="{{ $account->id }}" {{ $warehouseSales->first()->account_id == $account->id ? 'selected' : '' }}>
                                                            {{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> قابل پرداخت</td>
                                            <td><input type="number" readonly step="0.01"  class="form-control" name="payable" id="payable" required
                                            value="{{ $payable }}" ></td>
                                            <td> پرداخت فعلی</td>
                                            <td><input type="number" step="0.01"  class="form-control" name="cur_pay" required
                                            value="{{ $warehouseSales->first()->cur_pay ?? '' }}" oninput="updateRemain(this.value)" ></td>
                                            <td> باقی </td>
                                            <td><input type="number" step="0.01" readonly class="form-control" name="remained" id="remained" required
                                            value="{{ $payable - $warehouseSales->first()->cur_pay }}" ></td>
                                        </tr>
                                        <tr>
                                            <td>نوت </td>
                                            <td colspan="5"><input type="text"  class="form-control" name="note" id="note" readonly
                                            value="{{  $warehouseSales->first()->note }}" ></td>
                                        </tr>
                                    </table>

                                </div>



                                <!-- buttons -->
                                <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    
                                    <!-- edit button -->
                                        <button type="submit" class="btn btn-primary btn-sm m-r-10">
                                           <i class="fas fa-pen"></i>  ثبت نهایی 
                                        </button>

                                    <!-- print button -->
                                    @if(auth()->user()->hasAccess('buy','delete_records'))
                                        @if($salesDetails->count() == 0)
                                        <a href="{{ route('sales.destroy', $warehouseSales->first()->times) }}"  
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
                                <!-- /buttons -->

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
            <form action="{{ route('sales.updateSalesAndWarehouseItems')}}" method="POST">
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
            url: `/sales/getSingleRecordForEdit/${id}`,
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

    

    function deleteThisRecord(salesDetailsId) {
    if (!confirm('آیا میخواهید حذف نمایید ؟')) {
        return; // Exit function if user cancels
    }

    if (salesDetailsId) {
        $.ajax({
            url: `/sales/deleteSingleItem/${salesDetailsId}`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: () => {
                showNotification('موفقانه حذف گردید', 'success', 'top', 'right', 'withicon');
                window.location.reload();
            },
            error: () => {
                showNotification('حذف نگردید', 'danger', 'top', 'right', 'withicon');
            }
        });
    }
}


   

</script>

@endsection
