<script>
// =========================================
// ORDER LIST SCRIPTS
// =========================================
$(document).ready(function() {

    // =========================================
    // CSRF TOKEN
    // =========================================
    var csrfToken = '{{ csrf_token() }}';


    // =========================================
    // NOTIFICATION FUNCTION
    // =========================================
    function showNotification(
        message,
        type = 'info',
        from = 'top',
        align = 'center',
        style = 'withicon'
    ) {

        var content = {
            message:
                '<span style="font-size:16px;">' +
                message +
                '</span>',

            title:
                '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">' +
                '{{ __("settings.message") }}' +
                '</span>',

            icon:
                style === 'withicon'
                    ? 'fa fa-bell'
                    : 'none',

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

                d.ord_num =
                    $('#ord_num').val();

                d.supplier_name =
                    $('#supplier_name').val();

                d.category_name =
                    $('#category_name').val();

                d.state =
                    $('#state').val();

                d.start_date =
                    $('#start_date').val();

                d.end_date =
                    $('#end_date').val();
            },

            error: function(xhr, status, error) {

                console.log(
                    'DataTable Error:',
                    error
                );

                console.log(
                    'Response:',
                    xhr.responseText
                );
            }
        },

        columns: [

            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },

            {
                data: 'ord_num',
                name: 'ord_num'
            },

            {
                data: 'supplier_name',
                name: 'supplier_name'
            },

            {
                data: 'category_name',
                name: 'category_name'
            },

            {
                data: 'state',
                name: 'state'
            },

            {
                data: 'idate',
                name: 'idate'
            },

            {
                data: 'user_name',
                name: 'user_name',
                orderable: false,
                searchable: false
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'hidden-print'
            }
        ],

        language: {

            processing:
                "در حال پردازش...",

            search:
                "جستجو:"
        }
    });


    // =========================================
    // FILTER BUTTON
    // =========================================
    $('#btn-filter').off('click').on('click', function() {

        orderTable.ajax.reload(
            null,
            false
        );

    });


    // =========================================
    // RESET BUTTON
    // =========================================
    $('#btn-reset').off('click').on('click', function() {

        $('#ord_num').val('');

        $('#supplier_name').val('');

        $('#employee_name').val('');

        $('#category_name').val('');

        $('#start_date').val('');

        $('#end_date').val('');

        orderTable.ajax.reload(
            null,
            false
        );

    });


    // =========================================
    // ENTER KEY SEARCH
    // =========================================
    $('.filter-section input')
        .off('keypress.orderFilter')
        .on('keypress.orderFilter', function(e) {

            if (e.which === 13) {

                e.preventDefault();

                $('#btn-filter').click();
            }

        });


    // =========================================
    // DATE PICKER ICON
    // =========================================
    $(document)
        .off(
            'click.orderDatepicker',
            '.datepicker-icon'
        )
        .on(
            'click.orderDatepicker',
            '.datepicker-icon',
            function(e) {

                e.preventDefault();

                e.stopPropagation();

                var $input =
                    $(this)
                        .closest('.input-group')
                        .find('input');

                if ($input.length) {

                    $input.datepicker('show');
                }

            }
        );


    // =========================================
    // VIEW ORDER
    // =========================================
    $(document)
        .off('click.viewOrder', '.viewOrder')
        .on('click.viewOrder', '.viewOrder', function(e) {

            e.preventDefault();

            var orderId =
                $(this).data('id');


            $('#viewOrderModal')
                .modal('show');


            $('#modalLoader')
                .show();


            $('#ViewFormWrapper')
                .empty();


            $.ajax({

                url:
                    '/orders/show/' +
                    orderId,

                type:
                    'GET',


                success: function(result) {

                    $('#ViewFormWrapper')
                        .html(result);


                    $('#modalLoader')
                        .hide();

                },


                error: function() {

                    $('#modalLoader')
                        .hide();


                    showNotification(
                        'اطلاعات یافت نشد',
                        'danger'
                    );
                }

            });

        });


    // =========================================
    // DELETE ORDER
    // =========================================
    $(document)
        .off('click.deleteOrder', '.deleteOrder')
        .on(
            'click.deleteOrder',
            '.deleteOrder',
            function(e) {

                e.preventDefault();

                var orderId =
                    $(this).data('id');


                if (!orderId) {

                    showNotification(
                        'شماره سفارش نامعتبر است',
                        'danger'
                    );

                    return;
                }


                if (
                    !confirm(
                        "{{ __('common.delete_confirm') }}"
                    )
                ) {
                    return;
                }


                $.ajax({

                    url:
                        '/orders/destroy/' +
                        orderId,

                    type:
                        'DELETE',

                    data: {
                        _token:
                            csrfToken
                    },


                    success: function(response) {

                        if (
                            response.status ===
                            'success'
                        ) {

                            orderTable
                                .ajax
                                .reload(
                                    null,
                                    false
                                );


                            showNotification(
                                response.message,
                                'success'
                            );

                        } else {

                            showNotification(
                                response.message ||
                                'حذف ناموفق بود',
                                'danger'
                            );
                        }
                    },


                    error: function() {

                        showNotification(
                            'حذف ناموفق بود',
                            'danger'
                        );
                    }

                });

            }
        );


    // =========================================
    // STATE MODAL SAVE
    // =========================================
    $(document)
        .off('click.saveState', '#saveStateBtn')
        .on(
            'click.saveState',
            '#saveStateBtn',
            function(e) {

                e.preventDefault();

                var ordNum =
                    $('#state_ord_num')
                        .val();

                var state =
                    $('#state_status')
                        .val();


                if (
                    !ordNum ||
                    state === ''
                ) {

                    showNotification(
                        'داده‌های نامعتبر',
                        'danger'
                    );

                    return;
                }


                var $btn =
                    $(this);


                var originalText =
                    $btn.html();


                if (
                    $btn.prop('disabled')
                ) {
                    return;
                }


                $btn
                    .prop(
                        'disabled',
                        true
                    )
                    .html(
                        '<i class="fa fa-spinner fa-spin"></i> در حال ذخیره...'
                    );


                $.ajax({

                    url:
                        '/orders/update-status/' +
                        ordNum,

                    type:
                        'POST',

                    data: {

                        _token:
                            csrfToken,

                        state:
                            state
                    },


                    success: function(response) {

                        $btn
                            .prop(
                                'disabled',
                                false
                            )
                            .html(
                                originalText
                            );


                        if (
                            response.status ===
                            'success'
                        ) {

                            $('#stateOrderModal')
                                .modal('hide');


                            showNotification(
                                response.message,
                                'success'
                            );


                            orderTable
                                .ajax
                                .reload(
                                    null,
                                    false
                                );

                        } else {

                            showNotification(
                                response.message ||
                                'خطا رخ داده است',
                                'danger'
                            );
                        }
                    },


                    error: function(xhr) {

                        $btn
                            .prop(
                                'disabled',
                                false
                            )
                            .html(
                                originalText
                            );


                        if (
                            xhr.status === 422 &&
                            xhr.responseJSON &&
                            xhr.responseJSON.errors
                        ) {

                            var errors =
                                xhr.responseJSON.errors;


                            var errorMessages =
                                [];


                            $.each(
                                errors,
                                function(
                                    key,
                                    messages
                                ) {

                                    errorMessages.push(
                                        messages[0]
                                    );

                                }
                            );


                            showNotification(
                                errorMessages.join('<br>'),
                                'danger'
                            );

                        } else {

                            showNotification(
                                'خطا رخ داده است',
                                'danger'
                            );
                        }
                    }

                });

            }
        );


    // =========================================
    // STATE MODAL CLEANUP
    // =========================================
    $('#stateOrderModal')
        .off('hidden.bs.modal')
        .on(
            'hidden.bs.modal',
            function() {

                $('#state_status')
                    .val(0);

                $('#state_ord_num')
                    .val('');

                $('.state-error')
                    .remove();

            }
        );


    // ===================================================
    // EDIT ORDER - OPEN MODAL
    // ===================================================
    $(document)
        .off('click.editOrder', '.editOrder')
        .on(
            'click.editOrder',
            '.editOrder',
            function(e) {

                e.preventDefault();


                var orderId =
                    $(this).data('id');


                $('#editOrderModal')
                    .modal('show');


                $('#modalLoader')
                    .show();


                $('#EditFormWrapper')
                    .empty();


                $.ajax({

                    url:
                        '/orders/edit/' +
                        orderId,

                    type:
                        'GET',


                    success: function(result) {

                        $('#EditFormWrapper')
                            .html(result);


                        $('#modalLoader')
                            .hide();


                        // Initialize Edit Select2
                        initializeEditSelect2();

                    },


                    error: function() {

                        $('#modalLoader')
                            .hide();


                        showNotification(
                            'اطلاعات یافت نشد',
                            'danger'
                        );
                    }

                });

            }
        );


    // ===================================================
    // INITIALIZE EDIT SELECT2
    // ===================================================
    function initializeEditSelect2() {

        if (!$.fn.select2) {
            return;
        }


        $('#EditFormWrapper')
            .find('.select2')
            .each(function() {

                var $select =
                    $(this);


                // Destroy previous Select2 instance
                if (
                    $select.hasClass(
                        'select2-hidden-accessible'
                    )
                ) {

                    $select.select2(
                        'destroy'
                    );
                }


                $select.select2({
                    width: '100%',
                    dropdownParent:
                        $('#editOrderModal')
                });

            });

    }


    // ===================================================
    // MOVE ORDER - OPEN MODAL
    // ===================================================
    $(document)
        .off(
            'click.openMoveModal',
            '.moveOrderItem'
        )
        .on(
            'click.openMoveModal',
            '.moveOrderItem',
            function(e) {

                e.preventDefault();

                e.stopPropagation();


                var orderId =
                    $(this).data('id');


                // Clear previous content
                $('#MoveFormWrapper')
                    .empty();


                // Show modal
                $('#moveOrderModal')
                    .modal('show');


                // Show loader
                $('#moveModalLoader')
                    .show();


                $.ajax({

                    url:
                        '/orders/getOrderItemForMove/' +
                        orderId,

                    type:
                        'GET',


                    success: function(result) {


                        // Insert new form
                        $('#MoveFormWrapper')
                            .html(result);


                        // Hide loader
                        $('#moveModalLoader')
                            .hide();


                        // Initialize Select2
                        initializeMoveSelect2();


                        // Initialize buttons
                        $('#MoveFormWrapper')
                            .find('.item-row')
                            .each(function() {

                                toggleMoveSaveButton(
                                    $(this)
                                );

                            });

                    },


                    error: function(xhr) {


                        $('#moveModalLoader')
                            .hide();


                        $('#MoveFormWrapper')
                            .empty();


                        showNotification(
                            'اطلاعات یافت نشد',
                            'danger'
                        );


                        console.log(
                            'Move form load error:',
                            xhr
                        );

                    }

                });

            }
        );


    // ===================================================
    // INITIALIZE MOVE SELECT2
    // ===================================================
    function initializeMoveSelect2() {

        if (!$.fn.select2) {

            console.log(
                'Select2 is not loaded'
            );

            return;
        }


        var $modal =
            $('#moveOrderModal');


        $('#MoveFormWrapper')
            .find('.supplier-select')
            .each(function() {

                var $select =
                    $(this);


                // -------------------------------------------
                // Destroy existing Select2 safely
                // -------------------------------------------
                if (
                    $select.hasClass(
                        'select2-hidden-accessible'
                    )
                ) {

                    $select.select2(
                        'destroy'
                    );
                }


                // -------------------------------------------
                // Initialize Select2
                // -------------------------------------------
                $select.select2({

                    width:
                        '100%',

                    placeholder:
                        "{{ __('order.supplier_selection') }}",

                    allowClear:
                        true,

                    dropdownParent:
                        $modal

                });

            });

    }


    // ===================================================
    // MOVE SELECT2 CHANGE
    // ===================================================
    //
    // Select2 still triggers the normal change event.
    // Therefore one delegated change handler is enough.
    //
    $(document)
        .off(
            'change.moveSupplier',
            '.supplier-select'
        )
        .on(
            'change.moveSupplier',
            '.supplier-select',
            function() {

                var $row =
                    $(this)
                        .closest('tr');


                toggleMoveSaveButton(
                    $row
                );

            }
        );


    // ===================================================
    // MOVE AMOUNT VALIDATION
    // ===================================================
    $(document)
        .off(
            'input.moveAmount change.moveAmount',
            '.move-amount'
        )
        .on(
            'input.moveAmount change.moveAmount',
            '.move-amount',
            function() {

                var $input =
                    $(this);


                var value =
                    parseFloat(
                        $input.val()
                    ) || 0;


                var maxAmount =
                    parseFloat(
                        $input.attr('max')
                    ) || 0;


                // Prevent negative value
                if (value < 0) {

                    value = 0;

                    $input.val(0);


                    showNotification(
                        '{{ __("order.amount_cannot_be_negative") }}',
                        'warning'
                    );
                }


                // Prevent amount greater than available
                if (
                    maxAmount > 0 &&
                    value > maxAmount
                ) {

                    value =
                        maxAmount;


                    $input.val(
                        maxAmount
                    );


                    showNotification(
                        '{{ __("order.amount_cannot_exceed") }}: ' +
                        maxAmount,
                        'warning'
                    );
                }


                toggleMoveSaveButton(
                    $input.closest('tr')
                );

            }
        );


    // ===================================================
    // ENABLE / DISABLE MOVE SAVE BUTTON
    // ===================================================
    function toggleMoveSaveButton($row) {

        if (!$row || !$row.length) {
            return;
        }


        var amount =
            parseFloat(
                $row
                    .find('.move-amount')
                    .val()
            ) || 0;


        var supplierId =
            $row
                .find('.supplier-select')
                .val();


        var $btn =
            $row
                .find('.save-move-btn');


        // Do not change state while request is processing
        if (
            $btn.data('processing') === true
        ) {
            return;
        }


        if (
            amount > 0 &&
            supplierId
        ) {

            $btn
                .prop(
                    'disabled',
                    false
                )
                .css({
                    opacity: '1',
                    cursor: 'pointer'
                });

        } else {

            $btn
                .prop(
                    'disabled',
                    true
                )
                .css({
                    opacity: '0.5',
                    cursor: 'not-allowed'
                });
        }

    }


    // ===================================================
    // MOVE ORDER ITEM
    // ===================================================
    //
    // IMPORTANT:
    //
    // This is registered ONCE only.
    //
    // Namespace + off() prevents duplicate handlers.
    //
    $(document)
        .off(
            'click.moveItem',
            '.save-move-btn'
        )
        .on(
            'click.moveItem',
            '.save-move-btn',
            function(e) {

                e.preventDefault();

                e.stopPropagation();


                var $btn =
                    $(this);


                // -------------------------------------------
                // Prevent duplicate request
                // -------------------------------------------
                if (
                    $btn.data(
                        'processing'
                    ) === true
                ) {

                    console.log(
                        'Move request already processing'
                    );

                    return;
                }


                // Also check disabled state
                if (
                    $btn.prop(
                        'disabled'
                    )
                ) {
                    return;
                }


                var $row =
                    $btn.closest(
                        'tr'
                    );


                var itemId =
                    $row.data(
                        'item-id'
                    );


                var amount =
                    parseFloat(
                        $row
                            .find('.move-amount')
                            .val()
                    ) || 0;


                var maxAmount =
                    parseFloat(
                        $row
                            .find('.move-amount')
                            .attr('max')
                    ) || 0;


                var supplierId =
                    $row
                        .find('.supplier-select')
                        .val();


                var orderId =
                    $('#MoveFormWrapper')
                        .find(
                            'input[name="order_id"]'
                        )
                        .val();


                // -------------------------------------------
                // Validate order
                // -------------------------------------------
                if (!orderId) {

                    showNotification(
                        '{{ __("common.not_found") }}',
                        'danger'
                    );

                    return;
                }


                // -------------------------------------------
                // Validate item
                // -------------------------------------------
                if (!itemId) {

                    showNotification(
                        '{{ __("common.not_found") }}',
                        'danger'
                    );

                    return;
                }


                // -------------------------------------------
                // Validate amount
                // -------------------------------------------
                if (amount <= 0) {

                    showNotification(
                        '{{ __("order.enter_valid_amount") }}',
                        'danger'
                    );

                    return;
                }


                // -------------------------------------------
                // Validate maximum amount
                // -------------------------------------------
                if (
                    maxAmount > 0 &&
                    amount > maxAmount
                ) {

                    showNotification(
                        '{{ __("order.amount_cannot_exceed") }}: ' +
                        maxAmount,
                        'danger'
                    );

                    return;
                }


                // -------------------------------------------
                // Validate supplier
                // -------------------------------------------
                if (!supplierId) {

                    showNotification(
                        '{{ __("order.select_supplier") }}',
                        'danger'
                    );

                    return;
                }


                // -------------------------------------------
                // Mark request as processing
                // -------------------------------------------
                $btn.data(
                    'processing',
                    true
                );


                var originalText =
                    $btn.html();


                // -------------------------------------------
                // Disable button immediately
                // -------------------------------------------
                $btn
                    .prop(
                        'disabled',
                        true
                    )
                    .html(
                        '<i class="fas fa-spinner fa-spin"></i>'
                    );


                // ===========================================
                // SEND AJAX REQUEST
                // ===========================================
                $.ajax({

                    url:
                        '{{ route("orders.moveItem") }}',

                    type:
                        'POST',

                    data: {

                        _token:
                            csrfToken,

                        order_id:
                            orderId,

                        item_id:
                            itemId,

                        move_amount:
                            amount,

                        to_supplier_id:
                            supplierId
                    },


                    // =======================================
                    // SUCCESS
                    // =======================================
                    success: function(response) {


                        console.log(
                            'Move response:',
                            response
                        );


                        if (
                            response.status ===
                            'success'
                        ) {


                            // ---------------------------------
                            // Show success message
                            // ---------------------------------
                            showNotification(
                                response.message ||
                                '{{ __("order.item_moved_successfully") }}',
                                'success'
                            );


                            // ---------------------------------
                            // Close modal
                            // ---------------------------------
                            $('#moveOrderModal')
                                .modal('hide');


                            // ---------------------------------
                            // Reload DataTable
                            // ---------------------------------
                            orderTable
                                .ajax
                                .reload(
                                    null,
                                    false
                                );


                            // ---------------------------------
                            // Clear processing state
                            // ---------------------------------
                            $btn.data(
                                'processing',
                                false
                            );

                        } else {


                            // ---------------------------------
                            // Backend returned an error
                            // ---------------------------------
                            showNotification(
                                response.message ||
                                '{{ __("common.error_occurred") }}',
                                'danger'
                            );


                            // Reset button
                            $btn
                                .data(
                                    'processing',
                                    false
                                )
                                .prop(
                                    'disabled',
                                    false
                                )
                                .html(
                                    originalText
                                );
                        }

                    },


                    // =======================================
                    // ERROR
                    // =======================================
                    error: function(xhr) {


                        console.log(
                            'Move error:',
                            xhr
                        );


                        var errorMessage =
                            '{{ __("common.error_occurred") }}';


                        // Laravel validation errors
                        if (
                            xhr.status === 422 &&
                            xhr.responseJSON &&
                            xhr.responseJSON.errors
                        ) {

                            var messages =
                                [];


                            $.each(
                                xhr.responseJSON.errors,
                                function(
                                    key,
                                    errors
                                ) {

                                    messages.push(
                                        errors[0]
                                    );

                                }
                            );


                            errorMessage =
                                messages.join(
                                    '<br>'
                                );

                        } else if (

                            xhr.responseJSON &&
                            xhr.responseJSON.message

                        ) {

                            errorMessage =
                                xhr.responseJSON.message;
                        }


                        showNotification(
                            errorMessage,
                            'danger'
                        );


                        // ---------------------------------
                        // Reset button
                        // ---------------------------------
                        $btn
                            .data(
                                'processing',
                                false
                            )
                            .prop(
                                'disabled',
                                false
                            )
                            .html(
                                originalText
                            );

                    }

                });

            }
        );


    // ===================================================
    // MOVE MODAL CLEANUP
    // ===================================================
    //
    // This prevents Select2 instances and old form content
    // from remaining when the modal is closed.
    //
    $('#moveOrderModal')
        .off('hidden.bs.modal.moveCleanup')
        .on(
            'hidden.bs.modal.moveCleanup',
            function() {


                $('#MoveFormWrapper')
                    .find(
                        '.supplier-select.select2-hidden-accessible'
                    )
                    .each(function() {

                        $(this)
                            .select2(
                                'destroy'
                            );

                    });


                // Remove loaded form content
                $('#MoveFormWrapper')
                    .empty();


                // Hide loader
                $('#moveModalLoader')
                    .hide();

            }
        );


    // =========================================
    // GLOBAL EVENT LISTENERS FOR EDIT FORM
    // =========================================

    // =========================================
    // VALIDATE EDIT AMOUNT
    // =========================================
    $(document)
        .off(
            'change.editItemAmount',
            '.item-amount'
        )
        .on(
            'change.editItemAmount',
            '.item-amount',
            function() {

                var value =
                    parseFloat(
                        $(this).val()
                    ) || 0;


                if (value < 0) {

                    $(this).val(0);


                    showNotification(
                        '{{ __("common.amount_positive") }}',
                        'warning'
                    );
                }


                $(this)
                    .closest('tr')
                    .find('.save-status')
                    .hide();


                updateCategoryTotals();

            }
        );


    // =========================================
    // SAVE INDIVIDUAL ITEM
    // =========================================
    $(document)
        .off(
            'click.saveOrderItem',
            '.save-item-btn'
        )
        .on(
            'click.saveOrderItem',
            '.save-item-btn',
            function(e) {

                e.preventDefault();

                e.stopPropagation();


                var $btn =
                    $(this);


                if (
                    $btn.prop(
                        'disabled'
                    )
                ) {
                    return;
                }


                var itemId =
                    $btn.data(
                        'item-id'
                    );


                var $row =
                    $btn.closest(
                        'tr'
                    );


                var amount =
                    $row
                        .find(
                            '.item-amount'
                        )
                        .val();


                var $statusSpan =
                    $row
                        .find(
                            '.save-status'
                        );


                if (
                    !amount ||
                    parseFloat(amount) < 0
                ) {

                    showNotification(
                        '{{ __("wh.enter_valid_amount") }}',
                        'danger'
                    );

                    return;
                }


                var originalText =
                    $btn.html();


                $btn
                    .prop(
                        'disabled',
                        true
                    )
                    .html(
                        '<i class="fas fa-spinner fa-spin"></i>'
                    );


                var updateUrl =
                    '{{ route("orders.update", ["id" => "ITEM_ID"]) }}'
                        .replace(
                            'ITEM_ID',
                            itemId
                        );


                $.ajax({

                    url:
                        updateUrl,

                    type:
                        'PUT',

                    data: {

                        _token:
                            csrfToken,

                        amount:
                            amount,

                        item_id:
                            itemId
                    },


                    success: function(response) {

                        $btn
                            .prop(
                                'disabled',
                                false
                            )
                            .html(
                                originalText
                            );


                        if (
                            response.status ===
                            'success'
                        ) {

                            $statusSpan
                                .show()
                                .html(
                                    '<i class="fas fa-check-circle text-success"></i>'
                                );


                            setTimeout(
                                function() {

                                    $statusSpan.fadeOut();

                                },
                                3000
                            );


                            showNotification(
                                response.message ||
                                '{{ __("common.updated_successfully") }}',
                                'success'
                            );

                        } else {

                            showNotification(
                                response.message ||
                                '{{ __("common.error_occurred") }}',
                                'danger'
                            );
                        }

                    },


                    error: function() {

                        $btn
                            .prop(
                                'disabled',
                                false
                            )
                            .html(
                                originalText
                            );


                        showNotification(
                            '{{ __("common.error_occurred") }}',
                            'danger'
                        );

                    }

                });

            }
        );


    // =========================================
    // SAVE ALL ITEMS
    // =========================================
    $(document)
        .off(
            'click.saveAllItems',
            '#saveAllItemsBtn'
        )
        .on(
            'click.saveAllItems',
            '#saveAllItemsBtn',
            function(e) {

                e.preventDefault();

                e.stopPropagation();


                var $btn =
                    $(this);


                if (
                    $btn.prop(
                        'disabled'
                    )
                ) {
                    return;
                }


                var items =
                    [];


                var isValid =
                    true;


                var errorMessages =
                    [];


                $('.item-row')
                    .each(function() {

                        var $row =
                            $(this);


                        var itemId =
                            $row.data(
                                'item-id'
                            );


                        var amount =
                            $row
                                .find(
                                    '.item-amount'
                                )
                                .val();


                        if (
                            !amount ||
                            parseFloat(amount) < 0
                        ) {

                            isValid =
                                false;


                            errorMessages.push(
                                '{{ __("wh.enter_valid_amount") }}'
                            );


                            $row
                                .find(
                                    '.item-amount'
                                )
                                .css(
                                    'border-color',
                                    'red'
                                );

                        } else {

                            $row
                                .find(
                                    '.item-amount'
                                )
                                .css(
                                    'border-color',
                                    ''
                                );


                            items.push({
                                id:
                                    itemId,

                                amount:
                                    parseFloat(
                                        amount
                                    )
                            });

                        }

                    });


                if (
                    !isValid ||
                    items.length === 0
                ) {

                    showNotification(
                        errorMessages.join('<br>'),
                        'danger'
                    );

                    return;
                }


                var originalText =
                    $btn.html();


                $btn
                    .prop(
                        'disabled',
                        true
                    )
                    .html(
                        '<i class="fas fa-spinner fa-spin"></i>'
                    );


                var orderId =
                    $('#EditFormWrapper')
                        .find(
                            '#orderId'
                        )
                        .val() || 0;


                $.ajax({

                    url:
                        '{{ route("orders.updateAll") }}',

                    type:
                        'PUT',

                    data: {

                        _token:
                            csrfToken,

                        items:
                            items,

                        order_id:
                            orderId
                    },


                    success: function(response) {

                        $btn
                            .prop(
                                'disabled',
                                false
                            )
                            .html(
                                originalText
                            );


                        if (
                            response.status ===
                            'success'
                        ) {

                            showNotification(
                                response.message ||
                                '{{ __("common.updated_successfully") }}',
                                'success'
                            );


                            updateCategoryTotals();

                        } else {

                            showNotification(
                                response.message ||
                                '{{ __("common.error_occurred") }}',
                                'danger'
                            );
                        }

                    },


                    error: function() {

                        $btn
                            .prop(
                                'disabled',
                                false
                            )
                            .html(
                                originalText
                            );


                        showNotification(
                            '{{ __("common.error_occurred") }}',
                            'danger'
                        );

                    }

                });

            }
        );


    // =========================================
    // UPDATE CATEGORY TOTALS
    // =========================================
    function updateCategoryTotals() {

        var categoryTotals =
            {};


        $('#EditFormWrapper')
            .find('.item-row')
            .each(function() {

                var $row =
                    $(this);


                var $categoryHeader =
                    $row
                        .prevAll(
                            '.category-header'
                        )
                        .first();


                var categoryName =
                    $categoryHeader
                        .find('strong')
                        .text()
                        .trim();


                var amount =
                    parseFloat(
                        $row
                            .find(
                                '.item-amount'
                            )
                            .val()
                    ) || 0;


                if (
                    !categoryTotals[
                        categoryName
                    ]
                ) {

                    categoryTotals[
                        categoryName
                    ] = 0;
                }


                categoryTotals[
                    categoryName
                ] += amount;

            });


        $('#EditFormWrapper')
            .find('.category-header')
            .each(function() {

                var $header =
                    $(this);


                var headerText =
                    $header
                        .find('strong')
                        .text()
                        .trim();


                var total =
                    categoryTotals[
                        headerText
                    ] || 0;


                $header
                    .find(
                        '.category-total'
                    )
                    .text(
                        '{{ __("common.total") }}: ' +
                        total.toFixed(2)
                    );

            });

    }


    // =========================================
    // SCRIPT READY
    // =========================================
    console.log(
        'Order list scripts loaded successfully'
    );

});
</script>