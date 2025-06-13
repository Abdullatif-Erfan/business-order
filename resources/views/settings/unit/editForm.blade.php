<form id="unitEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $unit->id }}">
    <div class="form-group">
        <label for="name">  {{__('common.name')}} </label>
        <input type="text" class="form-control" name="name" value="{{ $unit->name }}" required>
        <span id="unitNameError" class="text-danger"></span>
    </div>
    
</form>

