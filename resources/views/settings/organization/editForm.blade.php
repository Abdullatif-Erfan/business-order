<form id="orgProfileEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $orgBio->id }}">

    <div class="col-md-12">
        <div class="row">
                <div class="col-md-6">
                    <label for="name">{{__('settings.profile_name')}} </label>
                    <input type="text" class="form-control" name="name" value="{{ $orgBio->name }}" required>
                    <span id="orgProfileNameError" class="text-danger"></span>
                </div>

                <div class="col-md-6">
                    <label for="phone"> {{__('settings.phone')}} </label>
                    <input type="text" class="form-control" name="phone" value="{{ $orgBio->phone }}" required>
                    <span id="orgProfilePhoneError" class="text-danger"></span>
                </div>
        </div>
    </div>


    <div class="col-md-12">
        <div class="row">
                <div class="col-md-6">
                   <label for="address"> {{__('settings.address')}}  </label>
                    <input type="text" class="form-control" name="address" value="{{ $orgBio->address }}" required>
                    <span id="orgProfileAddressError" class="text-danger"></span>
                </div>

                <div class="col-md-6">
                    <label for="note_for_print">   {{__('settings.note_for_print')}}  </label>
                    <input type="text" class="form-control" name="note_for_print" value="{{ $orgBio->note_for_print }}" >
                    <span id="noteForPrintError" class="text-danger"></span>
                </div>
        </div>
    </div>
    

    <div class="form-group">
        <label for="name"> {{__('settings.header')}} </label>
        <input class="form-control input-solid" id="header" name="header" type="file" required>
        <span id="orgProfileHeaderError" class="text-danger"></span>
        @if ($orgBio->header)
            <img id="headerPreview" src="{{ asset($orgBio->header) }}" alt="Header Image" class="img-thumbnail mt-2">
        @else
            <img id="headerPreview" src="{{ asset('storage/user_photos/no_image.png') }}" alt="No Image" width="150" class="img-thumbnail mt-2">
        @endif
    </div>


    <div class="col-md-12">
        <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">{{__('settings.logo')}}</label>
                        <input class="form-control input-solid" id="logos" name="logos" type="file" required>
                        <span id="orgProfileLogosError" class="text-danger"></span>
                        @if ($orgBio->logos)
                            <img id="logoPreview" src="{{ asset($orgBio->logos) }}" alt="Logo Image" width="150" class="img-thumbnail mt-2">
                        @else
                            <img id="logoPreview" src="{{ asset('storage/user_photos/no_image.png') }}" alt="No Image" width="150" class="img-thumbnail mt-2">
                        @endif
                    </div>
                </div>

                <div class="col-md-6">

                    <div class="form-group">
                       <label for="tax_activation">{{ __('settings.tax_activation') }}</label>
                        <select class="form-control" name="tax_activation">
                            <option value="0" {{ $orgBio->tax_activation == 0 ? 'selected' : '' }}>{{ __('settings.noactive') }}</option>
                            <option value="1" {{ $orgBio->tax_activation == 1 ? 'selected' : '' }}>{{ __('settings.active') }}</option>
                        </select>
                    <div>
                        

                </div>
        </div>
    </div>
    
</form>

