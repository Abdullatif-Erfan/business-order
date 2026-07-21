<form id="accountEditForm">
   @csrf
   <input type="hidden" name="id" value="{{ $account->id }}">
    <div class="container-fluid">
        <div class="row">
          
           <div class="form-group col-xs-6 col-sm-4 col-md-4">
                <label for="account_type_id"> {{__('settings.account_type_selection')}}  </label>
                @if($account->accountType->is_disabled == 1)
                <select class="form-control" name="account_type_id" required>
                    <option value="{{ $account->account_type_id }}">{{ $account->accountType->name }}</option>
                </select>
                @else
                <select class="form-control" name="account_type_id" onchange="checkAccountTypeEdit(this.value)" required>
                    <option value="">{{__('settings.account_type_selection')}}</option>
                    @foreach($accountTypes as $accountType)
                    <option value="{{ $accountType->id }}" {{ $account->account_type_id == $accountType->id ? 'selected' : '' }}>
                        {{ $accountType->name }}
                    </option>
                    @endforeach
                </select>
                @endif
                <span id="accountTypeIdError" class="text-danger"></span>
            </div>

            <div class="form-group col-xs-6 col-sm-4 col-md-4">
                <label for="name">{{__('settings.account_name')}}</label>
                <input type="text" class="form-control" name="name" value="{{ $account->name }}" required>
                <span id="accountNameError" class="text-danger"></span>
            </div>

            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="phone_edit">
                <label for="phone">{{__('settings.phone')}}</label>
                <input type="text" class="form-control" name="phone" value="{{ $account->phone }}">
                <span id="phoneError" class="text-danger"></span>
            </div>

            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="address_edit">
                <label for="address">{{__('settings.address')}}</label>
                <input type="text" class="form-control" name="address" value="{{ $account->address }}">
                <span id="addressError" class="text-danger"></span>
            </div>

            <!-- belongs to employee -->
            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="net_salary_edit" style="{{ $account->account_type_id == 2 ? '' : 'display:none;' }}">
                <label for="net_salary">{{ __('settings.net_salary')}}</label>
                <input type="number" class="form-control" name="net_salary" value="{{ $account->net_salary }}">
                <span id="netSalaryError" class="text-danger"></span>
            </div>

            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="salary_currency_edit" style="{{ $account->account_type_id == 2 ? '' : 'display:none;' }}">
                <label for="salary_currency">{{ __('settings.paid_currency')}}</label>
                <select class="form-control" name="salary_currency">
                    <option value="">{{ __('settings.paid_currency')}}</option>
                    @foreach($currencies as $currency)
                       <option value="{{ $currency->id }}" {{ $currency->id == $account->salary_currency ? 'selected': '' }}>
                       {{ $currency->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="emp_car_id_edit" style="{{ $account->account_type_id == 2 || $account->account_type_id == 7 ? '' : 'display:none;' }}">
                <label for="emp_car_id"> {{ __('settings.car')}}</label>
                <select class="form-control" name="emp_car_id">
                    <option value=""> {{ __('settings.car_selection')}} </option>
                    @foreach($cars as $car)
                       <option value="{{ $car->id }}" {{ $car->id == $account->emp_car_id ? 'selected': '' }}>{{ $car->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="emp_start_date_edit" style="{{ $account->account_type_id == 2 ? '' : 'display:none;' }}">
                <div class="filter-group" style="min-width: 120px;">
                    <label for="start_date"> {{ __('common.start_date')}}</label>
                    <div class="input-group">
                        <input type="text" class="form-control datepicker-input" value="{{ $account->emp_start_date ?? '' }}" name="emp_start_date" 
                          placeholder="{{__('common.start_date')}}">
                        <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                </div>
            </div>
            <!-- /belongs to employee -->

            <!-- belongs to qarz limit for customers and suppliers -->
            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="loan_limit_edit" style="{{ ($account->account_type_id == 3 || $account->account_type_id == 4) ? '' : 'display:none;' }}">
                <label for="loan_limit">{{ __('settings.loan_limit')}}</label>
                <input type="number" class="form-control" name="loan_limit" value="{{ $account->loan_limit ?? '' }}">
                <span id="loanLimitError" class="text-danger"></span>
            </div>

            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="loan_limit_option_edit" style="{{ ($account->account_type_id == 3 || $account->account_type_id == 4) ? '' : 'display:none;' }}">
                <label for="loan_limit_option">{{ __('settings.loan_limit_option')}}</label>
                <select class="form-control" name="loan_limit_option">
                    <option value="1" {{ ($account->loan_limit_option ?? '') == '1' ? 'selected' : '' }}>{{ __('settings.yes') }}</option>
                    <option value="0" {{ ($account->loan_limit_option ?? '') == '0' ? 'selected' : '' }}>{{ __('settings.no') }}</option>
                </select>
                <span id="loanLimitOptionError" class="text-danger"></span>
            </div>
            <!-- /belongs to qarz limit for customers and suppliers -->

            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="percent_edit" style="{{ $account->account_type_id == 5 ? '' : 'display:none;' }}">
                <label for="percent">{{__('settings.percentage')}}</label>
                <input type="number" class="form-control" name="percent" value="{{ $account->percent }}">
                <span id="percentError" class="text-danger"></span>
            </div>
            
            <div class="form-group col-xs-6 col-sm-4 col-md-4" id="is_pre_select_edit" style="{{ $account->account_type_id == 1 ? '' : 'display:none;' }}">
                <label for="is_pre_select">{{ __('settings.default_account') }}</label>
                <select class="form-control" name="is_pre_select">
                    <option value="0" {{ $account->is_pre_select == 0 ? 'selected' : '' }}>{{ __('settings.yes') }}</option>
                    <option value="1" {{ $account->is_pre_select == 1 ? 'selected' : '' }}>{{ __('settings.no') }}</option>
                </select>
            </div>

            <div class="col-12">
              <hr />
               <h3>{{ __('settings.paid_old_journal')}}</h3>
            </div>
           
            <!-- form repeater -->
            <div id="formContainer" class="col-12" style="background:#eef2ff;">
                @if($journals->isEmpty()) 
                    <!-- If no records exist, show an empty row -->
                    <div class="repeatable-form row">
                        <div class="form-group col-sm-3">
                            <label for="amount">{{ __('settings.amount')}}</label>
                            <input type="number" step="0.01" class="form-control" name="amount[]">
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="options">{{ __('settings.option_selection') }}</label>
                            <select class="form-control" name="options[]" required>
                                @if($account->account_type_id == 1 || $account->account_type_id == 6)
                                    <option value="1">{{__('settings.increase_cache')}}</option>
                                @else
                                    <option value="">{{__('settings.option_selection')}}</option>
                                    <option value="1">{{__('settings.increase_cache')}}</option>
                                    <option value="2">{{__('settings.save_in_talabat')}}</option>
                                    <option value="3">{{__('settings.save_in_qarza')}}</option>
                                @endif
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="currency_id">{{__('settings.currency_selection')}}</label>
                            <select class="form-control" name="currency_id[]" required>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-2">
                            <br />
                            <button type="button" class="btn btn-sm btn-success m-t-10 add-more font-10">➕</button>
                            <button type="button" class="btn btn-sm btn-danger remove m-t-10 font-10">❌</button>
                        </div>
                    </div>
                @else
                    @foreach($journals as $index => $item)
                    <input type="hidden" name="times" value="{{ $item['times'] }}">
                    <input type="hidden" name="code" value="{{ $item['code'] }}">
                    <div class="repeatable-form row">
                        <div class="form-group col-sm-3">
                            <label for="amount">{{ __('settings.amount')}}</label>
                            <input type="number" step="0.01" class="form-control" name="amount[]" value="{{ $item['amount'] }}">
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="options">{{ __('settings.option_selection') }}</label>
                            <select class="form-control" name="options[]" required>
                                @if($account->account_type_id == 1 || $account->account_type_id == 6)
                                    <option value="1" {{ $item['option'] == 1 ? 'selected' : '' }}>{{__('settings.increase_cache')}}</option>
                                @else
                                    <option value="">{{__('settings.option_selection')}}</option>
                                    <option value="1" {{ $item['option'] == 1 ? 'selected' : '' }}>{{__('settings.increase_cache')}}</option>
                                    <option value="2" {{ $item['option'] == 2 ? 'selected' : '' }}>{{__('settings.save_in_talabat')}}</option>
                                    <option value="3" {{ $item['option'] == 3 ? 'selected' : '' }}>{{__('settings.save_in_qarza')}}</option>
                                @endif
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="currency_id">{{__('settings.currency_selection')}}</label>
                            <select class="form-control" name="currency_id[]" required>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" {{ $item['currency_id'] == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger"></span>
                        </div>

                        <div class="form-group col-sm-2">
                            <br />
                            @if($index == 0)
                                <button type="button" class="btn btn-sm btn-success m-t-10 add-more font-10">➕</button>
                            @endif
                            <button type="button" class="btn btn-sm btn-danger remove m-t-10 font-10">❌</button>
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
    // Initialize datepicker
    $('.datepicker-input').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        clearBtn: true
    });

    // Run checkAccountTypeEdit on page load with the selected value
    var selectedAccountType = $('select[name="account_type_id"]').val();
    if (selectedAccountType) {
        checkAccountTypeEdit(selectedAccountType);
    }
});

$(document).on('click', '.datepicker-icon', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $input = $(this).closest('.input-group').find('input');
    if ($input.length) {
        $input.datepicker('show');
    }
});

function checkAccountTypeEdit(account_type_id) {
    /**
     * 1: حساب شرکت (Company)
     * 2: کارمندان (Employees)
     * 3: مشتریان (Customers)
     * 4: فروشندگان (Suppliers/Vendors)
     * 5: سهم داران (Shareholders)
     * 6: صرافی و بانک (Exchange & Bank)
     * 7: موتر (Car)
     */
    account_type_id = parseInt(account_type_id);
    
    // First hide all conditional fields
    $('#is_pre_select_edit, #percent_edit, #net_salary_edit, #salary_currency_edit, #loan_limit_edit, #loan_limit_option_edit, #emp_start_date_edit, #emp_car_id_edit').hide().removeAttr('required');
    
    if (account_type_id === 1) {
        // Company Account
        $('#is_pre_select_edit, #phone_edit, #address_edit').show().attr('required', true);
        
        // Show only increase cache option
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="1">{{__('settings.increase_cache')}}</option>
            `);
        });
    } 
    else if (account_type_id === 2) {
        // Employee Account
        $('#net_salary_edit, #salary_currency_edit, #emp_start_date_edit, #emp_car_id_edit, #phone_edit, #address_edit').show().attr('required', true);
        
        // Show save in talabat and qarza options
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="">{{ __('settings.option_selection') }}</option>
                <option value="2">{{__('settings.save_in_talabat')}}</option>
                <option value="3">{{__('settings.save_in_qarza')}}</option>
            `);
        });
    } 
    else if (account_type_id === 3 || account_type_id === 4) {
        // Customer or Supplier Account
        $('#loan_limit_edit, #loan_limit_option_edit, #phone_edit, #address_edit').show().attr('required', true);
        // Show save in talabat and qarza options
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="">{{ __('settings.option_selection') }}</option>
                <option value="2">{{__('settings.save_in_talabat')}}</option>
                <option value="3">{{__('settings.save_in_qarza')}}</option>
            `);
        });
    } 
    else if (account_type_id === 5) {
        // Shareholder Account
        $('#percent_edit,#phone_edit, #address_edit').show().attr('required', true);
        
        // Show all options
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="">{{ __('settings.option_selection') }}</option>
                <option value="1">{{__('settings.increase_cache')}}</option>
                <option value="2">{{__('settings.save_in_talabat')}}</option>
                <option value="3">{{__('settings.save_in_qarza')}}</option>
            `);
        });
    } 
    else if (account_type_id === 6) {
        // Exchange & Bank Account
        // Show only increase cache option
        $('#phone_edit, #address_edit').show().attr('required', true);
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="1">{{__('settings.increase_cache')}}</option>
            `);
        });
    } 
    else if (account_type_id === 7) {
        // Car Account
        $('#emp_car_id_edit').show().attr('required', true);
        
        $('#phone_edit, #address_edit').hide().removeAttr('required');

        // Show save in talabat and qarza options
        $('select[name="options[]"]').each(function () {
            $(this).html(`
                <option value="">{{ __('settings.option_selection') }}</option>
                <option value="2">{{__('settings.save_in_talabat')}}</option>
                <option value="3">{{__('settings.save_in_qarza')}}</option>
            `);
        });
    }
}
</script>