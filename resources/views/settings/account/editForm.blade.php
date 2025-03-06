<form id="accountEditForm">
   @csrf
   <input type="hidden" name="id" value="{{ $account->id }}">
    <div class="col-xs-12">
        <div class="row">
          
           <div class="form-group col-sm-6">
                <label for="account_type_id"> انتخاب نوع حساب </label>
                <select class="form-control"  name="account_type_id"  onchange="checkAccountTypeEdit(this.value)"  required>
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


            <!-- belongs to employee -->
            @if($account->account_type_id == 2)
            <div class="form-group col-sm-6" id="net_salary2">
                    <label for="percent"> معاش خالص ماهانه </label>
                    <input type="number" class="form-control" name="net_salary" value="{{ $account->net_salary }}"  placeholder="معاش خالص ماهانه را بنویسید">
                    <span id="netSalaryError" class="text-danger"></span>
                </div>

                <div class="form-group col-sm-6" id="salary_currency2">
                    <label for="percent">  پرداخت معاش به کرنسی </label>
                    <select class="form-control" name="salary_currency">
                        <option value=""> انتخاب کرنسی </option>
                        @foreach($currencies as $currency)
                           <option value="{{ $currency->id }}" {{ $currency->id == $account->salary_currency ? 'selected': '' }} >
                           {{ $currency->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <!-- /belongs to employee -->

            @if($account->account_type_id == 5)
            <div class="form-group col-sm-6" id="percent2">
                <label for="percent"> فیصدی </label>
                <input type="number" class="form-control" name="percent" value="{{ $account->percent }}"  placeholder="فیصدی سهم این سهامدار را بنویسید">
                <span id="percentError" class="text-danger"></span>
            </div>
            @endif

            
            @if($account->account_type_id == 1)
            <div class="form-group col-sm-6" id="is_pre_select2">
                <label for="is_pre_select"> انتخاب حساب پیش فرض / دیفالت </label>
                <select class="form-control" name="is_pre_select" >
                    <option value="{{ $account->is_pre_select }}">{{ $account->is_pre_select == 1 ? 'بلی':'نخیر' }}</option>
                    <option value="0">نخیر</option>
                    <option value="1">بلی</option>
                </select>
            </div>
            @endif


            @if(count($branchs) >= 2)
            <div class="form-group col-sm-6">
                <label for="account_type_id"> انتخاب شعبه </label>
                <select class="form-control"  name="branch_id" required>
                    <option value="">انتخاب  شعبه</option>
                    @foreach($branchs as $branch)
                    <option value="{{ $branch->id }}" 
                    {{ $account['branch_id'] == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <span id="branchError" class="text-danger"></span>
            </div>
            @elseif(count($branchs) == 1) 
                <input type="hidden" value="{{ $branchs[0]->id }}" name="branch_id" required>
            @endif

            
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
                            <label for="options"> انتخاب گزینه  </label>
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
                            <label for="options"> انتخاب گزینه  </label>
                            <select class="form-control" name="options[]" required>
                                <option value=""> انتخاب گزینه </option>
                                <option value="1" {{ $item['options'] == 1 ? 'selected' : '' }}> افزایش پول نقد</option>
                                <option value="2" {{ $item['options'] == 2 ? 'selected' : '' }}> ثبت در بخش طلبات </option>
                                <option value="3" {{ $item['options'] == 3 ? 'selected' : '' }}> ثبت در بخش قرضه </option>
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

<script>
$(document).ready(function () {
    // Run checkAccountTypeEdit on page load with the selected value
    var selectedAccountType = $('select[name="account_type_id"]').val();
    checkAccountTypeEdit(selectedAccountType);
});
function checkAccountTypeEdit(account_type_id) {
    /**
     * 1: حساب شرکت
     * 2: کارمندان
     * 3: مشتریان
     * 4: فروشندگان
     * 5: سهم داران
     */
    account_type_id = parseInt(account_type_id);
   if (account_type_id === 1) {
        $('#is_pre_select2').show().attr('required', true);
        $('#percent2, #net_salary2, #salary_currency2').hide().removeAttr('required');

        // Show only the first option in the select dropdowns
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="1"> افزایش پول نقد</option>
            `);
        });
    } 
    else if (account_type_id === 2) {
        $('#net_salary2, #salary_currency2').show().attr('required', true);
        $('#is_pre_select2, #percent2').hide().removeAttr('required');

       // Reset the select options to show all options
       $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value=""> انتخاب گزینه </option>
                <option value="2"> ثبت در بخش طلبات </option>
                <option value="3"> ثبت در بخش قرضه </option>
            `);
        });
    } 
    else if (account_type_id === 3 || account_type_id === 4) {
        $('#net_salary2, #is_pre_select2, #salary_currency2, #percent2')
            .hide()
            .removeAttr('required');

        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value=""> انتخاب گزینه </option>
                <option value="2"> ثبت در بخش طلبات </option>
                <option value="3"> ثبت در بخش قرضه </option>
            `);
        });
    } 
    else if (account_type_id === 5) {
        $('#percent2').show().attr('required', true);
        $('#is_pre_select2, #salary_currency2, #net_salary2').hide().removeAttr('required');

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
