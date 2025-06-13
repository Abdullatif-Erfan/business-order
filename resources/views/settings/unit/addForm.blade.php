<form id="unitForm">
   @csrf
    <input type="hidden" id="unitId">
    <div class="form-group">
        <label for="name">  {{__('common.name')}} </label>
        <input type="text" class="form-control" name="name" required >
        <span id="unitNameError" class="text-danger"></span>
    </div>
    
</form>

