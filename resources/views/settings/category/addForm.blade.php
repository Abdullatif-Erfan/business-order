<form id="categoryForm">
   @csrf
    <input type="hidden" id="unitId">
    <div class="form-group col-12">
        <label for="name">  {{__('common.name')}} </label>
        <input type="text" class="form-control" name="name" required >
        <span id="categoryNameError" class="text-danger"></span>
    </div>

    <div class="form-group col-12">
        <label for="name">  {{__('order.supplier_name')}} </label>
        <select name="supplier_id" id="supplier_id" required class="form-control select2" style="width:100%">
            <option value="">{{__('order.supplier_name')}}</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
    </div> 


    
</form>

