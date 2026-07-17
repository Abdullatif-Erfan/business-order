<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('order.edit_order') }} - #{{ $order->ord_num ?? $order->id }}</h4>
                </div>
                <div class="card-body">
                    <!-- ========================================= -->
                    <!-- HEADER INFORMATION -->
                    <!-- ========================================= -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>{{ __('order.created_at') }}</th>
                                <td>{{ $order->created_at ? $order->created_at->format('Y/m/d H:i') : '-' }}</td>
                                <th>{{ __('common.user') }}</th>
                                <td>{{ $order->user_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('order.status') }}</th>
                                <td colspan="3">
                                    <select class="form-control" id="orderStatus" style="width: 200px;">
                                        <option value="1" {{ $order->state == 1 ? 'selected' : '' }}>{{ __('order.new') }}</option>
                                        <option value="2" {{ $order->state == 2 ? 'selected' : '' }}>{{ __('order.cancelled') }}</option>
                                        <option value="3" {{ $order->state == 3 ? 'selected' : '' }}>{{ __('order.completed') }}</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- ========================================= -->
                    <!-- ITEMS LIST GROUPED BY CATEGORY -->
                    <!-- ========================================= -->
                    <div class="m-t-20">
                        <h5>{{ __('order.order_items') }}</h5>
                        
                        @if(isset($groupedItems) && count($groupedItems) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead>
                                        <tr style="background:#e9fffe">
                                            <th style="width:5%">{{ __('common.number') }}</th>
                                            <th style="width:35%">{{ __('common.items') }}</th>
                                            <th style="width:20%">{{ __('common.amount') }}</th>
                                            <th style="width:15%">{{ __('common.unit') }}</th>
                                            <th style="width:15%">{{ __('common.save') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $counter = 0; @endphp
                                        @foreach($groupedItems as $categoryGroup)
                                            <!-- Category Header Row -->
                                            <tr style="background-color:#f0f8ff;" class="category-header">
                                                <td colspan="5">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <strong>
                                                            <i class="fas fa-folder-open"></i> 
                                                            {{ $categoryGroup['category_name'] }}
                                                        </strong>
                                                        <span class="badge badge-info category-total">
                                                            {{ __('common.total') }}: {{ number_format($categoryGroup['total_amount'], 2) }}
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            <!-- Items in this category -->
                                            @foreach($categoryGroup['items'] as $item)
                                                @php $counter++; @endphp
                                                <tr class="item-row" data-item-id="{{ $item['id'] }}">
                                                    <td class="text-center">{{ $counter }}</td>
                                                    <td>
                                                        {{ $item['pre_list_name'] }}
                                                        <input type="hidden" class="item-id" value="{{ $item['id'] }}">
                                                        <input type="hidden" class="pre-list-id" value="{{ $item['pre_list_id'] }}">
                                                        <input type="hidden" class="unit-id" value="{{ $item['unit_id'] }}">
                                                        <input type="hidden" id="orderId" value="{{ $order->id }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control text-center item-amount" 
                                                               value="{{ $item['amount'] }}" 
                                                               step="0.01" 
                                                               min="0"
                                                               style="width: 120px;">
                                                    </td>
                                                    <td>{{ $item['unit_name'] }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary save-item-btn" data-item-id="{{ $item['id'] }}">
                                                            <i class="fas fa-save"></i> {{ __('common.save') }}
                                                        </button>
                                                        <span class="save-status" style="display: none; margin-left: 5px;">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> {{ __('order.no_items_found') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
