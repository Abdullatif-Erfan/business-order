@php
    $unit_name = '';
    if ($boughtItemDetails && $units) {
        foreach ($units as $unitItem) {
            if ($boughtItemDetails->unit_id == $unitItem->id) {
                $unit_name = $unitItem->name;
                break;
            }
        }
    }
@endphp
    <div class="row">
        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="item_name">{{__('buy.item')}} </label>

            <input class="form-control" name="id"  type="hidden" 
            value="{{ $boughtItemDetails->id ?? 0 }}" >

            <input class="form-control" name="pre_list_id"  type="hidden"
            value="{{ $boughtItemDetails->pre_list_id ?? 0}}" >

            <input class="form-control" name="times"  type="hidden"  
            value="{{ $boughtItemDetails->times ?? 0}}" >

            <input class="form-control" name="item_name" id="item_name" type="text" readonly value={{ $boughtItemDetails->preListRelation->name ?? ''}} >
        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount">{{__('buy.amount')}} </label>
            <input  name="old_amount" id="old_amount" type="hidden" value={{ $boughtItemDetails->amount ?? 0}} >
            <input class="form-control" name="amount" id="amount" type="number" step="0.01" oninput="checkAmountChanges(this.value)"
            value="{{ $boughtItemDetails->amount ?? ''}}" required >
        </div>

          <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount">{{__('common.unit')}}  </label>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="unit_id" id="unit_id" >
                @foreach($units as $unitItem)
                $unit_name = $boughtItemDetails->unit_id == $unitItem->id ? $unitItem->name : '';
                 <option value="{{  $unitItem->id }}" {{ $boughtItemDetails->unit_id == $unitItem->id ? 'selected' : '' }} >{{ $unitItem->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- tax_activation -->
        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="buy_up">{{__('buy.bought_up')}} </label>
            <input  name="old_buy_up" id="old_buy_up" type="hidden" value="{{ $boughtItemDetails->buy_up ?? 0}}" >
            <input class="form-control" name="buy_up" id="buy_up" type="number" step="0.01" 
            value="{{ $boughtItemDetails->buy_up ?? 0}}" required >

            <input class="form-control" name="total" id="total" type="hidden" step="0.01" 
            value="{{ $boughtItemDetails->total ?? 0 }}" >
        </div>



        <!-- VAT = Value Added Tax -->
        @if(intval($tax_activation->tax_activation) === 1) 
            <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
                <label for="buy_tax_per">  {{__('buy.buy_tax_percentage')}} </label>
                <input class="form-control" name="buy_tax_per" id="buy_tax_per" type="number" placeholder="نمبر: 0 - 100" min=0 , 
                max=100 value="{{ $boughtItemDetails->buy_tax_per ?? 0 }}"
                oninput="calculateTax(this.value);" >
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                <label for="buy_tax_price"> {{__('buy.buy_tax_price')}} </label>
                <input class="form-control" name="buy_tax_price" id="buy_tax_price" value="{{ $boughtItemDetails->buy_tax_price ?? 0 }}"  type="number" step="0.01" >
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                <label for="buy_up_vat"> {{__('buy.buy_up_vat')}} </label>
                <input class="form-control" name="buy_up_vat" id="buy_up_vat" value="{{ $boughtItemDetails->buy_up_vat ?? 0 }}"  type="number" step="0.01" >
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
                <label for="total_vat"> {{__('buy.total_buy_with_tax')}} </label>
                <input class="form-control" name="total_vat" id="total_vat" value="{{ $boughtItemDetails->total_vat ?? 0 }}"  type="number" step="0.01" >
            </div>
            
         @endif
            <div class="col-md-3 col-sm-4 col-xs-12 m-t-10">
                <label for="note"> {{__('buy.comment')}} </label>
                <input class="form-control" name="note" id="note" type="text" value="{{ $boughtItemDetails->note ?? 0 }}" placeholder="{{__('buy.comment')}}" >
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
        <div class="col-md-3 col-sm-4 col-xs-6">
        <label for="sell_up"> {{__('buy.sell_up')}} </span></label>
        <input type="number" name="sell_up" id="sell_up" step="0.01" class="form-control" placeholder="{{__('buy.sell_up')}}" 
        value="{{ $boughtItemDetails->sell_up ?? 0 }}">
     </div>

    @if(intval($tax_activation->tax_activation) === 1) 
     <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
        <label for="sell_tax_per">  {{__('buy.sales_tax_percentage')}} </label>
        <input class="form-control" name="sell_tax_per" id="sell_tax_per" type="number" placeholder="نمبر: 0 - 100" min=0 , max=100
        value="{{ $boughtItemDetails->sell_tax_per ?? 0 }}" oninput="calculateSalesTax(this.value);"  >
    </div>

    <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
        <label for="sell_tax_price"> {{__('buy.sell_tax_price')}} </label>
        <input class="form-control" name="sell_tax_price" id="sell_tax_price"  type="number" step="0.01"
        value="{{ $boughtItemDetails->sell_tax_price ?? 0 }}">
    </div>

    <div class="col-md-2 col-sm-4 col-xs-6 m-t-10">
        <label for="sell_up_vat"> {{__('buy.sell_up_vat')}} </label>
        <input class="form-control" name="sell_up_vat" id="sell_up_vat"  type="number" step="0.01" 
        value="{{ $boughtItemDetails->sell_up_vat ?? 0 }}" >
    </div>

    <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
        <label for="total_sales_with_tax"> {{__('buy.total_sales_with_tax')}} </label>
        <input class="form-control" name="total_sales_with_tax" id="total_sales_with_tax"  type="number" step="0.01" 
        value="{{ $boughtItemDetails->sell_up_vat * $boughtItemDetails->amount ?? 0 }}" >
    </div>

    @endif
    <!-- / fifth Row -->


    </div>


<script>
$(document).ready(function () {

    $('#amount').on('input change', function () {

        calculateTax($('#buy_tax_per').val());
        calculateSalesTax($('#sell_tax_per').val());

        updateTotal();
    });

    $('#buy_up').on('input change', function () {

        calculateTax($('#buy_tax_per').val());

        updateTotal();
    });

    $('#buy_tax_per').on('input change', function () {

        calculateTax($(this).val());
    });

    $('#sell_up').on('input change', function () {

        calculateSalesTax($('#sell_tax_per').val());
    });

    $('#sell_tax_per').on('input change', function () {

        calculateSalesTax($(this).val());
    });

    // Initial load
    calculateTax($('#buy_tax_per').val());
    calculateSalesTax($('#sell_tax_per').val());
    updateTotal();
});


function updateTotal()
{
    var amount = parseFloat($('#amount').val()) || 0;
    var buyUp  = parseFloat($('#buy_up').val()) || 0;

    $('#total').val((amount * buyUp).toFixed(2));
}


function calculateTax(tax_percent)
{
    var taxPercent = parseFloat(tax_percent) || 0;
    var quantity = parseFloat($('#amount').val()) || 0;
    var unitPrice = parseFloat($('#buy_up').val()) || 0;

    var curTotal = quantity * unitPrice;
    var taxAmount = (curTotal * taxPercent) / 100;

    $('#buy_tax_price').val(taxAmount.toFixed(2));

    var unitPriceWithVAT = unitPrice + taxAmount;

    $('#buy_up_vat').val(unitPriceWithVAT.toFixed(2));

    var totalWithVAT = unitPriceWithVAT * quantity;

    $('#total_vat').val(totalWithVAT.toFixed(2));
}


function calculateSalesTax(sales_tax_percent)
{
    var salesTaxPercent = parseFloat(sales_tax_percent) || 0;
    var quantity = parseFloat($('#amount').val()) || 0;
    var unitPrice = parseFloat($('#sell_up').val()) || 0;

    var totalWithoutTax = quantity * unitPrice;
    var totalTaxAmount = (totalWithoutTax * salesTaxPercent) / 100;

    $('#sell_tax_price').val(totalTaxAmount.toFixed(2));

    var unitPriceWithTax = unitPrice + totalTaxAmount;

    $('#sell_up_vat').val(unitPriceWithTax.toFixed(2));

    var totalWithTax = unitPriceWithTax * quantity;

    $('#total_sales_with_tax').val(totalWithTax.toFixed(2));
}
</script>