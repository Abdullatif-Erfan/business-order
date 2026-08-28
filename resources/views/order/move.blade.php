<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                     #{{ $order->ord_num ?? $order->id }}</h4>
                </div>
                <div class="card-body">
                    <!-- ========================================= -->
                    <!-- ORDER INFORMATION -->
                    <!-- ========================================= -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:15%">{{ __('order.order_number') }}</th>
                                <td style="width:35%">{{ $order->ord_num ?? $order->id }}</td>
                                <th style="width:15%">{{ __('order.created_at') }}</th>
                                <td style="width:35%">{{ $order->created_at ? $order->created_at->format('Y/m/d H:i') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('common.user') }}</th>
                                <td>{{ $order->user_name ?? '-' }}</td>
                                <th>{{ __('order.status') }}</th>
                                <td>
                                    <span class="badge badge-{{ $order->state == 1 ? 'primary' : ($order->state == 2 ? 'warning' : ($order->state == 3 ? 'success' : 'danger')) }}">
                                        {{ $order->state == 1 ? __('order.new') : ($order->state == 2 ? __('order.pending') : ($order->state == 3 ? __('order.completed') : __('order.cancelled'))) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('common.category') }}</th>
                                <td>{{ $order->categoryRelation->name ?? '-' }}</td>
                                <th>{{ __('common.car') }}</th>
                                <td>{{ $order->car->plate_number ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- ========================================= -->
                    <!-- MOVE ITEMS FORM -->
                    <!-- ========================================= -->
                    <div class="m-t-20">
                            <!-- Items List Table -->
                            <div class="m-t-20">
                                @if(isset($orderItems) && count($orderItems) > 0)
                                <form id="moveItemsForm">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="itemsPreviewTable">
                                        <thead>
                                            <tr style="background:#e9fffe">
                                                <th style="width:5%">#</th>
                                                <th style="width:35%">{{ __('common.items') }}</th>
                                                <th style="width:15%">تهیه کننده</th>
                                                <th style="width:15%">مقدار سفارش</th>
                                                <th style="width:15%">مقدار انتقال</th>
                                                <th style="width:15%">انتقال به </th>
                                                <th style="width:10%">تایید</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $counter = 0; @endphp
                                            @foreach($orderItems as $item)
                                                @php $counter++; @endphp
                                                <tr class="item-row" data-item-id="{{ $item->id }}" data-supplier-id="{{ $item->preList->supplier_id ?? null }}">
                                                    <td class="text-center">{{ $counter }}</td>
                                                    <td>{{ $item->preList->name ?? '-' }}</td>
                                                    <td>
                                                        <span class="supplier-name">
                                                            {{ $order->supplierRelation->name ?? $order->supplierRelation->name ?? '-' }}
                                                        </span>
                                                    </td>

                                                     

                                                    <td class="text-center">{{ number_format($item->amount, 2) }} {{ $item->unit->name ?? '-' }}</td>
                                                    <td><input type="number" name="move_amount" value="0" min="0" max="{{ $item->amount ?? 0 }}" placeholder="ex: 1" class="move-amount" ></td>
                                                    <td>
                                                        <select class="form-control select2 supplier-select" style="width: 100%; background-color:#ddd;" 
                                                            name="supplier_id" id="supplier_id" required>
                                                        <option value="">{{ __('order.supplier_selection') }}</option>
                                                        @foreach($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}">
                                                                {{ $supplier->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    </td>
                                                    <td><input type="button" class="save-move-btn" name="save" value="ثبت"></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                </form>
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

