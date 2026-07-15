<form id="preListForm">
   @csrf
   <div class="row">
        
            <div class="form-group col-6">
                 <label for="name">  {{__('common.category')}} </label>
                <select name="category_id" class="form-control select2" style="width:100%">
                    <option value="">{{__('buy.select_category')}}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        <div class="form-group col-6">
            <label for="name">  {{__('common.name')}} </label>
            <input type="text" class="form-control" name="name" required >
            <span id="preListNameError" class="text-danger"></span>
       </div>

    </div> 
</form>

