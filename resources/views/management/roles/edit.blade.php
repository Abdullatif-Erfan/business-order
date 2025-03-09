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
                                <span class="fas fa-arrow-left"></span> &nbsp;  برگشت به لست 
                            </a>
                            <span class="card-title pull-right"> ایجاد رول  </span>
                        </div>
                        <div class="card-body">
						
                        <form action="{{ route('roles.update', ['roleId' => $role->roleId]) }}" method="POST">
                        @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label>نام رول</label>
                                <input type="hidden" name="id" value="{{ $role->roleId }}">
                                <input type="text" name="role" class="form-control" value="{{ $role->role }}" required>
                            </div>
                            <div class="form-group">
                                <label>وضعیت</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ $role->status ? 'selected' : '' }}>فعال</option>
                                    <option value="0" {{ !$role->status ? 'selected' : '' }}>غیرفعال</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">بروزرسانی</button>
                        </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
