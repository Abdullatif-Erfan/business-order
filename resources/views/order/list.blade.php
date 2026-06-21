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
    
    /* Datepicker Styles */
    .datepicker {
        z-index: 9999 !important;
    }
    .input-group.date .input-group-text {
        cursor: pointer;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-left: none;
    }
    .input-group.date .form-control {
        border-right: none;
    }
    .input-group.date .form-control:focus {
        border-color: #80bdff;
        box-shadow: none;
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
                            <span class="card-title">{{ __('order.orders_title') }}</span>
                            <span class="pull-left visible-print">{{ __('common.print_date') }} : {{ $todaysDate }}</span>
                        </div>

                        <!-- ========================================= -->
                        <!-- FILTER FORM -->
                        <!-- ========================================= -->
                        <div class="filterForm" id="searchWrapper1" style="padding: 10px 15px;">
                            <div class="col-md-12">
                                <div class="row">
                                    <!-- Order Number -->
                                    <div class="col-md-2 col-sm-6">
                                        <input type="text" id="ord_num" placeholder="{{ __('order.order_number') }}" class="form-control">
                                    </div>

                                    <!-- Supplier Name -->
                                    <div class="col-md-3 col-sm-6">
                                        <input type="text" id="supplier_name" placeholder="{{ __('order.supplier_name') }}" class="form-control">
                                    </div>

                                    <!-- Employee Name -->
                                    <div class="col-md-3 col-sm-6">
                                        <input type="text" id="employee_name" placeholder="{{ __('order.employee_name') }}" class="form-control">
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-2 col-sm-6">
                                        <input type="text" id="category_name" placeholder="{{ __('order.category') }}" class="form-control">
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-2 col-sm-6">
                                        <select class="form-control" id="state">
                                            <option value="">{{ __('order.status') }}</option>
                                            <option value="1">{{ __('order.new') }}</option>
                                            <option value="2">{{ __('order.done') }}</option>
                                        </select>
                                    </div>

                                    <!-- Start Date -->
                                    <div class="col-md-3 col-sm-6">
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

                                    <div class="col-md-3 col-sm-6">
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
                                    <div class="col-md-1 col-sm-6">
                                        <button class="btn mybtn search_btn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Reset Button -->
                                    <div class="col-md-1 col-sm-6">
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
                            <div class="table-responsive">
                                <table id="orderTable" class="display responsive nowrap table table-bordered" width="100%">
                                    <thead>
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
                                            <th>{{ __('common.action') }}</th>
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
@endpush

@endsection