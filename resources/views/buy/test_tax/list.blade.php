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
                            <h4 class="card-title">  امتحان کردن فورمول مالیات
                                <span class="pull-left">
                                    <a href="{{ url('boughtList') }}">
                                        <button class="btn mybtn bg-default"> {{__('common.back')}}</button>
                                    </a>
                                </span>
                                
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
                                        <div class="row dynamic-row">
                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="sell_up"> {{__('buy.sell_up')}} </span></label>
                                                <input type="number" name="sell_up" id="sell_up" step="0.01" class="form-control" placeholder="{{__('buy.sell_up')}}" >
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="sell_tax_per">  {{__('buy.sales_tax_percentage')}} </label>
                                                <input class="form-control" name="sell_tax_per" id="sell_tax_per" type="number" placeholder="نمبر: 0 - 100" min=0 , max=100 oninput="calculateSalesTax(this.value);"  >
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="sell_tax_price"> {{__('buy.sell_tax_price')}} </label>
                                                <input class="form-control" name="sell_tax_price" id="sell_tax_price"  type="number" step="0.01" >
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="sell_up_vat"> {{__('buy.sell_up_vat')}} </label>
                                                <input class="form-control" name="sell_up_vat" id="sell_up_vat"  type="number" step="0.01" >
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
                                                <label for="total_sales_with_tax"> {{__('buy.total_sales_with_tax')}} </label>
                                                <input class="form-control" name="total_sales_with_tax" id="total_sales_with_tax"  type="number" step="0.01" >
                                            </div>
                                        </div>


                                    </div>
                                   <!-- / fifth Row -->




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

</script>

@endsection


