<form id="expenseTypeEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $expenseType->id }}">
    <div class="form-group">
        <label for="name">{{ __('settings.expense_category') }} </label>
        <input type="text" class="form-control" name="name" value="{{ $expenseType->name }}" required >
        <span id="expenseTypeNameError" class="text-danger"></span>
    </div>
    
</form>

