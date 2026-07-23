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
                                <span class="card-title">{{__('user.users_list')}}</span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <input type="hidden" id="warehouse_id" value="14">
                                <table id="userTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="10">
                                                <img src="{{ asset($orgbios[0]->header ?? '') }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="10">
                                                <center>{{__('user.users_list')}}</center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width:5%">{{__('common.number')}}</th>
                                            <th style="width:15%">{{__('common.name')}}</th>
                                            <th style="width:12%">{{__('common.user')}}</th>
                                            <th style="width:15%">{{__('common.email')}}</th>
                                            <th style="width:8%">{{__('common.image')}}</th>
                                            <th style="width:10%">{{__('user.priviledge')}}</th>
                                            <th style="width:10%">{{__('user.hasAccount')}}</th>
                                            <th style="width:8%">{{__('user.login')}}</th>
                                            <th style="width:8%">{{__('common.view')}}</th>
                                            <th style="width:8%">{{__('common.edit')}}</th>
                                            <th style="width:8%">{{__('common.delete')}}</th>
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

<!-- View User Details -->
<div class="modal fade" id="userViewModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="width: 900px !important; max-width: 95vw !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{ __('common.view') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="userViewModalContent"></div>
                <div id="userViewModalLoader" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
@include('management.users.scripts')
@endpush

@endsection

