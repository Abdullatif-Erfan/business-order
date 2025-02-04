<form id="accountForm">
   @csrf
    <div class="col-xs-12">
        <div class="row">
          
           <div class="form-group col-sm-6">
                <label for="account_type_id"> انتخاب نوع حساب </label>
                <select class="form-control"  name="account_type_id" required>
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

            @if(count($branchs) >= 2)
            <div class="form-group col-sm-12">
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
                            <label for="transaction_type"> انتخاب گزینه </label>
                            <select class="form-control" name="transaction_type[]" required>
                                <option value=""> انتخاب گزینه </option>
                                <option value="1">افزایش در حساب (طلب) </option>
                                <option value="2">کاهش از حساب (باقی)</option>
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



