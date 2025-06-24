@extends('layouts.app')
@section('content')

@section('title', __('journal.title'))

@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
    // Show the notification using the data from the session
    $(document).ready(function(){
        showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
        // showNotification("message", 'success', 'top', 'right', 'withicon');
        // showNotification(@json($notification['message']), @json($notification['type']), 'top', 'right', 'withicon');
    });
</script>
@endif

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
                                <button type="button" onclick="alert('{{ __('commont.not_allowed') }}')" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> <th>{{__('common.add')}}</th>
                                </button>
                            @endif

                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>

                            <button type="button" class="btn btn-sm mybtn visible-xs" onclick="show_search_form(1)">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>

                        {{-- Filter Form --}}
                        <div class="filterForm" id="searchWrapper1">  
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
                                        <select class="form-control select2" id="currency_id" style="width:100%">
                                            <option value=""> {{__('common.currency')}} </option>
                                            @foreach($currencies as $currency)
                                               <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>

                                    
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#start_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="start_date" id="start_date"
                                            data-targetselector="#start_date" value="" 
                                            data-mddatetimepicker="true" 
                                            placeholder="{{__('common.start_date')}}"  data-placement="right" data-englishnumber="true"  >
                                        </div>
							     	</div>
                                


                                     <div class="col-md-3 col-sm-6 col-xs-6">
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
                                        <input class="form-control" id="code_number" placeholder="{{__('journal.code')}}">
                                    </div>

                                    <div class="col-md-1 col-sm-6 col-xs-6">
                                        <input class="form-control" id="bill_number" placeholder="{{__('journal.bill_no')}}">
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
                                            
                                            <!-- <th> رفت / قرض  </th>
                                            <th>  آمد / طلب </th> -->

                                       <!-- <th>  پرداخت / قرض  </th>
                                            <th>  دریافت / طلب  </th> -->

                                            <!-- <th>  رسیدگی / قرض  </th>
                                            <th>  بردگی / طلب  </th> -->

                                            <!-- <th>بردگی <br> نقد (+)</th>
                                            <th>رسیدگی <br> نقد (-)</th>
                                            <th>بردگی <br> قرض</th>
                                            <th>رسیدگی <br> قرض / طلب</th> -->

                                            <th> {{__('journal.recieved')}} <br> {{__('journal.cache')}} (+)</th>
                                            <th>{{__('journal.paid')}} <br> {{__('journal.cache')}} (-)</th>
                                            <th> {{__('journal.recieved_loan')}}</th>
                                            <th> {{__('journal.paid_loan')}} <br>/ {{__('journal.talab')}}  </th>
                                            
                                            <th>{{__('journal.unit')}}</th>
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


<!-- For Persian Date Picker -->
<script src="{{ asset('assets/datepicker/jalaali.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js') }}" type="text/javascript"></script>



<script>
function showNotification(message, type = 'info', from = 'top', align = 'left', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> {{ __('settings.message') }} </span>';
    
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
                url: '{{ route('journal.data') }}',
                data: function (d) {
                    d.account_id = $('#account_id').val();
                    d.currency_id = $('#currency_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.code_number = $('#code_number').val();
                    d.bill_number = $('#bill_number').val();
                },
                error: function(xhr, status, error) 
                {
                   console.log("Error fetching data: ", error);
                   $('#journalTable tbody').html('<tr><td colspan="12" class="text-center">مواردی یافت نشد</td></tr>');
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'code', name: 'code' },
                { data: 'accountRelation', name: 'accountRelation' },
                { data: 'details', name: 'details' },
                // { data: 'transaction_type_1', name: 'transaction_type_1' },
                // { data: 'transaction_type_2', name: 'transaction_type_2' },

                { data: 'cacheRecieved', name: 'cacheRecieved' },
                { data: 'cachePaid', name: 'cachePaid' },
                { data: 'loanRecieved', name: 'loanRecieved' },
                { data: 'loanPaid', name: 'loanPaid' },

                { data: 'currency', name: 'currency' },
                // { data: 'option_label', name: 'option_label' },
                { data: 'inserted_short_date', name: 'inserted_short_date' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
           
            drawCallback: function (settings) {
                var api = this.api();
                let isCompanyAccount = settings.json.isCompanyAccount;

                // Handle case where no records exist
                if (api.rows().data().length === 0) {
                    $('#journalTable tbody').html('<tr><td colspan="12" class="text-center">No records found</td></tr>');
                    return; // Exit early to avoid unnecessary calculations
                }

                // Check if account_id is filtered (i.e., has a value)
                var accountId = $('#account_id').val();

                // Function to sum columns and return raw numbers
                function sumColumn(index) {
                    return api
                        .column(index, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            var numA = parseFloat((a || '0').toString().replace(/,/g, '')) || 0;
                            var numB = parseFloat((b || '0').toString().replace(/,/g, '')) || 0;
                            return numA + numB;
                        }, 0);
                }

                // Only calculate finalResult if account_id is filtered
                if (parseInt(accountId) > 0) {
                    // Store column sums (as numbers, not formatted strings)
                    let sum4 = sumColumn(4);
                    let sum5 = sumColumn(5);
                    let sum6 = sumColumn(6);
                    let sum7 = sumColumn(7);

                    /**
                    * (بیلانس = (آورد نقد + طلبات) - (برد نقد + قرضه
                    * balance = (CachePaid + LoanPaid) - (CacheRecieved + LoanRecieved); 
                    */

                    // Ensure valid numbers for all sums
                    sum4 = isNaN(sum4) ? 0 : sum4;
                    sum5 = isNaN(sum5) ? 0 : sum5;
                    sum6 = isNaN(sum6) ? 0 : sum6;
                    sum7 = isNaN(sum7) ? 0 : sum7;

                    // Calculate the final result based on account type
                    let finalResult = isCompanyAccount 
                        ? (sum4 + sum7) - (sum5 + sum6)
                        : (sum5 + sum7) - (sum4 + sum6);

                    // // Ensure finalResult is not NaN
                    // finalResult = isNaN(finalResult) ? 0 : finalResult;

                    // // Format final result with proper decimal places
                    // let finalResultFormatted = Number.isInteger(finalResult)
                    //     ? finalResult.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                    //     : finalResult.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    // // Determine badge type
                    // let badgeType = finalResult >= 0 ? 'badge-info' : 'badge-danger';

                    // Update footer totals with formatted results
                    $(api.column(4).footer()).html(sum4.toLocaleString());
                    $(api.column(5).footer()).html(sum5.toLocaleString());
                    $(api.column(6).footer()).html(sum6.toLocaleString());
                    $(api.column(7).footer()).html(sum7.toLocaleString());
                    // $(api.column(8).footer()).html(`<span class="badge ${badgeType}">${finalResultFormatted}</span>`);
                } else {
                    // Hide results when no account is filtered
                    $(api.column(4).footer()).html('');
                    $(api.column(5).footer()).html('');
                    $(api.column(6).footer()).html('');
                    $(api.column(7).footer()).html('');
                    // $(api.column(8).footer()).html('');
                }
            }

        });

        // When the filter button is clicked, refresh the table
         $('#btn-filter').on('click', function() {
            table.ajax.reload();
        });
    });
</script>

@endsection('content')

