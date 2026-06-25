<div class="row dynamic-row">

    <div class="col-md-3 col-sm-4 col-xs-6">
        <label for="sell_up"> {{__('buy.sell_up')}} </span></label>
        <input type="number" name="sell_up" id="sell_up" step="0.01" class="form-control" placeholder="{{__('buy.sell_up')}}" >
    </div>

    @if(intval($tax->tax_activation) === 1) 
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

    @endif
    
</div>

