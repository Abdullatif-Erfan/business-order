<form id="currencyForm">
    @csrf
    <div class="col-xs-12">
        <div class="row">

            <div class="form-group col-sm-6">
                <label for="name">{{ __('settings.name_label') }}</label>
                <input type="text" class="form-control" name="name" required placeholder="{{ __('settings.name_placeholder') }}">
                <span id="currencyNameError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="symbols">{{ __('settings.symbol_label') }}</label>
                <input type="text" class="form-control" name="symbols" required placeholder="{{ __('settings.symbol_placeholder') }}">
                <span id="symbolsError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-12">
                <label for="color">{{ __('settings.color_label') }}</label>
                <input type="color" class="form-control" name="color" placeholder="{{ __('settings.color_placeholder') }}" style="height: 30px !important;">
                <span id="colorError" class="text-danger"></span>
            </div>

            <!--
            <div class="form-group col-sm-6">
                <label for="is_base">{{ __('currency.base_currency_label') }}</label>
                <select class="form-control" name="is_base">
                    <option value="0">{{ __('currency.base_currency_option') }}</option>
                    <option value="yes">{{ __('currency.yes') }}</option>
                    <option value="no">{{ __('currency.no') }}</option>
                </select>
                <span id="isBaseError" class="text-danger"></span>
            </div>
            -->

        </div>
    </div>
</form>
