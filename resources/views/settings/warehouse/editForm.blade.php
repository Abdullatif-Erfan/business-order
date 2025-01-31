<form id="warehouseEditForm">
   @csrf
    <input type="hidden" name="id" value="{{ $warehouse->id }}">
    <div class="form-group">
        <label for="name">نام گدام</label>
        <input type="text" class="form-control" name="name" value="{{ $warehouse->name }}" required placeholder="نام گدام را وارد کنید">
        <span id="wHnameError" class="text-danger"></span>
    </div>

    <div class="form-group">
        <label for="branch_id">شعبه مربوطه</label>
        <select class="form-control select2 " id="branch_id" 
            required style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id" > 
            <option value="{{ $warehouse->branch_id }}">  {{ $warehouse->branch->name }} </option>
            <option value="">  --- انتخاب شعبه --- </option>
            @foreach($branchs as $branch){ ?>
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
        </select> 

        <span id="branchError" class="text-danger"></span>
    </div>


    <div class="form-group">
        <label for="responsible">مسول گدام</label>
        <input type="text" class="form-control" name="responsible" value="{{ $warehouse->responsible }}" required placeholder="نام مسول را وارد کنید">
        <span id="responsibleError" class="text-danger"></span>
    </div>
    <div class="form-group">
        <label for="address">آدرس</label>
        <input type="text" class="form-control" name="address" value="{{ $warehouse->address }}" required placeholder="آدرس را وارد کنید">
        <span id="addressError" class="text-danger"></span>
    </div>
</form>
