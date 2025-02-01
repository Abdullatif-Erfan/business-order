<form id="currencyEditForm">
   @csrf
    <div class="col-xs-12">
        <div class="row">
        <input type="hidden" name="id" value="{{ $currency->id }}">
           <div class="form-group col-sm-6">
                <label for="name">نام واحد پولی </label>
                <input type="text" class="form-control" name="name" value="{{ $currency->name }}" required placeholder="نام را وارد کنید">
                <span id="currencyNameError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="symbols">  علامت مختصر را بنویسید </label>
                <input type="text" class="form-control" name="symbols" value="{{ $currency->symbols }}" required placeholder="سمبول ویا نام مختصر">
                <span id="symbolsError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="symbols">    نمایش رنگ واحد پولی </label>
                <input type="color" class="form-control" name="color" value="{{ $currency->color }}" placeholder="  نمایش رنگ واحد پولی">
                <span id="colorError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="symbols"> انتخاب پول پایه </label>
                <select class="form-control"  name="is_base">
                    <option value="{{ $currency->is_base }}">{{ $currency->is_base == 'yes' ? 'بلی' : 'نخیر' }}</option>
                    <option value="0">انتخاب پول پایه</option>
                    <option value="yes">بلی</option>
                    <option value="no">نخیر</option>
                </select>
                <span id="isBaseError" class="text-danger"></span>

            </div>


        </div>
    </div>

    
</form>

