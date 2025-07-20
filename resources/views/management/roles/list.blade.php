@extends('layouts.app')
@section('content')


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
                            <span class="card-title"> {{__('user.role_list')}} </span>
                        </div>
                        <div class="card-body">
						<table id='roleTable'  class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{__('common.number')}}</th>
                                        <th>{{__('user.role')}}</th>
                                        <th>{{__('user.role_status')}}</th>
                                        <th class="text-center"> {{__('user.role_selection')}} </th>
                                        <th class="text-center">{{__('common.edit')}}</th>
                                        <th class="text-center">{{__('common.delete')}}</th>
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
