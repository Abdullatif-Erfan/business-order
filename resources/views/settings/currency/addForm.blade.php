<form id="currencyForm">
   @csrf
    <div class="col-xs-12">
        <div class="row">
          
           <div class="form-group col-sm-6">
                <label for="name">نام واحد پولی </label>
                <input type="text" class="form-control" name="name" required placeholder="نام را وارد کنید">
                <span id="currencyNameError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="symbols">  علامت مختصر را بنویسید </label>
                <input type="text" class="form-control" name="symbols" required placeholder="سمبول ویا نام مختصر">
                <span id="symbolsError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-12">
                <label for="symbols">    نمایش رنگ واحد پولی </label>
                <input type="color" class="form-control" name="color"  placeholder="  نمایش رنگ واحد پولی" style="height: 30px !important;">
                <span id="colorError" class="text-danger"></span>
            </div>

            <!-- <div class="form-group col-sm-6">
                <label for="symbols"> انتخاب پول پایه </label>
                <select class="form-control"  name="is_base">
                    <option value="0">انتخاب پول پایه</option>
                    <option value="yes">بلی</option>
                    <option value="no">نخیر</option>
                </select>
                <span id="isBaseError" class="text-danger"></span>

            </div> -->


        </div>
    </div>

    
</form>

