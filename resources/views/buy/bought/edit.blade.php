@extends('layouts.app')
@section('content')

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
                            <h4 class="card-title"> {{__('buy.update_title')}}
                                <span class="pull-left">
                                    <a href="{{ url('boughtList') }}">
                                        <button class="btn mybtn bg-default"> {{__('common.back')}} </button>
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
                             <input type="hidden" name="todays_date" value="{{ $boughtItems->first()->idate ?? '' }}">
                             <input type="hidden" name="journal_code" value="{{ $journal_code->code ?? '' }}">
                             <input type="hidden" name="tax_activation" value="{{ $orgbios[0]->tax_activation ?? '' }}">
                             <input type="hidden" name="supplier_account_id" value="{{ $boughtItemDetails->first()->accountRelation->id ?? '' }}">
                             
                                <div class="form-body" style="padding: 0px 0px 15px !important;" id="print_area">
                            
                                    <div class="container col-md-12 col-sm-12 col-xs-12" style="padding: 10px 10px;">
                                        <p class="d-none">{{__('common.print_date')}}‌ : {{ now()->format('Y-m-d') }}</p>

                                        <table style="width:100%">
                                            <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                                <td colspan="4">
                                                <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td> {{__('common.account_payer')}} 
                                                    <select name="from_account_id" class="form-control select2" required>
                                                        @foreach($ownBanks as $account)
                                                            <option value="{{ $account->id }}" {{ $boughtItems->first()->account_id == $account->id ? 'selected' : '' }}>
                                                                {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td> {{__('common.currency')}}: 
                                                <select name="currency_id" class="form-control select2" required>
                                                        @foreach($currencies as $currency)
                                                            <option value="{{ $currency->id }}" {{ $boughtItems->first()->currency_id == $currency->id ? 'selected' : '' }}>
                                                                {{ $currency->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    </td>
                                                <td> {{__('common.save_date')}} : {{ $boughtItems->first()->idate ?? '' }}</td>
                                                <td> {{__('common.bill')}} : <input type="text" class="form-control" name="billno" required value="{{ $boughtItems->first()->billno ?? '' }}" readonly></td>
                                            </tr>
                                        </table>
                                        <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                        <div class="table-responsive">
                                            <table class="table table-bordered new" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>{{__('common.number')}}</th>
                                                        <th width="150">{{__('common.seller')}}</th>
                                                        <th width="150"> {{__('buy.item')}} </th>
                                                        <th width="100">{{__('common.currency')}}</th> 
                                                        <th width="100">{{__('buy.bought_amount')}}</th>
                                                        <th width="100"> {{__('common.unit_price')}} </th>

                                                        @if($orgbios[0]->tax_activation === 1)
                                                        <th>{{__('buy.buy_tax_percentage_s')}}</th>
                                                        <th>{{__('buy.buy_tax_price_s')}}</th>
                                                        @endif
                                                        <th>{{__('common.total_price')}}</th>

                                                        <th class="hidden-print"> {{__('common.edit')}}</th>
                                                        <th class="hidden-print">{{__('common.delete')}}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach($boughtItemDetails as $key => $detail)
                                                    @php
                                                      $ttl = $orgbios[0]->tax_activation === 1 ?  $detail->total_vat: $detail->total;
                                                        $grandTotal +=  $ttl;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{$detail->accountRelation->name}} </td>
                                                        <td>{{ $detail->preListRelation->name }}</td>
                                                        <td>{{ $detail->unitRelation->name }} </td>
                                                        <td>{{ $detail->amount }} </td>
                                                        <td>{{ number_format($detail->buy_up,2) }}</td>
                                                        @if($orgbios[0]->tax_activation === 1)
                                                         <td> % {{$detail->buy_tax_per}}  </td>
                                                         <td> {{$detail->buy_tax_price}} </td>
                                                         @endif
                                                        <td>
                                                            @if($orgbios[0]->tax_activation === 1)
                                                            {{number_format($detail->total_vat,2)}}  
                                                            @else 
                                                            {{number_format($detail->total,2)}} 
                                                            @endif
                                                        </td>
                                                    
                                                        <td class="hidden-print"><i class="fas fa-pen-square font-20" onclick="updateThisRecord({{ $detail->id }})" ></i></td>

                                                        <td class="hidden-print">
                                                         @if(auth()->user()->hasAccess('buy','delete_records'))
                                                            <a href="{{ route('boughtList.deleteSingleItem', $detail->id) }}"  
                                                                 onClick="return doConfirm();" class="hidden-print">
                                                                <i class="fas fa-trash-alt danger font-20"></i> 
                                                            </a>
                                                            @endif
                                                          </td>
                                                        <!-- <i class="fas fa-trash-alt danger font-20"  onclick="deleteThisRecord({{ $detail->id }})"></i> -->
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <table class="table table-bordered new tableTdPadding" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                            <tr>
                                                <td> {{__('buy.total_price')}} &nbsp; </td>
                                                <td><input type="number" step="0.01"  class="form-control" name="total_price" id="total_price" required readonly
                                                value="{{  $grandTotal }}" ></td>
                                                <td> {{__('buy.cur_pay')}} </td>
                                                <td><input type="number" step="0.01"  class="form-control" name="cur_pay" required
                                                value="{{ $boughtItems->first()->cur_pay ?? '' }}" oninput="updateRemain(this.value)" >
                                                </td>
                                                <td> {{__('buy.remained')}} </td>
                                               <td>
                                                @php
                                                    $remained = $grandTotal - ($boughtItems->first()->cur_pay ?? 0);
                                                @endphp
                                                     <input type="number" step="0.01" readonly class="form-control" name="remained" id="remained" 
                                                      required value="{{ $remained }}">
                                                </td>
                                            </tr>
                                            <tr>
                                               <td>{{__('buy.note')}} </td>
                                                <td colspan="5"><input type="text"  class="form-control" name="note" id="note"
                                                value="{{ $boughtItems->first()->note }}" ></td>
                                            </tr>
                                        </table>


                                    </div>


                                    <!-- buttons -->
                                    <div class="col-md-8 col-sm-8 col-xs-12 m-t-10 m-b-10">
                                    <div class="row">
                                        <div class="col-md-8">
                                             <!-- edit button -->
                                             <button type="submit" id="submit_button" class="btn btn-primary btn-sm m-r-10 form-control">
                                            <i class="fas fa-pen"></i>  {{__('common.save')}}  
                                            </button>
                                           <!-- /buttons -->
                                        </div>
                                        <div class="col-md-4">
                                          
                                            @if(auth()->user()->hasAccess('buy','delete_records'))
                                               @if($boughtItemDetails->count() == 0)
                                              <a href="{{ route('boughtList.destroy', $boughtItems->first()->times) }}"  
                                                onClick="return doConfirm();" class="hidden-print">
                                                    <button type="button" class="btn btn-danger btn-sm m-r-10">
                                                    <i class="fas fa-trash error "></i>  {{__('common.delete')}}  
                                                    </button>
                                                </a>
                                                   @else
                                                   <button type="button" class="btn btn-danger btn-sm m-r-10" onclick='alert("{{__('buy.delete_instruction')}}")'>
                                                       <i class="fas fa-trash error "></i>  {{__('common.delete')}}  
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




<script>
    function updatePayAble(discount)
    {
        var total_price = parseFloat($('#total_price').val());
        var result = total_price - parseFloat(discount);
        $('#payable').val(result).toFixed(2);
    }

    function updateRemain(cur_pay)
    {
        var totalPrice = parseFloat($('#total_price').val());
        var result = totalPrice - parseFloat(cur_pay);
        if(result < 0) {
            alert("{{__('buy.incorrect_payment')}}");
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

    
    function deleteThisRecord(times)
    {
        $('#deleteModal').modal('show');
        $('#loading_delete').show();
        $.ajax({
            url: `/boughtList/getWarehouseListForDelete/${times}`,
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

