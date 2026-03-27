@extends('layouts.app')

@section('content')


<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px;">
                            <span class="card-title">    {{__('menu.warehouse_all_list')}}  </span>
                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                        </div>


                        <div class="filterForm" id="searchWrapper1">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <input type="text" id="item_name" placeholder="{{__('common.item_name')}}" class="form-control">
                                    </div>

                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <input type="text" id="wh_name" placeholder="{{__('wh.warehouse')}}" class="form-control">
                                    </div>

                                    <div class="col-md-3 col-sm-6 col-xs-6 m-b-4">
                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id">
                                            <!-- <option value="">  واحد پولی </option> -->
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-3 col-sm-6 col-xs-6">
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
                                <table id="warehouseItemTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="13">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="13">
                                            <center> {{__('menu.warehouse_all_list')}}  </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} &nbsp; </th>
                                            <th> {{__('common.name')}} </th>
                                            <th> {{__('wh.warehouse')}} </th>
                                            <th> {{__('common.currency')}}  </th>                                            
                                            <th> {{__('common.unit')}} </th>
                                            <th> {{__('common.in')}} </th>
                                            <th> {{__('common.out')}} </th>
                                            <th> {{__('common.available')}}  </th>
                                            <th> {{__('wh.last_bought')}} </th>
                                            <th> {{__('wh.average')}}</th>
                                            <th> {{__('buy.sold_price')}} </th>
                                            <th> {{__('common.total')}} </th> 
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
function showNotification(message, type = 'info', from = 'top', align = 'left', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> پیام </span>';
    
    if (style === "withicon") {
        content.icon = 'fa fa-bell';
    } else {
        content.icon = 'none';
    }
    content.url = '#';
    content.target = '_blank';

    $.notify(content, {
        type: type, // Default, Primary, Secondary, Info, Success, Warning, Danger
        placement: {
            from: from, // top, bottom
            align: align // right, center, left
        },
        time: 500
    });
}
</script>

<script>
$(document).ready(function() {
    fetchList();

    // Move the filter button click event outside
    $('#btn-filter').click(function() {
        $('#warehouseItemTable').DataTable().ajax.reload(null, false); // Reload data without resetting pagination
    });
});


function fetchList() {
    let warehouseItemTable = $('#warehouseItemTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(warehouseItemTable)) {
        warehouseItemTable.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,   
            lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'همه']
                ],
            ajax: {  
                url: '{{ route("warehousesList.allData") }}',
                // url: '{{ route("boughtList.data") }}',
                data: function (d) {
                    d.warehouse_id = $('#warehouse_id').val();
                    d.item_name = $('#item_name').val();
                    d.currency_id = $('#currency_id').val();
                    d.wh_name = $('#wh_name').val();
                    // alert(d.warehouse_id);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'prelist', name: 'prelist' },
                { data: 'wh_name', name: 'wh_name' },
                { data: 'currency', name: 'currency' },
                { data: 'unit', name: 'unit' },
                { data: 'in_amount', name: 'in_amount' },
                { data: 'out_amount', name: 'out_amount' }, 
                { data: 'available_amount', name: 'available_amount'},
                { data: 'bought_up', name: 'bought_up' },
                { data: 'avg_up', name: 'avg_up' },
                { data: 'sell_up', name: 'sell_up' },
                { data: 'available_total', name: 'available_total' },
            ],
            drawCallback: function () {
                var api = this.api();

                function sumColumn(index) {
                    return api
                        .column(index, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            var numA = parseFloat(a.toString().replace(/,/g, '')) || 0;
                            var numB = parseFloat(b.toString().replace(/,/g, '')) || 0;
                            return numA + numB;
                        }, 0)
                        .toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
                $(api.column(7).footer()).html(sumColumn(7));
                $(api.column(8).footer()).html(sumColumn(8));
                $(api.column(9).footer()).html(sumColumn(9));
                $(api.column(10).footer()).html(sumColumn(10));
            }
        });

    } else {
        warehouseItemTable.DataTable().ajax.reload(null, false);
    }
}
</script>
@endsection

