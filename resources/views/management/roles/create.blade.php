@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-center" style="padding:10px;">
                            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-default pull-left"> 
                                <span class="fas fa-arrow-left"></span> &nbsp;  {{__('common.back')}} 
                            </a>
                            <span class="card-title pull-right"> {{__('user.create_role')}}  </span>
                        </div>
                        <div class="card-body">
						
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="role">{{__('user.role_name')}}</label>
                                <input type="text" name="role" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="status">{{__('user.role_status')}}</label>
                                <select name="status" class="form-control">
                                    <option value="1">{{__('user.active')}}</option>
                                    <option value="0">{{__('user.inActive')}}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">{{__('common.save')}}</button>
                        </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
