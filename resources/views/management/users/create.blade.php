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
                            <span class="card-title pull-right">{{__('user.user_create')}}</span>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="isAdmin">{{__('user.user_type')}} ({{__('user.required')}})</label>
                                            <select class="form-control @error('isAdmin') is-invalid @enderror" name="isAdmin" id="userType" onchange="checkUserType(this.value)" required>
                                                <option value="">--- {{__('user.user_type_selection')}} ---</option> 
                                                <option value="0">{{__('user.simple_user')}}</option>
                                                <option value="1">{{__('user.admin')}}</option>
                                            </select>
                                            @error('isAdmin') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="full_name">{{__('user.full_name')}} ({{__('user.required')}})</label>
                                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" minlength="5" maxlength="128" required value="{{ old('full_name') }}">
                                            @error('full_name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="email">{{__('user.email')}}</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" minlength="15" maxlength="128" value="{{ old('email') }}">
                                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="user_name">{{__('user.user_name')}} ({{__('user.required')}})</label>
                                            <input type="text" class="form-control @error('user_name') is-invalid @enderror" name="user_name" minlength="5" maxlength="128" required value="{{ old('user_name') }}">
                                            @error('user_name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- ========================================= -->
                                    <!-- PASSWORD FIELD WITH VISIBILITY TOGGLE -->
                                    <!-- ========================================= -->
                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="password">{{__('user.password')}} ({{__('user.required')}})</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                       name="password" id="password" minlength="5" maxlength="20" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text password-toggle" onclick="togglePasswordVisibility('password', this)">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                            <small class="text-muted">{{__('user.password_min_length')}}</small>
                                        </div>
                                    </div>

                                    <!-- ========================================= -->
                                    <!-- CONFIRM PASSWORD WITH VISIBILITY TOGGLE -->
                                    <!-- ========================================= -->
                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">{{__('user.password_confirmation')}} ({{__('user.required')}})</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                       name="password_confirmation" id="password_confirmation" maxlength="20" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text password-toggle" onclick="togglePasswordVisibility('password_confirmation', this)">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <span id="passwordMatchError" class="text-danger" style="display:none;">
                                                {{__('user.password_mismatch')}}
                                            </span>
                                            <span id="passwordMatchSuccess" class="text-success" style="display:none;">
                                                <i class="fas fa-check-circle"></i> {{__('user.password_match')}}
                                            </span>
                                            @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- ========================================= -->
                                    <!-- ACCOUNT SELECT - Hidden for Admin -->
                                    <!-- ========================================= -->
                                    <div class="col-md-4 col-sm-6 col-xs-6" id="accountDiv">
                                        <div class="form-group">
                                            <label for="account_id">{{__('journal.select_account')}} <span class="text-danger" id="accountRequired">*</span></label>
                                            <select class="form-control select2" name="account_id" id="account_id" style="width:100%">
                                                <option value="">{{__('journal.account')}}</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('account_id') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <small style="font-size:10px;color:blue">
                                            {{__('user.link_with_account')}}
                                        </small>
                                    </div>

                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="roleId">{{__('user.role')}}</label>
                                            <select class="form-control @error('roleId') is-invalid @enderror" name="roleId" required>
                                                <option value="">{{__('user.role_selection')}}</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->roleId }}" {{ old('roleId') == $role->roleId ? 'selected' : '' }}>
                                                        {{ $role->role }} @if ($role->roleStatus == 2) ({{__('user.inActive')}}) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('roleId') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="photo">{{__('user.imageUpload')}}</label>
                                            <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" accept=".jpg, .jpeg, .png, .PNG">
                                            @error('photo') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- ========================================= -->
                                <!-- MULTIPLE SELECT: CARS & CUSTOMERS -->
                                <!-- Hidden for Admin -->
                                <!-- ========================================= -->
                                <div class="row" id="simpleUserFields">
                                    <!-- Multiple Select: Cars -->
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="car_ids">{{__('user.assigned_cars')}} <span class="text-danger" id="carRequired">*</span></label>
                                            <select class="form-control select2" name="car_ids[]" id="car_ids" multiple style="width:100%">
                                                @foreach($cars as $car)
                                                    <option value="{{ $car->id }}">{{ $car->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">{{__('user.hold_ctrl_to_select_multiple')}}</small>
                                            @error('car_ids') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Multiple Select: Customers -->
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="customer_ids">{{__('user.assigned_customers')}} <span class="text-danger" id="customerRequired">*</span></label>
                                            <select class="form-control select2" name="customer_ids[]" id="customer_ids" multiple style="width:100%">
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">{{__('user.hold_ctrl_to_select_multiple')}}</small>
                                            @error('customer_ids') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">{{__('user.add_new_user')}}</button>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for multiple select
    $('#car_ids, #customer_ids').select2({
        placeholder: "{{ __('user.select_options') }}",
        allowClear: true,
        width: '100%'
    });

    // Initialize Select2 for single select
    $('#account_id').select2({
        width: '100%'
    });

    // ✅ Initially hide all conditional fields
    $('#simpleUserFields').hide();
    $('#accountDiv').hide();
    
    // Hide asterisks initially
    $('#accountRequired').hide();
    $('#carRequired').hide();
    $('#customerRequired').hide();

    // Check if a value is already selected (for edit mode)
    var initialValue = $('#userType').val();
    if (initialValue !== '') {
        checkUserType(initialValue);
    }

    // =========================================
    // PASSWORD CONFIRMATION VALIDATION
    // =========================================
    $('#password, #password_confirmation').on('keyup', function() {
        validatePasswordMatch();
    });

    // Also validate on blur (when user leaves the field)
    $('#password_confirmation').on('blur', function() {
        validatePasswordMatch();
    });

    // =========================================
    // FORM SUBMISSION VALIDATION
    // =========================================
    $('#userForm').on('submit', function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#password_confirmation').val();
        
        if (password !== confirmPassword) {
            e.preventDefault();
            $('#passwordMatchError').show();
            $('#password_confirmation').css('border-color', 'red');
            showNotification('{{__("user.password_mismatch")}}', 'danger');
            return false;
        }
        
        return true;
    });
});

// =========================================
// PASSWORD VISIBILITY TOGGLE
// =========================================
function togglePasswordVisibility(inputId, element) {
    var input = document.getElementById(inputId);
    var icon = element.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// =========================================
// PASSWORD MATCH VALIDATION
// =========================================
function validatePasswordMatch() {
    var password = $('#password').val();
    var confirmPassword = $('#password_confirmation').val();
    
    if (confirmPassword.length === 0) {
        $('#passwordMatchError').hide();
        $('#passwordMatchSuccess').hide();
        $('#password_confirmation').css('border-color', '');
        return;
    }
    
    if (password === confirmPassword) {
        $('#passwordMatchError').hide();
        $('#passwordMatchSuccess').show();
        $('#password_confirmation').css('border-color', 'green');
    } else {
        $('#passwordMatchError').show();
        $('#passwordMatchSuccess').hide();
        $('#password_confirmation').css('border-color', 'red');
    }
}

// =========================================
// CHECK USER TYPE (Admin / Simple User)
// =========================================
function checkUserType(value) {
    if (value == '') {
        // ❌ No selection: Hide everything
        $('#simpleUserFields').hide();
        $('#accountDiv').hide();
        
        // Remove required attributes
        $('#account_id').removeAttr('required');
        $('#car_ids').removeAttr('required');
        $('#customer_ids').removeAttr('required');
        
        // Hide asterisks
        $('#accountRequired').hide();
        $('#carRequired').hide();
        $('#customerRequired').hide();
        
    } else if (value == 1) {
        // 👑 ADMIN: Hide simple user fields
        $('#simpleUserFields').hide();
        $('#accountDiv').hide();
        
        // Remove required attributes
        $('#account_id').removeAttr('required');
        $('#car_ids').removeAttr('required');
        $('#customer_ids').removeAttr('required');
        
        // Hide asterisks
        $('#accountRequired').hide();
        $('#carRequired').hide();
        $('#customerRequired').hide();
        
    } else {
        // 👤 SIMPLE USER (value == 0): Show simple user fields
        $('#simpleUserFields').show();
        $('#accountDiv').show();
        
        // Add required attributes
        $('#account_id').attr('required', 'required');
        $('#car_ids').attr('required', 'required');
        $('#customer_ids').attr('required', 'required');
        
        // Show asterisks
        $('#accountRequired').show();
        $('#carRequired').show();
        $('#customerRequired').show();
    }
}

// =========================================
// NOTIFICATION FUNCTION
// =========================================
function showNotification(message, type = 'info') {
    if (typeof $.notify === 'function') {
        $.notify({
            message: '<span style="font-size:14px;">' + message + '</span>',
            title: '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{ __("settings.message") }}</span>',
            icon: 'fa fa-bell'
        }, {
            type: type,
            placement: {
                from: 'top',
                align: 'center'
            },
            time: 3000
        });
    } else {
        alert(message);
    }
}
</script>
@endpush

@endsection