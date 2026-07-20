<form id="preListForm">
   @csrf
   <div class="row">
        
            <div class="form-group col-4">
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

        <div class="form-group col-4">
            <label for="name">  {{__('common.name')}} </label>
            <input type="text" class="form-control" name="name" required >
            <span id="preListNameError" class="text-danger"></span>
       </div>

        <div class="form-group col-4">
            <label for="name">  {{__('common.default_unit')}} </label>
            <select name="unit_id" class="form-control select2" style="width:100%">
                <option value="">{{__('common.unit')}}</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>

    </div> 
</form>

