<form id="incomeTypeForm">
   @csrf
    <input type="hidden" id="incomeTypeId">
    <div class="form-group">
        <label for="name">نام کتگوری عواید </label>
        <input type="text" class="form-control" name="name" required placeholder="نام را وارد کنید">
        <span id="incomeTypeNameError" class="text-danger"></span>
    </div>
    
</form>

