    <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="item_name">{{__('sales.item')}}</label>

            <input class="form-control" name="id"  type="hidden" 
            value="{{ $salesDetails->id ?? 0 }}" >

            <input class="form-control" name="pre_list_id"  type="hidden"
            value="{{ $salesDetails->pre_list_id ?? 0}}" >

            <input class="form-control" name="billno"  type="hidden"  
            value="{{ $salesDetails->billno ?? 0}}" >

            <input class="form-control" name="warehouse_id"  type="hidden"  
            value="{{ $salesDetails->warehouse_id ?? 0 }}" >

            <input class="form-control" name="item_name" id="item_name" type="text" readonly value={{ $salesDetails->preListRelation->name ?? ''}} >
        </div>

        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="amount"> {{__('common.amount')}} </label>
            <input name="old_amount"  type="hidden" step="0.01" value="{{ $salesDetails->amount ?? ''}}">
            <input class="form-control" name="amount"  type="number" step="0.01" 
            value="{{ $salesDetails->amount ?? ''}}" required >
        </div>

        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="amount"> {{__('common.unit')}}  </label>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="unit_id" id="unit_id" >
                <option value=""> --- {{__('wh.unit_selection')}} ---</option>
                @foreach($units as $unitItem)
                    <option value="{{  $unitItem->id }}" {{ $salesDetails->unit_id == $unitItem->id ? 'selected' : '' }} >{{ $unitItem->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="sell_up"> {{__('sales.sold_up')}} </label>
            <input class="form-control" name="sell_up" id="sell_up" type="number" step="0.01" 
            value="{{ $salesDetails->sell_up ?? 0}}" required >
        </div>

        <div class="col-md-8 col-sm-4 col-xs-6">
            <label for="discount">{{__('common.discount')}} </label>
            <input class="form-control" name="discount" id="discount" type="number" step="0.01"
            value="{{ $salesDetails->discount ?? 0}}">
        </div>

    
    </div>
