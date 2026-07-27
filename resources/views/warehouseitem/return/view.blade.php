<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Return Header Info -->
            <div class="alert alert-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong>{{ __('buy.return_number') }}:</strong> 
                        <span class="badge badge-primary" style="font-size:14px;">{{ $return->return_number }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('common.bill') }}:</strong> 
                        <span class="badge badge-info">BUY_{{ $return->billno }}</span>
                    </div>
                    <!-- <div class="col-md-4">
                        <strong>{{ __('common.status') }}:</strong> 
                        {!! $return->status_badge !!}
                    </div> -->
                </div>
            </div>

            <!-- Return Details Table -->
            <table class="table table-bordered ">
                <tr>
                    <th width="20%" style="background:#f8f9fa;">{{ __('order.supplier_name') }}</th>
                    <td width="30%">{{ $return->supplier->name ?? '-' }}</td>
                    <th style="background:#f8f9fa;">{{ __('common.return_date') }}</th>
                    <td>{{ $return->return_date ? $return->return_date->format('Y-m-d') : '-' }}</td>
                </tr>
                <tr>
                    <th style="background:#f8f9fa;">{{ __('sales.quantity') }}</th>
                    <td><strong>{{ number_format($return->quantity, 2) }}</strong></td>
                    <th style="background:#f8f9fa;">{{ __('common.car') }}</th>
                    <td>{{ $return->car->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th style="background:#f8f9fa;">{{ __('common.unit') }}</th>
                    <td>{{ $return->unit->name ?? '-' }}</td>
                    <th style="background:#f8f9fa;">{{ __('common.unit_price') }}</th>
                    <td>{{ number_format($return->unit_price, 2) }}</td>
                </tr>
                <tr>
                    <th width="20%" style="background:#f8f9fa;">{{ __('sales.item') }}</th>
                    <td width="30%">{{ $return->preList->name ?? '-' }}</td>
                    <th style="background:#f8f9fa;">{{ __('common.currency') }}</th>
                    <td>{{ $return->currency->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th style="background:#f8f9fa;">{{ __('common.total_price') }}</th>
                    <td><strong>{{ number_format($return->total, 2) }}</strong></td>
                  
                    <th style="background:#f8f9fa;">{{ __('common.user') }}</th>
                    <td>{{ $return->user_name ?? '-' }}</td>
                </tr>
                <tr>
                    <th style="background:#f8f9fa;">{{ __('buy.reason') }}</th>
                    <td colspan="3">{{ $return->reason ?? '-' }}</td>
                </tr>
                <tr>
                    <th style="background:#f8f9fa;">{{ __('common.created_at') }}</th>
                    <td>{{ $return->created_at ? $return->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                    <th style="background:#f8f9fa;">{{ __('common.updated_at') }}</th>
                    <td>{{ $return->updated_at ? $return->updated_at->format('Y-m-d H:i:s') : '-' }}</td>
                </tr>
            </table>

            <!-- Payment Summary -->
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <strong>{{ __('buy.paid_amount') }}:</strong> 
                        {{ number_format($return->paid_amount, 2) }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <strong>{{ __('buy.remaining_amount') }}:</strong> 
                        {{ number_format($return->remaining_amount, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>