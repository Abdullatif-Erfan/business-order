<form id="warehouseForm">
   @csrf
    <input type="hidden" id="warehouseId">
    <div class="form-group">
        <label for="name">نام گدام</label>
        <input type="text" class="form-control" name="name" required placeholder="نام گدام را وارد کنید">
        <span id="wHnameError" class="text-danger"></span>
    </div>
    <div class="form-group">
        <label for="branch_id">شعبه مربوطه</label>
        <select  class="form-control select2 " id="branch_id" required style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id" > 
            <!-- <option value=""> انتخاب شعبه</option> -->
            @foreach($branchs as $branch){ ?>
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
        </select> 

        <span id="branchError" class="text-danger"></span>
    </div>


    <div class="form-group">
        <label for="responsible">مسول گدام</label>
        <input type="text" class="form-control" name="responsible" required placeholder="نام مسول را وارد کنید">
        <span id="responsibleError" class="text-danger"></span>
    </div>
    <div class="form-group">
        <label for="address">آدرس</label>
        <input type="text" class="form-control" name="address" required placeholder="آدرس را وارد کنید">
        <span id="addressError" class="text-danger"></span>
    </div>
</form>

