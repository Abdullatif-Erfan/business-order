    <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="item_name">{{__('buy.item')}} </label>

            <input class="form-control" name="id"  type="hidden" 
            value="{{ $boughtItemDetails->id ?? 0 }}" >

            <input class="form-control" name="pre_list_id"  type="hidden"
            value="{{ $boughtItemDetails->pre_list_id ?? 0}}" >

            <input class="form-control" name="times"  type="hidden"  
            value="{{ $boughtItemDetails->times ?? 0}}" >

            <input class="form-control" name="item_name" id="item_name" type="text" readonly 
            value="{{ $boughtItemDetails->preListRelation->name ?? ''}}" >

            <input class="form-control" name="hidden" id="max_amount" type="hidden" readonly 
            value="{{ $boughtItemDetails->amount ?? ''}}" >

        </div>

        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="amount">{{__('buy.return_amount')}} </label>
            <input class="form-control" name="amount" id="amount" type="number" step="0.01"
             oninput="checkAmountChanges(this.value)"  required >

             <small class="text-muted">{{ __('buy.max_return') }}: <span id="max_return_label">{{ $boughtItemDetails->amount ?? ''}}</span></small>
        </div>

          <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="amount">{{__('common.unit')}}  </label>
            <input name="unit_id" class="form-control" id="unit_id" readonly type="text" value="{{ $boughtItemDetails->unitRelation->name ?? '' }}">
        </div>

         <div class="col-md-12 col-sm-12 col-xs-12 m-t-10">
                <label for="reason"> {{__('buy.reason')}} </label>
                <input class="form-control" name="reason" id="reason" type="text" required placeholder="{{__('buy.comment')}}" >
        </div>
    </div>


<script>
    function checkAmountChanges(cur_amount) 
    {
        var maxAmount = parseFloat($('#max_amount').val()) || 0;
        var curAmount  = parseFloat(cur_amount) || 0;
        if(curAmount > maxAmount) {
        //    showNotification('مقدار برگشت بزرگتر از مقدار موجود میباشد', 'danger');
        alert('مقدار برگشت بزرگتر از مقدار موجود میباشد');
           ('#return_submit_button').fadeOut(1);
           $('#return_submit_button').prop('disabled', true);
        } else {
           ('#return_submit_button').fadeIn(1);
           $('#return_submit_button').prop('disabled', false);
        }
    }
</script>