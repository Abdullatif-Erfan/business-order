<form id="expenseTypeForm">
   @csrf
    <input type="hidden" id="expenseTypeId">
    <div class="form-group">
        <label for="name">نام کتگوری مصارف </label>
        <input type="text" class="form-control" name="name" required placeholder="نام را وارد کنید">
        <span id="expenseTypeNameError" class="text-danger"></span>
    </div>
    
</form>

