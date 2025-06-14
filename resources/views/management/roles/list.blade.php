@extends('layouts.app')

@section('content')

@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
        $(document).ready(function(){
            showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
        });
    </script>
@endif


<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-center" style="padding:10px;">
                            <a href="{{ route('roles.create') }}" class="btn btn-sm btn-default pull-right"> 
                                <span class="fas fa-plus-square"></span> &nbsp; <th>{{__('common.add')}}</th> 
                            </a>
                            <span class="card-title"> لیست رول ها </span>
                        </div>
                        <div class="card-body">
						<table id='roleTable'  class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{__('common.number')}}</th>
                                        <th>رول</th>
                                        <th>وضعیت</th>
                                        <th class="text-center"> تعیین صلاحیت</th>
                                        <th class="text-center">ویرایش</th>
                                        <th class="text-center">حذف</th>

                                    </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('management.roles.scripts')
@endsection
