@extends('layouts.app')

@section('content')

<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px;">

                            <div class="card-header text-center" style="padding:10px;">
                            <a href="{{ route('user.create') }}" class="btn btn-sm btn-default pull-right"> 
                                <span class="fas fa-plus-square"></span> &nbsp; {{__('common.add')}} 
                            </a>
                                <span class="card-title">   {{__('user.users_list')}}  </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <input type="hidden" id="warehouse_id" value="14" >
                                <table id="userTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="13">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="13">
                                            <center> {{__('user.users_list')}}  </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} &nbsp; </th>
                                            <th> {{__('common.name')}} </th>
                                            <th> {{__('common.user')}} </th>
                                            <th> {{__('common.branch')}} </th>
                                            <th> {{__('common.email')}} </th>
                                            <th> {{__('common.image')}}  </th>
                                            <th> {{__('user.priviledge')}} </th>
                                            <th> {{__('user.login')}} </th>
                                            <th> {{__('common.edit')}} </th>
                                            <th> {{__('common.delete')}} </th>
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

@include('management.users.scripts')
@endsection

