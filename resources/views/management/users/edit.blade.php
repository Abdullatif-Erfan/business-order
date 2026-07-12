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
                                <span class="fas fa-arrow-left"></span> {{ __('common.back') }}
                            </a>
                            <span class="card-title pull-right">{{ __('user.user_edit') }}</span>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="old_account_id" value="{{ $user->account_id }}">
                                @csrf
                                @method('PATCH')

                                <div class="row">
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
                                    <!-- Password -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="password">{{ __('user.password') }}</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                name="password" id="password"
                                                minlength="5" maxlength="20" 
                                                placeholder="{{ __('user.password') }}">
                                            <small class="text-muted">{{ __('user.leave_blank_to_keep') }}</small>
                                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Password Confirmation -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="password_confirmation">{{ __('user.password_confirmation') }}</label>
                                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                name="password_confirmation" id="password_confirmation"
                                                maxlength="20" 
                                                placeholder="{{ __('user.password_confirmation') }}">
                                            @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Account Selection -->
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="account_id">{{ __('journal.select_account') }}</label>

                                            <select class="form-control select2 @error('account_id') is-invalid @enderror" 
                                                name="account_id" id="account_id" style="width:100%">
                                                
                                                <!-- Option to remove account (set to 0) -->
                                                <option value="0">--- {{ __('journal.other_account_selection') }} --- </option>
                                                
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
                                                <!-- {{__('user.just_can_see_this_records')}} -->
                                            </small>
                                            
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Role (Admin Only) -->
                                    @if($isAdmin)
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="roleId">{{ __('user.role') }} <span class="text-danger">*</span></label>
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

                                        <!-- User Type (Admin Only) -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="isAdmin">{{ __('user.user_type') }} <span class="text-danger">*</span></label>
                                                <select class="form-control @error('isAdmin') is-invalid @enderror" 
                                                    name="isAdmin" id="isAdmin" required>
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
                                    @endif

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

                                <!-- Submit Buttons -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mt-3 m-b-10">
                                            <button type="submit" class="btn btn-success">
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
        // Initialize Select2
        $('.select2').select2({
            width: '100%',
            placeholder: '{{ __("journal.select_account") }}',
            allowClear: true
        });

        // Password validation
        $('#password').on('input', function() {
            var password = $(this).val();
            if (password.length > 0 && password.length < 5) {
                $(this).addClass('is-invalid');
                $(this).next('.text-danger').remove();
                $(this).after('<span class="text-danger">{{ __("user.password_min_length") }}</span>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.text-danger').remove();
            }
        });

        // Confirm password validation
        $('#password_confirmation').on('input', function() {
            var password = $('#password').val();
            var confirm = $(this).val();
            
            if (password !== confirm) {
                $(this).addClass('is-invalid');
                $(this).next('.text-danger').remove();
                $(this).after('<span class="text-danger">{{ __("user.password_mismatch") }}</span>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.text-danger').remove();
            }
        });

        // Form validation
        $('form').on('submit', function(e) {
            var password = $('#password').val();
            var confirm = $('#password_confirmation').val();
            
            if (password !== confirm) {
                e.preventDefault();
                showNotification('{{ __("user.password_mismatch") }}', 'danger');
                $('#password_confirmation').focus();
                return false;
            }
            
            return true;
        });

        // Account selection change - show warning if changing
        $('#account_id').on('change', function() {
            var selected = $(this).find('option:selected');
            if (selected.attr('disabled')) {
                showNotification('{{ __("user.account_already_assigned") }}', 'warning');
                $(this).val('').trigger('change');
            }
        });
    });

    // Notification function
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
</script>
@endpush

@endsection