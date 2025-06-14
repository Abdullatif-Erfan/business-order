<form id="currencyEditForm">
    @csrf
    <div class="col-xs-12">
        <div class="row">
            <input type="hidden" name="id" value="{{ $currency->id }}">

            <div class="form-group col-sm-6">
                <label for="name">{{ __('settings.name_label') }}</label>
                <input type="text" class="form-control" name="name" value="{{ $currency->name }}" required >
                <span id="currencyNameError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-6">
                <label for="symbols">{{ __('settings.symbol_label') }}</label>
                <input type="text" class="form-control" name="symbols" value="{{ $currency->symbols }}" required >
                <span id="symbolsError" class="text-danger"></span>
            </div>

            <div class="form-group col-sm-12">
                <label for="color">{{ __('settings.color_label') }}</label>
                <input type="color" class="form-control" name="color" value="{{ $currency->color }}"  style="height: 30px !important;">
                <span id="colorError" class="text-danger"></span>
            </div>
        </div>
    </div>
</form>
