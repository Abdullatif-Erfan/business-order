<form id="incomeTypeForm">
   @csrf
    <input type="hidden" id="incomeTypeId">
    <div class="form-group">
        <label for="name"> {{ __('settings.income_category') }} </label>
        <input type="text" class="form-control" name="name" required >
        <span id="incomeTypeNameError" class="text-danger"></span>
    </div>
    
</form>

