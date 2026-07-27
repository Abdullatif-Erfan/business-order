@extends('layouts.app')

@section('content')

<style>
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
    .profile-image-container {
        position: relative;
        display: inline-block;
    }
    .profile-image-container img {
        min-width:  100px;
        min-height: 100px;
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #ddd;
        transition: all 0.3s ease;
    }
    .profile-image-container:hover img {
        opacity: 0.7;
    }
    .profile-image-container .upload-hint {
        position: absolute;
        bottom: 5px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .profile-image-container:hover .upload-hint {
        opacity: 1;
    }
</style>

<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header text-center" style="padding:15px;">
                            <a href="{{ route('user.index') }}" class="btn btn-sm btn-default pull-left">
                                <span class="fas fa-arrow-left"></span> {{ __('common.back') }}
                            </a>
                            <span class="card-title pull-right">{{ __('user.user_edit') }}</span>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.updateProfile', $user->id) }}" method="POST" enctype="multipart/form-data" id="profileForm">
                                @csrf
                                @method('PATCH')

                                <!-- ========================================= -->
                                <!-- PROFILE IMAGE (Centered) -->
                                <!-- ========================================= -->
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <div class="form-group">
                                            <label for="photo" class="profile-image-container">
                                                @if($user->photo)
                                                    <img src="{{ asset('storage/' . $user->photo) }}" 
                                                        alt="{{ $user->full_name }}" 
                                                        id="profileImagePreview">
                                                @else
                                                    <img src="{{ asset('assets/img/default-avatar.png') }}" 
                                                        alt="{{ $user->full_name }}" 
                                                        id="profileImagePreview">
                                                @endif
                                                <span class="upload-hint">
                                                    <i class="fas fa-camera"></i> {{ __('user.imageUpload') }}
                                                </span>
                                            </label>
                                            <input type="file" class="form-control d-none @error('photo') is-invalid @enderror" 
                                                name="photo" id="photo" 
                                                accept=".jpg, .jpeg, .png"
                                                onchange="previewImage(this)">
                                            <br>
                                            <small class="text-muted">{{ __('user.click_image_to_change') }}</small>
                                            @error('photo') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ========================================= -->
                                <!-- USERNAME & FULL NAME -->
                                <!-- ========================================= -->
                                <div class="row">
                                    <!-- Username -->
                                    <div class="col-md-6 col-sm-6 col-xs-12">
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

                                    <!-- Full Name (Read Only) -->
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="full_name">{{ __('user.full_name') }}</label>
                                            <input type="text" class="form-control" 
                                                name="full_name" id="full_name"
                                                value="{{ $user->full_name }}"
                                                disabled readonly
                                                style="background-color: #f5f5f5; cursor: not-allowed;">
                                            <small class="text-muted">{{ __('user.full_name') }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- ========================================= -->
                                <!-- EMAIL -->
                                <!-- ========================================= -->
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="email">{{ __('user.email') }} <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                name="email" id="email"
                                                minlength="10" maxlength="128" required
                                                value="{{ old('email', $user->email) }}"
                                                placeholder="{{ __('user.email') }}">
                                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ========================================= -->
                                <!-- PASSWORD CHANGE SECTION -->
                                <!-- ========================================= -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="text-muted">{{ __('user.password_confirmation') }}</h5>
                                        <hr style="margin-top:5px;">
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- New Password -->
                                    <div class="col-md-6 col-sm-6 col-xs-12">
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

                                    <!-- Confirm New Password -->
                                    <div class="col-md-6 col-sm-6 col-xs-12">
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
                                                <i class="fas fa-times-circle"></i> {{ __('user.password_mismatch') }}
                                            </span>
                                            <span id="passwordMatchSuccess" class="text-success" style="display:none;">
                                                <i class="fas fa-check-circle"></i> {{ __('user.password_match') }}
                                            </span>
                                            @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- ========================================= -->
                                <!-- SUBMIT BUTTONS -->
                                <!-- ========================================= -->
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
    // =========================================
    // IMAGE PREVIEW
    // =========================================
    window.previewImage = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profileImagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

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
    // FORM SUBMISSION VALIDATION
    // =========================================
    $('#profileForm').on('submit', function(e) {
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

    // =========================================
    // TRIGGER FILE INPUT ON IMAGE CLICK
    // =========================================
    $('#profileImagePreview').on('click', function() {
        $('#photo').click();
    });
});
</script>
@endpush

@endsection