@extends('layouts.app')
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
                            <strong> {{__('reports.cash_flow_title')}}   </strong>
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
                                        <select class="form-control select2" id="account_id" style="width:100%">
                                            <option value=""> {{__('reports.account')}} </option>
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
                                            data-mddatetimepicker="true"  placeholder="{{__('common.start_date')}}"  data-placement="right" data-englishnumber="true"  >
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
                                            data-mddatetimepicker="true"  placeholder="{{__('common.end_date')}}"  data-placement="right" data-englishnumber="true" >
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
                                                   {{__('reports.cash_flow_print_title')}}  
                                                </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} </th>
                                            <th> {{__('common.code')}} </th>
                                            <th> {{__('reports.account')}} </th>
                                            <th> {{__('common.details')}} </th>
                                            
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

                                            <th> {{__('reports.cache_in')}}</th>
                                            <th> {{__('reports.cache_out')}}</th>
                                            <th> {{__('reports.loan')}}</th>
                                            <th> {{__('reports.talab')}}</th>
                                            
                                            <th>{{__('common.unit')}}</th>
                                            <th>  {{__('reports.transaction_type')}}  </th>
                                            <th>{{__('common.date')}}</th>
                                            <th>{{__('common.user')}}</th>
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

<script>
    $(document).ready(function() {
        let table = $('#journalTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("cacheflow.data") }}',
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
                   $('#journalTable tbody').html('<tr><td colspan="12" class="text-center">No records found</td></tr>');
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
                { data: 'option_label', name: 'option_label' },
                { data: 'inserted_short_date', name: 'inserted_short_date' },
                { data: 'full_name', name: 'full_name'}
            ],
           
            drawCallback: function(settings) 
            {
                var api = this.api();

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
                let sum4 = sumColumn(4).toLocaleString();
                let sum5 = sumColumn(5).toLocaleString();                
                
                // Ensure values are defined, otherwise default to '0'
                var sumCacheRecieved = settings.json.sumCacheRecieved || '0';
                var sumCachePaid = settings.json.sumCachePaid || '0';
                var sumLoanRecieved = settings.json.sumLoanRecieved || '0';
                var sumLoanPaid = settings.json.sumLoanPaid || '0';

                /**
                 * اگر خزانه انتخاب شده باشد باید
                 * ۱: طلبات مشتری در قرضه خزانه نشان داده شود
                 * ۲: قرضه مشتریان در طلبات خزانه نشان داده شود
                 * ۳: بیلانس طلب و قرض و دریافت نقد و پرداخت نقد محاسبه گردد
                 */

                var isKhazana = Boolean(settings.json.isKhazana) || false;
                var isCompanyAccount = Boolean(settings.json.isCompanyAccount) || false;

                
                    let cacheRecieved =   Number(sumCacheRecieved.replace(/,/g, ''));
                    let cachePaid = Number(sumCachePaid.replace(/,/g, ''));
                    let loanRecieved =  isCompanyAccount && !isKhazana ? 0 : Number(sumLoanRecieved.replace(/,/g, ''));
                    let loanPaid =  isCompanyAccount && !isKhazana ? 0 : Number(sumLoanPaid.replace(/,/g, ''));

                    // Calculate the final result based on account type
                    // let finalResult = isCompanyAccount 
                    //     ? (cacheRecieved + loanPaid) - (cachePaid + loanRecieved)
                    //     : (cachePaid + loanPaid) - (cacheRecieved + loanRecieved);

                    let finalResult = (cacheRecieved + loanPaid) - (cachePaid + loanRecieved);

                    // Format the final result properly
                    let finalResultFormatted = Number.isInteger(finalResult)
                        ? finalResult.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                        : finalResult.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                

                // Determine badge type
                let badgeType = finalResult >= 0 ? 'badge-info' : 'badge-danger';

                // Update footer with formatted values
                $(api.column(4).footer()).html(cacheRecieved.toLocaleString());
                $(api.column(5).footer()).html(cachePaid.toLocaleString());

                $(api.column(6).footer()).html(loanRecieved.toLocaleString());
                $(api.column(7).footer()).html(loanPaid.toLocaleString());
                $(api.column(8).footer()).html(`<span class="badge ${badgeType}">${finalResultFormatted}</span>`);
            }
        });

        // When the filter button is clicked, refresh the table
        $('#btn-filter').click(function() {
            table.draw(); // Refresh DataTable with new filters
        });
    });
</script>

@endsection('content')

