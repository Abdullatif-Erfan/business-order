@extends('layouts.app')
@section('title', __('journal.income_title'))
@section('content')


<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                        <div class="card-header" style="padding: 11px 20px !important;">
                            
                            @if(auth()->user()->hasAccess('journal','create_records'))
                                <a href="{{ route('income.create') }}">
                                    <button type="button" class="btn btn-sm mybtn">
                                        <i class="fas fa-plus"></i> {{__('common.add')}}
                                    </button>
                                </a>
                            @else
                                <button type="button" onclick="alert('{{__('common.not_allowed')}}')" class="btn btn-sm mybtn">
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
                                    <div class="col-md-2">
                                        <select class="form-control select2" id="type_id">
                                            <option value=""> {{__('journal.income_type')}} </option>
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control select2" id="currency_id">
                                            <option value=""> {{__('common.currency')}} </option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>

                                    
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
                                


                                     <div class="col-md-3">
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

                                  

                                    <div class="col-md-1">
                                        <input class="form-control" id="code_number" placeholder="{{__('common.code')}}">
                                    </div>

                                    <div class="col-md-1">
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
                                <table id="incomeTable" class="display responsive nowrap table table-bordered" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                              <img src="{{ asset($orgbios[0]->header)  }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                            
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                                <center>
                                                    {{__('journal.income_list')}} 
                                                </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}}  </th>
                                            <th> {{__('common.code')}}  </th>
                                            <th> {{__('journal.income_type')}}  </th>
                                            <th> {{__('journal.reciever')}} </th>
                                            <th> {{__('common.details')}} </th>
                                            <th>  {{__('common.recieved')}}  </th>
                                            <th>{{__('common.currency')}} </th>
                                            <th>{{__('common.date')}} </th>
                                            <th>{{__('common.document')}} </th>
                                            <th>{{__('common.edit')}}</th>
                                            <th>{{__('common.delete')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="5">{{__('common.total')}} </td>
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
    $(document).ready(function() {
        let table = $('#incomeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("income.data") }}',
                data: function (d) 
                {
                    d.type_id = $('#type_id').val();
                    d.currency_id = $('#currency_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.code_number = $('#code_number').val();
                    d.bill_number = $('#bill_number').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'code', name: 'code' },
                { data: 'incomeTypeRelation', name: 'incomeTypeRelation' },
                { data: 'accountRelation', name: 'accountRelation' },
                { data: 'details', name: 'details' },
                { data: 'transaction_type_2', name: 'transaction_type_2' },
                { data: 'currency', name: 'currency' },
                { data: 'inserted_short_date', name: 'inserted_short_date' },
                { data: 'doc', name: 'doc', orderable: false, searchable: false },
                { data: 'edit', name: 'edit', orderable: false, searchable: false },
                { data: 'delete', name: 'delete', orderable: false, searchable: false }

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

                $(api.column(5).footer()).html(sumColumn(5));
                // $(api.column(5).footer()).html(sumColumn(5));
                
            }
        });

        // When the filter button is clicked, refresh the table
        $('#btn-filter').click(function() {
            table.draw(); // Refresh DataTable with new filters
        });
    });

    function viewDetails(id) {
        alert("جزییات برای آی دی " + id);
    }
</script>


@endsection('content')

