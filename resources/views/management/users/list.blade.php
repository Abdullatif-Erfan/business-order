@extends('layouts.app')

@section('content')

@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
    // Show the notification using the data from the session
    $(document).ready(function(){
        showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
    });
</script>
@endif


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
                                <span class="fas fa-plus-square"></span> &nbsp; ثبت جدید 
                            </a>
                                <span class="card-title">   لست کاربران  </span>
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
                                            <center> لست فروشات   </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> شماره &nbsp; </th>
                                            <th>  نام </th>
                                            <th> نام کاربری </th>
                                            <th> شعبه </th>
                                            <th>  ایمیل </th>
                                            <th> عکس  </th>
                                            <th>  صلاحیت </th>
                                            <th>  وورد </th>
                                            <th>  ویرایش </th>
                                            <th> حذف </th>
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

