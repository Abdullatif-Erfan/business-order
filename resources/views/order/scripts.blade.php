<script>
// =========================================
// ORDER LIST SCRIPTS
// =========================================
$(document).ready(function() {
    // =========================================
    // CURRENT STATE - Default: New (1)
    // =========================================
    var currentState = {{ $state ?? 1 }};
    var csrfToken = '{{ csrf_token() }}';

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
            { data: 'supplier_name', name: 'supplier_name' },
            { data: 'category_name', name: 'category_name' },
            { data: 'state', name: 'state' },
            { data: 'idate', name: 'idate' },
            { data: 'user_name', name: 'user_name', orderable: false, searchable: false },
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
        var order_id = $(this).data('id');
        $('#viewOrderModal').modal('show');
        $('#modalLoader').show();
        $('#ViewFormWrapper').html('');
        
        $.ajax({
            url: '/orders/show/' + order_id,
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
        var order_id = $(this).data('id');
        if (!order_id) {
            showNotification('شماره سفارش نامعتبر است', 'danger');
            return;
        }
        
        if (!confirm("{{ __('common.delete_confirm') }}")) {
            return;
        }
        
        $.ajax({
            url: '/orders/destroy/' + order_id,
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

    // ===================================================
    // EDIT ORDER - Load Modal Content
    // ===================================================
    $(document).on('click', '.editOrder', function() {
        var order_id = $(this).data('id');
        $('#editOrderModal').modal('show');
        $('#modalLoader').show();
        $('#EditFormWrapper').html('');
        
        $.ajax({
            url: '/orders/edit/' + order_id,
            type: 'GET',
            success: function(result) {
                $('#EditFormWrapper').html(result);
                $('#modalLoader').hide();

                // Re-initialize any plugins after content loads
                if ($.fn.select2) {
                    $('.select2').select2();
                }
                
                // Note: Don't call initEditForm() here anymore
                // Event listeners are already attached globally
            },
            error: function() {
                $('#modalLoader').hide();
                showNotification('اطلاعات یافت نشد', 'danger');
            }
        });
    });

    // =========================================
    // GLOBAL EVENT LISTENERS FOR EDIT FORM
    // (These are attached once and work for dynamically loaded content)
    // =========================================
    
    // VALIDATE AMOUNT
    $(document).on('change', '.item-amount', function() {
        var value = parseFloat($(this).val()) || 0;
        if (value < 0) {
            $(this).val(0);
            showNotification('{{ __("common.amount_positive") }}', 'warning');
        }
        $(this).closest('tr').find('.save-status').hide();
        updateCategoryTotals();
    });

    // SAVE INDIVIDUAL ITEM - SINGLE EVENT LISTENER
    $(document).off('click', '.save-item-btn').on('click', '.save-item-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Save button clicked');
        
        var $btn = $(this);
        
        // Check if already processing
        if ($btn.prop('disabled')) {
            console.log('Button already disabled - ignoring click');
            return;
        }
        
        var itemId = $btn.data('item-id');
        var row = $btn.closest('tr');
        var amount = row.find('.item-amount').val();
        var statusSpan = row.find('.save-status');
        
        console.log('Item ID:', itemId, 'Amount:', amount);
        
        if (!amount || parseFloat(amount) < 0) {
            showNotification('{{ __("wh.enter_valid_amount") }}', 'danger');
            return;
        }
        
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        var updateUrl = '{{ route("orders.update", ["id" => "ITEM_ID"]) }}'.replace('ITEM_ID', itemId);
        
        $.ajax({
            url: updateUrl,
            type: 'PUT',
            data: {
                _token: csrfToken,
                amount: amount,
                item_id: itemId
            },
            success: function(response) {
                console.log('Success:', response);
                $btn.prop('disabled', false).html(originalText);
                
                if (response.status === 'success') {
                    statusSpan.show().html('<i class="fas fa-check-circle text-success"></i>');
                    setTimeout(function() {
                        statusSpan.fadeOut();
                    }, 3000);
                    
                    showNotification(response.message || '{{ __("common.updated_successfully") }}', 'success');
                } else {
                    showNotification(response.message || '{{ __("common.error_occurred") }}', 'danger');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                $btn.prop('disabled', false).html(originalText);
                showNotification('{{ __("common.error_occurred") }}', 'danger');
            }
        });
    });

    // SAVE ALL ITEMS
    $(document).off('click', '#saveAllItemsBtn').on('click', '#saveAllItemsBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Save All clicked');
        
        var $btn = $(this);
        
        // Check if already processing
        if ($btn.prop('disabled')) {
            console.log('Button already disabled - ignoring click');
            return;
        }
        
        var items = [];
        var isValid = true;
        var errorMessages = [];
        
        $('.item-row').each(function() {
            var row = $(this);
            var itemId = row.data('item-id');
            var amount = row.find('.item-amount').val();
            
            if (!amount || parseFloat(amount) < 0) {
                isValid = false;
                errorMessages.push('{{ __("wh.enter_valid_amount") }}');
                row.find('.item-amount').css('border-color', 'red');
            } else {
                row.find('.item-amount').css('border-color', '');
                items.push({
                    id: itemId,
                    amount: parseFloat(amount)
                });
            }
        });
        
        if (!isValid || items.length === 0) {
            showNotification(errorMessages.join('<br>'), 'danger');
            return;
        }
        
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        var orderId = $('#orderId').val() || '{{ $order->id ?? 0 }}';
        
        $.ajax({
            url: '{{ route("orders.updateAll") }}',
            type: 'PUT',
            data: {
                _token: csrfToken,
                items: items,
                order_id: orderId
            },
            success: function(response) {
                console.log('Success:', response);
                $btn.prop('disabled', false).html(originalText);
                
                if (response.status === 'success') {
                    $('.item-row').each(function() {
                        $(this).find('.save-status').show().html('<i class="fas fa-check-circle text-success"></i>');
                        setTimeout(function() {
                            $(this).find('.save-status').fadeOut();
                        }.bind(this), 3000);
                    });
                    
                    showNotification(response.message || '{{ __("common.updated_successfully") }}', 'success');
                    updateCategoryTotals();
                } else {
                    showNotification(response.message || '{{ __("common.error_occurred") }}', 'danger');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                $btn.prop('disabled', false).html(originalText);
                showNotification('{{ __("common.error_occurred") }}', 'danger');
            }
        });
    });

    // ORDER STATUS UPDATE
    $(document).on('change', '#orderStatus', function() {
        var status = $(this).val();
        var times = '{{ $order->times ?? 0 }}';
        
        console.log('Status changed to:', status);
        
        $.ajax({
            url: '{{ route("orders.updateStatus", ["times" => "TIMES_VALUE"]) }}'.replace('TIMES_VALUE', times),
            type: 'POST',
            data: {
                _token: csrfToken,
                order_id: '{{ $order->id ?? 0 }}',
                status: status
            },
            success: function(response) {
                if (response.status === 'success') {
                    showNotification(response.message || '{{ __("common.updated_successfully") }}', 'success');
                } else {
                    showNotification(response.message || '{{ __("common.error_occurred") }}', 'danger');
                }
            },
            error: function() {
                showNotification('{{ __("common.error_occurred") }}', 'danger');
            }
        });
    });

    // =========================================
    // UPDATE CATEGORY TOTALS (Global function)
    // =========================================
    function updateCategoryTotals() {
        var categoryTotals = {};
        
        $('.item-row').each(function() {
            var row = $(this);
            var categoryHeader = row.prevAll('.category-header').first();
            var categoryName = categoryHeader.find('strong').text().trim();
            var amount = parseFloat(row.find('.item-amount').val()) || 0;
            
            if (!categoryTotals[categoryName]) {
                categoryTotals[categoryName] = 0;
            }
            categoryTotals[categoryName] += amount;
        });
        
        $('.category-header').each(function() {
            var headerText = $(this).find('strong').text().trim();
            var total = categoryTotals[headerText] || 0;
            $(this).find('.category-total').text('{{ __("common.total") }}: ' + total.toFixed(2));
        });
    }

    console.log('All scripts loaded successfully');
});
</script>