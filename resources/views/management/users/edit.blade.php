@extends('layouts.app')

@section('content')

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ff6b6b;
    }
    .selected-options-container {
        margin-top: 5px;
    }
    .selected-option-badge {
        display: inline-block;
        padding: 4px 10px;
        margin: 2px 4px;
        border-radius: 12px;
        font-size: 12px;
        background: #e9ecef;
        color: #495057;
    }
</style>

<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header text-center" style="padding:10px;">
                            <a href="{{ route('user.index') }}" class="btn btn-sm btn-default pull-left">
                                <span class="fas fa-arrow-left"></span> {{ __('common.back') }}
                            </a>
                            <span class="card-title pull-right">{{ __('user.user_edit') }}</span>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data" id="userForm">
                                <input type="hidden" name="old_account_id" value="{{ $user->account_id }}">
                                @csrf
                                @method('PATCH')

                                <div class="row">
                                    <!-- User Type -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="isAdmin">{{ __('user.user_type') }} <span class="text-danger">*</span></label>
                                            <select class="form-control @error('isAdmin') is-invalid @enderror" 
                                                name="isAdmin" id="isAdmin" 
                                                onchange="checkUserType(this.value)" required>
                                                <option value="0" {{ old('isAdmin', $user->isAdmin) == 0 ? 'selected' : '' }}>
                                                    {{ __('user.simple_user') }}
                                                </option>
                                                <option value="1" {{ old('isAdmin', $user->isAdmin) == 1 ? 'selected' : '' }}>
                                                    {{ __('user.admin') }}
                                                </option>
                                            </select>
                                            @error('isAdmin') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Full Name -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="full_name">{{ __('user.full_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                                name="full_name" id="full_name"
                                                minlength="5" maxlength="128" required 
                                                value="{{ old('full_name', $user->full_name) }}"
                                                placeholder="{{ __('user.full_name') }}">
                                            @error('full_name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="email">{{ __('user.email') }}</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                name="email" id="email"
                                                minlength="15" maxlength="128" 
                                                value="{{ old('email', $user->email) }}"
                                                placeholder="{{ __('user.email') }}">
                                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Username -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="user_name">{{ __('user.user_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                                                name="user_name" id="user_name"
                                                minlength="5" maxlength="128" required 
                                                value="{{ old('user_name', $user->user_name) }}"
                                                placeholder="{{ __('user.user_name') }}">
                                            @error('user_name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="password">{{ __('user.password') }}</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                    name="password" id="password"
                                                    minlength="5" maxlength="20" 
                                                    placeholder="{{ __('user.password') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text password-toggle" onclick="togglePasswordVisibility('password', this)">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ __('user.leave_blank_to_keep') }}</small>
                                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Password Confirmation -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="password_confirmation">{{ __('user.password_confirmation') }}</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                    name="password_confirmation" id="password_confirmation"
                                                    maxlength="20" 
                                                    placeholder="{{ __('user.password_confirmation') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text password-toggle" onclick="togglePasswordVisibility('password_confirmation', this)">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <span id="passwordMatchError" class="text-danger" style="display:none;">
                                                {{ __('user.password_mismatch') }}
                                            </span>
                                            <span id="passwordMatchSuccess" class="text-success" style="display:none;">
                                                <i class="fas fa-check-circle"></i> {{ __('user.password_match') }}
                                            </span>
                                            @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Role -->
                                    <div class="col-md-4 col-sm-6 col-xs-12" id="roleDiv">
                                        <div class="form-group">
                                            <label for="roleId">{{ __('user.role') }} <span class="text-danger" id="roleRequired">*</span></label>
                                            <select class="form-control @error('roleId') is-invalid @enderror" 
                                                name="roleId" id="roleId" required>
                                                <option value="">{{ __('user.select_role') }}</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->roleId }}" 
                                                        {{ old('roleId', $user->roleId) == $role->roleId ? 'selected' : '' }}>
                                                        {{ $role->role }} 
                                                        @if ($role->roleStatus == 2) 
                                                            ({{ __('user.inActive') }}) 
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('roleId') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Account Selection -->
                                    <div class="col-md-4 col-sm-6 col-xs-12" id="accountDiv">
                                        <div class="form-group">
                                            <label for="account_id">{{ __('journal.select_account') }} <span class="text-danger" id="accountRequired">*</span></label>
                                            <select class="form-control select2 @error('account_id') is-invalid @enderror" 
                                                name="account_id" id="account_id" style="width:100%">
                                                <option value="0">--- {{ __('journal.other_account_selection') }} ---</option>
                                                @foreach($accounts as $account)
                                                    @php
                                                        $selected = old('account_id', $userAccount->id ?? 0) == $account->id;
                                                        $isAssignedToOther = $account->user_account_id && $account->user_account_id != $user->id;
                                                    @endphp
                                                    <option value="{{ $account->id }}" {{ $selected ? 'selected' : '' }}
                                                        @if($isAssignedToOther && !$selected) disabled style="color:#999;" @endif>
                                                        {{ $account->name }}
                                                        @if($isAssignedToOther)
                                                            ({{ __('user.assigned_to') }}: {{ $account->user->full_name ?? 'N/A' }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('account_id') <span class="text-danger">{{ $message }}</span> @enderror
                                            <small style="font-size:10px;color:blue">
                                                {{__('user.link_with_account')}}
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Photo Upload -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="photo">{{ __('user.imageUpload') }}</label>
                                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                                name="photo" id="photo" 
                                                accept=".jpg, .jpeg, .png">
                                            
                                            @if($user->photo)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $user->photo) }}" 
                                                        alt="{{ $user->full_name }}" 
                                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 2px solid #ddd;">
                                                    <br>
                                                    <small class="text-muted">{{ __('user.current_photo') }}</small>
                                                </div>
                                            @endif
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
                                            <label for="car_ids">{{ __('user.assigned_cars') }} <span class="text-danger" id="carRequired">*</span></label>
                                            <select class="form-control select2" name="car_ids[]" id="car_ids" multiple style="width:100%">
                                                @foreach($cars as $car)
                                                    <option value="{{ $car->id }}" 
                                                        {{ in_array($car->id, old('car_ids', $user->car_ids ?? [])) ? 'selected' : '' }}>
                                                        {{ $car->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">{{ __('user.hold_ctrl_to_select_multiple') }}</small>
                                            @error('car_ids') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Multiple Select: Customers -->
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="customer_ids">{{ __('user.assigned_customers') }} <span class="text-danger" id="customerRequired">*</span></label>
                                            <select class="form-control select2" name="customer_ids[]" id="customer_ids" multiple style="width:100%">
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        {{ in_array($customer->id, old('customer_ids', $user->customer_ids ?? [])) ? 'selected' : '' }}>
                                                        {{ $customer->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">{{ __('user.hold_ctrl_to_select_multiple') }}</small>
                                            @error('customer_ids') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mt-3 m-b-10">
                                            <button type="submit" class="btn btn-success" id="submit_button">
                                                <i class="fas fa-save"></i> {{ __('user.user_edit') }}
                                            </button>
                                            <a href="{{ route('user.index') }}" class="btn btn-danger">
                                                <i class="fas fa-times"></i> {{ __('user.cancel') }}
                                            </a>
                                        </div>
                                    </div>
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
        width: '100%',
        placeholder: '{{ __("journal.select_account") }}',
        allowClear: true
    });

    // ✅ Initially set visibility based on user type
    var initialValue = $('#isAdmin').val();
    checkUserType(initialValue);

    // =========================================
    // PASSWORD VISIBILITY TOGGLE
    // =========================================
    window.togglePasswordVisibility = function(inputId, element) {
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
    // PASSWORD CONFIRMATION VALIDATION
    // =========================================
    $('#password, #password_confirmation').on('keyup', function() {
        validatePasswordMatch();
    });

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
    window.checkUserType = function(value) {
        if (value == '') {
            // ❌ No selection: Hide everything
            $('#simpleUserFields').hide();
            $('#accountDiv').hide();
            $('#roleDiv').hide();
            
            // Remove required attributes
            $('#account_id').removeAttr('required');
            $('#car_ids').removeAttr('required');
            $('#customer_ids').removeAttr('required');
            $('#roleId').removeAttr('required');
            
            // Hide asterisks
            $('#accountRequired').hide();
            $('#carRequired').hide();
            $('#customerRequired').hide();
            $('#roleRequired').hide();
            
        } else if (value == 1) {
            // 👑 ADMIN: Hide simple user fields
            $('#simpleUserFields').hide();
            $('#accountDiv').hide();
            $('#roleDiv').show();
            
            // Remove required attributes from simple user fields
            $('#account_id').removeAttr('required');
            $('#car_ids').removeAttr('required');
            $('#customer_ids').removeAttr('required');
            $('#roleId').attr('required', 'required');
            
            // Hide asterisks for simple user fields
            $('#accountRequired').hide();
            $('#carRequired').hide();
            $('#customerRequired').hide();
            $('#roleRequired').show();
            
        } else {
            // 👤 SIMPLE USER (value == 0): Show simple user fields
            $('#simpleUserFields').show();
            $('#accountDiv').show();
            $('#roleDiv').show();
            
            // Add required attributes
            $('#account_id').attr('required', 'required');
            $('#car_ids').attr('required', 'required');
            $('#customer_ids').attr('required', 'required');
            $('#roleId').attr('required', 'required');
            
            // Show asterisks
            $('#accountRequired').show();
            $('#carRequired').show();
            $('#customerRequired').show();
            $('#roleRequired').show();
        }
    }

    // =========================================
    // FORM SUBMISSION VALIDATION
    // =========================================
    $('#userForm').on('submit', function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#password_confirmation').val();
        
        // Only validate if password is not empty
        if (password.length > 0 && password !== confirmPassword) {
            e.preventDefault();
            $('#passwordMatchError').show();
            $('#password_confirmation').css('border-color', 'red');
            showNotification('{{ __("user.password_mismatch") }}', 'danger');
            return false;
        }
        
        return true;
    });

    // =========================================
    // ACCOUNT SELECTION CHANGE - Show warning if changing
    // =========================================
    $('#account_id').on('change', function() {
        var selected = $(this).find('option:selected');
        if (selected.attr('disabled')) {
            showNotification('{{ __("user.account_already_assigned") }}', 'warning');
            $(this).val('').trigger('change');
        }
    });

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
        }
    }
});
</script>
@endpush

@endsection