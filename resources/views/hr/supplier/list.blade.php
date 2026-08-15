@extends('layouts.app')

@section('content')


<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px; text-align:center;">
                            <a href="{{ route('supplier.create') }}" class="pull-right">
                                <button type="button" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> {{__('common.add')}}
                                </button>
                            </a>
                            <span class="card-title"> {{__('menu.supplier_lists')}} </span>
                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                        </div>


                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <span class="pull-left visible-print">{{__('common.print_date')}} : {{ $todaysDate }}</span>
                                <table id="employeeTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="8">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="8">
                                                <center> {{__('menu.supplier_lists')}} </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{__('common.number')}} </th>									
                                            <th>{{__('common.name')}} </th>			
                                            <th>{{__('common.phone')}} </th>		
                                            <th>{{__('common.address')}}  </th>	
                                            <th>{{__('settings.loan_limit_label')}}</th>	
                                            <th>{{__('common.edit')}} </th>
                                            <th>{{__('common.delete')}}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div> <!-- /table responsive -->
                        </div> <!-- /card-body -->
                    </div> <!-- /card -->
                </div> <!-- /col-md-12 -->
            </div> <!-- /row -->
        </div> <!-- /page-inner -->
    </div> <!-- /content -->
</div> <!-- /main content -->

<script>
$(document).ready(function() {
    fetchList();

    // Move the filter button click event outside
    $('#btn-filter').click(function() {
        $('#employeeTable').DataTable().ajax.reload(null, false); // Reload data without resetting pagination
    });
});


function fetchList() {
    let employeeTable = $('#employeeTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(employeeTable)) {
        employeeTable.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,   // 👈 IMPORTANT
            lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'همه']
                ],
            ajax: {  
                url: '{{ route("supplier.data") }}',
                data: function (d) {
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                // { data: 'account_type', name: 'account_type'},
                { data: 'name', name: 'name'},
                { data: 'phone', name: 'phone' },
                { data: 'address', name: 'address' },
                {data: 'loan_limit', name: 'loan_limit'},
                { data: 'edit', name: 'edit', searchable: false, orderable: false, className: 'hidden-print' },
                { data: 'delete', name: 'delete', searchable: false, orderable: false, className: 'hidden-print' },
            ],
        });

    } else {
        employeeTable.DataTable().ajax.reload(null, false);
    }
}
</script>
@endsection

