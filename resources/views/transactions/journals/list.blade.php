@extends('layouts.app')
@section('content')
@section('title', __('journal.title'))

<style>
.clearance-row {
    background-color: #f6ffe4 !important;
    color: #000 !important;
}
.dataTables_filter {
    display: none !important;
}
</style>


<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                        <div class="card-header" style="padding: 11px 20px !important;">
                            
                            @if(auth()->user()->hasAccess('journal','create_records'))
                                <a href="{{ route('journal.create') }}">
                                    <button type="button" class="btn btn-sm mybtn">
                                        <i class="fas fa-plus"></i> {{ __('journal.add_new')}}
                                    </button>
                                </a>
                            @else
                                <button type="button" onclick="alert('{{ __('common.not_allowed') }}')" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> <th>{{__('common.add')}}</th>
                                </button>
                            @endif


                             <!-- Responsive Filter Toggle Button - Visible only on XS -->
                            <div class="pull-left" style="width:80px">
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
                                            <option value=""> {{__('journal.account')}} </option>
                                            @foreach($accounts as $account)
                                              <option value="{{ $account->id }}">{{ $account->name }}</option>
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
                                  

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input class="form-control" id="code_number" placeholder="{{__('journal.code')}}">
                                    </div>

                                    <div class="col-md-1 col-sm-6 col-xs-6">
                                        <input class="form-control" id="bill_number" placeholder="{{__('journal.bill_no')}}">
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
                       
                        {{-- Card Body --}}
                        <div class="card-body">
                            <div class="table-responsive" id="print_area">
                                <span class="pull-left visible-print">{{__('journal.print_date')}}: {{ now()->format('Y-m-d') }}</span>
                                <table id="journalTable" class="display responsive nowrap table table-bordered" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="12">
                                              <img src="{{ asset($orgbios[0]->header)  }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                            
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="12">
                                                <center>
                                                  {{__('journal.print_date')}}   
                                                </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} </th>
                                            <th> {{__('journal.code')}} </th>
                                            <th> {{__('journal.account')}} </th>
                                            <th> {{__('journal.details')}} </th>
                                            <th> {{__('journal.recieved')}} <br> {{__('journal.cache')}} (+)</th>
                                            <th>{{__('journal.paid')}} <br> {{__('journal.cache')}} (-)</th>
                                            <th> {{__('journal.recieved_loan')}}</th>
                                            <th> {{__('journal.paid_loan')}} <br>/ {{__('journal.talab')}}  </th>
                                            
                                            <th>{{__('common.expense')}}</th>
                                            <!-- <th>  نوع معامله  </th> -->
                                            <th>{{__('journal.date')}}</th>
                                            <th>{{__('common.details')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="4">{{__('common.total')}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <!-- <td></td> -->
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
    let isCompanyAccount = false;
    let accountId = 0;
    
    let table = $('#journalTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'همه']
        ],
        ajax: {
            url: '{{ route('journal.data') }}',
            data: function (d) {
                d.account_id = $('#account_id').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.code_number = $('#code_number').val();
                d.bill_number = $('#bill_number').val();
            },
            dataSrc: function (json) {
                // Get current account_id from filter
                var currentAccountId = $('#account_id').val();
                
                // Only update if there's an account selected
                if (currentAccountId && currentAccountId !== '') {
                    isCompanyAccount = json.isCompanyAccount || false;
                    accountId = json.accountId || 0;
                } else {
                    // No account selected, reset everything
                    isCompanyAccount = false;
                    accountId = 0;
                }
                
                return json.data;
            },
            error: function(xhr, status, error) {
                console.log("Error fetching data: ", error);
                $('#journalTable tbody').html('<tr><td colspan="12" class="text-center">مواردی یافت نشد</td></tr>');
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
            { data: 'expense', name: 'expense' },
            { data: 'idate', name: 'idate' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();
            
            function getNumber(val) {
                if (val === null || val === undefined || val === '') return 0;
                if (typeof val === 'string') {
                    val = val.replace(/,/g, '').replace(/[^0-9.-]/g, '');
                }
                return parseFloat(val) || 0;
            }
            
            // Calculate column totals with explicit variables
            var totalCacheRecieved = 0; // column 4
            var totalCachePaid = 0;      // column 5
            var totalLoanRecieved = 0;   // column 6
            var totalLoanPaid = 0;       // column 7
            var totalExpense = 0;        // column 8
            
            api.rows({ page: 'current' }).data().each(function(rowData) {
                totalCacheRecieved += getNumber(rowData[api.column(4).dataSrc()]);
                totalCachePaid += getNumber(rowData[api.column(5).dataSrc()]);
                totalLoanRecieved += getNumber(rowData[api.column(6).dataSrc()]);
                totalLoanPaid += getNumber(rowData[api.column(7).dataSrc()]);
                totalExpense += getNumber(rowData[api.column(8).dataSrc()]);
            });
            
            // Update footer for columns 4, 5, 6, 7, 8
            $(api.column(4).footer()).html(totalCacheRecieved.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $(api.column(5).footer()).html(totalCachePaid.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $(api.column(6).footer()).html(totalLoanRecieved.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $(api.column(7).footer()).html(totalLoanPaid.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $(api.column(8).footer()).html(totalExpense.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            
            // Calculate and display balance only if an account is selected
            if (accountId > 0) {
                var balance = isCompanyAccount 
                    ? (totalCacheRecieved + totalLoanRecieved) - (totalCachePaid + totalLoanPaid) 
                    : (totalCachePaid + totalLoanPaid + totalExpense) - (totalCacheRecieved + totalLoanRecieved);
                
                // Determine badge class based on balance
                var badgeClass = balance >= 0 ? 'badge-success' : 'badge-danger';
                var sign = balance >= 0 ? '+' : '';
                
                $(api.column(9).footer()).html(
                    '<span class="badge ' + badgeClass + '" style="font-size: 14px; padding: 5px 10px;">' +
                        sign + balance.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) +
                    '</span>'
                );
            } else {
                $(api.column(9).footer()).html('');
            }
        }
    });

    // Filter button
    $('#btn-filter').on('click', function() {
        table.ajax.reload();
    });

    // Reset button
    $('#btn-reset').on('click', function() {
        // Reset all filter inputs
        $('#account_id').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#code_number').val('');
        $('#bill_number').val('');
        
        // Reset variables
        isCompanyAccount = false;
        accountId = 0;
        
        // Reload the table with reset filters
        table.ajax.reload(null, false);
    });
});
</script>

@endsection('content')

