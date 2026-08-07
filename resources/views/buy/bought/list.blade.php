@extends('layouts.app')

@section('content')

<style>
    /* ========================================= */
/* DROPDOWN FIX FOR DATATABLE */
/* ========================================= */

/* Allow dropdown to overflow */
.dataTables_wrapper {
    overflow: visible !important;
}

.dataTables_scroll {
    overflow: visible !important;
}

.dataTables_scrollBody {
    overflow: visible !important;
    min-height: 200px;
}

/* Dropdown container */
.btn-group {
    position: relative;
    z-index: 100;
}

.detailsDropdown {
    position: relative;
}

/* Dropdown menu - ensure it appears above everything */
.detailsDropdown .dropdown-menu {
    z-index: 9999 !important;
    position: absolute !important;
    min-width: 140px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

/* For RTL support - open to the right */
.detailsDropdown .dropdown-menu {
    right: -120px;
    left: auto !important;
}

/* If you want to force right alignment */
.detailsDropdown .dropdown-menu-right {
    right: 0 !important;
    left: auto !important;
}

/* For LTR support - open to the left */
/* .dropdown-menu {
    left: 0;
    right: auto !important;
} */

/* Fix for table row */
tr {
    position: relative;
}

td {
    position: relative;
}

/* Ensure dropdown doesn't get cut off */
.table-responsive {
    overflow: visible !important;
    padding-bottom: 50px;
}

.card-body {
    overflow: visible !important;
}

/* Fix for DataTables responsive */
table.dataTable {
    overflow: visible !important;
}

table.dataTable tbody tr {
    overflow: visible !important;
}

table.dataTable tbody td {
    overflow: visible !important;
}

/* Dropup support */
.dropup .dropdown-menu {
    top: auto !important;
    bottom: 100% !important;
    margin-bottom: 2px;
}

/* Make dropdown toggle button more visible */
.detailsDropdown .dropdown-toggle::after {
    display: inline-block;
    margin-left: 4px;
    vertical-align: middle;
    content: "";
    border-top: 4px solid;
    border-right: 4px solid transparent;
    border-bottom: 0;
    border-left: 4px solid transparent;
}

/* Button styling */
.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}

.detailsDropdown .dropdown-toggle {
    min-width: 32px;
    text-align: center;
}

/* Mobile styles */
@media (max-width: 768px) {
    .detailsDropdown .dropdown-menu {
        min-width: 150px;
        font-size: 12px;
    }
    .detailsDropdown .dropdown-item {
        padding: 6px 12px;
        font-size: 12px;
    }
}

/* Fix for dropdown being cut off */
.main-panel>.content {
    overflow: visible !important;
}

/* Also fix any parent containers */
.page-inner {
    overflow: visible !important;
}

.card {
    overflow: visible !important;
}

.card-body {
    overflow: visible !important;
}

.table-responsive {
    overflow: visible !important;
    padding-bottom: 50px !important;
}
</style>

<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px; text-align:center;">
                            <input type="hidden" id="tax_activation" value="{{ $orgbios[0]->tax_activation }}" >
                            <a href="{{ route('boughtList.create') }}" class="pull-right">
                                <button type="button" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> {{__('common.add')}}
                                </button>
                            </a>
                            <span class="card-title">  {{__('buy.buy_title')}} </span>

                             <!-- Responsive Filter Toggle Button - Visible only on XS -->
                            <div class="pull-left" style="width:150px">
                                <button type="button" class="responsive_button btn btn-sm  visible-xs"
                                  id="filterToggleBtn" onclick="toggleFilterForm()"  style="margin-left:2px; margin-top:2px;">
                                   <i class="fas fa-filter"></i>
                                 </button>
                                 <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                            </div>
                        </div>


                          <div class="filter-section no-print" id="searchWrapper">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="customer_name" placeholder="{{__('order.supplier_name')}}" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
                                        <input type="text" id="user_name" placeholder="{{__('common.user')}}" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input class="form-control" id="bill_number" placeholder="{{__('common.bill')}}">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                         <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="start_date"  placeholder="{{__('common.start_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>
                                     <div class="col-md-2 col-sm-6 col-xs-6">
                                        <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="end_date" placeholder="{{__('common.end_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <div class="filter-actions">
                                            <button class="btn mybtn search_btn" id="btn-filter"><i class="fas fa-search"></i></button>
                                            <button class="btn mybtn search_btn" id="btn-reset" title="{{ __('common.reset') }}"><i class="fas fa-undo"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /filter_form -->

                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <span class="pull-left visible-print">{{__('common.print_date')}} : {{ $todaysDate }}</span>
                                <table id="boughtItemTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="9">
                                                <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" 
                                                style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="9">
                                                <center> {{__('buy.buy_title')}} </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{__('common.number')}} &nbsp;</th>
                                            <th>{{__('common.bill')}}</th>
                                            <th>{{__('order.supplier_name')}}</th>                                            
                                            <th>{{__('common.total_price')}}</th>
                                            <th>{{__('buy.cache_paid')}}</th>
                                            <th>{{__('buy.loan')}}</th>
                                            <th>{{__('common.user')}}</th>
                                            <th>{{__('common.date')}}</th>
                                            <th class="hidden-print">{{__('common.details')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="3">{{__('common.total')}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="hidden-print"></td>
                                        </tr>
                                    </tfoot> 
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Profit and Sell_up Modal -->
<div class="modal fade" id="EditRecordsModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="width: 1000px !important; max-width: 95vw !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{ __('common.edit') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditAccountFormWrapper"></div>
                <div id="loading_modal" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditAccountBtn">{{ __('common.save') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Show List of items in modal -->
<div class="modal fade" id="itemListModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="width: 900px !important; max-width: 95vw !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{ __('common.sold_item_list') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="itemListModalContent"></div>
                <div id="itemListModalLoader" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Show Bill Payment -->
<div class="modal fade" id="billPaymentModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="width: 900px !important; max-width: 95vw !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{ __('sales.bill_payment') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="billPaymentModalContent"></div>
                <div id="billPaymentModalLoader" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm m-l-5" id="paymentBill" data-dismiss="modal">{{ __('common.save_and_submit') }}</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('show.bs.dropdown', '.dropdown', function() {
    var $menu = $(this).find('.dropdown-menu');
    var $button = $(this).find('.dropdown-toggle');
    var buttonBottom = $button.offset().top + $button.outerHeight();
    var windowHeight = $(window).height();
    var menuHeight = $menu.outerHeight();

    // If not enough space below, open upward
    if (buttonBottom + menuHeight > windowHeight) {
        $(this).addClass('dropup');
    } else {
        $(this).removeClass('dropup');
    }
});

$(document).on('click', '.datepicker-icon', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $input = $(this).closest('.input-group').find('input');
    if ($input.length) {
        $input.datepicker('show');
    }
});

// =========================================
// ENTER KEY SEARCH
// =========================================
$('.filter-section input').on('keypress', function(e) {
    if (e.which === 13) {
        $('#btn-filter').click();
    }
});

$(document).ready(function() {
    fetchList();

    // Move the filter button click event outside
    $('#btn-filter').click(function() {
        $('#boughtItemTable').DataTable().ajax.reload(null, false);
    });

    // =========================================
    // RESET BUTTON
    // =========================================
    $('#btn-reset').on('click', function() {
        $('#customer_name').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#user_name').val('');
        $('#bill_number').val('');
        $('#boughtItemTable').DataTable().ajax.reload(null, false);
    });
});

function fetchList() {
    let boughtItemTable = $('#boughtItemTable');

    if (!$.fn.DataTable.isDataTable(boughtItemTable)) {
        boughtItemTable.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,   
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'همه']
            ],
            ajax: {  
                url: '{{ route("boughtList.data") }}',
                data: function (d) {
                    d.customer_name = $('#customer_name').val();
                    d.user_name = $('#user_name').val();
                    d.bill_number = $('#bill_number').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.tax_activation = $('#tax_activation').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'billno', name: 'billno' },
                { data: 'customer_relation.name', name: 'customer_relation.name' },
                { data: 'total', name: 'total' },
                { data: 'cur_pay', name: 'cur_pay' },
                { data: 'remained', name: 'remained' },
                { data: 'user_name', name: 'user_name' },
                { data: 'idate', name: 'idate' },
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'hidden-print' }
            ],
            drawCallback: function () 
            {
                var api = this.api();

                function fmod(a, b) {
                    return a - (b * Math.floor(a / b));
                }

                function sumColumn(index) {
                    return api
                        .column(index, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            var numA = parseFloat(a.toString().replace(/,/g, '')) || 0;
                            var numB = parseFloat(b.toString().replace(/,/g, '')) || 0;
                            var sum = numA + numB;

                            if (fmod(sum, 1) === 0) {
                                return sum.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                            } else {
                                return sum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }

                        }, 0)
                        .toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
    
                $(api.column(3).footer()).html(sumColumn(3));
                $(api.column(4).footer()).html(sumColumn(4));
                $(api.column(5).footer()).html(sumColumn(5));
            }
        });

    } else {
        boughtItemTable.DataTable().ajax.reload(null, false);
    }
}

// Set Profit
$('table').on('click', '.setProfit', function () {
    var billno = $(this).data('id');
    var isEditable = $(this).data('id2');
    var hasInvoice = $(this).data('id3');

    // Check if invoice exists 
    if (isEditable == 1 || hasInvoice == 1) {
        showNotification('درصورتیکه انوایس ایجاد شده باشد ویا فروش شده باشد دیگر قابل ویرایش نمی باشد', 'danger');
        $('#EditAccountBtn').fadeOut(100);
        return; // Exit the function
    } else {
        $('#EditAccountBtn').fadeIn(100);
    }
    $('#EditRecordsModal').modal('show');
    $('#loading_modal').show();
    $.ajax({
        url: `/boughtList/getToUpdateProfit/${billno}`,
        type: 'GET',
        success: (result) => {
            $('#EditAccountFormWrapper').html(result);
            $('#loading_modal').hide();

            // Initialize Select2 after the form has been injected
            $(".select2").select2();
        },
        error: () => {
            $('#loading_modal').hide();
            alert('اطلاعات یافت نشد');
        }
    });
});

// Show Items in modal
$('table').on('click', '.itemList', function () {
    $('#itemListModal').modal('show');
    $('#itemListModalLoader').show();
    const billno = $(this).data('id');
    $.ajax({
        url: `/boughtList/getListOfItemsToShowInModal/${billno}`,
        type: 'GET',
        success: (result) => {
            $('#itemListModalContent').html(result);
            $('#itemListModalLoader').hide();
        },
        error: () => {
            $('#itemListModalLoader').hide();
            alert('اطلاعات یافت نشد');
        }
    });
});

// Sales Bill
$('table').on('click', '.billPayment', function () {
    var billno = $(this).data('id');
    var remained = $(this).data('id2');
    
    if (remained <= 0) {
        showNotification('پرداخت تکمیل گردیده است', 'success');
        return; // Exit the function
    }
    
    $('#billPaymentModal').modal('show');
    $('#billPaymentModalLoader').show();
    $('#billPaymentModalContent').html('');
    
    $.ajax({
        url: `/boughtList/billPayment/${billno}`,
        type: 'GET',
        success: function(result) {
            $('#billPaymentModalContent').html(result);
            $('#billPaymentModalLoader').hide();
        },
        error: function() {
            $('#billPaymentModalLoader').hide();
            alert('اطلاعات یافت نشد');
        }
    });
});

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
@endsection