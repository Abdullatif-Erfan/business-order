<form id="expenseTypeForm">
   @csrf
    <input type="hidden" id="expenseTypeId">
    <div class="form-group">
        <label for="name"> {{ __('settings.expense_category') }} </label>
        <input type="text" class="form-control" name="name" required >
        <span id="expenseTypeNameError" class="text-danger"></span>
    </div>
    
</form>

