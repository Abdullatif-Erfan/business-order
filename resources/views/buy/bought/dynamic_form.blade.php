<div class="row dynamic-row">
    <div class="col-md-3 col-sm-4 col-xs-6">
        <label for="billno"> {{__('buy.warehouse_selection')}} <span class="danger">*</span></label>
        <select class="form-control select2 warehouse-select" style="width: 100%; background-color:#ddd;" name="warehouse_id[]" >
            <option value=""> {{__('buy.warehouse_selection')}} </option>
            @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 col-sm-4 col-xs-6">
        <label for="warehouse_amount"> {{__('buy.amount')}}  <span class="danger">*</span></label>
        <input type="number" name="warehouse_amount[]" step="0.01" class="form-control" placeholder="{{__('buy.warehouse_transfer_amount')}}" >
    </div>

    <div class="col-md-3 col-sm-4 col-xs-6">
        <label for="warehouse_sell_up"> {{__('buy.sold_up')}} <span class="danger">*</span></label>
        <input type="number" name="warehouse_sell_up[]" step="0.01" class="form-control" placeholder="{{__('buy.sold_up')}}" >
    </div>

    <div class="col-md-3 col-sm-4 col-xs-6">
        <br/>
        <button type="button" class="btn btn-sm btn-info addMoreBtn">
            <i class="fa fa-plus"></i>
        </button>
    </div> 
</div>

<script>
$(document).ready(function () {
    // Initialize Select2 on page load
    $('.select2').select2();

    // Store warehouse options in a JavaScript variable
    let warehouseOptions = `{!! json_encode($warehouses) !!}`;
    warehouseOptions = JSON.parse(warehouseOptions);

    let warehouseDropdown = '<option value="">"{{__('buy.warehouse_selection')}}"</option>';
    warehouseOptions.forEach(wh => {
        warehouseDropdown += `<option value="${wh.id}">${wh.name}</option>`;
    });

    $(document).on('click', '.addMoreBtn', function () {
        let newRow = `
            <div class="row dynamic-row">
                <div class="col-md-3 col-sm-4 col-xs-6">
                    <label>{{__('buy.warehouse_selection')}}<span class="danger">*</span></label>
                    <select class="form-control select2 warehouse-select" style="width: 100%; background-color:#ddd;" name="warehouse_id[]" required>
                        ${warehouseDropdown}
                    </select>
                </div>

                <div class="col-md-3 col-sm-4 col-xs-6">
                    <label>{{__('buy.amount')}}  <span class="danger">*</span></label>
                    <input type="number" name="warehouse_amount[]" step="0.01" class="form-control" placeholder="{{__('buy.warehouse_transfer_amount')}}" required>
                </div>

                <div class="col-md-3 col-sm-4 col-xs-6">
                    <label>  {{__('buy.sold_up')}} <span class="danger">*</span></label>
                    <input type="number" name="warehouse_sell_up[]" step="0.01" class="form-control" placeholder="{{__('buy.sold_up')}}" required>
                </div>

                <div class="col-md-3 col-sm-4 col-xs-6">
                    <br/>
                    <button type="button" class="btn btn-sm btn-info addMoreBtn">
                        <i class="fa fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger removeBtn">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
        `;

        $('.dynamic-row:last').after(newRow);
        
        // Reinitialize Select2 for new elements
        $('.select2').select2();
    });

    $(document).on('click', '.removeBtn', function () {
        if ($('.dynamic-row').length > 1) {
            $(this).closest('.dynamic-row').remove();
        }
    });

});

</script>
