<div class="container-fluid">
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
            <tbody>
                <!-- Row 1: Return Number & Bill & Supplier -->
                <tr>
                    <td style="width: 18%; background: #f8f9fa; font-weight: bold;">
                        {{ __('buy.return_number') }}:
                    </td>
                    <td style="width: 32%;">
                        <span class="badge badge-info">{{ $return->return_number }}</span>
                    </td>
                    <td style="width: 18%; background: #f8f9fa; font-weight: bold;">
                        {{ __('common.bill') }}:
                    </td>
                    <td style="width: 32%;">
                        <span class="text-primary font-weight-bold">BUY_{{ $return->billno }}</span>
                    </td>
                </tr>
                
                <!-- Row 2: Supplier & Item & Unit -->
                <tr>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('order.supplier_name') }}:
                    </td>
                    <td>
                        {{ $return->supplier->name ?? '-' }}
                    </td>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('sales.item') }}:
                    </td>
                    <td>
                        {{ $return->preList->name ?? '-' }}
                    </td>
                </tr>
                
                <!-- Row 3: Quantity & Unit Price & Total -->
                <tr>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('sales.quantity') }}:
                    </td>
                    <td>
                        <span class="text-info">{{ number_format($return->quantity, 2) }}</span>
                    </td>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('common.unit_price') }}:
                    </td>
                    <td>
                        {{ number_format($return->unit_price, 2) }}
                    </td>
                </tr>
                
                <!-- Row 4: Unit & Date & User -->
                <tr>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('common.unit') }}:
                    </td>
                    <td>
                        {{ $return->unit->name ?? '-' }}
                    </td>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('common.date') }}:
                    </td>
                    <td>
                        {{ $return->return_date ? \Carbon\Carbon::parse($return->return_date)->format('Y-m-d') : '-' }}
                    </td>
                </tr>
                
                <!-- Row 5: Total & Tax & Tax Amount -->
                <tr>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('common.total_price') }}:
                    </td>
                    <td>
                        <span class="text-success font-weight-bold">{{ number_format($return->total, 2) }}</span>
                    </td>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('buy.tax') }}:
                    </td>
                    <td>
                        {{ $return->tax_percentage ? $return->tax_percentage . '%' : '-' }}
                    </td>
                </tr>
                
                <!-- Row 6: Tax Amount & User & Empty -->
                <tr>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('buy.tax_amount') }}:
                    </td>
                    <td>
                        {{ $return->tax_amount ? number_format($return->tax_amount, 2) : '-' }}
                    </td>
                    <td style="background: #f8f9fa; font-weight: bold;">
                        {{ __('common.user') }}:
                    </td>
                    <td>
                        {{ $return->user_name ?? '-' }}
                    </td>
                </tr>
                
                <!-- Row 7: Reason (full width) -->
                <tr>
                    <td style="background: #f8f9fa; font-weight: bold; vertical-align: top;">
                        {{ __('buy.reason') }}:
                    </td>
                    <td colspan="3" style="padding: 8px;">
                        <p class="well well-sm" style="margin: 0; background: #f9f9f9; padding: 10px; border-radius: 4px; border: 1px solid #eee;">
                            {{ $return->reason ?? '-' }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>