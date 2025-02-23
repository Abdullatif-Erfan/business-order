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

                            <div class="card-header text-center" style="padding:10px;">
                            <a href="{{ route('user.index') }}" class="btn btn-sm btn-default pull-left"> 
                                <span class="fas fa-arrow-left"></span>   
                            </a>
                                <span class="card-title pull-right">  ویرایش کاربر   </span>
                            </div>
                        <div class="card-body">
                        <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="full_name">نام مکمل (ضروری)</label>
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" minlength="5" maxlength="128" required value="{{ old('full_name') }}">
                                        @error('full_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">ایمیل آدرس</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" minlength="15" maxlength="128" value="{{ old('email') }}">
                                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="user_name">نام کاربری (ضروری)</label>
                                        <input type="text" class="form-control @error('user_name') is-invalid @enderror" name="user_name" minlength="5" maxlength="128" required value="{{ old('user_name') }}">
                                        @error('user_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">رمز عبور (ضروری)</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" minlength="6" maxlength="20" value="{{ old('password') }}" required>
                                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">تکرار رمز عبور (ضروری)</label>
                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" maxlength="20" required>
                                        @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                              
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="role">رول</label>
                                        <select class="form-control @error('roleId') is-invalid @enderror" name="roleId" required>
                                            <option value="">انتخاب رول</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->roleId }}" {{ old('roleId') == $role->roleId ? 'selected' : '' }}>
                                                    {{ $role->role }} @if ($role->roleStatus == 2) (غیرفعال) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('roleId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="isAdmin">نوعیت کاربر (ضروری)</label>
                                        <select class="form-control @error('isAdmin') is-invalid @enderror" name="isAdmin" required>
                                            <option value="0" @if(old('isAdmin') == 0) selected @endif>کاربر عادی</option>
                                            <option value="1" @if(old('isAdmin') == 1) selected @endif>ادمین</option>
                                        </select>
                                        @error('isAdmin') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="photo">آپلود عکس</label>
                                        <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" accept=".jpg, .jpeg, .png, .PNG">
                                        @error('photo') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">ثبت کاربر جدید</button>
                                <a href="{{ route('user.index') }}" class="btn btn-warning">لغو</a>
                            </div>
                        </form>
                                
                                

                        </div> <!-- /card-body -->
                    </div> <!-- /card -->
                </div> <!-- /col-md-12 -->
            </div> <!-- /row -->
        </div> <!-- /page-inner -->
    </div> <!-- /content -->
</div> <!-- /main content -->

@endsection

