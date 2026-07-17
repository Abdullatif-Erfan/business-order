<!-- ========================================= -->
<!-- FILTER SECTION - ORDERS -->
<!-- ========================================= -->
<div class="filter-section no-print" id="FilterWrapper">
    <div class="filter-group">
        <input type="text" id="order_ord_num" placeholder="{{ __('order.order_number') }}" class="form-control">
    </div>
    <div class="filter-group">
        <input type="text" id="order_supplier_name" placeholder="{{ __('order.supplier_name') }}" class="form-control">
    </div>
    <div class="filter-group">
        <input type="text" id="order_employee_name" placeholder="{{ __('order.employee_name') }}" class="form-control">
    </div>
    <div class="filter-group">
        <input type="text" id="order_category_name" placeholder="{{ __('order.category') }}" class="form-control">
    </div>
    <div class="filter-group" style="min-width: 120px;">
        <div class="input-group">
            <input type="text" class="form-control datepicker-input" id="order_start_date" 
            placeholder="{{__('common.start_date')}}">
            <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
        </div>
    </div>
    <div class="filter-group" style="min-width: 120px;">
        <div class="input-group">
            <input type="text" class="form-control datepicker-input" id="order_end_date"  placeholder="{{__('common.end_date')}}">
            <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
        </div>
    </div>
    <div class="filter-actions">
        <button class="btn btn-search" id="order_btn_filter"><i class="fas fa-search"></i></button>
        <button class="btn btn-reset" id="order_btn_reset" title="{{ __('common.reset') }}"><i class="fas fa-undo"></i></button>
    </div>
</div>

<!-- ========================================= -->
<!-- TABLE - ORDERS -->
<!-- ========================================= -->
<div class="table-responsive" id="print_area" style="padding:5px;">
    <span class="pull-left visible-print"> {{__('common.print_date')}} : {{ $todaysDate }}</span>
    <table id="orderTable" class="display responsive nowrap table table-bordered" width="100%">
        <thead>
            <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                <td colspan="12">
                    <img src="{{ asset($orgbios[0]->header ?? '') }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                </td>
            </tr>
            <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                <td colspan="12">
                    <center> {{__('order.list_title')}} </center>
                </td>
            </tr>
            <tr>
                <th style="width:5%">{{ __('common.number') }}</th>
                <th style="width:10%">{{ __('order.order_number') }}</th>
                <th style="width:15%">{{ __('order.item') }}</th>
                <th style="width:15%">{{ __('order.supplier_name') }}</th>
                <th style="width:15%">{{ __('order.employee_name') }}</th>
                <th style="width:10%">{{ __('order.category') }}</th>
                <th style="width:10%">{{ __('order.amount') }}</th>
                <th style="width:10%">{{ __('order.unit') }}</th>
                <th style="width:10%">{{ __('order.status') }}</th>
                <th style="width:10%">{{ __('common.date') }}</th>
                <th style="width:10%" class="hidden-print">{{ __('order.done_by') }}</th>
                <th style="width:10%" class="hidden-print">{{ __('order.actions') }}</th>
            </tr>
        </thead>
    </table>
</div>

@push('scripts')
<script>
// =========================================
// ORDER LIST SCRIPTS
// =========================================
$(document).ready(function() {
    // =========================================
    // CURRENT STATE - Default: New (1)
    // =========================================
    var currentState = 1;

    // =========================================
    // INITIALIZE DATATABLE
    // =========================================
    var orderTable = $('#orderTable').DataTable({
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
            url: '{{ route("orders.data") }}',
            type: 'GET',
            data: function(d) {
                d.ord_num = $('#order_ord_num').val();
                d.supplier_name = $('#order_supplier_name').val();
                d.employee_name = $('#order_employee_name').val();
                d.category_name = $('#order_category_name').val();
                d.state = currentState;
                d.start_date = $('#order_start_date').val();
                d.end_date = $('#order_end_date').val();
            },
            error: function(xhr, status, error) {
                console.log('DataTable Error:', error);
                console.log('Response:', xhr.responseText);
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
            { data: 'ord_num', name: 'ord_num' },
            { data: 'item_name', name: 'item_name' },
            { data: 'supplier_name', name: 'supplier_name' },
            { data: 'employee_name', name: 'employee_name' },
            { data: 'category_name', name: 'category_name' },
            { data: 'amount', name: 'amount' },
            { data: 'unit_name', name: 'unit_name' },
            { data: 'state', name: 'state' },
            { data: 'idate', name: 'idate' },
            { data: 'done_by', name: 'done_by', orderable: false, searchable: false, className: 'hidden-print' },
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
    $('#order_btn_filter').on('click', function() {
        orderTable.ajax.reload(null, false);
    });

    // =========================================
    // RESET BUTTON
    // =========================================
    $('#order_btn_reset').on('click', function() {
        $('#order_ord_num').val('');
        $('#order_supplier_name').val('');
        $('#order_employee_name').val('');
        $('#order_category_name').val('');
        $('#order_start_date').val('');
        $('#order_end_date').val('');
        orderTable.ajax.reload(null, false);
    });

    // =========================================
    // ENTER KEY SEARCH
    // =========================================
    $('#orderFilterWrapper input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#order_btn_filter').click();
        }
    });

    // =========================================
    // VIEW ORDER
    // =========================================
    $(document).on('click', '.viewOrder', function() {
        var ord_num = $(this).data('id');
        $('#viewOrderModal').modal('show');
        $('#modalLoader').show();
        $('#ViewFormWrapper').html('');
        
        $.ajax({
            url: '/orders/show/' + ord_num,
            type: 'GET',
            success: function(result) {
                $('#ViewFormWrapper').html(result);
                $('#modalLoader').hide();
            },
            error: function() {
                $('#modalLoader').hide();
                showNotification('اطلاعات یافت نشد', 'danger');
            }
        });
    });

    // =========================================
    // DELETE ORDER
    // =========================================
    $(document).on('click', '.deleteOrder', function() {
        var ord_num = $(this).data('id');
        if (!ord_num) {
            showNotification('شماره سفارش نامعتبر است', 'danger');
            return;
        }
        
        if (!confirm("{{ __('common.delete_confirm') }}")) {
            return;
        }
        
        $.ajax({
            url: '/orders/destroy/' + ord_num,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    orderTable.ajax.reload(null, false);
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

    // =========================================
    // STATE MODAL - Open
    // =========================================
    $(document).on('click', '.newState', function() {
        var ord_num = $(this).data('ord-num');
        var state = $(this).data('state');
        
        $('#state_ord_num').val(ord_num);
        $('#state_status').val(state);
        
        $('#stateOrderModal').modal('show');
    });

    // =========================================
    // STATE MODAL - Save
    // =========================================
    $(document).on('click', '#saveStateBtn', function() {
        var ord_num = $('#state_ord_num').val();
        var state = $('#state_status').val();
        
        if (!ord_num || state === '') {
            showNotification('داده‌های نامعتبر', 'danger');
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> در حال ذخیره...');
        
        $.ajax({
            url: '/orders/update-status/' + ord_num,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                state: state,
            },
            success: function(response) {
                $btn.prop('disabled', false).html('{{ __("common.save") }}');
                if (response.status === 'success') {
                    $('#stateOrderModal').modal('hide');
                    showNotification(response.message, 'success');
                    orderTable.ajax.reload(null, false);
                } else {
                    showNotification(response.message || 'خطا رخ داده است', 'danger');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('{{ __("common.save") }}');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    $.each(errors, function(key, messages) {
                        errorMessages.push(messages[0]);
                    });
                    showNotification(errorMessages.join('<br>'), 'danger');
                } else {
                    showNotification('خطا رخ داده است', 'danger');
                }
            }
        });
    });

    // =========================================
    // STATE MODAL - Hidden
    // =========================================
    $('#stateOrderModal').on('hidden.bs.modal', function() {
        $('#state_status').val(0);
        $('#state_ord_num').val('');
        $('.state-error').remove();
    });

    // =========================================
    // NOTIFICATION FUNCTION
    // =========================================
    function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
        var content = {
            message: '<span style="font-size:16px;">' + message + '</span>',
            title: '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{ __("settings.message") }}</span>',
            icon: style === 'withicon' ? 'fa fa-bell' : 'none',
            url: '#',
            target: '_blank'
        };

        $.notify(content, {
            type: type,
            placement: {
                from: from,
                align: align
            },
            time: 500
        });
    }

    // =========================================
    // EDIT ORDER - Redirect
    // =========================================
    $(document).on('click', '.editOrder', function() {
        var ord_num = $(this).data('id');
        window.location.href = '/orders/edit/' + ord_num;
    });
});
</script>
@endpush