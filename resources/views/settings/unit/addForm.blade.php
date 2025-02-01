<form id="unitForm">
   @csrf
    <input type="hidden" id="unitId">
    <div class="form-group">
        <label for="name">نام واحد اجناس </label>
        <input type="text" class="form-control" name="name" required placeholder="نام را وارد کنید">
        <span id="unitNameError" class="text-danger"></span>
    </div>
    
</form>

