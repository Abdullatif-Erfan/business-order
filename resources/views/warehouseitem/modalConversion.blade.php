
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-6">
            <label for="item_name"> <strong>{{__('common.item_name')}}</strong>  </label>

            <input class="form-control" name="id"  type="hidden" 
            value="{{ $warehouseItems->id ?? 0 }}" >

            <input class="form-control" id="old_avg_up"  type="hidden" 
            value="{{ $warehouseItems->avg_up ?? 0 }}" >

            <input class="form-control" name="bijak_code"  type="hidden" 
            value="{{ $warehouseItems->bijak_code ?? 0 }} " >

            <input class="form-control" name="source_warehouse_id"  type="hidden"
            value="{{ $warehouseItems->warehouse_id ?? 0}}" >

            <input class="form-control" name="currency_id" id="from_currency_id" type="hidden" readonly 
            value="{{ $warehouseItems->currency_id ?? '' }}" > 
            <input class="form-control" name="default_currency_id" id="default_currency_id" type="hidden" readonly 
            value="{{ $default_currency->id ?? '' }}" > 
            <input class="form-control" name="default_currency_symbol" id="default_currency_symbol" type="hidden" readonly 
            value="{{ $default_currency->symbols ?? '' }}" > 

            <input class="form-control" name="item_name" id="item_name" type="text" readonly 
            value="{{ $warehouseItems->preListRelation->name ?? '' }}" > 

        </div>



        <div class="col-md-3 col-sm-6 col-xs-6">
            <input class="form-control" name="available_amount" id="available_amount"  type="hidden"
            value="{{ $warehouseItems->available_amount ?? 0}}" > 
            <label for="available_amount"> <strong> {{__('common.available')}}</strong>  </label>
            <div>
                 {{ $warehouseItems->available_amount ?? '' }} 
                 <span id="unit_name">{{ $warehouseItems->unitRelation->name ?? '' }}</span> 
            </div>
        </div>

        <div class="col-md-2 col-sm-6 col-xs-6">
            <label> <strong>  {{__('wh.average')}} </strong>  </label>
            <div>
                 {{ number_format($warehouseItems->avg_up,2) ?? '' }} 
                 <span>{{ $warehouseItems->currencyRelation->symbols ?? '' }}</span> 
            </div>
        </div>



        <div class="col-md-4 col-sm-6 col-xs-6">
            <label for="convertable_amount"> <strong> </strong> {{__('wh.convert_amount')}} </label>
            <div style="display:flex;gap:5px">
                 <input class="form-control" name="convertable_amount" id="convertable_amount" type="number" step="0.01"
                  oninput="checkAmountChanges(this.value)" required>
                 <span>{{ $warehouseItems->unitRelation->name ?? '' }}</span> 
            </div>
        </div>


        <div class="col-md-4 col-sm-6 col-xs-6 m-t-20">
            <label for="amount">  <strong> {{__('wh.option_selection')}} </strong> </label>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="options" id="options" required
            onchange="createLabel()">
                <option value="">--- {{__('wh.option_selection')}} ---</option>
                <option value="1">{{__('wh.convert2smaller')}}</option>
                <option value="2">{{__('wh.convert2greater')}}</option>
            </select>
        </div>

        <div class="col-md-4 col-sm-6 col-xs-6 m-t-20">
            <label for="amount">  <strong>{{__('wh.new_unit')}} </strong> </label>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="new_unit_id" id="new_unit_id" required
            onchange="createLabel()">
                <option value="">--- {{__('wh.new_unit')}} ---</option>
                @foreach($units as $unit)
                    <option value="{{  $unit->id }}" >{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>


        <div class="col-md-4 col-sm-6 col-xs-12 m-t-10">
            <label for="amount"><strong id="label-description"></strong> </label>
            <input  name="old_amount" id="old_amount" type="hidden" value="{{ $warehouseItems->available_amount ?? 0 }}" >
            <input class="form-control" name="converted_amount" id="amount" type="number" step="0.01" 
             oninput="calculateUnitPrice()" required>
        </div>

        <hr>

        <div class="col-md-6 col-sm-6 col-xs-12 m-t-10">
            <label for=""><strong> قیمت فی واحد جدید -  آیا تایید میکنید ؟ </strong> </label>
            <input class="form-control" name="avg_up" id="average_unit_price" type="text" step="0.01" 
            required>
        </div>

    
    </div>

</div>

<script>
function calculateUnitPrice()
{
    // console.log('start calculating unit price');

    var convertable_amount = parseFloat($('#convertable_amount').val());
    var converted_amount = parseFloat($('#amount').val());
    var old_avg_up = parseFloat($('#old_avg_up').val());

    // console.log('convertable_amount', convertable_amount);
    // console.log('converted_amount', converted_amount);
    // console.log('old_avg_up', old_avg_up);



    if (!convertable_amount || !converted_amount || !old_avg_up) {
        $('#avg_up').val('');
        return;
    }

    var new_avg_up = ((convertable_amount * old_avg_up) / converted_amount).toFixed(2); 
    $('#average_unit_price').val(new_avg_up);

    // console.log('new_avg_up', new_avg_up);
    createLabel();

}
function checkAmountChanges(input) 
{  
    var available_amount = parseFloat(document.getElementById('available_amount').value) || 0;
    var enteredAmount = parseFloat(input) || 0;

    if (enteredAmount > available_amount) {
        alert("{{__('wh.greater_amount_msg')}}");
        input = available_amount; // Reset to max allowed value
        $('#submitConversion').fadeOut(1);
    } else {
        $('#submitConversion').fadeIn(1);
        createLabel();
    }
}


function createLabel() 
{
    var unit_name = $('#unit_name').text().trim();
    var new_unit_text = $('#new_unit_id option:selected').text().trim();
    var options = parseInt($('#options').val());
    var convertable_amount = parseFloat($('#convertable_amount').val());
    // var converted_amount = parseFloat($('#amount').val());
    var old_avg_up = parseFloat($('#old_avg_up').val());

    if (!unit_name || !new_unit_text || new_unit_text === '--- "{{__('wh.new_unit_selection')}}"  ---') {
        $('#label-description').html('');
        return;
    }

    // if(options === 1)
    // {
    //     var label = converted_amount +' ' + unit_name + ' "{{__('wh.howMuch')}}" ' + new_unit_text + ' "{{__('wh.become')}}"';
    //     $('#label-description').html('<h5>' + label + '</h5>');
    // }
    // else 
    // {
    //     var label =   "{{__('wh.total_in')}}" + ' ' + new_unit_text + ' ? ';
    //     $('#label-description').html('<h5>' + label + '</h5>');
    // }

    if(options === 1)
    {
        var label = convertable_amount + ' ' + unit_name +' '+ '{{__('wh.howMuch')}}'+' '+new_unit_text +' '+' {{__('wh.become')}}';
        $('#label-description').html('<h5>' + label + '</h5>');
        $('#new_bought_up').html(new_unit_text); 
        $('#new_sell_up').html(new_unit_text); 
    }
    else 
    {
        var label =   "{{__('wh.total_in')}}" + '  ' + new_unit_text + ' ؟ ';
        $('#label-description').html('<h5>' + ' ' + label + ' ' + ' </h5> ');
        $('#new_bought_up').html(new_unit_text);
        $('#new_sell_up').html(new_unit_text); 
    }
}

</script>
