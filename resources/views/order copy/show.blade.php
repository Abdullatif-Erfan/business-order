<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('order.order_details') }} - #{{ $order->ord_num }}</h4>
                </div>
                <div class="card-body">
                    <!-- ========================================= -->
                    <!-- HEADER INFORMATION -->
                    <!-- ========================================= -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:20%">{{ __('order.order_number') }}</th>
                                <td style="width:30%">{{ $order->ord_num }}</td>
                                <th style="width:20%">{{ __('order.status') }}</th>
                                <td style="width:30%">
                                    @if($order->state == 0)
                                        <span class="badge badge-warning">{{ __('order.draft') }}</span>
                                    @elseif($order->state == 1)
                                        <span class="badge badge-warning">{{ __('order.new') }}</span>
                                    @elseif($order->state == 2)
                                    <span class="badge badge-danger">{{ __('order.cancelled') }}</span>
                                    @elseif($order->state == 3)
                                    <span class="badge badge-success">{{ __('order.completed') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('order.unknown') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('order.supplier_name') }}</th>
                                <td>{{ $order->supplierRelation ? $order->supplierRelation->name : '-' }}</td>
                                <th>{{ __('order.employee_name') }}</th>
                                <td>{{ $order->employeeRelation ? $order->employeeRelation->name : '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('common.date') }}</th>
                                <td>{{ $order->idate ? \Carbon\Carbon::parse($order->idate)->format('Y/m/d') : '-' }}</td>
                                <th>{{ __('order.created_by') }}</th>
                                <td>{{ $order->iby ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('order.created_at') }}</th>
                                <td>{{ $order->created_at ? $order->created_at->format('Y/m/d H:i') : '-' }}</td>
                                <th>{{ __('order.done_by') }}</th>
                                <td>{{ $order->done_by ?? '-' }}</td>
                            </tr>
                            @if($order->done_year)
                            <tr>
                                <th>{{ __('order.completed_date') }}</th>
                                <td colspan="3">{{ $order->done_year }}/{{ $order->done_month }}/{{ $order->done_day }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    <!-- ========================================= -->
                    <!-- ITEMS LIST -->
                    <!-- ========================================= -->
                    <div class="m-t-20">
                        <h5>{{ __('order.order_items') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr style="background:#e9fffe">
                                        <th style="width:5%">{{ __('common.number') }}</th>
                                        <th style="width:40%">{{ __('wh.item_selection') }}</th>
                                        <th style="width:20%">{{ __('order.amount') }}</th>
                                        <th style="width:35%">{{ __('order.category') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderItems as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $item->preListRelation ? $item->preListRelation->name : '-' }}</td>
                                        <td>{{ $item->amount }} {{ $item->unitRelation ? $item->unitRelation->name : '-' }} 
                                            </td>
                                        <td>
                                            @if($item->category_id)
                                                <span class="badge badge-info"></span>
                                            @endif
                                            {{ $item->categoryRelation ? $item->categoryRelation->name : '-' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('orders.edit', $order->ord_num) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> {{ __('common.edit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>