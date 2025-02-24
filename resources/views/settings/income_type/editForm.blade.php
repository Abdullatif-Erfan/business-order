<form id="incomeTypeEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $incomeType->id }}">
    <div class="form-group">
        <label for="name">نام کتگوری عواید </label>
        <input type="text" class="form-control" name="name" value="{{ $incomeType->name }}" required placeholder="نام را وارد کنید">
        <span id="incomeTypeNameError" class="text-danger"></span>
    </div>
    
</form>

