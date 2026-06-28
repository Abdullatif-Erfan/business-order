@extends('layouts.app')

@section('content')

<style>

table.new thead tr th{background-color:#fff !important; color:#000 !important;text-align:center;}
table.my_table thead tr th{background-color:#3f7cc7  !important; color:#fff !important;text-align:center;}
.new tbody tr td{padding: 10px 5px;}
select.select2{text-align:right !important;direction:rtl !important;}


@keyframes blink {
  0% { opacity: 1; }
  50% { opacity: 0; }
  100% { opacity: 1; }
}

.blink {
  animation: blink 1s linear infinite;
  color: red;
  font-size: 18px;
}
.blink {
  color: red;
  font-size: 18px;
}

</style>


<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">  {{__('buy.create_title')}}
                                <span class="pull-left">
                                    <a href="{{ url('boughtList') }}">
                                        <button class="btn mybtn bg-default"> {{__('common.back')}}</button>
                                    </a>
                                </span>
                                
                                 <small class="badge badge-info badge-sm"> <strong class="m-r-10"> {{__('buy.note')}}:</strong>
                                  {{__('buy.note_text')}}
                                 </small>
                            </h4>
                        </div>

                        <form id="buyingForm" action="{{ route('boughtList.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="times" value="{{ $times; }}"> 
                        <input type="hidden" name="journal_code" value="{{ $newJournalCode; }}"> 
                        <input type="hidden" name="tax_activation" value="{{$tax->tax_activation}}">
                         <input type="hidden" name="currency_id" value="{{$currencies->first()->id}}" >


                        <!-- {{ json_encode(auth()->user()->full_name) }} -->
                        <!-- {{ json_encode(auth()->user()->id) }} -->

                        
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                                <div class="row" style="padding: 10px 20px;">

                                     <div class="col-md-12">
                                         <div class="col-md-12" style="display:none" id="errorWrapper">
                                            <div class="row">
                                                <!-- <div class="alert alert-danger col-12 " id="validationErrors"></div> -->
                                                <div class="alert alert-danger col-12" id="validationErrors">
                                                    <span class="fa fa-times close-error" style="cursor: pointer; float: left; margin-left: 10px;"></span>
                                                </div>
                                            </div>
                                         </div>
                                     </div>

                                     


                                    <!-- First Row -->
                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="supplier_account_id"> {{__('order.supplier_selection')}} <span class="danger">*</span></label>
                                                    <select class="form-control select2" tabindex="0" style="width: 100%; border:none !important; background-color:#ddd;" name="supplier_account_id" id="supplier_account_id" required>
                                                        <option value="">  {{__('order.supplier_name')}} </option>
                                                        @foreach($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}">  {{ $supplier->name }} </option>
                                                        @endforeach
                                                    </select>
                                                    @error('supplier_account_id')
                                                        <span style='color:red'>{{ $message }}</span>
                                                    @enderror
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="from_account_id">  {{__('buy.company_account')}} <span class="danger">*</span></label>
                                                <select class="form-control select2" tabindex="3" style="width: 100%; background-color:#ddd;" name="from_account_id" required>
                                                    <!-- <option value="">حساب پرداخت کننده</option> -->
                                                    @foreach($ownBanks as $acc)
                                                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>


                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="date">{{__('order.date')}} <span class="text-danger">*</span></label>
                                                    <div class="input-group date" id="datepicker">
                                                        <input type="text" class="form-control" name="todays_date" required
                                                            value="{{ date('Y-m-d') }}" placeholder="{{__('order.date')}} ">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>


                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="billno"> {{__('common.bill')}}   <span class="danger">*</span></label>
                                                <input type="number" tabindex="2" onkeyup="checkBillNoDuplication(this.value)" class="form-control" value="{{ $billno }}" name="billno" id="billno"
                                                    placeholder="{{__('common.bill')}}" required readonly>
                                                 <span id="successMsg" style="display:none">
                                                 <div style="color:green">{{__('buy.confirmed')}}</div></span>
                                                 <span id="failurMsg" style="display:none"><div style="color:red">
                                                 {{__('buy.repeated_billno')}}</div></span>
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="factor">  {{__('buy.factor')}} </label>
                                                <input type="text" tabindex="2" class="form-control"  name="factor" id="factor" placeholder="{{__('buy.factor')}}" >
                                            </div>
                                    <!-- / first Row -->

                                    <!-- Second Row -->
                                            <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
                                                <label for="pre_list_id">  {{__('buy.item_selection')}}<span class="danger">*</span> </label>
                                                <select class="form-control select2" tabindex="4" style="width: 100%; background-color:#ddd;" name="pre_list_id" id="pre_list_id">
                                                    <option value="0">{{__('buy.item_selection')}}</option>
                                                    @foreach($preLists as $item)
                                                        <option value="{{ $item->id }}" data-code="{{ $item->code }}" data-name="{{ $item->name }}">
                                                          {{ $item->name }} 
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
                                                <label for="amount">{{__('buy.amount')}} <span class="danger">*</span> </label>
                                                <input class="form-control" name="amount" id="amount" type="number" step="0.01" >
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="unit_id"> {{__('common.unit')}} <span class="danger">*</span> </label>
                                                <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="unit_id" id="unit_id" >
                                                    <option value="">{{__('common.unit')}}</option>
                                                    @foreach($units as $unitItem)
                                                        <option value="{{ $unitItem->id }}">{{ $unitItem->name }}</option>
                                                    @endforeach
                                                </select>
                                                
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="buy_up">{{__('common.unit_price')}}<span class="danger">*</span> </label>
                                                <input class="form-control" name="buy_up" id='buy_up' type="number" step="0.01"
                                                oninput="calculateTotalPrice(this.value)">
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="total_price">{{__('common.total')}}<span class="danger">*</span> </label>
                                                <input class="form-control" name="total" id='total_with_or_without_tax' type="number" step="0.01" >
                                            </div>
                                    <!-- / Second Row -->

                                    <!-- Third Row -->
                                     <!-- VAT = Value Added Tax -->
                                        @if(intval($tax->tax_activation) === 1) 
                                            <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
                                               <label for="buy_tax_per">  {{__('buy.buy_tax_percentage')}} </label>
                                                <input class="form-control" name="buy_tax_per" id="buy_tax_per" type="number" placeholder="نمبر: 0 - 100" min=0 , max=100 
                                                oninput="calculateTax(this.value);" >
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="buy_tax_price"> {{__('buy.buy_tax_price')}} </label>
                                                <input class="form-control" name="buy_tax_price" id="buy_tax_price"  type="number" step="0.01" >
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="buy_up_vat"> {{__('buy.buy_up_vat')}} </label>
                                                <input class="form-control" name="buy_up_vat" id="buy_up_vat"  type="number" step="0.01" >
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="total_vat"> {{__('buy.total_buy_with_tax')}} </label>
                                                <input class="form-control" name="total_vat" id="total_vat"  type="number" step="0.01" >
                                            </div>
                                            
                                      @endif
                                            <div class="col-md-3 col-sm-4 col-xs-12 m-t-10">
                                                <label for="note"> {{__('buy.comment')}} </label>
                                                <input class="form-control" name="note" id="note" type="text" placeholder="{{__('buy.comment')}}" >
                                            </div>
                                    <!-- / Third Row -->

                                    <!-- Fourth Row -->
                                     <div class="col-12">
                                        <div class="col-12" style="background-color:#f3f3f3; margin-top:10px;padding: 5px;">
                                           <strong><center>{{__('buy.sales_section')}}</center></strong>
                                        </div>
                                     </div>
                                    <!-- / Fourth Row -->


                                   <!-- fifth Row -->
                                    <hr />
                                    <div class="col-md-12 m-t-20">
                                        @include('buy.bought.dynamic_form')
                                    </div>
                                   <!-- / fifth Row -->

                                     <!-- Add to list button  -->
                                     <div class="col-12">
                                        <div class="col-12" style="margin-top:10px;padding: 5px;">
                                           <button type="button" class="form-control btn btn-sm btn-info" onclick="submiteBuyingForm()">{{__('buy.add_to_buy_list')}} </button>
                                        </div>
                                     </div>
                                    <!-- /  Add to list button  -->

                                    <div class="col-md-12">
                                        <div id="loader" style="display:none; text-align: center;">
                                            <i class="fa fa-spinner fa-spin" style="font-size:40px;"></i> 
                                        </div>
                                     </div>


                                    <!-- inserted result list -->
                                        <div class="col-12" id="insertedResult"> </div>
                                    <!-- /inserted result list -->


                                    <!-- Submit and Cancel Buttons -->
                                    <div class="col-md-8 m-t-20">
                                        <div class="row">
                                            <div class="col-12">
                                                <input type="submit" id="submit_button" name="submit" value="{{__('buy.final_submit')}}" class="form-control btn bg-blue pull-left btn-sm">
                                            </div>
                                        </div>
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




<script>
function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> {{ __('settings.message') }}  </span>';
    
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

function calculateTotalPrice(buy_up)
{
    var buyUp = parseFloat(buy_up) || 0;
    var amount = parseFloat($('#amount').val()) || 0;
    var total = (buyUp * amount).toFixed(2);
    $('#total_with_or_without_tax').val(total);
}

function calculateTax(tax_percent) {
    var taxPercent = parseFloat(tax_percent) || 0;
    var quantity = parseFloat($('#amount').val()) || 0;
    var unitPrice = parseFloat($('#buy_up').val()) || 0;
    
    // Calculate totals
    var curTotal = quantity * unitPrice;  // Total without VAT
    var taxAmount = (curTotal * taxPercent) / 100;  // Total VAT amount
    
    // Update fields with proper formatting
    $('#buy_tax_price').val(taxAmount.toFixed(2));  // Total tax amount //مبلغ مالیات خرید
    
    // Unit price WITH VAT (per item)
    var unitPriceWithVAT = unitPrice + taxAmount;
    $('#buy_up_vat').val(unitPriceWithVAT.toFixed(2)); // فیات با مالیات
    
    // Total WITH VAT (all items)
    var totalWithVAT = unitPriceWithVAT * quantity;  // Total with VAT
    $('#total_vat').val(totalWithVAT.toFixed(2)); // مجموع با مالیات
}

function calculateSalesTax(sales_tax_percent) {
    var salesTaxPercent = parseFloat(sales_tax_percent) || 0;
    var quantity = parseFloat($('#amount').val()) || 0;
    var unitPrice = parseFloat($('#sell_up').val()) || 0;
    
    // Calculate totals
    var totalWithoutTax = quantity * unitPrice;  // Total without VAT
    var totalTaxAmount = (totalWithoutTax * salesTaxPercent) / 100;  // Total VAT
    
    // Update fields with proper formatting
    $('#sell_tax_price').val(totalTaxAmount.toFixed(2));  // Total tax amount
    
    // Unit price WITH VAT (per item)
    var unitPriceWithTax = unitPrice + totalTaxAmount;
    $('#sell_up_vat').val(unitPriceWithTax.toFixed(2));
    
    // Total WITH VAT (all items)
    var totalWithTax = unitPriceWithTax * quantity;  // Total with VAT
    $('#total_sales_with_tax').val(totalWithTax.toFixed(2));
}


function updateCurPay(curPay) {
    var total_price = parseFloat($('#fina_total_price').val()) || 0;
    var curPayVal = parseFloat(curPay) || 0;
    console.log('updateCurPay is called');
    console.log('total_price', total_price);
    console.log('curPayVal', curPayVal);
    
    var result = total_price - curPayVal;
    $('#remained').val(Math.max(result, 0).toFixed(2)); // Prevent negative values

    // Hide submit button if curPay is greater than total_price
    if (curPayVal > total_price) {
        $('#submit_button').hide(); // Hides the submit button
        alert("{{__('buy.over_pay')}}")
    } else {
        $('#submit_button').show(); // Shows the submit button
    }
}

function submiteBuyingForm()
{
    
     // Serialize form data
     var formData = $('#buyingForm').serialize();

    //  console.log('formData', JSON.parse(formData));

     // Show loader state
     $('#loader').show();

     // AJAX form submission
     $.ajax({
        url: '/boughtList/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: (response) => {
            if(response.status ==='failed') 
            {
                showNotification(response.message, 'danger', 'top', 'right', 'withicon');
                $('#loader').hide();
            }
            else 
            {
                $('#loader').hide();
                $('#errorWrapper').fadeOut(10);
                showNotification("{{__('common.added_successfully')}}", 'success', 'top', 'right', 'withicon');
                $('#insertedResult').html(response);

                
                // Clear form fields properly
                // For regular inputs
                $('#amount').val('');
                $('#buy_up').val('');
                $('#buy_tax_per').val('');
                $('#buy_tax_price').val('');
                $('#buy_up_vat').val('');
                $('#total_vat').val('');
                $('#sell_up').val('');
                $('#sell_tax_per').val('');
                $('#sell_tax_price').val('');
                $('#sell_up_vat').val('');
                $('#total_sales_with_tax').val('');
                $('#total_vat').val('');
                $('#total_with_or_without_tax').val('');
                $('#note').val('');

                // Clear Select2 fields properly
                $('#pre_list_id').val('').trigger('change');
                
                // For unit_id
                $('#unit_id').val('').trigger('change');
                
                // For supplier_account_id
                // $('#supplier_account_id').val('').trigger('change');

                // For from_account_id
                // $('select[name="from_account_id"]').val('').trigger('change');

            }

        },
        error: (xhr) => {
            $('#loader').hide();
            // $('#insertedResult').html('خطا رخ داد'); // General error message

            if (xhr.status === 422) { // Laravel validation error response
                var errors = xhr.responseJSON.errors;
                var errorList = '<ul>';
                
                $.each(errors, function (key, messages) {
                    errorList += '<li>' + messages[0] + '</li>'; // Show only the first error for each field
                    $('#' + key + 'Error').text(messages[0]); // Display inline error message if an element exists
                });

                errorList += '</ul>';

                // Add close button dynamically inside the validationErrors div
                var errorHtml = `
                    <span class="fa fa-times close-error" style="cursor: pointer; float: left; margin-left: 10px;"></span>
                    ${errorList}
                `;

                $('#validationErrors').html(errorHtml).show();
                $('#errorWrapper').fadeIn(10);

                showNotification("{{__('buy.fill_all_fields')}}", 'danger', 'top', 'right', 'withicon');
            } else {
                showNotification("{{__('buy.all_fields_required')}}", 'danger', 'top', 'right', 'withicon');
            }
        }

    });
}
</script>

<script>

// Function to check sum validation
function validateWarehouseAmounts() 
{
    let totalAmount = parseFloat($('#amount').val()) || 0;
    let sumWarehouseAmount = 0;

    $('input[name="warehouse_amount[]"]').each(function () {
         sumWarehouseAmount += parseFloat($(this).val()) || 0;
    });

    console.log('totalAmount', totalAmount);
    console.log('warehouse_amount', sumWarehouseAmount);

    if (sumWarehouseAmount > totalAmount) {
        showNotification("{{__('buy.over_amount')}}", 'danger', 'top', 'right', 'withicon');
        return false;
    } else if (sumWarehouseAmount < totalAmount) {
        showNotification("{{__('buy.select_less_than')}}", 'danger', 'top', 'right', 'withicon');
        return false;
    } else {
        $('#warehouseAmountError').text(''); // Clear error if valid
        return true;
    }
}

$(document).ready(function () {
    $(document).on('click', '.close-error', function () {
        $('#errorWrapper').fadeOut();
    });
});


function checkBillNoDuplication(billNo) 
{
    if (billNo == 0) {
        $("#failurMsg").show();
        $("#successMsg").hide();
        return;
    }

    $.ajax({
        url: "{{ route('boughtList.checkBillNoDuplication') }}", // Define this route in web.php
        type: "GET",
        data: { billno: billNo },
        success: function(response) {
            if (response.exists) {
                $("#failurMsg").show();
                $("#successMsg").hide();
            } else {
                $("#successMsg").show();
                $("#failurMsg").hide();
            }
        },
        error: function() {
            console.log("Error checking bill number.");
        }
    });
}

</script>

@endsection


