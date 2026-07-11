@extends('layouts.app')

@section('content')

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
                                <span class="card-title pull-right">  {{__('user.user_edit')}}   </span>
                            </div>
                        <div class="card-body">
                        <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="full_name">{{__('user.full_name')}} ({{__('user.required')}})</label>
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" minlength="5" maxlength="128" required value="{{ old('full_name') }}">
                                        @error('full_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">{{__('user.email')}} </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" minlength="15" maxlength="128" value="{{ old('email') }}">
                                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="user_name">{{__('user.user_name')}} ({{__('user.required')}})</label>
                                        <input type="text" class="form-control @error('user_name') is-invalid @enderror" name="user_name" minlength="5" maxlength="128" required value="{{ old('user_name') }}">
                                        @error('user_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password">{{__('user.password')}} ({{__('user.required')}})</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" minlength="5" maxlength="20" value="{{ old('password') }}" required>
                                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password_confirmation"> {{__('user.password_confirmation')}} ({{__('user.required')}})</label>
                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" maxlength="20" required>
                                        @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                    
                              
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="isAdmin">{{__('user.user_type')}} ({{__('user.required')}})</label>
                                        <select class="form-control @error('isAdmin') is-invalid @enderror" name="isAdmin"
                                        onchange="checkUserType(this.value)" required>
                                            <option value="0">{{__('user.simple_user')}}</option>
                                            <option value="1">{{__('user.admin')}}</option>
                                        </select>
                                        @error('isAdmin') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="role">{{__('user.role')}}</label>
                                        <select class="form-control @error('roleId') is-invalid @enderror" name="roleId" required>
                                            <option value=""> {{__('user.role_selection')}} </option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->roleId }}" {{ old('roleId') == $role->roleId ? 'selected' : '' }}>
                                                    {{ $role->role }} @if ($role->roleStatus == 2) ({{__('user.inActive')}}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('roleId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="photo">{{__('user.imageUpload')}}</label>
                                        <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" accept=".jpg, .jpeg, .png, .PNG">
                                        @error('photo') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary"> {{__('user.add_new_user')}} </button>
                                <a href="{{ route('user.index') }}" class="btn btn-warning">{{__('user.cancel')}}</a>
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

