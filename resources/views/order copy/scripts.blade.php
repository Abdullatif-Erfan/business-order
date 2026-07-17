<script>
// =========================================
// ORDER LIST SCRIPTS
// =========================================
$(document).ready(function() {
    // =========================================
    // CURRENT STATE - Default: New (1)
    // =========================================
    var currentState = {{ $state ?? 1 }};

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
                d.ord_num = $('#ord_num').val();
                d.supplier_name = $('#supplier_name').val();
                d.employee_name = $('#employee_name').val();
                d.category_name = $('#category_name').val();
                d.state = currentState;
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
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
    $('#btn-filter').on('click', function() {
        orderTable.ajax.reload(null, false);
    });

    // =========================================
    // RESET BUTTON
    // =========================================
    $('#btn-reset').on('click', function() {
        $('#ord_num').val('');
        $('#supplier_name').val('');
        $('#employee_name').val('');
        $('#category_name').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        orderTable.ajax.reload(null, false);
    });

    // =========================================
    // ENTER KEY SEARCH
    // =========================================
    $('.filter-section input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#btn-filter').click();
        }
    });

    // =========================================
    // DATE PICKER - Click handler
    // =========================================
    $(document).on('click', '.datepicker-icon', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $input = $(this).closest('.input-group').find('input');
        if ($input.length) {
            $input.datepicker('show');
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