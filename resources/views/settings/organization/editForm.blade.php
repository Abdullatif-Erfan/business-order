<form id="orgProfileEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $orgBio->id }}">

    <div class="col-md-12">
        <div class="row">
                <div class="col-md-6">
                    <label for="name">نام شرکت </label>
                    <input type="text" class="form-control" name="name" value="{{ $orgBio->name }}" required placeholder="نام را وارد کنید">
                    <span id="orgProfileNameError" class="text-danger"></span>
                </div>

                <div class="col-md-6">
                    <label for="phone"> شماره فعال شرکت </label>
                    <input type="text" class="form-control" name="phone" value="{{ $orgBio->phone }}" required>
                    <span id="orgProfilePhoneError" class="text-danger"></span>
                </div>
        </div>
    </div>


    <div class="col-md-12">
        <div class="row">
                <div class="col-md-6">
                   <label for="address"> آدرس شرکت </label>
                    <input type="text" class="form-control" name="address" value="{{ $orgBio->address }}" required>
                    <span id="orgProfileAddressError" class="text-danger"></span>
                </div>

                <div class="col-md-6">
                    <label for="note_for_print">   نوت برای پرنت </label>
                    <input type="text" class="form-control" name="note_for_print" value="{{ $orgBio->note_for_print }}" >
                    <span id="noteForPrintError" class="text-danger"></span>
                </div>
        </div>
    </div>
    

    <div class="form-group">
        <label for="name"> هیدر / سربرگ </label>
        <input class="form-control input-solid" id="header" name="header" type="file" required>
        <span id="orgProfileHeaderError" class="text-danger"></span>
        @if ($orgBio->header)
            <img id="headerPreview" src="{{ asset($orgBio->header) }}" alt="Header Image" class="img-thumbnail mt-2">
        @else
            <img id="headerPreview" src="{{ asset('storage/user_photos/no_image.png') }}" alt="No Image" width="150" class="img-thumbnail mt-2">
        @endif
    </div>

    <div class="form-group">
        <label for="name">لوگو</label>
        <input class="form-control input-solid" id="logos" name="logos" type="file" required>
        <span id="orgProfileLogosError" class="text-danger"></span>
        @if ($orgBio->logos)
            <img id="logoPreview" src="{{ asset($orgBio->logos) }}" alt="Logo Image" width="150" class="img-thumbnail mt-2">
        @else
            <img id="logoPreview" src="{{ asset('storage/user_photos/no_image.png') }}" alt="No Image" width="150" class="img-thumbnail mt-2">
        @endif
    </div>
    
</form>

