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

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="item_name" placeholder="{{__('sales.item')}}" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id">
                                            <option value="">  {{__('common.currency')}} </option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-1 col-sm-6 col-xs-6">
                                        <input class="form-control" id="bill_number" placeholder="{{__('common.bill')}}">
                                    </div>

                                     <div class="col-md-2  col-sm-6 col-xs-6">
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
                                    <div class="col-md-1 col-sm-6 col-xs-12 mt-2">
                                        <button class="btn mybtn search_btn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /filter_form -->


                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <input type="hidden" id="tax_activation" value="{{ $orgbios[0]->tax_activation}}" />
                                <span class="pull-left visible-print">{{__('common.print_date')}} : {{ $todaysDate }}</span>
                                <table id="boughtItemTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                             @if($orgbios[0]->tax_activation === 1)
                                             <td colspan="12">
                                             @else
                                             <td colspan="9">
                                             @endif 
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                             @if($orgbios[0]->tax_activation === 1)
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
                                            <th>{{ __('sales.unit') }}</th>
                                            <th>{{ __('sales.unit_price') }}</th>
                                           @if($orgbios[0]->tax_activation === 1)
                                            <th>% {{__('buy.tax')}}</th>
                                            <th>{{__('buy.buy_tax_price_s')}}</th>
                                            <th>{{__('buy.buy_up_vat')}}</th>
                                            @endif 
                                            <th>{{ __('sales.total_price') }}</th>
                                            <th>{{ __('sales.date') }}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="3">{{__('common.total')}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            @if($orgbios[0]->tax_activation === 1)
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            @endif 
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot> 
                                </table>
                            </div> <!-- /table responsive -->
                        </div> <!-- /card-body -->
                    </div> <!-- /card -->
                </div> <!-- /col-md-12 -->
            </div> <!-- /row -->
        </div> <!-- /page-inner -->
    </div> <!-- /content -->
</div> <!-- /main content -->

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
});

function fetchList() {
    var flag = parseInt($('#tax_activation').val()) || 0;
    var boughtItemTable = $('#boughtItemTable');

    // Define columns based on flag
    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
        { data: 'billno', name: 'billno' },
        { data: 'account_relation.name', name: 'account_relation.name' },
        { data: 'pre_list_relation.name', name: 'pre_list_relation.name' },
        { data: 'amount', name: 'amount' },
        { data: 'unit_relation.name', name: 'unit_relation.name' },
        { data: 'buy_up', name: 'buy_up' }
    ];

    // Add tax columns if flag is 1
    if (flag === 1) {
        columns.push(
            { data: 'buy_tax_per', name: 'buy_tax_per' },
            { data: 'buy_tax_price', name: 'buy_tax_price' },
            { data: 'buy_up_vat', name: 'buy_up_vat' }
        );
    }

    // Add remaining columns
    columns.push(
        { data: 'total', name: 'total' },
        { data: 'bought_item_relation.idate', name: 'bought_item_relation.idate' }
    );

    // Check if DataTable is already initialized
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
                url: '{{ route("boughtListBasedItem.data") }}',
                data: function(d) {
                    d.customer_name = $('#customer_name').val();
                    d.currency_id = $('#currency_id').val();
                    d.bill_number = $('#bill_number').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.item_name = $('#item_name').val();
                    d.tax_activation = $('#tax_activation').val();
                }
            },
            columns: columns,
            drawCallback: function() {
                var api = this.api();

                function fmod(a, b) {
                    return a - (b * Math.floor(a / b));
                }

                function sumColumn(index) {
                    return api
                        .column(index, { page: 'current' })
                        .data()
                        .reduce(function(a, b) {
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

                // Total column index (depends on flag)
                // Without tax: total is at index 6
                // With tax: total is at index 8 (because 2 tax columns added)
                // Tax columns (only if flag is 1)
                if (flag === 1) {
                    // buy_tax_per is at index 7
                    $(api.column(8).footer()).html(sumColumn(8));
                    $(api.column(9).footer()).html(sumColumn(9)); // buy_tax_price is at index 8
                    $(api.column(10).footer()).html(sumColumn(10));
                } else {
                    $(api.column(6).footer()).html(sumColumn(6));
                    $(api.column(7).footer()).html(sumColumn(7));

                }
            }
        });
    } else {
        boughtItemTable.DataTable().ajax.reload(null, false);
    }
}
</script>
@endsection

