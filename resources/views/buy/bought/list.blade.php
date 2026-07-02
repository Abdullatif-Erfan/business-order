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
                            <input type="hidden" id="tax_activation" value="{{ $orgbios[0]->tax_activation }}" >
                            <a href="{{ route('boughtList.create') }}" class="pull-right">
                                <button type="button" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> {{__('common.add')}}
                                </button>
                            </a>
                             <!-- Generate Invoice Button -->
                            <button type="button" class="btn pull-right m-r-10 btn-success btn-sm" id="generateInvoiceBtn" 
                            style="display:none;">
                                <i class="fas fa-file-invoice"></i> {{__('buy.generate_invoice')}}
                            </button>
                            <span class="card-title">  {{__('buy.buy_title')}} </span>
                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                            
                            <!-- Invoice Button -->
                            <!-- <a href="{{ route('boughtList.invoices') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-invoice"></i> {{ __('buy.invoices') }}
                            </a> -->

                           
                        </div>

                         <div class="filterForm" id="searchWrapper1">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="customer_name" placeholder="{{__('order.supplier_name')}}" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id">
                                            <!-- <option value="">  {{__('common.currency')}} </option> -->
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input class="form-control" id="bill_number" placeholder="{{__('common.bill')}}">
                                    </div>

                                    <div class="col-md-2">
                                         <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="start_date"  placeholder="{{__('common.start_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>
                                     <div class="col-md-3">
                                        <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="end_date" placeholder="{{__('common.end_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>

                                    <div class="col-md-1 col-sm-6 col-xs-6">
                                        <button class="btn mybtn search_btn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
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
                                            <td colspan="10">
                                                <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" 
                                                style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="10">
                                                <center> {{__('buy.buy_title')}} </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width:5%">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th> {{__('common.number')}} &nbsp; </th>
                                            <th> {{__('common.bill')}} </th>
                                            <th> {{__('order.supplier_name')}} </th>                                            
                                            <th> {{__('common.total_price')}} </th>
                                            <th> {{__('buy.cache_paid')}} </th>
                                            <th> {{__('buy.loan')}} </th>
                                            <th>  {{__('common.currency')}} </th>
                                            <th> {{__('common.date')}} </th>
                                            <th> {{__('common.details')}} </th>
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
    fetchList();

    // Move the filter button click event outside
    $('#btn-filter').click(function() {
        $('#boughtItemTable').DataTable().ajax.reload(null, false);
    });

    // Select All checkbox
    $('#selectAll').on('click', function() {
        $('.row-checkbox').prop('checked', this.checked);
        toggleGenerateButton();
    });

    // Individual checkbox
    $(document).on('change', '.row-checkbox', function() {
        toggleGenerateButton();
    });
});

function toggleGenerateButton() {
    var checked = $('.row-checkbox:checked').length;
    if (checked > 0) {
        $('#generateInvoiceBtn').show();
    } else {
        $('#generateInvoiceBtn').hide();
    }
}

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
                    d.currency_id = $('#currency_id').val();
                    d.bill_number = $('#bill_number').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.tax_activation = $('#tax_activation').val();
                }
            },
            columns: [
                { 
                    data: 'id', 
                    name: 'checkbox',
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        // Check if invoice already generated
                        var hasInvoice = row.has_invoice || false;
                        if (hasInvoice) {
                            return '<span class="badge badge-success" title="{{ __("buy.invoice_generated") }}"><i class="fas fa-check"></i></span>';
                        }
                        return '<input type="checkbox" class="row-checkbox" value="' + data + '">';
                    }
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'billno', name: 'billno' },
                { data: 'customer_relation.name', name: 'customer_relation.name' },
                { data: 'total', name: 'total' },
                { data: 'cur_pay', name: 'cur_pay' },
                { data: 'remained', name: 'remained' },
                { data: 'currencyRelation', name: 'currencyRelation' },
                { data: 'idate', name: 'idate' },
                { data: 'view', name: 'view', orderable: false, searchable: false }
            ],
            drawCallback: function () {
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
    
                $(api.column(4).footer()).html(sumColumn(4));
                $(api.column(5).footer()).html(sumColumn(5));
                $(api.column(6).footer()).html(sumColumn(6));
                
                // Toggle generate button after draw
                toggleGenerateButton();
            }
        });

    } else {
        boughtItemTable.DataTable().ajax.reload(null, false);
    }
}

// Generate Invoice
$('#generateInvoiceBtn').on('click', function() {
    var selectedIds = [];
    $('.row-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    
    if (selectedIds.length === 0) {
        showNotification('{{ __("buy.select_at_least_one") }}', 'warning');
        return;
    }
    
    if (confirm('{{ __("buy.confirm_generate_invoice") }}')) {
        $.ajax({
            url: '{{ route("boughtList.generateInvoice") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                bought_item_ids: selectedIds
            },
            success: function(response) {
                if (response.status === 'success') {
                    showNotification(response.message, 'success');
                    window.location.href = '{{ url("boughtList/invoice") }}/' + response.invoice_id;
                } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function(xhr) {
                showNotification('{{ __("common.error_occurred") }}', 'danger');
            }
        });
    }
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