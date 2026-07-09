@extends('layouts.app')

@section('content')

<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px; text-align:center;">
                            <a href="{{ route('boughtListBasedItem.create') }}" class="pull-right">
                                <button type="button" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> {{__('common.add')}}
                                </button>
                            </a>
                            <span class="card-title"> {{__('buy.buy_title')}} </span>
                            <div class="pull-left" style="width:150px">
                                <button type="button" class="responsive_button btn btn-sm visible-xs"
                                  id="filterToggleBtn" onclick="toggleFilterForm()" style="margin-left:2px; margin-top:2px;">
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

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="item_name" placeholder="{{__('sales.item')}}" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id">
                                            <option value=""> {{__('common.currency')}} </option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-1 col-sm-6 col-xs-6">
                                        <input class="form-control" id="bill_number" placeholder="{{__('common.bill')}}">
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
                                    <div class="col-md-1 col-sm-6 col-xs-12 mt-2">
                                        <button class="btn mybtn search_btn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <input type="hidden" id="tax_activation" value="{{ $orgbios[0]->tax_activation ?? 0 }}" />
                                <span class="pull-left visible-print">{{__('common.print_date')}} : {{ $todaysDate }}</span>
                                <table id="boughtItemTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            @if(($orgbios[0]->tax_activation ?? 0) == 1)
                                            <td colspan="12">
                                            @else
                                            <td colspan="9">
                                            @endif 
                                            <img src="{{ asset($orgbios[0]->header ?? '') }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            @if(($orgbios[0]->tax_activation ?? 0) == 1)
                                            <td colspan="12">
                                            @else
                                            <td colspan="9">
                                            @endif 
                                                <center> {{__('buy.buy_title')}} </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} &nbsp; </th>
                                            <th> {{__('common.bill')}} </th>
                                            <th> {{__('order.supplier_name')}} </th>                                            
                                            <th>{{ __('sales.item') }}</th>
                                            <th>{{ __('sales.quantity') }}</th>
                                            <th>{{ __('common.unit') }}</th>
                                            <th>{{ __('common.unit_price') }}</th>
                                            @if(($orgbios[0]->tax_activation ?? 0) == 1)
                                            <th>% {{__('buy.tax')}}</th>
                                            <th>{{__('buy.buy_tax_price_s')}}</th>
                                            <th>{{__('buy.buy_up_vat')}}</th>
                                            @endif 
                                            <th>{{ __('common.total_price') }}</th>
                                            <th>{{ __('common.date') }}</th>
                                            <th>{{ __('buy.return') }}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="3">{{__('common.total')}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            @if(($orgbios[0]->tax_activation ?? 0) == 1)
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            @endif 
                                            <td id="totalSum"></td>
                                            <td></td>
                                            <td></td>
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

<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:800px !important">
            <form action="{{ route('return.addReturn')}}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"> {{__('common.edit')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="ModalContent"></div>
                <div id="loading" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin font-20"></i> {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{__('common.close')}}</button>
                <button type="submit" id="return_submit_button" class="btn btn-success btn-sm m-r-10" >{{__('common.save')}}</button>
            </div>
            </form>
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
    fetchList();

    // Filter button click
    $('#btn-filter').click(function() {
        if ($.fn.DataTable.isDataTable('#boughtItemTable')) {
            $('#boughtItemTable').DataTable().ajax.reload(null, false);
        }
    });

    // Enter key search
    $('#customer_name, #item_name, #currency_id, #bill_number, #start_date, #end_date').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn-filter').click();
        }
    });
});

function fetchList() {
    var flag = parseInt($('#tax_activation').val()) || 0;
    var table = $('#boughtItemTable');

    // =============================================
    // DEFINE COLUMNS BASED ON TAX ACTIVATION
    // =============================================
    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
        { data: 'billno', name: 'billno' },
        { data: 'account_relation.name', name: 'account_relation.name' },
        { data: 'pre_list_relation.name', name: 'pre_list_relation.name' },
        { data: 'amount', name: 'amount' },
        { data: 'unit_relation.name', name: 'unit_relation.name' },
        { data: 'buy_up', name: 'buy_up' }
    ];

    // Add tax columns if tax is enabled
    if (flag === 1) {
        columns.push(
            { data: 'buy_tax_per', name: 'buy_tax_per' },
            { data: 'buy_tax_price', name: 'buy_tax_price' },
            { data: 'buy_up_vat', name: 'buy_up_vat' }
        );
    }

    // Add remaining columns - FIXED: Added missing comma
    columns.push(
        { data: 'total', name: 'total' },
        { data: 'bought_item_relation.idate', name: 'bought_item_relation.idate' },
        { data: 'return', name: 'return', orderable: false, searchable: false }
    );

    // =============================================
    // INITIALIZE DATATABLE
    // =============================================
    if (!$.fn.DataTable.isDataTable(table)) {
        table.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'همه']
            ],
            ajax: {
                url: '{{ route("boughtListBasedItem.data") }}',
                data: function(d) {
                    d.customer_name = $('#customer_name').val();
                    d.currency_id = $('#currency_id').val();
                    d.bill_number = $('#bill_number').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.item_name = $('#item_name').val();
                    d.tax_activation = flag;
                }
            },
            columns: columns,
            order: [[1, 'desc']],
            drawCallback: function() {
                var api = this.api();
                var totalColumnIndex = flag === 1 ? 10 : 7; // FIXED: Correct column index for total

                // Calculate sum for total column
                var totalSum = api
                    .column(totalColumnIndex, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        var numA = parseFloat(a.toString().replace(/,/g, '')) || 0;
                        var numB = parseFloat(b.toString().replace(/,/g, '')) || 0;
                        return numA + numB;
                    }, 0);

                // Format the sum
                var formattedSum = totalSum.toLocaleString(undefined, { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                });

                // Update footer
                $('#totalSum').html(formattedSum);
            }
        });
    } else {
        table.DataTable().ajax.reload(null, false);
    }
}


$(document).on('click', '.returnItem', function() {
    var $this = $(this);
    var id = $this.data('id');
 $('#editModal').modal('show');
 $('#loading').show();
    $.ajax({
        url: `/return/getSingleRecordForReturn/${id}`,
        type: 'GET',
        success: (result) => {
            $('#ModalContent').html(result);
            $('#loading').hide();

            // Initialize Select2 after the form has been injected
            $(".select2").select2();
        },
        error: () => {
            $('#loading').hide();
            alert('اطلاعات یافت نشد');
        }
    });
});



// ---------------------------------------------------


// =============================================
// CALCULATE RETURN TOTAL ON AMOUNT CHANGE
// =============================================
$(document).on('input', '#return_amount', function() {
    var amount = parseFloat($(this).val()) || 0;
    var unitPrice = parseFloat($('#display_unit_price').val()) || 0;
    var taxPercentage = parseFloat($('#tax_activation').val()) || 0;
    
    var total = amount * unitPrice;
    var tax = taxPercentage > 0 ? (total * taxPercentage / 100) : 0;
    
    $('#return_total').text(total.toFixed(2));
    if (taxPercentage > 0) {
        $('#return_tax').text(tax.toFixed(2));
    }
});

// =============================================
// HANDLE RETURN BUTTON CLICK - Open Modal



$(document).on('click', '.returnItem2', function() {
    var $this = $(this);
    var id = $this.data('id');
    var billno = $this.data('billno');
    
    // Reset form
    $('#returnForm')[0].reset();
    $('#return_id').val(id);
    $('#return_billno').val(billno);
    $('#return_amount').val('');
    $('#return_reason').val('');
    $('#return_notes').val('');
    $('#return_total').text('0.00');
    $('#return_tax').text('0.00');
    
    // Show modal with loader
    $('#returnModal').modal('show');
    $('#returnModalLoader').show();
    $('#returnFormWrapper').hide();
    
    // FIXED: Correct URL
    $.ajax({
        url: '/boughtListBasedItem/get-return-data/' + id,
        type: 'GET',
        success: function(response) {
            $('#returnModalLoader').hide();
            $('#returnFormWrapper').show();
            
            if (response.status === 'success') {
                var data = response.data;
                $('#display_billno').val('BUY_' + data.billno);
                $('#display_supplier').val(data.supplier_name);
                $('#display_item').val(data.pre_list_name);
                $('#display_unit').val(data.unit_name);
                $('#display_unit_price').val(data.unit_price_vat || data.unit_price);
                $('#cur_amount').val(data.current_amount);
                $('#max_return_label').text(data.current_amount);
                $('#return_amount').attr('max', data.current_amount);
            } else {
                showNotification(response.message || 'Error loading data', 'danger');
            }
        },
        error: function() {
            $('#returnModalLoader').hide();
            showNotification('Error loading return data', 'danger');
        }
    });
});

// =============================================
// CONFIRM RETURN BUTTON
// =============================================
$(document).on('click', '#confirmReturnBtn', function() {
    var id = $('#return_id').val();
    var billno = $('#return_billno').val();
    var returnAmount = $('#return_amount').val();
    var reason = $('#return_reason').val();
    var notes = $('#return_notes').val();
    var maxAmount = $('#cur_amount').val();
    
    // Validate
    if (!returnAmount || parseFloat(returnAmount) <= 0) {
        showNotification('Please enter a valid return quantity', 'danger');
        $('#return_amount').focus();
        return;
    }
    
    if (parseFloat(returnAmount) > parseFloat(maxAmount)) {
        showNotification('Return quantity cannot exceed available quantity', 'danger');
        $('#return_amount').focus();
        return;
    }
    
    // Show confirmation
    if (!confirm('Are you sure you want to return ' + returnAmount + ' items from Bill: BUY_' + billno + '?')) {
        return;
    }
    
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    
    // FIXED: Correct URL
    $.ajax({
        url: '/boughtListBasedItem/process-return',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
            billno: billno,
            return_amount: returnAmount,
            reason: reason,
            notes: notes
        },
        success: function(response) {
            $btn.prop('disabled', false).html('<i class="fas fa-check"></i> {{ __('buy.confirm_return') }}');
            
            if (response.status === 'success') {
                showNotification(response.message, 'success', 'top', 'center');
                $('#returnModal').modal('hide');
                $('#boughtItemTable').DataTable().ajax.reload(null, false);
            } else {
                showNotification(response.message || 'Error processing return', 'danger', 'top', 'center');
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fas fa-check"></i> {{ __('buy.confirm_return') }}');
            var message = xhr.responseJSON?.message || 'Error processing return';
            showNotification(message, 'danger', 'top', 'center');
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