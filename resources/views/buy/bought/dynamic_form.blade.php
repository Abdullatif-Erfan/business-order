<div class="row dynamic-row">

    <div class="col-md-3 col-sm-4 col-xs-6">
        <label for="sell_up"> {{__('buy.sell_up')}} </span></label>
        <input type="number" name="sell_up" id="sell_up" step="0.01" class="form-control" placeholder="{{__('buy.sell_up')}}" >
    </div>

    @if(intval($tax->tax_activation) === 1) 
     <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
        <label for="sales_tax_percentage">  {{__('buy.sales_tax_percentage')}} </label>
        <input class="form-control" name="sales_tax_percentage" id="sales_tax_percentage" type="number" placeholder="نمبر: 0 - 100" min=0 , max=100 oninput="calculateSalesTax(this.value);"  >
    </div>

    <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
        <label for="sales_tax_price"> {{__('buy.sales_tax_price')}} </label>
        <input class="form-control" name="sales_tax_price" id="sales_tax_price"  type="number" step="0.01" >
    </div>

    <div class="col-md-3 col-sm-4 col-xs-6 m-t-10">
        <label for="total_sales_with_tax"> {{__('buy.total_sales_with_tax')}} </label>
        <input class="form-control" name="total_sales_with_tax" id="total_sales_with_tax"  type="number" step="0.01" >
    </div>

    @endif
    
</div>

