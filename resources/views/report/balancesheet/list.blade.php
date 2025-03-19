@extends('layouts.app')
@section('title', 'بیلانس شیت')

@section('content')




<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                        <div class="card-header" style="padding: 11px 20px !important;">
                            
                            <strong>  بیلانس شیت   </strong>
                            <button class="printBtn m-b-10" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>

                            <button type="button" class="btn btn-sm mybtn visible-xs" onclick="show_search_form(1)">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>

                        {{-- Filter Form --}}
                        <div class="filterForm" id="searchWrapper1">  
                            <div class="col-md-12">
                                <div class="row">
                                

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <select class="form-control select2" id="account_type_id" style="width:100%">
                                            <!-- <option value=""> حساب اصلی </option> -->
                                            @foreach($accountTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>


                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <select class="form-control select2" id="account_id" style="width:100%">
                                            <option value=""> حساب </option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>

                                    <div class="col-md-2  col-sm-6 col-xs-6">
                                        <select class="form-control select2" id="currency_id" style="width:100%">
                                            <!-- <option value=""> واحد پولی </option> -->
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>

                                    
                                    <div class="col-md-2  col-sm-6 col-xs-6">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#start_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="start_date" id="start_date"
                                            data-targetselector="#start_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ شروع"  data-placement="right" data-englishnumber="true"  >
                                        </div>
							     	</div>
                                


                                     <div class="col-md-3  col-sm-6 col-xs-6">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#end_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="end_date" id="end_date"
                                            data-targetselector="#end_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ ختم / الی امروز"  data-placement="right" data-englishnumber="true" >
                                        </div>
							     	</div>

                                  

                                    <div class="col-md-1">
                                        <button class="btn mybtn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div> 
                        </div>
                       
                        {{-- Card Body --}}
                        <div class="card-body">
                            <div class="table-responsive" id="print_area">
                                <span class="pull-left visible-print">تاریخ چاپ: {{ now()->format('Y-m-d') }}</span>
                                <table id="journalTable" class="display responsive nowrap table table-bordered" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="9">
                                              <img src="{{ asset($orgbios[0]->header)  }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                            
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="9">
                                                <center>
                                                      بیلانس شیت   
                                                </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> شماره </th>
                                             <th> حساب </th>
                                            <th> آمد نقد</th>
                                            <th>رفت نقد</th>
                                            <th> قرض</th>
                                            <th> طلب</th>
                                            <th> بیلانس </th>
                                            <th>واحد</th>
                                            <th>تشخیص</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="2">مجموع</td>
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
        let table = $('#journalTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("balancesheet.data") }}',
                data: function (d) {
                    d.account_id = $('#account_id').val();
                    d.currency_id = $('#currency_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.account_type_id = $('#account_type_id').val();
                },
                error: function(xhr, status, error) {
                console.log("Error fetching data: ", error);
                   $('#journalTable tbody').html('<tr><td colspan="12" class="text-center">No records found</td></tr>');
               }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'cache_recieved', name: 'cache_recieved' },
                { data: 'cache_paid', name: 'cache_paid' },
                { data: 'loan_recieved', name: 'loan_recieved' },
                { data: 'loan_paid', name: 'loan_paid' },
                { data: 'balance', name: 'balance' },
                { data: 'currency', name: 'currency' },
                { data: 'result_label', name: 'result_label' },
            ],
            drawCallback: function (settings) {
                var api = this.api();

                // Handle case where no records exist
                if (api.rows().data().length === 0) {
                    $('#journalTable tbody').html('<tr><td colspan="12" class="text-center">No records found</td></tr>');
                    
                    // Clear the footer when no records are available
                    $(api.column(2).footer()).html('');
                    $(api.column(3).footer()).html('');
                    $(api.column(4).footer()).html('');
                    $(api.column(5).footer()).html('');
                    $(api.column(6).footer()).html('');
                    
                    return; // Exit early to avoid unnecessary calculations
                }

                // Function to sum columns and return raw numbers
                function sumColumn(index) {
                    return api
                        .column(index, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            // Make sure to parse floats and handle commas correctly
                            var numA = parseFloat((a || '0').toString().replace(/,/g, '')) || 0;
                            var numB = parseFloat((b || '0').toString().replace(/,/g, '')) || 0;
                            var sum = numA + numB;
                            return sum;
                        }, 0);
                }

                // Calculate the sum for the 6th column (index 6)
                let sum6 = sumColumn(6);

                // Check if the sum is NaN or invalid and handle it accordingly
                sum6 = isNaN(sum6) ? 0 : sum6;

                // Determine badge type based on sum value
                let badgeType = sum6 >= 0 ? 'badge-info' : 'badge-danger';

                // Update footer with sums for columns 2, 3, 4, 5, and 6
                $(api.column(2).footer()).html(sumColumn(2).toLocaleString());
                $(api.column(3).footer()).html(sumColumn(3).toLocaleString());
                $(api.column(4).footer()).html(sumColumn(4).toLocaleString());
                $(api.column(5).footer()).html(sumColumn(5).toLocaleString());
                $(api.column(6).footer()).html(`<span class="badge ${badgeType}">${sum6.toLocaleString()}</span>`);
            }



        });

        // When the filter button is clicked, refresh the table
         $('#btn-filter').on('click', function() {
            table.ajax.reload();
        });
    });
</script>

@endsection('content')

