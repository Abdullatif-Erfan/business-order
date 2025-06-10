
<form id="editBranchForm">
   @csrf
   <input type="hidden" name="id" value="{{ $branch->id ?? 0 }}">
   <div class="row">
        <div class="form-group col-md-6">
            <label for="name"> {{ __('settings.branch_name')}} </label>
            <input type="text" class="form-control" name="name" value="{{ $branch->name ?? '' }}" required >
            <span id="bNameError2" class="text-danger"></span>
        </div>

        <div class="form-group col-md-6">
            <label for="responsible"> {{ __('settings.branch_resp')}}</label>
            <input type="text" class="form-control" name="responsible" value="{{ $branch->responsible ?? '' }}" required>
            <span id="bResponsibleError2" class="text-danger"></span>
        </div>

        <div class="form-group col-md-6">
            <label for="address"> {{ __('settings.branch_phone')}}</label>
            <input type="text" class="form-control" name="phone" required value="{{ $branch->phone ?? '' }}" >
            <span id="bPhoneError2" class="text-danger"></span>
        </div>

        <div class="form-group col-md-6">
            <label for="address"> {{ __('settings.branch_email')}}</label>
            <input type="email" class="form-control" name="email" value="{{ $branch->email ?? '' }}" >
            <span id="bEmailError2" class="text-danger"></span>
        </div>

        <div class="form-group col-md-12">
            <label for="address"> {{ __('settings.branch_address')}}</label>
            <input type="text" class="form-control" name="address" value="{{ $branch->address ?? '' }}" required>
            <span id="bAddressError2" class="text-danger"></span>
        </div>
   </div>

</form>

