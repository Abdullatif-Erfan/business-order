@extends('layouts.app')
@section('title', __('hr.salary'))
@section('content')

<style>
.clearance-row {
    background-color: #f6ffe4 !important;
    color: #000 !important;
}
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                        <div class="card-header" style="padding: 11px 20px !important;">
                            <strong> {{ __('reports.employee_report') }}   </strong>
                             <!-- Responsive Filter Toggle Button - Visible only on XS -->
                            <div class="pull-left" style="width:90px">
                                <button type="button" class="responsive_button btn btn-sm  visible-xs"
                                  id="filterToggleBtn" onclick="toggleFilterForm()"  style="margin-left:2px; margin-top:2px;">
                                   <i class="fas fa-filter"></i>
                                 </button>
                                 <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                            </div>
                        </div>

                        {{-- Filter Form --}}
                         <div class="filter-section no-print" id="searchWrapper"> 
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <select class="form-control select2" id="account_id" style="width:100%">
                                            <option value=""> {{__('reports.account')}} </option>
                                              @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                              @endforeach
                                        </select> 
                                    </div>
                                    <div class="col-md-2  col-sm-6 col-xs-6">
                                        <select class="form-control select2" id="currency_id" style="width:100%">
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>

                                     <div class="col-md-2 col-sm-6 col-xs-6">
                                         <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="start_date"  placeholder="{{__('common.start_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>
                                     <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="end_date" placeholder="{{__('common.end_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>

                                    <div class="col-md-1  col-sm-6 col-xs-6">
                                        <input class="form-control" id="code_number" placeholder="{{__('common.code')}}">
                                    </div>

                                    <div class="col-md-1  col-sm-6 col-xs-6">
                                        <input class="form-control" id="bill_number" placeholder="{{__('common.bill')}}">
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
                                <span class="pull-left visible-print">{{__('common.print_date')}}: {{ now()->format('Y-m-d') }}</span>
                                <table id="employeeSalaryTable" class="display responsive nowrap table table-bordered" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="12">
                                              <img src="{{ asset($orgbios[0]->header)  }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                            
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="12">
                                                <center>
                                                   {{__('reports.cash_flow_print_title')}}  
                                                </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} </th>
                                            <th> {{__('common.code')}} </th>
                                            <th> {{__('reports.account')}} </th>
                                            <th> {{__('common.details')}} </th>
                                            <th> {{__('journal.recieved')}} <br> {{__('journal.cache')}}</th>
                                            <th>{{__('journal.paid')}} <br> {{__('journal.cache')}}</th>
                                            <th> {{__('reports.loan')}}</th>
                                            <th> {{__('reports.talab')}}</th>
                                            <th>  {{__('reports.balance')}}  </th>
                                            <th>{{__('common.date')}}</th>
                                            <th>{{__('common.user')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff; font-weight:bold;">
                                            <td colspan="4" style="text-align:center;">{{__('common.total')}}</td>
                                            <td id="totalCacheRecieved" style="text-align:center;">0</td>
                                            <td id="totalCachePaid" style="text-align:center;">0</td>
                                            <td id="totalLoanRecieved" style="text-align:center;">0</td>
                                            <td id="totalLoanPaid" style="text-align:center;">0</td>
                                            <td id="totalBalance" style="text-align:center;">0</td>
                                            <td colspan="2"></td>
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
</script>

<script>
    $(document).ready(function() {
        let table = $('#employeeSalaryTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,   
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'همه']
            ],
            ajax: {
                url: '{{ route("salary.report.data") }}',
                data: function (d) {
                    d.account_id = $('#account_id').val();
                    d.currency_id = $('#currency_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.code_number = $('#code_number').val();
                    d.bill_number = $('#bill_number').val();
                },
                error: function(xhr, status, error) {
                    console.log("Error fetching data: ", error);
                    $('#employeeSalaryTable tbody').html('<tr><td colspan="12" class="text-center">No records found</td></tr>');
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'code', name: 'code' },
                { data: 'accountRelation', name: 'accountRelation' },
                { data: 'details', name: 'details' },
                { data: 'cacheRecieved', name: 'cacheRecieved' },
                { data: 'cachePaid', name: 'cachePaid' },
                { data: 'loanRecieved', name: 'loanRecieved' },
                { data: 'loanPaid', name: 'loanPaid' },
                { data: 'balance', name: 'balance' },
                { data: 'idate', name: 'idate' },
                { data: 'full_name', name: 'full_name' }
            ],
            // =========================================
            // FOOTER CALLBACK FOR TOTALS
            // =========================================
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                
                // Helper function to parse numbers from formatted strings
                function parseNumber(value) {
                    if (!value) return 0;
                    // Remove commas and convert to float
                    var num = parseFloat(value.toString().replace(/,/g, ''));
                    return isNaN(num) ? 0 : num;
                }
                
                // Calculate totals for each column
                // Column 4: cacheRecieved
                var totalCacheRecieved = api
                    .column(4, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        return a + parseNumber(b);
                    }, 0);
                
                // Column 5: cachePaid
                var totalCachePaid = api
                    .column(5, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        return a + parseNumber(b);
                    }, 0);
                
                // Column 6: loanRecieved
                var totalLoanRecieved = api
                    .column(6, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        return a + parseNumber(b);
                    }, 0);
                
                // Column 7: loanPaid
                var totalLoanPaid = api
                    .column(7, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        return a + parseNumber(b);
                    }, 0);
                
                // Column 8: balance
                 // ✅ Column 8: balance - Get the LAST value (not sum)
                var balanceData = api.column(8, { page: 'current' }).data();
                var finalBalance = balanceData.length > 0 ? parseNumber(balanceData[balanceData.length - 1]) : 0;
                
                // Update footer with formatted totals
                $('#totalCacheRecieved').html(totalCacheRecieved.toFixed(2));
                $('#totalCachePaid').html(totalCachePaid.toFixed(2));
                $('#totalLoanRecieved').html(totalLoanRecieved.toFixed(2));
                $('#totalLoanPaid').html(totalLoanPaid.toFixed(2));
                 $('#totalBalance').html(finalBalance.toFixed(2));
            }
        });

        // When the filter button is clicked, refresh the table
        $('#btn-filter').click(function() {
            table.draw(); // Refresh DataTable with new filters
        });
    });
</script>

@endsection('content')