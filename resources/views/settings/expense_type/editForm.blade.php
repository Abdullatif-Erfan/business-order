<form id="expenseTypeEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $expenseType->id }}">
    <div class="form-group">
        <label for="name">نام کتگوری مصارف </label>
        <input type="text" class="form-control" name="name" value="{{ $expenseType->name }}" required placeholder="نام را وارد کنید">
        <span id="expenseTypeNameError" class="text-danger"></span>
    </div>
    
</form>

