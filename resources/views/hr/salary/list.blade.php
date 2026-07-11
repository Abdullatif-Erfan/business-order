@extends('layouts.app')
@section('title', __('hr.salary'))
@section('content')


<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                        <div class="card-header" style="padding: 11px 20px !important;">
                            
                            <a href="{{ route('salary.create') }}">
                                <button type="button" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> {{__('common.add')}}
                                </button>
                            </a>
                            <span class="m-r-20">{{__('hr.emp_salary_list')}}</span>

                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>

                            <button type="button" class="btn btn-sm mybtn visible-xs" onclick="show_search_form(1)">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>

                        {{-- Filter Form --}}
                        <div class="filterForm" id="searchWrapper1">  
                            <div class="col-md-12">
                                <div class="row">
                                   
                                     <div class="col-md-2 col-sm-6 col-xs-12">
                                        <input class="form-control" id="employee_name" placeholder="{{__('hr.employee_name')}}">
                                    </div>
                                    <div class="col-md-2 col-sm-6 col-xs-12">
                                        <select class="form-control select2" id="currency_id">
                                            <option value=""> {{__('common.currency')}} </option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-12">
                                        <select class="form-control select2" id="month">
                                            <option value="">  {{__('common.month')}} </option>
                                            @foreach($months as $key => $month)
                                                <option value="{{ $key }}">{{ $month }}</option>
                                            @endforeach
                                        </select> 
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-12">
                                        <select class="form-control select2" id="year">
                                            <option value="">  {{__('common.year')}} </option>
                                            @for($i=2025; $i<=2026; $i++)
                                                <option value="{{ $i }}" >{{ $i }}</option>
                                            @endfor
                                        </select> 
                                    </div>


                                    <div class="col-md-2 col-sm-6 col-xs-12">
                                        <input class="form-control" id="code_number" placeholder="{{__('common.code')}}">
                                    </div>

                                    <div class="col-md-2">
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
                                <table id="dataTable" class="display responsive nowrap table table-bordered" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                               <img src="{{ asset($orgbios[0]->header)  }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                                <center> 
                                                    {{__('hr.emp_salary_list')}}
                                                </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} </th>
                                            <th> {{__('common.code')}} </th>
                                            <th> {{__('hr.employee')}}  </th>
                                            <th> {{__('common.details')}} </th>
                                            <th> {{__('common.amount')}}  </th>
                                            <th>{{__('common.unit')}}</th>
                                            <th> {{__('common.year')}} </th>
                                            <th>{{__('common.month')}}</th>
                                            <th>{{__('common.date')}}</th>
                                            <th>{{__('common.edit')}}</th>
                                            <th>{{__('common.delete')}}</th>
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
    $(document).ready(function() {
        let table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,   
            lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'همه']
                ],
            ajax: {
                url: '{{ route("salary.data") }}',
                data: function (d) 
                {
                    d.employee_name = $('#employee_name').val();
                    d.currency_id = $('#currency_id').val();
                    d.month = $('#month').val();
                    d.year = $('#year').val();
                    d.code_number = $('#code_number').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'code', name: 'code' },
                { data: 'accountRelation', name: 'accountRelation' },
                { data: 'details', name: 'details' },
                { data: 'amount', name: 'amount' },
                { data: 'currency', name: 'currency' },
                { data: 'year', name: 'year' },
                { data: 'month', name: 'month' },
                { data: 'idate', name: 'idate' },
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

                $(api.column(4).footer()).html(sumColumn(4));
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

