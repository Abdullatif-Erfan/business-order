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
                                            <!-- <option value=""> واحد پولی </option> -->
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
                                            
                                            <!-- <th> {{__('reports.cache_in')}}</th>
                                            <th> {{__('reports.cache_out')}}</th> -->
                                            <th> {{__('journal.recieved')}} <br> {{__('journal.cache')}} (+)</th>
                                            <th>{{__('journal.paid')}} <br> {{__('journal.cache')}} (-)</th>
                                            <th> {{__('reports.loan')}}</th>
                                            <th> {{__('reports.talab')}}</th>
                                            
                                            <th>  {{__('reports.balance')}}  </th>
                                            <th>{{__('common.unit')}}</th>
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
        let table = $('#journalTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,   
            lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'همه']
                ],
            ajax: {
                url: '{{ route("cacheflowWithBalance.data") }}',
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

                { data: 'belance', name: 'belance' },
                { data: 'currency', name: 'currency' },
                { data: 'idate', name: 'idate' },
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

                // var isKhazana = Boolean(settings.json.isKhazana) || false;
                var isCompanyAccount = Boolean(settings.json.isCompanyAccount) || false;

                
                // let cacheRecieved =   Number(sumCacheRecieved.replace(/,/g, ''));
                // let cachePaid = Number(sumCachePaid.replace(/,/g, ''));
                // let loanRecieved =  isCompanyAccount && !isKhazana ? 0 : Number(sumLoanRecieved.replace(/,/g, ''));
                // let loanPaid =  isCompanyAccount && !isKhazana ? 0 : Number(sumLoanPaid.replace(/,/g, ''));

                let cacheRecieved =   sumColumn(4);
                let cachePaid = sumColumn(5);
                let loanRecieved =  sumColumn(6);
                let loanPaid =  sumColumn(7);


                    // let finalResult = (cacheRecieved +  loanRecieved) - (cachePaid + loanPaid);
                    let finalResult = isCompanyAccount ? (cacheRecieved +  loanPaid) - (cachePaid + loanRecieved) : 
                    (cachePaid + loanPaid) - (cacheRecieved + loanRecieved);

                    // Format the final result properly
                    let finalResultFormatted = Number.isInteger(finalResult)
                        ? finalResult.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                        : finalResult.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                

                // Determine badge type
                let badgeType = finalResult >= 0 ? 'badge-info' : 'badge-danger';

                

                $(api.column(4).footer()).html(sumColumn(4).toLocaleString());
                $(api.column(5).footer()).html(sumColumn(5).toLocaleString());
                $(api.column(6).footer()).html(sumColumn(6).toLocaleString());
                $(api.column(7).footer()).html(sumColumn(7).toLocaleString());
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

