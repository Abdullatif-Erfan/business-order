
    <div class="row">
        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="item_name"> {{__('wh.transferable_items')}} </label>
            <input class="form-control" name="id" type="hidden" value="{{ $warehouseItems->id ?? 0 }}">
            <input class="form-control" name="source_warehouse_id" type="hidden" value="{{ $warehouseItems->warehouse_id ?? 0 }}">
            <input class="form-control" name="unit_id" type="hidden" value="{{ $warehouseItems->unit_id ?? 0 }}">
            <input class="form-control" name="item_name" id="item_name" type="text" readonly value="{{ $warehouseItems->preListRelation->name ?? '' }}">

        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount"> {{__('buy.transfer_to_car')}} </label>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="car_id" id="car_id" required>
                <option value=""> --- {{__('buy.car')}} ---</option>
                @foreach($cars as $car)
                   @if($car->id != $warehouseItems->car_id)
                    <option value="{{  $car->id }}" >{{ $car->name }}</option>
                   @endif
                @endforeach
            </select>
        </div>


        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount">{{__('buy.amount')}} </label>
            <input  name="old_amount" id="old_amount" type="hidden" value="{{ $warehouseItems->available_amount ?? 0}}" >
            <input class="form-control" name="amount" id="amount" type="number" step="any" min="0.1" oninput="checkAmountChanges(this.value)"
                    required  value="{{ $warehouseItems->available_amount ?? ''}}"  >
        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount"> {{__('common.unit')}}  </label>
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

<!-- <div class="row mt-3">
    <div class="col-md-12">
        <button type="button" id="submitTransfer" class="btn btn-primary">
            <i class="fas fa-exchange-alt"></i> {{__('common.transfer')}}
        </button>
    </div>
</div> -->


<script>
function checkAmountChanges(input) {
    var oldAmount = parseFloat(document.getElementById('old_amount').value) || 0;
    var enteredAmount = parseFloat(input) || 0;

    if (enteredAmount > oldAmount) {
        alert("{{__('wh.greater_amount_msg')}}");
        document.getElementById('amount').value = oldAmount;
        $('#submitTransfer').hide();
    } else {
        $('#submitTransfer').show();
    }
}




function checkAmountChanges(input) {
    var oldAmount = parseFloat(document.getElementById('old_amount').value) || 0;
    var enteredAmount = parseFloat(input) || 0;

    if (enteredAmount > oldAmount) {
        alert("{{__('wh.greater_amount_msg')}}");
        input = oldAmount; // Reset to max allowed value
        $('#submitTransfer').fadeOut(1);
    } else {
        $('#submitTransfer').fadeIn(1);
    }
}
</script>
