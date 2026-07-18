<script>
// =========================================
// ORDER LIST SCRIPTS
// =========================================
$(document).ready(function() {
    // =========================================
    // CURRENT STATE - Default: Draft (0)
    // =========================================
    // var currentState = 1; //1:new, 2:in progress, 3:completed

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
                d.item_name = $('#item_name').val();
                d.customer_name = $('#customer_name').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.state = $('#state').val();
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
            { data: 'item_name'    , name: 'item_name' },
            { data: 'amount', name: 'amount' },
            { data: 'unit_name', name: 'unit_name' },
            { data: 'state', name: 'state' },
            { data: 'idate', name: 'idate' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'hidden-print'  }
        ],
        language: {
            processing: "در حال پردازش...",
            search: "جستجو:",
            // lengthMenu: "نمایش _MENU_ رکورد در هر صفحه",
            // info: "نمایش _START_ تا _END_ از _TOTAL_ رکورد",
            // infoEmpty: "هیچ رکوردی یافت نشد",
            // infoFiltered: "(فیلتر شده از _MAX_ رکورد کل)",
            // loadingRecords: "در حال بارگذاری...",
            // zeroRecords: "هیچ رکوردی یافت نشد",
            // emptyTable: "هیچ داده‌ای در جدول وجود ندارد",
            // paginate: {
            //     first: "اول",
            //     previous: "قبلی",
            //     next: "بعدی",
            //     last: "آخر"
            // }
        }
    });

    // =========================================
    // TAB SWITCHING
    // =========================================
    // $('.tab-link').on('click', function(e) {
    //     e.preventDefault();
        
    //     $('.tab-link').removeClass('active');
    //     $(this).addClass('active');
        
    //     currentState = $(this).data('tab');
    //     //  console.log('currentState', currentState);
    //     // Reload table with new state
    //     draftOrderTable.ajax.reload(null, false);
    // });

    // =========================================
    // FILTER BUTTON
    // =========================================
    $('#btn-filter').on('click', function() {
        draftOrderTable.ajax.reload(null, false);
    });

    // =========================================
    // RESET BUTTON
    // =========================================
    $('#btn-reset').on('click', function() {
        $('#dord_num').val('');
        $('#supplier_name').val('');
        $('#employee_name').val('');
        $('#category_name').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        draftOrderTable.ajax.reload(null, false);
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
        var dord_num = $(this).data('id');
        $('#viewOrderModal').modal('show');
        $('#modalLoader').show();
        $('#ViewFormWrapper').html('');
        
        $.ajax({
            url: '/draftOrders/show/' + dord_num,
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

    // =========================================
    // STATE MODAL - Open
    // =========================================
    $(document).on('click', '.newState', function() {
        var dord_num = $(this).data('ord-num');
        var state = $(this).data('state');
        
        $('#state_dord_num').val(dord_num);
        $('#state_status').val(state);
        
        if (state == 3) {
            $('#done_by_group').show();
        } else {
            $('#done_by_group').hide();
        }
        
        $('#stateOrderModal').modal('show');
    });

    // =========================================
    // STATE MODAL - Status Change
    // =========================================
    $(document).on('change', '#state_status', function() {
        if ($(this).val() == 3) {
            $('#done_by_group').show();
        } else {
            $('#done_by_group').hide();
        }
    });

    // =========================================
    // STATE MODAL - Save
    // =========================================
    $(document).on('click', '#saveStateBtn', function() {
        var dord_num = $('#state_dord_num').val();
        var state = $('#state_status').val();
        
        if (!dord_num || state === '') {
            showNotification('داده‌های نامعتبر', 'danger');
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> در حال ذخیره...');
        
        $.ajax({
            url: '/draftOrders/update-status/' + dord_num,
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
                    draftOrderTable.ajax.reload(null, false);
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
        $('#done_by_group').hide();
        $('#state_dord_num').val('');
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
        var dord_num = $(this).data('id');
        window.location.href = '/draftOrders/edit/' + dord_num;
    });
});
</script>