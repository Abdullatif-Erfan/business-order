@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-center" style="padding:10px;">
                            <a href="{{ route('roles.create') }}" class="btn btn-sm btn-default pull-left"> 
                                <span class="fas fa-arrow-left"></span> &nbsp;  برگشت به لست 
                            </a>
                            <span class="card-title pull-right"> ایجاد رول  </span>
                        </div>
                        <div class="card-body">
						
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="role">نام رول</label>
                                <input type="text" name="role" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="status">وضعیت</label>
                                <select name="status" class="form-control">
                                    <option value="1">فعال</option>
                                    <option value="0">غیرفعال</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">ثبت</button>
                        </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
