@foreach($warehouseItems as $item)
    <div class="col-md-3 col-sm-6 mb-4 px-2" 
     data-item-id="{{ $item->id }}" 
     data-pre-list-id="{{ $item->pre_list_id }}"
     data-sell-up="{{ $item->sell_up }}"
     data-avg-up="{{ $item->avg_up }}"
     data-item-name="{{ $item->item_name }}"
     data-image-path="{{ $item->image_path }}"
     data-unit-name="{{ $item->unit_name }}"
     data-warehouse-id="{{ $item->warehouse_id }}"
     data-unit-id="{{ $item->unit_id }}"
     data-available-amount="{{ $item->available_amount }}"
     onclick="addItem({{ $item->id }})">

     <div class="warehouse-card shadow-sm">
            <div class="card-image">
                @if($item->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->image_path))
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->item_name }}">
                @else
                    <img src="{{ asset('assets/img/no_image.png') }}" alt="{{ $item->item_name }}">
                @endif
            </div>
            <div class="card-body">
                <h5 class="item-title">{{ $item->item_name }}</h5>
                <div class="badge-group">
                    <span class="amount bordered-badge">{{ $item->available_amount }} {{ $item->unit_name }}</span>
                    <span class="price">{{__('sales.price')}}: {{ $item->sell_up }}</span>
                    <input type="hidden" id="avg_up" value="{{ $item->avg_up }}">
                    <input type="hidden" id="pre_list_id" value="{{ $item->pre_list_id }}">
                    <input type="hidden" id="sell_up" value="{{ $item->sell_up }}">
                </div>
                <center><span class="badge badge-secondary w-100 m-t-10"> {{__('wh.at')}} {{ $item->warehouse_name }}</span></center>
            </div>
        </div>
</div>

@endforeach