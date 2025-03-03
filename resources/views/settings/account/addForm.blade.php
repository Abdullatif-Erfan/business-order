<form id="accountForm">
   @csrf
    <div class="col-xs-12">
        <div class="row">
          
           <div class="form-group col-sm-6">
                <label for="account_type_id"> انتخاب نوع حساب </label>
                <select class="form-control"  name="account_type_id" required onchange="checkAccountType(this.value)">
                    <option value="">انتخاب نوع حساب</option>
                    @foreach($accountTypes as $accountType)
                    <option value="{{ $accountType->id }}">{{ $accountType->name }}</option>
                    @endforeach
                </select>
                <span id="accountTypeIdError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="name"> نام حساب  </label>
                <input type="text" class="form-control" name="name" required placeholder="نام حساب را بنویسید">
                <span id="accountNameError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="phone"> شماره تماس </label>
                <input type="text" class="form-control" name="phone"  placeholder="شماره تماس ...">
                <span id="phoneError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="address"> آدرس </label>
                <input type="text" class="form-control" name="address"  placeholder=" آدرس ...">
                <span id="addressError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6" id="percent" style="display:none;">
                <label for="percent"> فیصدی </label>
                <input type="number" class="form-control" name="percent"  placeholder="فیصدی سهم این سهامدار را بنویسید">
                <span id="percentError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6" id="is_pre_select" style="display:none;">
                <label for="is_pre_select"> صرف یک حساب پیش فرض انتخاب نمایید    </label>
                <select class="form-control" name="is_pre_select" >
                    <option value="0">نخیر</option>
                    <option value="1">بلی</option>
                </select>
            </div>

            @if(count($branchs) >= 2)
            <div class="form-group col-sm-6">
                <label for="account_type_id"> انتخاب شعبه </label>
                <select class="form-control"  name="branch_id" required>
                    <option value="">انتخاب  شعبه</option>
                    @foreach($branchs as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <span id="branchError" class="text-danger"></span>
            </div>
            @elseif(count($branchs) == 1) 
                <input type="hidden" value="{{ $branchs[0]->id }}" name="branch_id" required>
            @endif


            <div class="col-12">
              <hr />
               <h3>رسید حساب سابقه </h3>
            </div>
           

            <!-- form repeater -->
                <div id="formContainer" class="col-12" style="background:#eef2ff;">
                    <div class="repeatable-form row">
                        <div class="form-group col-sm-3">
                            <label for="amount"> مبلغ </label>
                            <input type="number" step="0.01" class="form-control" name="amount[]" placeholder=" مبلغ ...">
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="options"> انتخاب گزینه </label>
                            <select class="form-control" name="options[]" required>
                                <option value=""> انتخاب گزینه </option>
                                <option value="1"> افزایش پول نقد</option>
                                <option value="2"> ثبت در بخش طلبات </option>
                                <option value="3"> ثبت در بخش قرضه </option>
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="currency_id"> انتخاب واحد پولی </label>
                            <select class="form-control" name="currency_id[]" required>
                                <option value=""> انتخاب واحد پولی </option>
                                @foreach($currencies as $currency)
                                 <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-2">
                            <br />
                            <button type="button" class="btn btn-sm btn-success m-t-10 add-more">➕ افزودن</button>
                            <button type="button" class="btn btn-sm btn-danger remove" style="display:none;">❌ حذف</button>
                        </div>
                    </div>
                </div>
            <!-- /form repeater -->
            

        </div>
    </div>

    
</form>


<script>
 function checkAccountType(account_type_id)
 {
     if(parseInt(account_type_id) === 5) {
        $('#percent').fadeIn(1);
        $('#is_pre_select').fadeOut(1);
     } 
     else if(parseInt(account_type_id) === 1) 
     {
         $('#is_pre_select').fadeIn(1);
         $('#percent').fadeOut(1);

        //   Show only the first option in the select dropdowns
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="1"> افزایش پول نقد</option>
            `);
        });
     }
     else 
     {
        $('#percent').fadeOut(1);
        $('#is_pre_select').fadeOut(1);

        // Reset the select options to show all options
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value=""> انتخاب گزینه </option>
                <option value="1"> افزایش پول نقد</option>
                <option value="2"> ثبت در بخش طلبات </option>
                <option value="3"> ثبت در بخش قرضه </option>
            `);
        });
     }
 }


</script>
