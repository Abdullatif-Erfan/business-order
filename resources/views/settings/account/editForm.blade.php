<form id="accountEditForm">
   @csrf
   <input type="hidden" name="id" value="{{ $account->id }}">
    <div class="col-xs-12">
        <div class="row">
          
           <div class="form-group col-sm-6">
                <label for="account_type_id"> انتخاب نوع حساب </label>
                <select class="form-control"  name="account_type_id"  required>
                    <option value="{{ $account->account_type_id }}">  {{ $account->accountType->name }} </option>
                    <option value="">انتخاب نوع حساب</option>
                    @foreach($accountTypes as $accountType)
                    <option value="{{ $accountType->id }}">{{ $accountType->name }}</option>
                    @endforeach
                </select>
                <span id="accountTypeIdError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="name"> نام حساب  </label>
                <input type="text" class="form-control" name="name" value="{{ $account->name }}" required placeholder="نام حساب را بنویسید">
                <span id="accountNameError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="phone"> شماره تماس </label>
                <input type="text" class="form-control" name="phone" value="{{ $account->phone }}"  placeholder="شماره تماس ...">
                <span id="phoneError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="address"> آدرس </label>
                <input type="text" class="form-control" name="address" value="{{ $account->address }}"  placeholder=" آدرس ...">
                <span id="addressError" class="text-danger"></span>
            </div>

            
            <div class="col-12">
              <hr />
               <h3>رسید حساب سابقه</h3>
            </div>
           

            <!-- form repeater -->
            <div id="formContainer" class="col-12" style="background:#eef2ff;">
                @if($journals->isEmpty()) 
                    <!-- If no records exist, show an empty row -->
                    <div class="repeatable-form row">
                        <div class="form-group col-sm-3">
                            <label for="amount"> مبلغ </label>
                            <input type="number" step="0.01" class="form-control" name="amount[]" placeholder=" مبلغ ...">
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="transaction_type"> انتخاب گزینه  </label>
                            <select class="form-control" name="transaction_type[]" required>
                                <option value=""> انتخاب گزینه </option>
                                <option value="1"> افزایش در حساب (طلب) </option>
                                <option value="2"> کاهش از حساب (باقی) </option>
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
                            <button type="button" class="btn btn-sm btn-danger remove">❌ حذف</button>
                        </div>
                    </div>
                @else
                    @foreach($journals as $index => $item)
                    <input type="hidden" name="times" value="{{ $item['times'] }}">
                    <input type="hidden" name="code" value="{{ $item['code'] }}">
                    <div class="repeatable-form row">
                        <div class="form-group col-sm-3">
                            <label for="amount"> مبلغ </label>
                            <input type="number" step="0.01" class="form-control" name="amount[]" value="{{ $item['amount'] }}" placeholder=" مبلغ ...">
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="transaction_type"> انتخاب گزینه  </label>
                            <select class="form-control" name="transaction_type[]" required>
                                <option value=""> انتخاب گزینه </option>
                                <option value="1" {{ $item['transaction_type'] == 1 ? 'selected' : '' }}>افزایش در حساب (طلب)</option>
                                <option value="2" {{ $item['transaction_type'] == 2 ? 'selected' : '' }}>کاهش از حساب (باقی)</option>
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="currency_id"> انتخاب واحد پولی </label>
                            <select class="form-control" name="currency_id[]" required>
                                <option value=""> انتخاب واحد پولی </option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" 
                                        {{ $item['currency_id'] == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-2">
                            <br />
                            @if($index == 0) <!-- Show Add More button only for the first row -->
                                <button type="button" class="btn btn-sm btn-success m-t-10 add-more">➕ افزودن</button>
                            @endif
                            <button type="button" class="btn btn-sm btn-danger remove">❌ حذف</button> <!-- Show Remove button for all rows -->
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            <!-- /form repeater -->




        </div>
    </div>

    
</form>

