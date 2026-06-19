<form id="categoryEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $category->id }}">
    <div class="form-group">
        <label for="name">  {{__('common.name')}} </label>
        <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
        <span id="categoryNameError" class="text-danger"></span>
    </div>
    
</form>

