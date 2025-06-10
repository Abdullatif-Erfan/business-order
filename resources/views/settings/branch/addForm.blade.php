
<form id="branchForm">
   @csrf
   <div class="row">
    <div class="form-group col-md-6">
            <label for="name">  {{ __('settings.branch_name')}} </label>
            <input type="text" class="form-control" name="name" required >
            <span id="bNameError" class="text-danger"></span>
        </div>
        <div class="form-group col-md-6">
            <label for="responsible">{{ __('settings.branch_resp')}}</label>
            <input type="text" class="form-control" name="responsible" required>
            <span id="bResponsibleError" class="text-danger"></span>
        </div>
        <div class="form-group col-md-6">
            <label for="address">{{ __('settings.branch_phone')}}</label>
            <input type="text" class="form-control" name="phone" required >
            <span id="bPhoneError" class="text-danger"></span>
        </div>
        <div class="form-group col-md-6">
            <label for="address"> {{ __('settings.branch_email')}}</label>
            <input type="email" class="form-control" name="email"  >
            <span id="bEmailError" class="text-danger"></span>
        </div>

        <div class="form-group col-md-12">
            <label for="address"> {{ __('settings.branch_address')}}</label>
            <input type="text" class="form-control" name="address" required >
            <span id="bAddressError" class="text-danger"></span>
        </div>
   </div>

</form>

