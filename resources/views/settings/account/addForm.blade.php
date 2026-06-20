<form id="accountForm">
   @csrf
    <div class="col-xs-12">
        <div class="row">
          
           <div class="form-group col-sm-6">
                <label for="account_type_id"> {{__('settings.account_type_selection')}} </label>
                <select class="form-control"  name="account_type_id" required onchange="checkAccountType(this.value)">
                    <option value="">{{__('settings.account_type_selection')}}</option>
                    @foreach($accountTypes as $accountType)
                    <option value="{{ $accountType->id }}">{{ $accountType->name }}</option>
                    @endforeach
                </select>
                <span id="accountTypeIdError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="name"> {{__('settings.account_name')}}  </label>
                <input type="text" class="form-control" name="name" required >
                <span id="accountNameError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="phone"> {{__('settings.phone')}}  </label>
                <input type="text" class="form-control" name="phone" >
                <span id="phoneError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="address"> {{__('settings.address')}}  </label>
                <input type="text" class="form-control" name="address" >
                <span id="addressError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6" id="percent" style="display:none;">
                <label for="percent"> {{__('settings.percentage')}} </label>
                <input type="number" class="form-control" name="percent" >
                <span id="percentError" class="text-danger"></span>
            </div>

            <!-- belongs to employee -->
                <div class="form-group col-sm-6" id="net_salary" style="display:none;">
                    <label for="percent"> {{ __('settings.net_salary')}} </label>
                    <input type="number" class="form-control" name="net_salary" >
                    <span id="netSalaryError" class="text-danger"></span>
                </div>

                <div class="form-group col-sm-6" id="salary_currency" style="display:none;">
                    <label for="percent"> {{ __('common.currency')}}</label>
                    <select class="form-control" name="salary_currency">
                        <!-- <option value=""> {{ __('settings.paid_currency')}} </option> -->
                        @foreach($currencies as $currency)
                           <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                        @endforeach
                    </select>
                </div>
            <!-- /belongs to employee -->


            <!-- belongs to qarz limit for customers and suppliers -->
                <div class="form-group col-sm-6" id="loan_limit" style="display:none;">
                    <label for="percent"> {{ __('settings.loan_limit')}} </label>
                    <input type="number" class="form-control" name="loan_limit" >
                    <span id="loanLimitError" class="text-danger"></span>
                </div>

                <div class="form-group col-sm-6" id="loan_limit_option" style="display:none;">
                    <label for="percent"> {{ __('settings.loan_limit_option')}}</label>
                    <select class="form-control" name="loan_limit_option">
                        <option value="0">{{ __('settings.no') }}</option>
                        <option value="1">{{ __('settings.yes') }}</option>
                    </select>
                    <span id="loanLimitOptionError" class="text-danger"></span>
                </div>
            <!-- /belongs to qarz limit for customers and suppliers -->


            <div class="form-group col-sm-6" id="is_pre_select" style="display:none;">
                <label for="is_pre_select"> {{ __('settings.default_account') }}    </label>
                <select class="form-control" name="is_pre_select" >
                    <option value="0">{{ __('settings.yes') }}</option>
                    <option value="1">{{ __('settings.no') }}</option>
                </select>
            </div>

         

            <div class="col-12">
              <hr />
               <h3> {{ __('settings.paid_old_journal')}} </h3>
            </div>
           

            <!-- form repeater -->
                <div id="formContainer" class="col-12" style="background:#eef2ff;">
                    <div class="repeatable-form row">
                        <div class="form-group col-sm-3">
                            <label for="amount"> {{ __('settings.amount')}} </label>
                            <input type="number" step="0.01" class="form-control" name="amount[]">
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="options"> {{ __('settings.option_selection') }} </label>
                            <select class="form-control" name="options[]" required>
                                <option value="">  {{__('settings.option_selection')}} </option>
                                <option value="1"> {{__('settings.increase_cache')}} </option>
                                <option value="2"> {{__('settings.save_in_talabat')}} </option>
                                <option value="3"> {{__('settings.save_in_qarza')}} </option>
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="currency_id"> {{__('settings.currency_selection')}} </label>
                            <select class="form-control" name="currency_id[]" required>
                                <!-- <option value=""> {{__('settings.currency_selection')}} </option> -->
                                @foreach($currencies as $currency)
                                 <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-2">
                            <br />
                            <button type="button" class="btn btn-sm btn-success m-t-10 add-more font-10">➕ </button>
                            <button type="button" class="btn btn-sm btn-danger remove m-t-10 font-10" style="display:none;">❌ </button>
                        </div>
                    </div>
                </div>
            <!-- /form repeater -->
            

        </div>
    </div>

    
</form>


<script>
function checkAccountType(account_type_id) {
    /**
     * 1: حساب شرکت
     * 2: کارمندان
     * 3: مشتریان
     * 4: تهیه کنندگان
     * 5: سهم داران
     * 6: صرافی و بانک
     */
    
    if (parseInt(account_type_id) === 1) {
        $('#is_pre_select').fadeIn(1).attr('required', true);
        $('#percent, #net_salary, #salary_currency, #loan_limit, #loan_limit_option').fadeOut(1).removeAttr('required');

        // Show only the first option in the select dropdowns
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="1"> {{__('settings.increase_cache')}} </option>
            `);
        });
    } 
    else if (parseInt(account_type_id) === 2) {
        $('#net_salary, #salary_currency').fadeIn(1).attr('required', true);
        $('#is_pre_select, #percent, #loan_limit, #loan_limit_option').fadeOut(1).removeAttr('required');

       // Reset the select options to show all options
       $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value=""> {{ __('settings.option_selection') }} </option>
                <option value="2"> {{__('settings.save_in_talabat')}} </option>
                <option value="3"> {{__('settings.save_in_qarza')}} </option>
            `);
        });
    } 
    else if (parseInt(account_type_id) === 3 || parseInt(account_type_id) === 4) {
        $('#loan_limit, #loan_limit_option').fadeIn(1).attr('required', true);
        $('#net_salary, #is_pre_select, #salary_currency, #percent')
            .fadeOut(1)
            .removeAttr('required');

        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value=""> {{ __('settings.option_selection') }} </option>
                <option value="2"> {{__('settings.save_in_talabat')}} </option>
                <option value="3"> {{__('settings.save_in_qarza')}} </option>
            `);
        });
    } 
    else if (parseInt(account_type_id) === 5) {
        $('#percent').fadeIn(1).attr('required', true);
        $('#is_pre_select, #salary_currency, #net_salary, #loan_limit, #loan_limit_option')
            .fadeOut(1)
            .removeAttr('required');

        // Reset the select options to show all options
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value=""> {{ __('settings.option_selection') }} </option>
                <option value="1"> {{__('settings.increase_cache')}}</option>
                <option value="2"> {{__('settings.save_in_talabat')}} </option>
                <option value="3"> {{__('settings.save_in_qarza')}} </option>
            `);
        });
    } else if (parseInt(account_type_id) === 6) {
        $('#percent, #net_salary, #salary_currency, #is_pre_select, #loan_limit, #loan_limit_option').fadeOut(1).removeAttr('required');

        // Show only the first option in the select dropdowns
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="1"> {{__('settings.increase_cache')}}</option>
            `);
        });
    } 
}

</script>
