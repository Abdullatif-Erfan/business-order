@extends('layouts.app')

@section('content')

<style>
    /* ========================================= */
    /* MODERN DASHBOARD STYLES */
    /* ========================================= */
    
    /* Card Header */
    .card-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 15px 20px !important;
        border-radius: 10px 10px 0 0 !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .card-header-modern .card-title {
        color: #fff;
        font-weight: 600;
        font-size: 18px;
        margin: 0;
    }
    .card-header-modern .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 6px 16px;
        font-size: 13px;
    }
    .card-header-modern .btn-white {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff;
    }
    .card-header-modern .btn-white:hover {
        background: rgba(255,255,255,0.3);
        color: #fff;
    }
    .card-header-modern .btn-primary {
        background: #fff;
        border: none;
        color: #667eea;
    }
    .card-header-modern .btn-primary:hover {
        background: rgba(255,255,255,0.9);
        color: #667eea;
    }
    
    /* Tabs */
    .order-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        padding: 0;
        margin: 0;
        list-style: none;
        border-bottom: none;
        flex: 1;
    }
    .order-tabs .tab-link {
        padding: 12px 24px;
        color: #636e72;
        font-weight: 500;
        font-size: 14px;
        border: none;
        background: transparent;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .order-tabs .tab-link:hover {
        color: #4a6cf7;
        background: #f0f4ff;
    }
    .order-tabs .tab-link.active {
        color: #4a6cf7;
        background: #f0f4ff;
    }
    .order-tabs .tab-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: #4a6cf7;
        border-radius: 3px 3px 0 0;
    }
     .filter-section .filter-group:first-child {
        flex: 0.5;
        min-width: 80px;
    }
    
    /* Filter Section - One Row */
    .filter-section {
        background: #f0f4ff;
        padding: 8px 16px 10px 8px;
        margin-bottom: 6px;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 8px;
        border-bottom: 1px solid #e9ecef;
        border-right: 3px solid #4a6cf7;
        border-left: 3px solid #4a6cf7;
    }
    .filter-section .filter-group {
        flex: 1;
        min-width: 120px;
    }
    .filter-section .filter-group label {
        font-size: 10px;
        font-weight: 600;
        color: #636e72;
        margin-bottom: 2px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    /* .filter-section .filter-group .form-control {
        height: 32px;
        font-size: 12px;
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 3px 8px;
        background: #fff;
    }
    .filter-section .filter-group .form-control:focus {
        border-color: #4a6cf7;
        box-shadow: 0 0 0 3px rgba(74,108,247,0.1);
    }
    .filter-section .filter-group .input-group .form-control {
        border-radius: 4px 0 0 4px;
        height: 32px;
        font-size: 12px;
        padding: 3px 8px;
    } */
    .filter-section .filter-group .input-group .input-group-text {
        border-radius: 0 4px 4px 0;
        background: #fff;
        border-left: none;
        cursor: pointer;
        padding: 0 10px;
        height: 32px;
        color: #636e72;
        font-size: 13px;
    }
    .filter-section .filter-group .input-group .input-group-text:hover {
        color: #4a6cf7;
    }
    .filter-section .filter-actions {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .filter-section .filter-actions .btn {
        height: 32px;
        padding: 0 14px;
        font-size: 12px;
        border-radius: 4px;
        font-weight: 500;
    }
    .filter-section .filter-actions .btn-search {
        background: #169054;
        color: #fff;
        border: none;
    }
    .filter-section .filter-actions .btn-search:hover {
        background: #3a5cd7;
    }
    .filter-section .filter-actions .btn-reset {
        background: #3990e7;
        color: #ffffff;
        border: none;
    }
    .filter-section .filter-actions .btn-reset:hover {
        background: #dee2e6;
    }
    
    /* Table Wrapper */
    .table-wrapper {
        padding: 0 20px 20px 20px;
    }
   
    /* Badges */
    .badge-draft { 
        background: #dfe6e9; 
        color: #2d3436; 
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
    }
    .badge-new { 
        background: #fdcb6e; 
        color: #2d3436; 
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
    }
    .badge-completed { 
        background: #00b894; 
        color: #fff; 
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
    }
    .badge-cancelled { 
        background: #e17055; 
        color: #fff; 
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
    }
    
    /* Action Icons */
    .action-icons {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .action-icons i {
        font-size: 18px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .action-icons i:hover {
        transform: scale(1.2);
    }
    .action-icons .viewOrder { color: #4a6cf7; }
    .action-icons .editOrder { color: #fdcb6e; }
    .action-icons .deleteOrder { color: #e17055; }
    
    /* Print Styles */
    @media print {
        .card-header-modern .btn,
        .filter-section,
        .order-tabs {
            display: none !important;
        }
        .table-wrapper {
            padding: 0 !important;
        }
        .table-wrapper .table thead th {
            background: #e9ecef !important;
        }
        .action-icons {
            display: none !important;
        }
        .no-print {
            display: none !important;
        }
    }

    .button-wrapper {
        padding: 0 20px; border-bottom: 2px solid #e9ecef;
    }
    
     /* ========================================= */
    /* RESPONSIVE - Mobile */
    /* ========================================= */
    
    /* Tablets and small desktops */
    @media (max-width: 992px) {
        .filter-section .filter-group {
            min-width: 100px;
        }
        .filter-section .filter-actions {
            flex: 1;
            justify-content: flex-end;
        }
        .filter-section .filter-group:first-child {
            flex: 0.5;
            min-width: 70px;
        }
        
       .button-wrapper {
          padding: 0px;
       }
    }
    
    /* Mobile devices */
    @media (max-width: 768px) {
        /* Title */
        .page-title {
            font-size: 16px;
            padding: 10px 15px 5px 15px;
            text-align: center;
        }
        
        /* Tabs & Actions Row - Scrollable */
        .tabs-actions-row {
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            padding: 0 10px !important;
            gap: 5px !important;
            scrollbar-width: thin;
        }
        .tabs-actions-row::-webkit-scrollbar {
            height: 3px;
        }
        .tabs-actions-row::-webkit-scrollbar-track {
            background: #e9ecef;
            border-radius: 10px;
        }
        .tabs-actions-row::-webkit-scrollbar-thumb {
            background: #4a6cf7;
            border-radius: 10px;
        }
        
        .order-tabs {
            flex-wrap: nowrap !important;
            padding: 0 !important;
            gap: 1px !important;
            flex: 1 !important;
            overflow: visible !important;
        }
        .order-tabs .tab-link {
            padding: 8px !important;
            font-size: 12px !important;
            white-space: nowrap !important;
        }
        .tab-actions {
            flex-shrink: 0 !important;
            padding-left: 8px !important;
            border-left: 1px solid #e9ecef !important;
            margin-left: 0 !important;
        }
        .tab-actions .btn-print,
        .tab-actions .btn-add {
            padding: 4px 10px !important;
            font-size: 12px !important;
        }
        
        /* Filter: 2 columns */
        .filter-section {
            padding: 8px 10px 10px 10px;
            gap: 5px;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
        }
        .filter-section .filter-group {
            flex: 0 0 calc(50% - 5px) !important;
            min-width: calc(50% - 5px) !important;
            margin-bottom: 4px;
        }
        .filter-section .filter-group:first-child {
            flex: 0 0 calc(50% - 5px) !important;
            min-width: calc(50% - 5px) !important;
        }
        .filter-section .filter-group .form-control,
        .filter-section .filter-group .input-group {
            height: 30px;
            font-size: 11px;
        }
        .filter-section .filter-group .input-group .form-control {
            height: 30px;
            font-size: 11px;
        }
        .filter-section .filter-group .input-group .input-group-text {
            height: 30px;
            padding: 0 8px;
            font-size: 11px;
        }
        .filter-section .filter-actions {
            flex: 0 0 100% !important;
            justify-content: center;
            margin-top: 2px;
        }
        .filter-section .filter-actions .btn {
            height: 28px;
            padding: 0 12px;
            font-size: 11px;
        }
        
        /* Table */
        .table-wrapper {
            padding: 0 10px 10px 10px;
        }
        .table-wrapper .table thead th {
            font-size: 10px;
            padding: 6px 8px;
        }
        .table-wrapper .table tbody td {
            font-size: 12px;
            padding: 6px 8px;
        }
        .action-icons i {
            font-size: 15px;
        }
    }
    
    /* Small mobile devices */
    @media (max-width: 480px) {
        .order-tabs .tab-link {
            padding: 8px !important;
            font-size: 11px !important;
        }
        .tab-actions .btn-print,
        .tab-actions .btn-add {
            padding: 3px 8px !important;
            font-size: 11px !important;
        }
        
        .filter-section .filter-group {
            flex: 0 0 calc(50% - 4px) !important;
            min-width: calc(50% - 4px) !important;
        }
        .filter-section .filter-group:first-child {
            flex: 0 0 calc(50% - 4px) !important;
            min-width: calc(50% - 4px) !important;
        }
        .filter-section .filter-group .form-control,
        .filter-section .filter-group .input-group {
            height: 28px;
            font-size: 10px;
        }
        .filter-section .filter-group .input-group .form-control {
            height: 28px;
            font-size: 10px;
        }
        .filter-section .filter-group .input-group .input-group-text {
            height: 28px;
            padding: 0 6px;
            font-size: 10px;
        }
        .filter-section .filter-actions .btn {
            height: 26px;
            padding: 0 10px;
            font-size: 10px;
        }
        
        .table-wrapper .table thead th {
            font-size: 9px;
            padding: 4px 6px;
        }
        .table-wrapper .table tbody td {
            font-size: 11px;
            padding: 4px 6px;
        }
        .action-icons i {
            font-size: 13px;
        }
    }
    
    /* Tablet */
    @media (min-width: 769px) and (max-width: 992px) {
        .filter-section .filter-group {
            flex: 0 0 calc(33.33% - 6px) !important;
            min-width: calc(33.33% - 6px) !important;
        }
        .filter-section .filter-group:first-child {
            flex: 0 0 calc(33.33% - 6px) !important;
            min-width: calc(33.33% - 6px) !important;
        }
        .filter-section .filter-actions {
            flex: 0 0 100% !important;
            justify-content: flex-start;
            margin-top: 4px;
        }
    }

</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <span class="card-title">
                            </i> {{ __('order.orders_title') }}
                        </span>
                    </div>
                    <div class="card" style="overflow: hidden; box-shadow: 0 2px 20px rgba(0,0,0,0.06);">
                        
                        <!-- ========================================= -->
                        <!-- TABS, PRINT & ADD BUTTON - ONE LINE -->
                        <!-- ========================================= -->
                        <div class="d-flex align-items-center justify-content-between no-print button-wrapper">
                            <ul class="order-tabs no-print" id="orderTabs" style="border-bottom: none; padding: 0; margin: 0;">
                                <li>
                                    <a class="tab-link" data-tab="0" href="#">
                                        <i class="fas fa-file-alt hidden-xs"></i> {{ __('order.draft') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="tab-link active" data-tab="1" href="#">
                                        <i class="fas fa-clock  hidden-xs"></i> {{ __('order.new') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="tab-link" data-tab="3" href="#">
                                        <i class="fas fa-check-circle  hidden-xs"></i> {{ __('order.completed') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="tab-link" data-tab="2" href="#">
                                        <i class="fas fa-times-circle  hidden-xs"></i> {{ __('order.cancelled') }}
                                    </a>
                                </li>
                            </ul>
                            <div class="d-flex align-items-center gap-2" style="gap: 4px;">
                                <button class="btn btn-sm no-print" onclick="print_page_with_image()" style="background: transparent; border: 1px solid #ddd; color: #636e72; border-radius: 5px; padding: 4px 6px;">
                                    <i class="fas fa-print"></i>
                                </button>
                                <!-- Responsive Filter Toggle Button - Visible only on XS -->
                               <button type="button" class="responsive_button btn btn-sm  visible-xs"
                                   id="filterToggleBtn" onclick="toggleFilterForm()"  style="margin-left:2px; margin-top:2px;">
                                   <i class="fas fa-filter"></i>
                               </button>
                                <a href="{{ route('orders.create') }}" class="btn btn-sm no-print" style="background: #3990e7; border: none; color: #fff; border-radius: 5px; padding: 4px 8px;">
                                    <i class="fas fa-plus"></i> <span class="hidden-xs">{{ __('common.add') }}</span>
                                </a>
                            </div>
                            <span class="pull-left visible-print">{{ __('common.print_date') }} : {{ $todaysDate }}</span>
                        </div>
                        <!-- ========================================= -->
                        <!-- FILTER SECTION - ONE ROW -->
                        <!-- ========================================= -->
                        <div class="filter-section no-print" id="searchWrapper">
                            <div class="filter-group">
                                <input type="text" id="ord_num" placeholder="{{ __('order.order_number') }}" class="form-control">
                            </div>
                            <div class="filter-group">
                                <input type="text" id="supplier_name" placeholder="{{ __('order.supplier_name') }}" class="form-control">
                            </div>
                            <div class="filter-group">
                                <input type="text" id="employee_name" placeholder="{{ __('order.employee_name') }}" class="form-control">
                            </div>
                            <div class="filter-group">
                                <input type="text" id="category_name" placeholder="{{ __('order.category') }}" class="form-control">
                            </div>
                            <div class="filter-group" style="min-width: 120px;">
                                <div class="input-group">
                                    <input type="text" class="form-control datepicker-input" id="start_date" placeholder="YYYY-MM-DD">
                                    <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                            </div>
                            <div class="filter-group" style="min-width: 120px;">
                                <div class="input-group">
                                    <input type="text" class="form-control datepicker-input" id="end_date" placeholder="YYYY-MM-DD">
                                    <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button class="btn btn-search" id="btn-filter"><i class="fas fa-search"></i></button>
                                <button class="btn btn-reset" id="btn-reset" title="{{ __('common.reset') }}"><i class="fas fa-undo"></i></button>
                            </div>
                        </div>

                        <!-- ========================================= -->
                        <!-- TABLE -->
                        <!-- ========================================= -->
                        <div class="table-wrapper" id="print_area" style="padding:5px;">
                            <span class="pull-left visible-print"> {{__('common.print_date')}} : {{ $todaysDate }}</span>
                            <table id="orderTable" class="display responsive nowrap table table-bordered" width="100%">
                                <thead>
                                    <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="10">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="10">
                                            <center> {{__('order.list_title')}}   </center>
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

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================= -->
<!-- VIEW MODAL -->
<!-- ========================================= -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:900px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye"></i> {{ __('common.details') }}</h5>
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
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================= -->
<!-- STATE MODAL -->
<!-- ========================================= -->
<div class="modal fade" id="stateOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:500px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt"></i> {{ __('order.update_status') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="stateUpdateForm">
                    @csrf
                    <input type="hidden" name="ord_num" id="state_ord_num">
                    <div class="form-group">
                        <label for="state_status">{{ __('order.status') }}</label>
                        <select class="form-control" name="state" id="state_status">
                            <option value="0">{{ __('order.draft') }}</option>
                            <option value="1">{{ __('order.new') }}</option>
                            <option value="2">{{ __('order.cancelled') }}</option>
                            <option value="3">{{ __('order.completed') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" id="saveStateBtn">{{ __('common.save') }}</button>
                 &nbsp;
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>



@push('scripts')
@include('order.scripts')
@endpush

@endsection