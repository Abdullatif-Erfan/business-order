<!-- ========================================= -->
<!-- FILTER SECTION - DRAFT ORDERS -->
<!-- ========================================= -->
<div class="filter-section no-print" id="FilterWrapper">
    <div class="filter-group">
        <input type="text" id="draft_item_name" placeholder="{{ __('buy.item') }}" class="form-control">
    </div>
    <div class="filter-group">
        <input type="text" id="draft_customer_name" placeholder="{{ __('order.customer') }}" class="form-control">
    </div>
    <div class="filter-group" style="min-width: 120px;">
        <div class="input-group">
            <input type="text" class="form-control datepicker-input" id="draft_start_date" 
            placeholder="{{__('common.start_date')}}">
            <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
        </div>
    </div>
    <div class="filter-group" style="min-width: 120px;">
        <div class="input-group">
            <input type="text" class="form-control datepicker-input" id="draft_end_date"  placeholder="{{__('common.end_date')}}">
            <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
        </div>
    </div>
    <div class="filter-actions">
        <button class="btn btn-search" id="draft_btn_filter"><i class="fas fa-search"></i></button>
        <button class="btn btn-reset" id="draft_btn_reset" title="{{ __('common.reset') }}"><i class="fas fa-undo"></i></button>
    </div>
</div>

<!-- ========================================= -->
<!-- TABLE - DRAFT ORDERS -->
<!-- ========================================= -->
 <div class="table-responsive" id="print_area" style="padding:5px;">
    <span class="pull-left visible-print"> {{__('common.print_date')}} : {{ $todaysDate }}</span>
    <table id="draftOrderTable" class="display responsive nowrap table table-bordered" width="100%">
        <thead>
            <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                <td colspan="8">
                    <img src="{{ asset($orgbios[0]->header ?? '') }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                </td>
            </tr>
            <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                <td colspan="8">
                    <center> {{__('order.list_title')}} </center>
                </td>
            </tr>
            <tr>
                <th style="width:5%">{{ __('common.number') }}</th>
                <th style="width:10%">{{ __('order.order_number') }}</th>
                <th style="width:15%">{{ __('order.customer') }}</th>
                <th style="width:15%">{{ __('order.item') }}</th>
                <th style="width:10%">{{ __('order.amount') }}</th>
                <th style="width:10%">{{ __('order.unit') }}</th>
                <th style="width:10%">{{ __('order.status') }}</th>
                <th style="width:10%">{{ __('common.date') }}</th>
                <th style="width:10%" class="hidden-print">{{ __('order.actions') }}</th>
            </tr>
        </thead>
    </table>
</div>

@push('scripts')
<script>
// =========================================
// DRAFT ORDER LIST SCRIPTS
// =========================================
$(document).ready(function() {
    // =========================================
    // INITIALIZE DATATABLE
    // =========================================
    var draftOrderTable = $('#draftOrderTable').DataTable({
        serverSide: true,
        processing: true,
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'همه']
        ],
        responsive: true,
        autoWidth: false,
        ajax: {
            url: '{{ route("draftOrders.data") }}',
            type: 'GET',
            data: function(d) {
                d.item_name = $('#draft_item_name').val();
                d.customer_name = $('#draft_customer_name').val();
                d.start_date = $('#draft_start_date').val();
                d.end_date = $('#draft_end_date').val();
            },
            error: function(xhr, status, error) {
                console.log('DataTable Error:', error);
                console.log('Response:', xhr.responseText);
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
            { data: 'dord_num_display', name: 'dord_num_display' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'item_name', name: 'item_name' },
            { data: 'amount', name: 'amount' },
            { data: 'unit_name', name: 'unit_name' },
            { data: 'state', name: 'state' },
            { data: 'idate', name: 'idate' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'hidden-print' }
        ],
        language: {
            processing: "در حال پردازش...",
            search: "جستجو:",
        }
    });

    // =========================================
    // FILTER BUTTON
    // =========================================
    $('#draft_btn_filter').on('click', function() {
        draftOrderTable.ajax.reload(null, false);
    });

    // =========================================
    // RESET BUTTON
    // =========================================
    $('#draft_btn_reset').on('click', function() {
        $('#draft_item_name').val('');
        $('#draft_customer_name').val('');
        $('#draft_start_date').val('');
        $('#draft_end_date').val('');
        draftOrderTable.ajax.reload(null, false);
    });

    // =========================================
    // ENTER KEY SEARCH
    // =========================================
    $('#draftFilterWrapper input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#draft_btn_filter').click();
        }
    });

    // =========================================
    // DELETE ORDER
    // =========================================
    $(document).on('click', '.deleteDraftOrder', function() {
        var id = $(this).data('id');
        if (!id) {
            showNotification('شماره سفارش نامعتبر است', 'danger');
            return;
        }
        
        if (!confirm("{{ __('common.delete_confirm') }}")) {
            return;
        }
        
        $.ajax({
            url: '/draftOrders/destroy/' + id,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    draftOrderTable.ajax.reload(null, false);
                    showNotification(response.message, 'success');
                } else {
                    showNotification(response.message || 'حذف ناموفق بود', 'danger');
                }
            },
            error: function() {
                showNotification('حذف ناموفق بود', 'danger');
            }
        });
    });
});
</script>
@endpush