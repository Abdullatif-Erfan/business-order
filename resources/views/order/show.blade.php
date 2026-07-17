<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- ========================================= -->
            <!-- PRINT HEADER (Visible only in print) -->
            <!-- ========================================= -->
            <div class="print-header visible-print" style="display: none; text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px double #333;">
                @if(isset($orgbios) && count($orgbios) > 0)
                    <img src="{{ asset($orgbios[0]->header) }}" alt="Company Logo" style="max-width: 100%; height: auto; max-height: 120px; margin-bottom: 10px;">
                @endif
                <h2 style="margin: 5px 0 5px 0; font-size: 24px; font-weight: bold; color: #000;">{{ __('order.order_details') }}</h2>
                <p style="margin: 0; font-size: 14px; color: #333;">
                    {{ __('common.print_date') }}: {{ now()->format('Y/m/d H:i') }}
                </p>
            </div>

            <div class="card" style="border-radius: 10px; box-shadow: 0 2px 20px rgba(0,0,0,0.08);">
                <!-- ========================================= -->
                <!-- CARD HEADER -->
                <!-- ========================================= -->
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 10px 10px 0 0; display: flex; justify-content: space-between; align-items: center;">
                    <h4 style="color: #fff; margin: 0; font-weight: 600; font-size: 18px;">
                        <i class="fas fa-file-invoice"></i> {{ __('order.order_details') }}
                        <span style="font-size: 14px; font-weight: normal; opacity: 0.9;">
                            #{{ $order->ord_num ?? $order->id ?? 'N/A' }}
                        </span>
                    </h4>
                    <div class="no-print">
                        <button onclick="print_page_with_image()" class="btn btn-sm" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #fff; border-radius: 5px; padding: 6px 12px;">
                            <i class="fas fa-print"></i> {{ __('common.print') }}
                        </button>
                    </div>
                </div>

                <div class="card-body" style="padding: 20px;">
                    <!-- ========================================= -->
                    <!-- ORDER INFORMATION -->
                    <!-- ========================================= -->
                    <div style="margin-bottom: 20px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                            <tr>
                                <td style="padding: 8px 12px; background: #f8f9fa; border: 1px solid #e9ecef; font-weight: 600; width: 15%;">
                                    {{ __('order.order_number') }}
                                </td>
                                <td style="padding: 8px 12px; border: 1px solid #e9ecef; width: 35%; font-weight: bold; font-size: 16px;">
                                    #{{ $order->ord_num ?? $order->id ?? 'N/A' }}
                                </td>
                                <td style="padding: 8px 12px; background: #f8f9fa; border: 1px solid #e9ecef; font-weight: 600; width: 15%;">
                                    {{ __('order.status') }}
                                </td>
                                <td style="padding: 8px 12px; border: 1px solid #e9ecef; width: 35%;">
                                    @php
                                        $state = $order->state ?? 0;
                                        $badgeClass = 'badge-warning';
                                        $statusText = __('order.new');
                                        if($state == 0) { $badgeClass = 'badge-secondary'; $statusText = __('order.draft'); }
                                        elseif($state == 1) { $badgeClass = 'badge-warning'; $statusText = __('order.new'); }
                                        elseif($state == 2) { $badgeClass = 'badge-danger'; $statusText = __('order.cancelled'); }
                                        elseif($state == 3) { $badgeClass = 'badge-success'; $statusText = __('order.completed'); }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="padding: 4px 12px; font-size: 13px; border-radius: 4px;">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 12px; background: #f8f9fa; border: 1px solid #e9ecef; font-weight: 600;">
                                     {{ __('order.created_at') }}
                                </td>
                                <td style="padding: 8px 12px; border: 1px solid #e9ecef;">
                                     {{ $order->created_at ? $order->created_at->format('Y/m/d H:i') : '-' }}
                                </td>
                                <td style="padding: 8px 12px; background: #f8f9fa; border: 1px solid #e9ecef; font-weight: 600;">
                                    {{ __('common.user') }}
                                </td>
                                <td style="padding: 8px 12px; border: 1px solid #e9ecef;">
                                    {{ $order->user_name ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 12px; background: #f8f9fa; border: 1px solid #e9ecef; font-weight: 600;">
                                    {{ __('order.supplier_name') }}
                                </td>
                                <td style="padding: 8px 12px; border: 1px solid #e9ecef;">
                                    {{ $order->supplier_name ?? $order->supplierRelation->name ?? '-' }}
                                </td>
                                <td style="padding: 8px 12px; background: #f8f9fa; border: 1px solid #e9ecef; font-weight: 600;">
                                    
                                </td>
                                <td style="padding: 8px 12px; border: 1px solid #e9ecef;">
                                   
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- ========================================= -->
                    <!-- ITEMS LIST - Grouped by Category -->
                    <!-- ========================================= -->
                    <div style="margin-top: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h5 style="margin: 0; font-weight: 600; font-size: 16px; color: #333;">
                                <i class="fas fa-list" style="color: #667eea;"></i> {{ __('order.order_items') }}
                            </h5>
                            <span style="font-size: 13px; color: #999;">
                                {{ __('common.totally') }}: <strong>{{ $groupedItems->sum('total_items') ?? 0 }}</strong> {{ __('common.records') }}
                            </span>
                        </div>
                        
                        @if(isset($groupedItems) && count($groupedItems) > 0)
                            <div class="table-responsive" id="print_area">
                                <table class="table table-bordered display my_table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="4">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="4">
                                            <center> {{__('order.list_title')}}   </center>
                                            </td>
                                        </tr>


                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="2">{{ __('order.order_number') }}</td>
                                             <td colspan="2"> #{{ $order->ord_num ?? $order->id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="2"> {{ __('order.supplier_name') }}</td>
                                             <td colspan="2"> {{ $order->supplier_name ?? $order->supplierRelation->name ?? '-' }} </td>
                                        </tr>
                                        <tr >
                                            <th style="width: 6%; padding: 10px 8px; text-align: center; font-weight: 600">#</th>
                                            <th style="width: 44%; padding: 10px 8px; text-align: right; font-weight: 600">{{ __('common.items') }}</th>
                                            <th style="width: 20%; padding: 10px 8px; text-align: center; font-weight: 600">{{ __('common.amount') }}</th>
                                            <th style="width: 15%; padding: 10px 8px; text-align: center; font-weight: 600">{{ __('common.unit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $counter = 0; @endphp
                                        @php $grandTotal = 0; @endphp
                                        
                                        @foreach($groupedItems as $categoryGroup)
                                            <!-- Category Header Row -->
                                            <tr style="background: #e8f4fd; border-top: 2px solid #ddd;">
                                                <td colspan="4" style="padding: 8px 12px;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                                        <strong style="font-size: 15px; color: #2c3e50;">
                                                            <i class="fas fa-folder-open" style="color: #667eea;"></i>
                                                            {{ $categoryGroup['category_name'] }}
                                                        </strong>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            <!-- Items in this category -->
                                            @foreach($categoryGroup['items'] as $item)
                                                @php $counter++; @endphp
                                                @php $grandTotal += $item['amount']; @endphp
                                                <tr style="border-bottom: 1px solid #eee;">
                                                    <td style="padding: 8px; text-align: center;">{{ $counter }}</td>
                                                    <td style="padding: 8px; text-align: right;">
                                                        {{ $item['pre_list_name'] }}
                                                    </td>
                                                    <td style="padding: 8px; text-align: center; font-weight: 600; color: #2c3e50;">
                                                        {{ number_format($item['amount'], 2) }}
                                                    </td>
                                                    <td style="padding: 8px; text-align: center;">{{ $item['unit_name'] }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        
                                        <!-- Grand Total Row -->
                                        <tr style="background: #f8f9fa; border-top: 3px double #667eea; font-weight: 600;">
                                            <td colspan="2" style="padding: 12px 8px; text-align: left; font-size: 15px;">
                                                <i class="fas fa-calculator" style="color: #667eea;"></i> {{ __('order.total_items') }}
                                            </td>
                                            <td style="padding: 12px 8px; text-align: center; font-size: 16px; color: #667eea;">
                                                {{ number_format($grandTotal, 2) }}
                                            </td>
                                            <td style="padding: 12px 8px; text-align: center; font-size: 13px; color: #666;">
                                                {{ $counter }} {{ __('order.item_type') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info" style="border-radius: 8px; padding: 20px; text-align: center;">
                                <i class="fas fa-info-circle" style="font-size: 24px;"></i>
                                <p style="margin: 10px 0 0 0;">{{ __('order.no_items_found') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- ========================================= -->
                <!-- CARD FOOTER -->
                <!-- ========================================= -->
                <div class="card-footer no-print" style="background: #f8f9fa; border-top: 1px solid #eee; padding: 12px 20px; text-align: center; border-radius: 0 0 10px 10px;">
                    <span style="font-size: 12px; color: #999;">
                        {{ __('common.print_date') }}: {{ now()->format('Y/m/d H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    /* Reset body for print */
    body {
        background: #fff !important;
        padding: 20px !important;
        margin: 0 !important;
    }
    
    /* Hide everything except the print area */
    .main-panel,
    .content,
    .page-inner,
    .row,
    .col-md-12,
    .card {
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        border: none !important;
        background: #fff !important;
    }
    
    .card-header {
        background: #fff !important;
        border-bottom: 2px solid #333 !important;
        padding: 10px 15px !important;
        border-radius: 0 !important;
    }
    
    .card-header h4 {
        color: #000 !important;
    }
    
    .card-header h4 span {
        color: #000 !important;
    }
    
    .card-body {
        padding: 10px 15px !important;
    }
    
    .no-print {
        display: none !important;
    }
    
    .visible-print {
        display: block !important;
    }
    
    /* Print header styles */
    .print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 3px double #333;
    }
    
    .print-header img {
        max-width: 100%;
        height: auto;
        max-height: 120px;
        margin-bottom: 10px;
    }
    
    .print-header h2 {
        font-size: 22px;
        font-weight: bold;
        color: #000;
        margin: 5px 0;
    }
    
    .print-header p {
        font-size: 14px;
        color: #333;
        margin: 0;
    }
    
    /* Table styles for print */
    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    .table-bordered {
        border: 1px solid #000 !important;
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000 !important;
        padding: 6px 8px !important;
    }
    
    .table thead th {
        background: #e9ecef !important;
        color: #000 !important;
        font-weight: bold !important;
    }
    
    .table tbody tr {
        background: #fff !important;
    }
    
    .table tbody tr:nth-child(even) {
        background: #f8f9fa !important;
    }
    
    .table tbody td {
        color: #000 !important;
    }
    
    /* Category header in print */
    .table tbody tr[style*="background: #e8f4fd"] {
        background: #e9ecef !important;
    }
    
    .table tbody tr[style*="background: #e8f4fd"] td {
        font-weight: bold !important;
    }
    
    /* Grand total row in print */
    .table tbody tr:last-child {
        background: #f8f9fa !important;
        border-top: 3px double #000 !important;
    }
    
    .table tbody tr:last-child td {
        font-weight: bold !important;
        color: #000 !important;
    }
    
    /* Badge styles for print */
    .badge {
        border: 1px solid #000 !important;
        background: #fff !important;
        color: #000 !important;
        padding: 2px 8px !important;
        border-radius: 4px !important;
        font-weight: bold !important;
    }
    
    /* Info table in print */
    .table-bordered tr td {
        border: 1px solid #000 !important;
    }
    
    .table-bordered tr td[style*="background: #f8f9fa"] {
        background: #e9ecef !important;
        font-weight: bold !important;
    }
    
    /* Responsive print */
    .col-md-12,
    .col-md-6,
    .col-sm-6,
    .col-xs-12 {
        width: 100% !important;
        float: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Print footer */
    .card-footer {
        display: none !important;
    }
}
</style>