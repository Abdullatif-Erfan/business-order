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
						
                        <form action="{{ route('roles.update', ['roleId' => $role->roleId]) }}" method="POST">
                        @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label>{{__('user.role_name')}}</label>
                                <input type="hidden" name="id" value="{{ $role->roleId }}">
                                <input type="text" name="role" class="form-control" value="{{ $role->role }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{__('user.role_status')}}</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ $role->status ? 'selected' : '' }}>
                                    {{__('user.active')}}</option>
                                    <option value="0" {{ !$role->status ? 'selected' : '' }}>
                                    {{__('user.inActive')}}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">{{__('user.update')}}</button>
                        </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
