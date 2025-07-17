
    <div class="row m-t-10 m-b-10 p-10">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <input type="hidden" id="delete_amount" name="delete_amount" value="{{ $boughtItemDetailsAmount }}" >
            <input type="hidden" id="delete_id" name="delete_id" value="{{ $boughtItemDetailsId  }}" >
            <input type="hidden" id="preListId" name="preListId" value="{{ $preListId  }}" >
            <input type="hidden" id="times" name="times" value="{{ $times  }}" >

            <div class="alert alert-success"> {{__('buy.in_amount')}} {{ $boughtItemDetailsAmount }} {{ $units->first()->name ?? '' }} {{ $preListName ?? ''}}  {{__('sales.from_which_store_decrease')}} </div>
        </div>
        @foreach($warehouseItems as $item)
        @php
            $warehouseName = $item->warehouseRelation ? $item->warehouseRelation->name : 'Not Found';
        @endphp
        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="name"> {{__('buy.warehouse')}} </label>
            <input  name="warehouse_id[]" id="wid" type="hidden" value="{{ $item->warehouse_id }}">
            <select  class="form-control select2" readonly disabled >
                <option value="">{{ $warehouseName }}</option>
            </select>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="available_amount"> {{__('buy.available_amount')}} </label>
            <input class="form-control" name="available_amount" id="available_amount" type="number" step="0.01" readonly
            value="{{ $item->available_amount }}">
        </div>
        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="decrement">  {{__('buy.decrease_amount')}}  </label>
            <input class="form-control" name="decrement[]" id="decrement" type="number" step="0.01"  
            placeholder="{{__('buy.deleteable_amount')}}" >
        </div>
        @endforeach

    </div>

<script>
    $(document).ready(function() {
        let deleteAmount = parseFloat($('#delete_amount').val());
        let $deleteButton = $('#delete_button');

        function validateDecrements() {
            let totalDecrement = 0;
            let isValid = true;

            $('input[name="decrement[]"]').each(function() {
                let decrementValue = parseFloat($(this).val()) || 0;
                let availableAmount = parseFloat($(this).closest('.col-xs-6').prev().find('input[name="available_amount"]').val()) || 0;

                if (decrementValue > availableAmount) {
                    alert("{{__('common.over_pay')}}");
                    $(this).val(availableAmount); // Auto-correct value
                    isValid = false;
                }

                totalDecrement += decrementValue;
            });

            if (totalDecrement !== deleteAmount) {
                $deleteButton.hide(); // Hide button if total is not equal
            } else {
                $deleteButton.show(); // Show button when total is correct
            }
        }

        $('input[name="decrement[]"]').on('input', validateDecrements);
    });
</script>


