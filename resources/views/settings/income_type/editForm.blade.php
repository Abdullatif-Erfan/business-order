<form id="incomeTypeEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $incomeType->id }}">
    <div class="form-group">
        <label for="name"> {{ __('settings.income_category') }} </label>
        <input type="text" class="form-control" name="name" value="{{ $incomeType->name }}" required>
        <span id="incomeTypeNameError" class="text-danger"></span>
    </div>
    
</form>

