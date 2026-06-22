@extends('layouts.app')

@section('content')

<style>
    /* Responsive Filters */
    .filterForm .row {
        margin: 0 -5px;
    }
    .filterForm .col-md-2,
    .filterForm .col-md-3 {
        padding: 0 5px;
        margin-bottom: 10px;
    }
    @media (max-width: 768px) {
        .filterForm .col-md-2,
        .filterForm .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    @media (max-width: 576px) {
        .filterForm .col-md-2,
        .filterForm .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    
    
    /* Action Buttons */
    .action-icons i {
        font-size: 18px;
        margin: 0 3px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .action-icons i:hover {
        transform: scale(1.2);
    }
    .action-icons .viewOrder { color: #4a6cf7; }
    .action-icons .editOrder { color: #fdcb6e; }
    .action-icons .deleteOrder { color: #e17055; }
    
    /* Badge Styles */
    .badge-new { background: #fdcb6e; color: #2d3436; }
    .badge-done { background: #00b894; color: #fff; }
    .badge-cancelled { background: #e17055; color: #fff; }
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px; text-align:center;">
                            <a href="{{ route('orders.create') }}" class="pull-right">
                                <button type="button" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> {{ __('common.add') }}
                                </button>
                            </a>
                            <span class="card-title hidden-xs">{{ __('order.orders_title') }}</span>
                            <span class="pull-left visible-print">{{ __('common.print_date') }} : {{ $todaysDate }}</span>
                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                           
                             <!-- Responsive Filter Toggle Button - Visible only on XS -->
                            <button type="button" class="responsive_button pull-left btn btn-sm mybtn visible-xs"
                                id="filterToggleBtn" onclick="toggleFilterForm()"  style="margin-left:50px; margin-top:2px;">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>

                        <!-- ========================================= -->
                        <!-- FILTER FORM -->
                        <!-- ========================================= -->
                        <div class="responsiveFilterForm" id="searchWrapper" style="padding: 10px 15px;">
                            <div class="col-md-12">
                                <div class="row">
                                    <!-- Order Number -->
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="ord_num" placeholder="{{ __('order.order_number') }}" class="form-control">
                                    </div>

                                    <!-- Supplier Name -->
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <input type="text" id="supplier_name" placeholder="{{ __('order.supplier_name') }}" class="form-control">
                                    </div>

                                    <!-- Employee Name -->
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <input type="text" id="employee_name" placeholder="{{ __('order.employee_name') }}" class="form-control">
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="category_name" placeholder="{{ __('order.category') }}" class="form-control">
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <select class="form-control" id="state">
                                            <option value="">{{ __('order.status') }}</option>
                                            <option value="1">{{ __('order.new') }}</option>
                                            <option value="2">{{ __('order.done') }}</option>
                                            <option value="3">{{ __('order.cancelled') }}</option>
                                        </select>
                                    </div>

                                    <!-- Start Date -->
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker-input" id="start_date" 
                                                name="start_date" placeholder="{{ __('common.start_date') }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text datepicker-icon">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker-input" id="end_date" 
                                                name="end_date" placeholder="{{ __('common.end_date') }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text datepicker-icon">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Search Button -->
                                    <div class="col-md-1 col-sm-6 col-xs-6">
                                        <button class="btn mybtn search_btn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Reset Button -->
                                    <div class="col-md-1 col-sm-6 col-xs-6">
                                        <button class="btn mybtn btn-secondary form-control" id="btn-reset">
                                            <i class="fa fa-undo"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================= -->
                        <!-- TABLE -->
                        <!-- ========================================= -->
                        <div class="card-body">
                             <span class="pull-left visible-print"> {{__('common.print_date')}} : {{ $todaysDate }}</span>
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <table id="orderTable" class="display responsive nowrap table table-bordered" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="12">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="12">
                                            <center> {{__('order.order_list')}}   </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('common.number') }}</th>
                                            <th>{{ __('order.order_number') }}</th>
                                            <th>{{ __('order.item') }}</th>
                                            <th>{{ __('order.supplier_name') }}</th>
                                            <th>{{ __('order.employee_name') }}</th>
                                            <th>{{ __('order.category') }}</th>
                                            <th>{{ __('order.amount') }}</th>
                                            <th>{{ __('order.unit') }}</th>
                                            <th>{{ __('order.status') }}</th>
                                            <th>{{ __('common.date') }}</th>
                                            <th>{{ __('order.done_by') }}</th>
                                            <th>{{ __('order.actions') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- View Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:900px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{ __('common.details') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="ViewFormWrapper"></div>
                <div id="modalLoader" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- State Modal -->
<div class="modal fade" id="stateOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:500px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('order.update_status') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="stateModalLoader" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading') }}
                </div>
                <form id="stateUpdateForm">
                    @csrf
                    <input type="hidden" name="ord_num" id="state_ord_num">
                    <div class="form-group">
                        <label for="state_status">{{ __('order.status') }}</label>
                        <select class="form-control" name="state" id="state_status">
                            <option value="1">{{ __('order.new') }}</option>
                            <option value="2">{{ __('order.done') }}</option>
                            <option value="3">{{ __('order.cancelled') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" id="saveStateBtn">{{ __('common.confirm') }}</button>
                &nbsp;
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')


<script>
$(document).ready(function() {
    // =========================================
    // FETCH DATA TABLE
    // =========================================
    fetchList();

    // =========================================
    // FILTER BUTTON
    // =========================================
    $('#btn-filter').click(function() {
        $('#orderTable').DataTable().ajax.reload(null, false);
    });

    // =========================================
    // RESET BUTTON
    // =========================================
    $('#btn-reset').click(function() {
        $('#ord_num').val('');
        $('#supplier_name').val('');
        $('#employee_name').val('');
        $('#category_name').val('');
        $('#state').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#orderTable').DataTable().ajax.reload(null, false);
    });

    // =========================================
    // ENTER KEY SEARCH
    // =========================================
    $('.filterForm input, .filterForm select').on('keypress', function(e) {
        if (e.which === 13) {
            $('#btn-filter').click();
        }
    });
});

// =========================================
// DATA TABLE FUNCTION
// =========================================
function fetchList() {
    let orderTable = $('#orderTable');

    if (!$.fn.DataTable.isDataTable(orderTable)) {
        orderTable.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, '{{ __("common.all") }}']
            ],
            responsive: true,
            autoWidth: false,
            ajax: {
                url: '{{ route("orders.data") }}',
                data: function(d) {
                    d.ord_num = $('#ord_num').val();
                    d.supplier_name = $('#supplier_name').val();
                    d.employee_name = $('#employee_name').val();
                    d.category_name = $('#category_name').val();
                    d.state = $('#state').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
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
                { data: 'done_by', name: 'done_by' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
          
        });
    } else {
        orderTable.DataTable().ajax.reload(null, false);
    }
}

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
</script>

 <script>
$(document).ready(function() { 
   // Single click handler for all datepicker icons - use a flag to prevent double trigger
    $(document).on('click', '.datepicker-icon', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $input = $(this).closest('.input-group').find('input');
        if ($input.length) {
            $input.datepicker('show');
        }
    });
});
</script>
<script> 
    $(document).on('click', '.viewOrder', function () {
        $('#viewOrderModal').modal('show');
        $('#modalLoader').show();
        $('#ViewFormWrapper').html('');
        
        const ord_num = $(this).data('id');
        $.ajax({
            url: `/orders/show/${ord_num}`,
            type: 'GET',
            success: function(result) {
                $('#ViewFormWrapper').html(result);
                $('#modalLoader').hide();
            },
            error: function() {
                $('#modalLoader').hide();
                showNotification('{{ __("common.not_found") }}', 'danger', 'top', 'right', 'withicon');
            }
        });
    });

// =========================================
// DELETE ORDER
// =========================================
$(document).on('click', '.deleteOrder', function () {
    const ord_num = $(this).data('id');
    if (ord_num && confirm("{{ __('common.delete_confirm') }}")) {
        $.ajax({
            url: `/orders/destroy/${ord_num}`,
            type: 'DELETE',
            data: { 
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#orderTable').DataTable().ajax.reload(null, false);
                    showNotification(response.message, 'success', 'top', 'right', 'withicon');
                } else {
                    showNotification(response.message || '{{ __("common.delete_failed") }}', 'danger', 'top', 'right', 'withicon');
                }
            },
            error: function(xhr) {
                showNotification('{{ __("common.delete_failed") }}', 'danger', 'top', 'right', 'withicon');
            }
        });
    }
});
</script>

<script>
$(document).ready(function() {
    // =========================================
    // OPEN STATE MODAL
    // =========================================
    $(document).on('click', '.newState', function () {
        const ord_num = $(this).data('ord-num');
        const currentState = $(this).data('state');
        
        // Set the order number in hidden field
        $('#state_ord_num').val(ord_num);
        
        // Set the current state in dropdown
        $('#state_status').val(currentState);
        
        // Show/hide done_by field based on state
        if (currentState == 2) {
            $('#done_by_group').show();
        } else {
            $('#done_by_group').hide();
        }
        
        // Clear previous error messages
        $('.state-error').remove();
        
        // Show modal
        $('#stateOrderModal').modal('show');
    });

    // =========================================
    // SHOW/HIDE DONE_BY FIELD ON STATE CHANGE
    // =========================================
    $(document).on('change', '#state_status', function() {
        if ($(this).val() == 2) {
            $('#done_by_group').show();
        } else {
            $('#done_by_group').hide();
        }
    });

    // =========================================
    // SAVE STATE UPDATE
    // =========================================
    $(document).on('click', '#saveStateBtn', function() {
        const ord_num = $('#state_ord_num').val();
        const state = $('#state_status').val();
        
        // Validate
        if (!ord_num || !state) {
            showNotification('{{ __("common.invalid_data") }}', 'danger');
            return;
        }
        
        // Disable button and show loading
        const $btn = $(this);
        $btn.prop('disabled', true).text('{{ __("common.saving") }}...');
        
        $.ajax({
            url: `/orders/update-status/${ord_num}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                state: state,
            },
            success: function(response) {
                $btn.prop('disabled', false).text('{{ __("common.save") }}');
                if (response.status === 'success') {
                    $('#stateOrderModal').modal('hide');
                    showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    $('#orderTable').DataTable().ajax.reload(null, false);
                } else {
                    showNotification(response.message || '{{ __("common.error_occurred") }}', 'danger');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('{{ __("common.save") }}');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    $.each(errors, function(key, messages) {
                        errorMessages.push(messages[0]);
                    });
                    showNotification(errorMessages.join('<br>'), 'danger');
                } else {
                    showNotification('{{ __("common.error_occurred") }}', 'danger');
                }
            }
        });
    });

    // =========================================
    // CLOSE MODAL - Reset form
    // =========================================
    $('#stateOrderModal').on('hidden.bs.modal', function() {
        $('#state_status').val(1);
        $('#done_by_group').hide();
        $('#state_ord_num').val('');
        $('.state-error').remove();
    });

    // =========================================
    // ENTER KEY TO SUBMIT
    // =========================================
    $(document).on('keypress', '#state_status', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#saveStateBtn').click();
        }
    });
});
</script>

@endpush

@endsection