<!-- Dynamic Form for Order Items -->
<div class="col-12" id="formContainer" style="background: #eef2ff; padding: 15px; border-radius: 8px;">
    <div class="dynamic-row row">
        <div class="form-group col-sm-3">
            <label for="item_id">{{ __('order.item') }}</label>
            <select class="form-control select2" name="item_id[]" style="width: 100%;">
                <option value="">{{ __('order.item_selection') }}</option>
                @foreach($preLists as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-sm-2">
            <label for="quantity">{{ __('order.quantity') }}</label>
            <input type="number" step="0.01" class="form-control" name="quantity[]">
        </div>

        <div class="form-group col-sm-2">
            <label for="item_unit">{{ __('common.unit') }}</label>
            <select class="form-control" name="item_unit[]">
                <option value="">{{ __('common.unit_selection') }}</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-sm-2">
            <label for="price">{{ __('order.price') }}</label>
            <input type="number" step="0.01" class="form-control price-input" name="price[]">
        </div>

        <div class="form-group col-sm-1">
            <label for="item_total">{{ __('order.total') }}</label>
            <input type="text" class="form-control item-total" readonly style="background: #f0f0f0; font-weight: bold;">
        </div>

        <div class="form-group col-sm-2">
            <br />
            <button type="button" class="btn btn-sm btn-success add-more">
                <i class="fas fa-plus"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger remove-row" style="display:none;">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</div>


<!-- TODO:
آردر از کدام مشتری است 
آردر به کدام درایور داده شود -->