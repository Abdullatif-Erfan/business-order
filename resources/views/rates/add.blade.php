<script>
function calculateReverseAmount() {
    let to_currency_amount = parseFloat($('#to_currency_amount').val());
    let result = (1 / to_currency_amount).toFixed(10);
    $('#reverse_amount').val(result);
}

function changePoints(value) {
    let to_currency_amount = parseFloat($('#to_currency_amount').val());
    let result = (1 / to_currency_amount).toFixed(parseFloat(value));
    $('#reverse_amount').val(result);
}
</script>

<div class="box-tools m-t-10 m-b-10 m-r-10">
    <button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" href="#add_currency" style="border-radius:0px;">
        <span class="fas fa-plus-square"></span> &nbsp; <th>{{ __('common.add') }}</th>
    </button>
</div>

<div id="add_currency" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="border-top:2px solid #89b4ea;">
    <div class="box-body">
        <form action="{{ route('rate.store') }}" method="POST">
            @csrf

            <div class="form-body">
                <div class="row">

                    <div class="col-md-12">
                        <h3>{{ __('rate.note_large_to_small') }}</h3>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control select2" name="from_currency_id" required style="width:100%">
                                <option value="">{{ __('rate.select_larger_currency') }}</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ __('rate.one_unit')}} {{ $currency->name }}</option>
                                @endforeach
                            </select>
                            @error('from_currency_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control select2" name="to_currency_id" required style="width:100%">
                                <option value="">{{ __('rate.select_smaller_currency') }}</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ __('rate.equivalent_to') }} {{ $currency->name }}</option>
                                @endforeach
                            </select>
                            @error('to_currency_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <input class="form-control" id="to_currency_amount" name="to_currency_amount" type="number" step="0.00001" onkeyup="calculateReverseAmount()" placeholder="{{ __('rate.amount') }}" required> 
                            @error('to_currency_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <input class="form-control" id="reverse_amount" placeholder="{{ __('rate.reverse_amount') }}" name="reverse_amount" type="hidden" required readonly>
                            @error('reverse_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Uncomment if needed
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control select2" onchange="changePoints(this.value)" style="width:100%">
                                <option value="">{{ __('rate.decimal_points') }}</option>
                                @for ($i = 10; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i . ' ' . __('rate.point_unit') }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    -->

                    <div class="col-md-2 center m-t-10">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <span class="btn-label"><i class="fa fa-save"></i></span> {{ __('common.save') }}
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
