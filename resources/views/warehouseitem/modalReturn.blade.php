
    <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="item_name"> {{__('wh.returnable_items')}} </label>
            <input class="form-control" name="id" type="hidden" value="{{ $warehouseItems->id ?? 0 }}">
             <input class="form-control" name="item_name" id="item_name" type="text" readonly value="{{ $warehouseItems->preListRelation->name ?? '' }}">
        </div>


        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="amount">{{__('buy.amount')}} </label>
            <input  name="old_amount" id="old_amount" type="hidden" value="{{ $warehouseItems->available_amount ?? 0}}" >
            <input class="form-control" name="amount" id="amount" type="number" step="any" min="0.1" 
               oninput="checkAmountChanges(this.value)" required value="{{ $warehouseItems->available_amount ?? ''}}"  >
        </div>

        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="amount"> {{__('common.unit')}}  </label>
            <input type="text" class="form-control" readonly value="{{ $warehouseItems->unitRelation->name ?? '' }}">
        </div>

         <div class="col-md-12 col-sm-12 col-xs-12">
            <label for="reason">{{__('common.reason')}} </label>
            <input class="form-control" name="reason" id="reason" type="text" required  >
        </div>

    </div>
</div>


<script>
function checkAmountChanges(input) {
    var oldAmount = parseFloat(document.getElementById('old_amount').value) || 0;
    var enteredAmount = parseFloat(input) || 0;

    if (enteredAmount > oldAmount) {
        alert("{{__('wh.greater_amount_msg')}}");
        document.getElementById('amount').value = oldAmount;
        $('#submitReturn').hide();
    } else {
        $('#submitReturn').show();
    }
}


function checkAmountChanges(input) {
    var oldAmount = parseFloat(document.getElementById('old_amount').value) || 0;
    var enteredAmount = parseFloat(input) || 0;

    if (enteredAmount > oldAmount) {
        alert("{{__('wh.greater_amount_msg')}}");
        input = oldAmount; // Reset to max allowed value
        $('#submitReturn').fadeOut(1);
    } else {
        $('#submitReturn').fadeIn(1);
    }
}
</script>
