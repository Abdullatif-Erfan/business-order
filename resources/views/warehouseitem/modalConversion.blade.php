
    <div class="row">
        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="item_name"> <strong>نام جنس</strong>  </label>

            <input class="form-control" name="id"  type="hidden" 
            value="{{ $warehouseItems->id ?? 0 }}" >

            <input class="form-control" name="source_warehouse_id"  type="hidden"
            value="{{ $warehouseItems->warehouse_id ?? 0}}" >

            <input class="form-control" name="item_name" id="item_name" type="text" readonly 
            value="{{ $warehouseItems->preListRelation->name ?? '' }}" > 

        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="available_amount"> <strong>مقدار موجود</strong>  </label>
            <div>{{ $warehouseItems->available_amount ?? '' }} 
                 <span id="unit_name">{{ $warehouseItems->unitRelation->name ?? '' }}</span> 
            </div>
        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount">  <strong> انتخاب گزینه </strong> </label>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="options" id="options" required
            onchange="createLabel()">
                <option value="">--- انتخاب  گزینه ---</option>
                <option value="1">تبدیل به واحد کوچکتر</option>
                <option value="2">تبدیل به واحد بزرگتر</option>
            </select>
        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount">  <strong>واحد جدید </strong> </label>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="new_unit_id" id="new_unit_id" required
            onchange="createLabel()">
                <option value="">--- انتخاب واحد جدید ---</option>
                @foreach($units as $unit)
                    <option value="{{  $unit->id }}" >{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12 m-t-10">
            <label for="amount"><strong id="label-description"></strong> </label>
            <input  name="old_amount" id="old_amount" type="hidden" value="{{ $warehouseItems->available_amount ?? 0}}" >
            <input class="form-control" name="amount" id="amount" type="number" step="0.01" oninput="checkAmountChanges(this.value)"
            required>
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
function createLabel() {
    var unit_name = $('#unit_name').text().trim();
    var new_unit_text = $('#new_unit_id option:selected').text().trim();
    var options = parseInt($('#options').val());

    if (!unit_name || !new_unit_text || new_unit_text === '--- انتخاب واحد جدید ---') {
        $('#label-description').html('');
        return;
    }

    if(options === 1)
    {
        var label = '۱ ' + unit_name + ' چند ' + new_unit_text + ' میشود؟';
        $('#label-description').html('<h4>' + label + '</h4>');
    }
    else 
    {
        var label = '۱ ' + new_unit_text + ' چند ' + unit_name + ' میشود؟' +'  / ویا '+' در یک ' + new_unit_text + ' چند ' 
        + unit_name + ' گذاشته میشود  ';
        $('#label-description').html('<h4>' + label + '</h4>');
    }
}

</script>
