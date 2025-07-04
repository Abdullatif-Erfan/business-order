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
            <label for="bought_up">{{__('buy.bought_up')}} </label>
            <input  name="old_bought_up" id="old_bought_up" type="hidden" value="{{ $boughtItemDetails->bought_up ?? 0}}" >
            <input class="form-control" name="bought_up" id="bought_up" type="number" step="0.01" 
            value="{{ $boughtItemDetails->bought_up ?? 0}}" required >

            <input class="form-control" name="total" id="total" type="hidden" step="0.01" 
            value="{{ $boughtItemDetails->total ?? 0 }}" >

        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="discount">{{__('buy.discount')}} </label>
            <input class="form-control" name="discount" id="discount" type="number" step="0.01"
            value="{{ $boughtItemDetails->discount ?? 0}}"  >
        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="transport">{{__('buy.transport')}} </label>
            <input class="form-control" name="transport" id="transport" type="number" step="0.01" 
            value="{{ $boughtItemDetails->transport ?? 0}}" required>
        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="expire_date"> {{__('buy.expired_date')}} </label>
            <input class="form-control" name="expire_date" id="expire_date" type="text" placeholder="1403-03-01"  
            value="{{ $boughtItemDetails->expire_date ?? ''}}"  >
        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="amount">{{__('common.currency')}}  </label>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="unit_id" id="unit_id" >
                <option value=""> ---  {{__('common.currency')}} ---</option>
                @foreach($units as $unitItem)
                $unit_name = $boughtItemDetails->unit_id == $unitItem->id ? $unitItem->name : '';
                 <option value="{{  $unitItem->id }}" {{ $boughtItemDetails->unit_id == $unitItem->id ? 'selected' : '' }} >{{ $unitItem->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="notification_amount">{{__('buy.notify_amount')}} </label>
            <input class="form-control" name="notification_amount" id="notification_amount" type="number" step="0.01" 
            value="{{ $warehouseItems->first()->notification_amount ?? 0 }}" >
        </div>
    </div>



    <div class="row m-t-10 m-b-10 p-10">
        
        <div class="col-md-12 col-sm-12 col-xs-12 ">
            <h5 class="m-r-10">
             {{__('buy.note1')}}
            <br>[]
             {{__('buy.note2')}}
            </h5>
            <div class="alert alert-success" id="amountMessage" style="display:none"></div>
            <!-- {{$warehouseItems->first()->warehouseRelation->name ?? ''}} -->
        </div>
        @foreach($warehouseItems as $item)
        @php
            $warehouseName = $item->warehouseRelation ? $item->warehouseRelation->name : 'Not Found';
        @endphp
        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="name"> {{__('buy.available_in')}} :  </label>
            <input  name="warehouse_id[]" id="wid" type="hidden" value="{{ $item->warehouse_id}} " >
            <select  class="form-control select2" readonly disabled >
                <option value="">{{ $warehouseName }}</option>
            </select>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="available_amount"> {{__('buy.available_amount')}} ({{ $unit_name }})  </label>
            <input class="form-control" name="available_amount" id="available_amount" type="number" step="0.01" readonly
            value="{{$item->available_amount}}" >
        </div>
        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="increment"> {{__('buy.increment_amount')}}  </label>
            <input class="form-control" name="increment[]" id="increment" type="number" step="0.01" readonly 
            placeholder="{{__('buy.incase_of_increment')}}" >
        </div>
        <div class="col-md-3 col-sm-4 col-xs-6">
            <label for="decrement"> {{__('buy.decrement_amount')}}  </label>
            <input class="form-control" name="decrement[]" id="decrement" type="number" step="0.01" readonly placeholder="{{__('buy.incase_of_decrement')}}" >
        </div>
        @endforeach

    </div>


<script>
    function checkAmountChanges(cur_amount) {
        var old_amount = parseFloat($('#old_amount').val());
        var new_amount = parseFloat(cur_amount || 0); // Ensure it's a number
        var bought_up = parseFloat($('#bought_up').val());

        var new_total = cur_amount * bought_up;
        $('#total').val(new_total.toFixed(2));

        // Empty the fields before applying readonly
        $('input[name="increment[]"]').val('');
        $('input[name="decrement[]"]').val('');
        
        if (new_amount === old_amount) {
            $('input[name="increment[]"]').prop('readonly', true);
            $('input[name="decrement[]"]').prop('readonly', true);
            $('#amountMessage').fadeOut(1);
        } 
        else if (new_amount > old_amount) { // Enable increment
            var diff = (new_amount - old_amount).toFixed(2);
            $('input[name="increment[]"]').prop('readonly', false);
            $('input[name="decrement[]"]').prop('readonly', true);
            $('#amountMessage').fadeIn(1);
            $('#amountMessage').html("{{__('buy.msg1')}}" + diff + "{{__('buy.msg1')}}");
        } 
        else if (new_amount < old_amount) { // Enable decrement
            var diff = (old_amount - new_amount).toFixed(2);
            $('input[name="increment[]"]').prop('readonly', true);
            $('input[name="decrement[]"]').prop('readonly', false);
            $('#amountMessage').fadeIn(1);
            $('#amountMessage').html("{{__('buy.msg3')}}" + diff + "{{__('buy.msg4')}}");
        }
    }
</script>