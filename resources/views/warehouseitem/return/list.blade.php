@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px; text-align:center;">
                            <span class="card-title"> {{__('buy.return_list')}} </span>
                            <div class="pull-left" style="width:150px">
                                <button class="printBtn" onclick="print_page_with_image()">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>

                            <!-- Responsive Filter Toggle Button -->
                            <div class="pull-left" style="width:150px">
                                <button type="button" class="responsive_button btn btn-sm visible-xs"
                                  id="filterToggleBtn" onclick="toggleFilterForm()" style="margin-left:2px; margin-top:2px;">
                                   <i class="fas fa-filter"></i>
                                 </button>
                            </div>
                        </div>

                        <div class="filter-section no-print" id="searchWrapper">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="return_number" placeholder="{{__('buy.return_number')}}" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="billno" placeholder="{{__('common.bill')}}" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <select class="form-control select2" id="supplier_id" style="width: 100%;">
                                            <option value=""> {{__('order.supplier_name')}} </option>
                                            @foreach($suppliers ?? [] as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="start_date" placeholder="{{__('common.start_date')}}">
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
                        </div>

                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <span class="pull-left visible-print">{{__('common.print_date')}} : {{ $todaysDate }}</span>
                                <table id="returnTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                                <img src="{{ asset($orgbios[0]->header ?? '') }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                                <center> {{__('buy.return_list')}} </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width:5%">{{__('common.number')}}</th>
                                            <th style="width:12%">{{__('buy.return_number')}}</th>
                                            <th style="width:8%">{{__('common.bill')}}</th>
                                            <th style="width:12%">{{__('order.supplier_name')}}</th>
                                            <th style="width:12%">{{__('sales.item')}}</th>
                                            <th style="width:8%">{{__('common.unit')}}</th>
                                            <th style="width:8%">{{__('sales.quantity')}}</th>
                                            <th style="width:10%">{{__('common.total_price')}}</th>
                                            <th style="width:8%">{{__('common.date')}}</th>
                                            <th style="width:8%">{{__('common.car')}}</th>
                                            <th style="width:8%">{{__('common.view')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="7">{{__('common.total')}}</td>
                                            <td id="totalSum"></td>
                                            <td colspan="3"></td>
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

<!-- View Return Modal -->
<div class="modal fade" id="viewReturnModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:800px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> <i class="fas fa-eye"></i> {{ __('buy.return_details') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewReturnBody">
                <div id="viewReturnLoader" style="display:none; text-align: center; padding: 20px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i> {{ __('common.loading') }}
                </div>
                <div id="viewReturnContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Return Modal -->
<div class="modal fade" id="editReturnModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:800px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> <i class="fas fa-eye"></i> {{ __('buy.return_details') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewReturnBody">
                <div id="editReturnLoader" style="display:none; text-align: center; padding: 20px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i> {{ __('common.loading') }}
                </div>
                <div id="editReturnContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).on('click', '.datepicker-icon', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $input = $(this).closest('.input-group').find('input');
    if ($input.length) {
        $input.datepicker('show');
    }
});

$(document).ready(function() {
    // Initialize DataTable
    fetchReturnList();

    // Filter button click
    $('#btn-filter').click(function() {
        if ($.fn.DataTable.isDataTable('#returnTable')) {
            $('#returnTable').DataTable().ajax.reload(null, false);
        }
    });

    // Reset button
    $('#btn-reset').on('click', function() {
        $('#return_number').val('');
        $('#billno').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#supplier_id').val('');
        $('#returnTable').DataTable().ajax.reload(null, false);
    });

    // Enter key search
    $('#return_number, #billno, #supplier_id, #start_date, #end_date').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn-filter').click();
        }
    });
});

function fetchReturnList() {
    var table = $('#returnTable');

    if (!$.fn.DataTable.isDataTable(table)) {
        table.DataTable({
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
                url: '{{ route("return.getData") }}',
                data: function(d) {
                    d.return_number = $('#return_number').val();
                    d.billno = $('#billno').val();
                    d.supplier_id = $('#supplier_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'return_number', name: 'return_number' },
                { data: 'billno', name: 'billno' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'item_name', name: 'item_name' },
                { data: 'unit_name', name: 'unit_name' },
                { data: 'quantity', name: 'quantity' },
                { data: 'total', name: 'total' },
                { data: 'return_date', name: 'return_date' },
                { data: 'car_name', name: 'car_name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            drawCallback: function() {
                var api = this.api();
                var totalSum = api
                    .column(7, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        var numA = parseFloat(a.toString().replace(/,/g, '')) || 0;
                        var numB = parseFloat(b.toString().replace(/,/g, '')) || 0;
                        return numA + numB;
                    }, 0);

                var formattedSum = totalSum.toLocaleString(undefined, { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                });

                $('#totalSum').html(formattedSum);
            }
        });
    } else {
        table.DataTable().ajax.reload(null, false);
    }
}

// =============================================
// VIEW RETURN
// =============================================
$(document).on('click', '.viewReturn', function() {
    var id = $(this).data('id');
    
    $('#viewReturnModal').modal('show');
    $('#viewReturnLoader').show();
    $('#viewReturnContent').hide();
    $('#viewReturnContent').html('');
    
    $.ajax({
        url: '/return/view/' + id,
        type: 'GET',
        success: function(response) {
            $('#viewReturnLoader').hide();
            $('#viewReturnContent').show();
            $('#viewReturnContent').html(response);
        },
        error: function(xhr) {
            $('#viewReturnLoader').hide();
            $('#viewReturnContent').show();
            $('#viewReturnContent').html('<div class="alert alert-danger">{{ __("common.error_occurred") }}</div>');
        }
    });
});

// =============================================
// EDIT RETURN
// =============================================
$(document).on('click', '.editReturn', function() {
    var id = $(this).data('id');
    
    $('#editReturnModal').modal('show');
    $('#editReturnLoader').show();
    $('#editReturnContent').hide();
    $('#editReturnContent').html('');
    
    $.ajax({
        url: '/return/edit/' + id,
        type: 'GET',
        success: function(response) {
            $('#editReturnLoader').hide();
            $('#editReturnContent').show();
            $('#editReturnContent').html(response);
        },
        error: function(xhr) {
            $('#editReturnLoader').hide();
            $('#editReturnContent').show();
            $('#editReturnContent').html('<div class="alert alert-danger">{{ __("common.error_occurred") }}</div>');
        }
    });
});



// =============================================
// NOTIFICATION FUNCTION
// =============================================
function showNotification(message, type = 'info', from = 'top', align = 'center') {
    $.notify({
        message: '<span style="font-size:14px;">' + message + '</span>',
        title: '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{ __("settings.message") }}</span>',
        icon: 'fa fa-bell'
    }, {
        type: type,
        placement: {
            from: from,
            align: align
        },
        time: 3000
    });
}
</script>
@endsection