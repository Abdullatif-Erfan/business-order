@extends('layouts.app')

@php 
    $grandTotal = 0; 
    $grandDiscount = 0;
    $grandTransport =0;
    $payable =0;
    $remained =0;
    $curPay=0;
    $count = 0;
@endphp
@section('content')

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
                            <h4 class="card-title">
                             {{__('sales.edit_title')}}
                               <span class="pull-left">
                                    <a href="{{ route('sales.index') }}">
                                        <button class="btn mybtn bg-default">{{__('common.back')}}</button>
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
                             <input type="hidden" name="times" value="{{ $warehouseSales->first()->times ?? '' }}">


                               <div class="form-body" style="padding: 0px 0px 15px !important;">
                                <div class="container col-md-12 col-sm-12 col-xs-12" style="padding: 10px 10px;">
                                   
                                    <table style="width:100%">
                                         <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="5">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                                <td> {{__('sales.customer_account')}}: 
                                                    <select name="customer_account_id" class="form-control select2" required>
                                                        @foreach($customers as $account)
                                                            <option value="{{ $account->id }}" {{ $warehouseSales->first()->customer_account_id == $account->id ? 'selected' : '' }}>
                                                                {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>  {{__('common.currency')}} : 
                                                <select name="currency_id" class="form-control select2" required>
                                                        @foreach($currencies as $currency)
                                                            <option value="{{ $currency->id }}" {{ $warehouseSales->first()->currency_id == $currency->id ? 'selected' : '' }}>
                                                                {{ $currency->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    </td>
                                                <td> {{__('common.date')}}  : {{ $warehouseSales->first()->idate ?? '' }}</td>
                                                <td> {{__('common.bill')}}  : <input type="text" class="form-control" name="billno" required value="{{ $warehouseSales->first()->billno ?? '' }}" readonly></td>

                                                <td> {{__('common.factor')}}  : <input type="text" class="form-control" name="factor"  value="{{ $warehouseSales->first()->factor ?? '' }}" ></td>

                                            </tr>
                                    </table>
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('common.number')}}</th>
                                                    <th>{{__('sales.item')}} </th>
                                                    <th>{{__('sales.sales_amount')}}</th>
                                                    <th>{{__('common.unit')}}</th>
                                                    @if($saved_with_tax) 
                                                    <th>  {{__('buy.sales_tax_percentage')}} </th>
                                                    <th>  {{__('buy.sell_tax_price')}} </th>
                                                    @endif
                                                    <th>  {{__('common.unit_price')}}</th>
                                                    <th>  {{__('common.total_price')}}</th>
                                                    <th class="hidden-print"> {{__('common.edit')}}</th>
                                                    <th class="hidden-print">{{__('common.delete')}}</th>
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
                                                    <td> {{ $detail->amount  }} </td>
                                                    <td>{{ $detail->unitRelation->name }}</td>
                                                     @if($saved_with_tax) 
                                                    <td> % {{ $detail->sell_tax_per }} </td>
                                                    <td> {{  number_format($detail->sell_tax_price,2) }} </td>
                                                    @endif
                                                    <td>{{ number_format($detail->sell_up,2) }}</td>
                                                    <td>{{ number_format($detail->total,2) }} </td>
                                                    <td class="hidden-print"><i class="fas fa-pen-square font-20" onclick="updateThisRecord({{ $detail->id }})" ></i></td>
                                                    <td class="hidden-print"><i class="fas fa-trash-alt danger font-20"  onclick="deleteThisRecord({{ $detail->id }})"></i></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                
                                        <table class="table table-bordered new tableTdPadding" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                        <tr>
                                            <td>{{__('buy.total_price')}} &nbsp; </td>
                                            <td><input type="number" step="0.01"  class="form-control" name="total_price" id="total_price" required readonly
                                            value="{{ $grandTotal }}" ></td>

                                            <td> {{__('buy.cur_pay')}} </td>
                                           <td><input type="number" step="0.01"  class="form-control" name="cur_pay" required
                                            value="{{ $warehouseSales->first()->cur_pay ?? '' }}" oninput="updateRemain(this.value)" ></td>
                                             <td> {{__('buy.remained')}} </td>
                                            <td><input type="number" step="0.01" readonly class="form-control" name="remained" id="remained" required
                                            value="{{ $payable - $warehouseSales->first()->cur_pay }}" ></td>
                                        </tr>
                                        <tr>
                                            <td> {{__('journal.reciever')}} </td>
                                            <td>
                                                <select name="from_account_id" class="form-control select2" required>
                                                    @foreach($ownBanks as $account)
                                                        <option value="{{ $account->id }}" {{ $warehouseSales->first()->account_id == $account->id ? 'selected' : '' }}>
                                                            {{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>{{__('buy.note')}} </td>
                                            <td colspan="3"><input type="text"  class="form-control" name="note" id="note"
                                            value="{{  $warehouseSales->first()->note }}" ></td>
                                        </tr>
                                    </table>

                                </div>



                                <!-- buttons -->
                                <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    
                                    <!-- edit button -->
                                        <button type="submit" id="submit_button" class="btn btn-primary btn-sm m-r-10">
                                           <i class="fas fa-pen"></i> {{__('sales.final_save')}}
                                        </button>

                                    <!-- print button -->
                                    @if(auth()->user()->hasAccess('buy','delete_records'))
                                        @if($salesDetails->count() == 0)
                                        <a href="{{ route('sales.destroy', $warehouseSales->first()->times) }}"  
                                        onClick="return doConfirm();" class="hidden-print">
                                            <button type="button" class="btn btn-danger btn-sm m-r-10">
                                            <i class="fas fa-trash error "></i>  {{__('common.delete')}} 
                                            </button>
                                        </a>
                                        @else
                                        <button type="button" class="btn btn-danger btn-sm m-r-10"
                                            onclick='alert("لطفا لیست بالا را دانه دانه حذف نمایید بعدا حذف کلی نمایید")'>
                                            <i class="fas fa-trash error "></i>  {{ __('common.delete') }}
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
                <h5 class="modal-title"> {{__('common.edit')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="ModalContent"></div>
                <div id="loading" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin font-20"></i> {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{__('common.close')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" >{{__('common.save')}}</button>
            </div>
            </form>
        </div>
    </div>
</div>


<!-- delete modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:800px !important">
            <form action="" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"> {{__('common.delete')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="deleteModalContent"></div>
                <div id="loading_delete" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin font-20"></i> {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{__('common.close')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="delete_button" >{{__('common.confirm')}}</button>
            </div>
            </form>
        </div>
    </div>
</div>


<script>
    function updatePayAble(discount)
    {
        var total_price = parseFloat($('#total_price').val());
        var result = total_price - parseFloat(discount);
        $('#payable').val(result).toFixed(2);
    }

    function updateRemain(cur_pay)
    {
        var total_price = parseFloat($('#total_price').val());
        var result = (total_price - parseFloat(cur_pay)).toFixed(2);
        if(result < 0) {
            alert("{{__('sales.invalid_payment')}}");
             $('#remained').val(0);
             $('#cur_pay').val(total_price);
            $('#submit_button').fadeOut(1);
        } else {
          $('#submit_button').fadeIn(1);
          $('#remained').val(result);
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
    if (!confirm("{{__('common.delete_confirm')}}")) {
        return; // Exit function if user cancels
    }

    if (salesDetailsId) {
        $.ajax({
            url: `/sales/deleteSingleItem/${salesDetailsId}`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: () => {
                showNotification("{{__('common.deleted_successfully')}}", 'success', 'top', 'right', 'withicon');
                window.location.reload();
            },
            error: () => {
                showNotification("{{__('delete_failed')}}", 'danger', 'top', 'right', 'withicon');
            }
        });
    }
}


   

</script>

@endsection
