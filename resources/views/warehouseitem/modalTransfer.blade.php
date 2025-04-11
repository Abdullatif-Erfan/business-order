
    <div class="row">
        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="item_name">جنس قابل انتقال </label>

            <input class="form-control" name="id"  type="hidden" 
            value="{{ $warehouseItems->id ?? 0 }}" >

            <input class="form-control" name="source_warehouse_id"  type="hidden"
            value="{{ $warehouseItems->warehouse_id ?? 0}}" >

            <input class="form-control" name="item_name" id="item_name" type="text" readonly 
            value="{{ $warehouseItems->preListRelation->name ?? '' }}" >

        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount"> انتقال به گدام </label>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="distination_warehouse_id" required>
                <option value=""> --- انتخاب گدام ---</option>
                @foreach($warehouses as $warehouse)
                   @if($warehouse->id != $warehouseItems->warehouse_id)
                    <option value="{{  $warehouse->id }}" >{{ $warehouse->name }}</option>
                   @endif
                @endforeach
            </select>
        </div>


        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount">تعداد </label>
            <input  name="old_amount" id="old_amount" type="hidden" value="{{ $warehouseItems->available_amount ?? 0}}" >
            <input class="form-control" name="amount" id="amount" type="number" step="0.01" oninput="checkAmountChanges(this.value)"
                    required  value="{{ $warehouseItems->available_amount ?? ''}}"  >
        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount">واحد  </label>
            <input type="text" class="form-control" readonly value="{{ $warehouseItems->unitRelation->name ?? '' }}">
            <!-- <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="unit_id" id="unit_id" required >
                <option value=""> --- انتخاب واحد ---</option>
                @foreach($units as $unitItem)
                    <option value="{{  $unitItem->id }}">{{ $unitItem->name }}</option>
                @endforeach
            </select> -->   
        </div>

    </div>

</div>

<script>
function checkAmountChanges(input) {
    var oldAmount = parseFloat(document.getElementById('old_amount').value) || 0;
    var enteredAmount = parseFloat(input) || 0;

    if (enteredAmount > oldAmount) {
        alert('مقدار وارد شده نباید بیشتر از مقدار موجود باشد!');
        input = oldAmount; // Reset to max allowed value
        $('#submitTransfer').fadeOut(1);
    } else {
        $('#submitTransfer').fadeIn(1);
    }
}
</script>
