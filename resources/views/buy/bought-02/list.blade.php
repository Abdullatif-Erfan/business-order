@extends('layouts.app')

@section('content')

@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
    // Show the notification using the data from the session
    $(document).ready(function(){
        showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
    });
</script>
@endif


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
                                    <i class="fas fa-plus"></i> ثبت خریداری جدید
                                </button>
                            </a>
                            <span class="card-title"> لیست خرید </span>

                            <button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button>
                        </div>

                        <div class="filterForm" id="searchWrapper1">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <input type="text" id="customer_name" placeholder="فروشنده" class="form-control">
                                    </div>
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="pre_list_name" placeholder="نوع خرید" class="form-control">
                                    </div>
                                    <div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id">
                                            <option value="">  واحد پولی </option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input class="form-control" id="bill_number" placeholder="بل نمبر">
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
                            <div class="table_responsive" id="print_area" style="padding:5px;">
                                <span class="pull-left visible-print">تاریخ چاپ : {{ $todaysDate }}</span>
                                <table id="boughtItemTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="13">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="13">
                                                <center> لیست خرید </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> شماره &nbsp; </th>
                                            <th> نمبربل </th>
                                             <th> فروشنده </th>
                                           <th> نوع خرید </th>
                                            <th> تعداد </th>
                                            <th> واحد </th>
                                            <th> فی واحد</th>
                                            <th> قیمت <br /> مجموعی </th>
                                            <th> رسید نقد </th>
                                            <th> قرض </th>
                                            <th> تاریخ </th>
                                            <th> انتقال </th> 
                                            <th> جزییات </th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="7">مجموع</td>
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

<script type="text/javascript">
    $('#input1').change(function() {  
        var $this = $(this), value = $this.val();  
        alert(value);
    });

    $('#textbox1').change(function () {  
        var $this = $(this), value = $this.val(); 
        alert(value); 
    });

    $('[data-name="disable-button"]').click(function() {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', true);
    });

    $('[data-name="enable-button"]').click(function () {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', false);
    });
</script>


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
});

function fetchList() {
    const boughtItemTable = $('#boughtItemTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(boughtItemTable)) {
        // Initialize DataTable if not already initialized
        boughtItemTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("boughtList.data") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'billno', name: 'billno' },
                { data: 'account_name', name: 'account_name' },
                { data: 'pre_list_name', name: 'pre_list_name' },
                { data: 'amount', name: 'amount' },
                { data: 'unit_name', name: 'unit_name' },
                { data: 'bought_up', name: 'bought_up' },
                { data: 'total', name: 'total' },
                { data: 'cur_pay', name: 'cur_pay' },
                { data: 'remained', name: 'remained' },
                { data: 'idate', name: 'idate' },
                { data: 'is_moved', name: 'is_moved',  orderable: false, searchable: false  },
                { data: 'view', name: 'view', orderable: false, searchable: false }
            ],
            drawCallback: function () {
                var api = this.api();

                // Function to calculate sum for a given column index
                function sumColumn(index) {
                    return api
                        .column(index, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            var numA = parseFloat(a.toString().replace(/,/g, '')) || 0;
                            var numB = parseFloat(b.toString().replace(/,/g, '')) || 0;
                            return numA + numB;
                        }, 0)
                        .toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); // Format number
                }

                // Calculate sums for columns 8 and 9
                var sumCol7 = sumColumn(7);
                var sumCol8 = sumColumn(8);
                var sumCol9 = sumColumn(9);

                // Update footer cells
                $(api.column(7).footer()).html(sumCol7);
                $(api.column(8).footer()).html(sumCol8);
                $(api.column(9).footer()).html(sumCol9);
            }

        });
    } else {
        // If already initialized, reload the data
        boughtItemTable.DataTable().ajax.reload(null, false); // Prevent table from resetting pagination
    }
}
</script>
@endsection

