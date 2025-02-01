<form id="unitEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $unit->id }}">
    <div class="form-group">
        <label for="name">نام واحد اجناس </label>
        <input type="text" class="form-control" name="name" value="{{ $unit->name }}" required placeholder="نام را وارد کنید">
        <span id="unitNameError" class="text-danger"></span>
    </div>
    
</form>

