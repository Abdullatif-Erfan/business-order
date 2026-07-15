<form id="carEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $car->id }}">
    <div class="form-group">
        <label for="name">  {{__('common.name')}} </label>
        <input type="text" class="form-control" name="name" value="{{ $car->name }}" required>
        <span id="carNameError" class="text-danger"></span>
    </div>
    
</form>

