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
                            <a href="{{ route('sales.create') }}" class="pull-right">
                                <button type="button" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> {{__('common.add')}}
                                </button>
                            </a>
                            <span class="card-title">   {{__('sales.list_title')}} </span>
                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                        </div>


                        <div class="filterForm" id="searchWrapper1">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="customer_name" placeholder="{{__('sales.customer')}}" class="form-control">
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

                                    <!-- <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="factor" placeholder="فاکتور" class="form-control">
                                    </div> -->

                                    <div class="col-md-2">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#start_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="start_date" id="start_date"
                                            data-targetselector="#start_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="{{__('common.start_date')}}"  data-placement="right" data-englishnumber="true"  >
                                        </div>
							     	</div>
                                

                                     <div class="col-md-2">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#end_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="end_date" id="end_date"
                                            data-targetselector="#end_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="{{__('common.end_date')}}"  data-placement="right" data-englishnumber="true" >
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
                                <table id="salesItemTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                                <center> {{__('sales.list_title')}} </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} &nbsp; </th>
                                            <th> {{__('common.bill')}} </th>
                                            <th>{{ __('sales.customer') }}</th>
                                            <th>{{ __('sales.sale_type') }}</th>
                                            <th>{{ __('sales.quantity') }}</th>
                                            <th>{{ __('sales.unit') }}</th>
                                            <th>{{ __('sales.unit_price') }}</th>
                                            <th>{{ __('sales.profit') }}</th>
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
                                            <td></td>
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


<!-- For Persian Date Picker -->
<script src="{{ asset('assets/datepicker/jalaali.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function() {
    fetchList();

    // Move the filter button click event outside
    $('#btn-filter').click(function() {
        $('#salesItemTable').DataTable().ajax.reload(null, false); // Reload data without resetting pagination
    });
});


function fetchList() {
    let salesItemTable = $('#salesItemTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(salesItemTable)) {
        salesItemTable.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,   // 👈 IMPORTANT
            lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'همه']
                ],
            ajax: {  
                url: '{{ route("soldItemList.data") }}',
                data: function (d) {
                    d.customer_name = $('#customer_name').val();
                    d.currency_id = $('#currency_id').val();
                    d.bill_number = $('#bill_number').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.item_name = $('#item_name').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'billno', name: 'billno' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'product_name', name: 'product_name' },
                { data: 'amount', name: 'amount' },
                { data: 'unit', name: 'unit' },
                { data: 'sell_up', name: 'sell_up' },
                { data: 'profit', name: 'profit' },
                { data: 'total', name: 'total' },
                { data: 'date', name: 'date' }
            ],
            drawCallback: function () {
                var api = this.api();

                // Helper function for the modulo operation to check if it's an integer
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

                            // Format the sum based on whether it has decimals
                            if (fmod(sum, 1) === 0) {
                                return sum.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                            } else {
                                return sum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }

                        }, 0)
                        .toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                $(api.column(4).footer()).html(sumColumn(4));
                $(api.column(6).footer()).html(sumColumn(6));
                $(api.column(7).footer()).html(sumColumn(7));
                $(api.column(8).footer()).html(sumColumn(8));
            }
        });

    } else {
        salesItemTable.DataTable().ajax.reload(null, false);
    }
}
</script>
@endsection

