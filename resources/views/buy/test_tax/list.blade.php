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
                                                <input class="form-control" name="amount" id="amount" type="number" step="any" >
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
                                                <input class="form-control" name="buy_up" id='buy_up' type="number" step="any"
                                                oninput="calculateTotalPrice(this.value)">
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                                                <label for="total_price">{{__('common.total')}}<span class="danger">*</span> </label>
                                                <input class="form-control" name="total" id='total_with_or_without_tax' type="number" step="any" >
                                            </div>
                                    <!-- / Second Row -->

                                    <!-- Third Row -->
                                     <!-- VAT = Value Added Tax -->
                                            <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
                                               <label for="buy_tax_per">  {{__('buy.buy_tax_percentage')}} </label>
                                                <input class="form-control" name="buy_tax_per" id="buy_tax_per" type="number" placeholder="نمبر: 0 - 100" min=0 , max=100 
                                                oninput="calculateTax(this.value);" step="any" >
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
$(document).ready(function() {
    // =========================================
    // CALCULATE TOTAL PRICE
    // =========================================
    function calculateTotalPrice() {
        var buyUp = parseFloat($('#buy_up').val()) || 0;
        var amount = parseFloat($('#amount').val()) || 0;
        var total = (buyUp * amount).toFixed(2);
        $('#total_with_or_without_tax').val(total);
        
        // Recalculate everything
        recalculateAll();
    }

    // =========================================
    // RECALCULATE ALL FIELDS
    // =========================================
    function recalculateAll() {
        var amount = parseFloat($('#amount').val()) || 0;
        var buyUp = parseFloat($('#buy_up').val()) || 0;
        var buyTaxPercent = parseFloat($('#buy_tax_per').val()) || 0;
        var sellUp = parseFloat($('#sell_up').val()) || 0;
        var sellTaxPercent = parseFloat($('#sell_tax_per').val()) || 0;

        // Calculate buy tax
        calculateBuyTax(amount, buyUp, buyTaxPercent);
        
        // Calculate sell tax
        calculateSellTax(amount, sellUp, sellTaxPercent);
        
        // Update total without tax
        var totalWithoutTax = amount * buyUp;
        $('#total_with_or_without_tax').val(totalWithoutTax.toFixed(2));
    }

    // =========================================
    // CALCULATE BUY TAX
    // =========================================
    function calculateBuyTax(amount, unitPrice, taxPercent) {
        if (!amount || !unitPrice) {
            $('#buy_tax_price').val('0.00');
            $('#buy_up_vat').val('0.00');
            $('#total_vat').val('0.00');
            return;
        }

        // Calculate totals
        var curTotal = amount * unitPrice;  // Total without VAT
        var taxAmount = (curTotal * taxPercent) / 100;  // Total VAT amount
        
        // Update fields with proper formatting
        $('#buy_tax_price').val(taxAmount.toFixed(2));  // Total tax amount
        
        // Unit price WITH VAT (per item)
        var unitPriceWithVAT = unitPrice + taxAmount;
        $('#buy_up_vat').val(unitPriceWithVAT.toFixed(2)); // Unit price with VAT
        
        // Total WITH VAT (all items)
        var totalWithVAT = unitPriceWithVAT * amount;  // Total with VAT
        $('#total_vat').val(totalWithVAT.toFixed(2)); // Total with VAT
    }

    // =========================================
    // CALCULATE SELL TAX
    // =========================================
    function calculateSellTax(amount, unitPrice, taxPercent) {
        if (!amount || !unitPrice) {
            $('#sell_tax_price').val('0.00');
            $('#sell_up_vat').val('0.00');
            $('#total_sales_with_tax').val('0.00');
            return;
        }

        // Calculate totals
        var totalWithoutTax = amount * unitPrice;  // Total without VAT
        var totalTaxAmount = (totalWithoutTax * taxPercent) / 100;  // Total VAT
        
        // Update fields with proper formatting
        $('#sell_tax_price').val(totalTaxAmount.toFixed(2));  // Total tax amount
        
        // Unit price WITH VAT (per item)
        var unitPriceWithTax = unitPrice + totalTaxAmount;
        $('#sell_up_vat').val(unitPriceWithTax.toFixed(2));
        
        // Total WITH VAT (all items)
        var totalWithTax = unitPriceWithTax * amount;  // Total with VAT
        $('#total_sales_with_tax').val(totalWithTax.toFixed(2));
    }

    // =========================================
    // EVENT HANDLERS
    // =========================================
    
    // When amount changes
    $('#amount').on('input', function() {
        var amount = parseFloat($(this).val()) || 0;
        var buyUp = parseFloat($('#buy_up').val()) || 0;
        
        // Update total
        var total = (buyUp * amount).toFixed(2);
        $('#total_with_or_without_tax').val(total);
        
        // Recalculate all
        recalculateAll();
    });

    // When buy_up changes
    $('#buy_up').on('input', function() {
        calculateTotalPrice();
    });

    // When buy_tax_per changes
    $('#buy_tax_per').on('input', function() {
        var taxPercent = parseFloat($(this).val()) || 0;
        var amount = parseFloat($('#amount').val()) || 0;
        var unitPrice = parseFloat($('#buy_up').val()) || 0;
        calculateBuyTax(amount, unitPrice, taxPercent);
    });

    // When sell_up changes
    $('#sell_up').on('input', function() {
        var sellUp = parseFloat($(this).val()) || 0;
        var amount = parseFloat($('#amount').val()) || 0;
        var sellTaxPercent = parseFloat($('#sell_tax_per').val()) || 0;
        calculateSellTax(amount, sellUp, sellTaxPercent);
    });

    // When sell_tax_per changes
    $('#sell_tax_per').on('input', function() {
        var sellTaxPercent = parseFloat($(this).val()) || 0;
        var amount = parseFloat($('#amount').val()) || 0;
        var sellUp = parseFloat($('#sell_up').val()) || 0;
        calculateSellTax(amount, sellUp, sellTaxPercent);
    });

    // =========================================
    // INITIAL CALCULATION ON PAGE LOAD
    // =========================================
    // Trigger initial calculation
    setTimeout(function() {
        recalculateAll();
    }, 100);

    // =========================================
    // ORIGINAL FUNCTIONS (keeping for compatibility)
    // =========================================
    window.calculateTotalPrice = function(buy_up) {
        calculateTotalPrice();
    };

    window.calculateTax = function(tax_percent) {
        var amount = parseFloat($('#amount').val()) || 0;
        var unitPrice = parseFloat($('#buy_up').val()) || 0;
        calculateBuyTax(amount, unitPrice, tax_percent);
    };

    window.calculateSalesTax = function(sales_tax_percent) {
        var amount = parseFloat($('#amount').val()) || 0;
        var unitPrice = parseFloat($('#sell_up').val()) || 0;
        calculateSellTax(amount, unitPrice, sales_tax_percent);
    };

    window.updateCurPay = function(curPay) {
        var total_price = parseFloat($('#fina_total_price').val()) || 0;
        var curPayVal = parseFloat(curPay) || 0;
        var result = total_price - curPayVal;
        $('#remained').val(Math.max(result, 0).toFixed(2));

        if (curPayVal > total_price) {
            $('#submit_button').hide();
            alert("{{__('buy.over_pay')}}");
        } else {
            $('#submit_button').show();
        }
    };

    window.validateWarehouseAmounts = function() {
        let totalAmount = parseFloat($('#amount').val()) || 0;
        let sumWarehouseAmount = 0;

        $('input[name="warehouse_amount[]"]').each(function() {
            sumWarehouseAmount += parseFloat($(this).val()) || 0;
        });

        if (sumWarehouseAmount > totalAmount) {
            showNotification("{{__('buy.over_amount')}}", 'danger', 'top', 'right', 'withicon');
            return false;
        } else if (sumWarehouseAmount < totalAmount) {
            showNotification("{{__('buy.select_less_than')}}", 'danger', 'top', 'right', 'withicon');
            return false;
        } else {
            $('#warehouseAmountError').text('');
            return true;
        }
    };

    $(document).on('click', '.close-error', function() {
        $('#errorWrapper').fadeOut();
    });

    // =========================================
    // ADD KEYBOARD SHORTCUTS
    // =========================================
    // Enter key to recalculate
    $('input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            recalculateAll();
        }
    });

    // =========================================
    // ADD REAL-TIME VALIDATION
    // =========================================
    // Validate tax percentage (0-100)
    $('#buy_tax_per, #sell_tax_per').on('blur', function() {
        var val = parseFloat($(this).val()) || 0;
        if (val < 0) {
            $(this).val(0);
        } else if (val > 100) {
            $(this).val(100);
        }
        recalculateAll();
    });

    // Validate amount and prices (not negative)
    $('#amount, #buy_up, #sell_up').on('blur', function() {
        var val = parseFloat($(this).val()) || 0;
        if (val < 0) {
            $(this).val(0);
            recalculateAll();
        }
    });
});

// =========================================
// ADD DEBUGGING HELPER (optional)
// =========================================
function logValues() {
    console.log({
        amount: $('#amount').val(),
        buy_up: $('#buy_up').val(),
        buy_tax_per: $('#buy_tax_per').val(),
        buy_tax_price: $('#buy_tax_price').val(),
        buy_up_vat: $('#buy_up_vat').val(),
        total_vat: $('#total_vat').val(),
        sell_up: $('#sell_up').val(),
        sell_tax_per: $('#sell_tax_per').val(),
        sell_tax_price: $('#sell_tax_price').val(),
        sell_up_vat: $('#sell_up_vat').val(),
        total_sales_with_tax: $('#total_sales_with_tax').val()
    });
}
</script>
@endsection


