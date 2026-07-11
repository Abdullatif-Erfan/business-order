@extends('layouts.app')
@section('title', __('hr.salary'))
@section('content')
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">{{ __('hr.update_salary_form_title')}}  
                                <span class="pull-left">
                                    <a href="{{ route('salary.index') }}">
                                        <button class="btn mybtn bg-default">{{ __('common.back') }}</button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <form action="{{ route('salary.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id" value="{{ $salary->id }}">
                                <input type="hidden" name="from_account_id" value="{{ $ownBanks->first()->id ?? 0 }}">
                                <input type="hidden" name="from_account_name" value="{{ $ownBanks->first()->name ?? '' }}">
                                              
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;margin-top:10px;">

                                        <!-- Row 1: Date -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="todays_date">{{ __('common.save_date') }} <span class="text-danger">*</span></label>
                                                <div class="input-group date" id="datepicker">
                                                    <input type="text" class="form-control" name="todays_date" id="todays_date" required
                                                        value="{{ old('todays_date', $salary->idate ?? $todaysDate) }}" 
                                                        placeholder="{{ __('common.save_date') }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text datepicker-icon">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                @error('todays_date')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row 1: Year -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="year">{{ __('common.year') }} <span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="year" id="year" required>
                                                    <option value="">{{ __('common.year') }}</option>
                                                    @for($i=2020; $i<=2050; $i++)
                                                        <option value="{{ $i }}" {{ old('year', $salary->year) == $i ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                @error('year')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row 1: Month -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="month">{{ __('common.month') }} <span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="month" id="month" required>
                                                    <option value="">{{ __('common.month') }}</option>
                                                    @foreach($months as $key => $month)
                                                        <option value="{{ $key }}" {{ old('month', $salary->month) == $key ? 'selected' : '' }}>
                                                            {{ $month }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('month')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row 2: Employee -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="to_account_id">{{ __('hr.emp_selection') }} <span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="to_account_id" id="to_account_id" required>
                                                    <option value="">{{ __('hr.emp_selection') }}</option>
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp->id }}" {{ old('to_account_id', $salary->account_id) == $emp->id ? 'selected' : '' }}>
                                                            {{ $emp->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('to_account_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row 2: Amount -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="amount">{{ __('hr.payable_amount') }} <span class="text-danger">*</span></label>
                                                <input class="form-control" id="amount" name="amount" type="number" step="0.01" required 
                                                    placeholder="{{ __('hr.payable_amount') }}" 
                                                    value="{{ old('amount', $salary->amount) }}">
                                                @error('amount')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>

                                        <!-- Row 2: Currency -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="currency_id">{{ __('common.currency') }} <span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="currency_id" id="currency_id" required>
                                                    @foreach($currencies as $currency)
                                                        <option value="{{ $currency->id }}" {{ old('currency_id', $salary->currency_id) == $currency->id ? 'selected' : '' }}>
                                                            {{ $currency->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('currency_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>

                                        <!-- Row 3: Details -->
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label for="details">{{ __('common.details') }}</label>
                                                <input class="form-control" id="details" name="details" type="text" 
                                                    placeholder="{{ __('common.details') }}" 
                                                    value="{{ old('details', $salary->details) }}">
                                                @error('details')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row 3: Bank Account (Display Only) -->
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label for="from_account_display">{{ __('journal.payer_account') }}</label>
                                                <input class="form-control" id="from_account_display" type="text" readonly
                                                    value="{{ $ownBanks->first()->name ?? __('journal.default_account') }}">
                                                
                                            </div>
                                        </div>

                                        <!-- Row 4: Submit Buttons -->
                                        <div class="col-md-12 col-sm-12 col-xs-12 m-t-20">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <button type="submit" id="submit_button" class="btn btn-success btn-block">
                                                        <i class="fas fa-save"></i> {{ __('common.save') }}
                                                    </button>
                                                </div>
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <a href="{{ route('salary.index') }}" class="btn btn-danger btn-block">
                                                        <i class="fas fa-times"></i> {{ __('common.cancel') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
      
      

        // Initialize Select2
        $('.select2').select2({
            width: '100%'
        });

        // Amount validation
        $('#amount').on('input', function() {
            var value = parseFloat($(this).val());
            if (value < 0) {
                $(this).val(0);
                showNotification('{{ __("common.amount_positive") }}', 'warning');
            }
        });

        // Form validation
        $('form').on('submit', function(e) {
            var amount = parseFloat($('#amount').val());
            if (isNaN(amount) || amount <= 0) {
                e.preventDefault();
                showNotification('{{ __("common.enter_valid_amount") }}', 'danger');
                $('#amount').focus();
                return false;
            }
            
            var employee = $('#to_account_id').val();
            if (!employee) {
                e.preventDefault();
                showNotification('{{ __("hr.select_employee") }}', 'danger');
                $('#to_account_id').focus();
                return false;
            }
            
            return true;
        });
    });

    // Notification function
    function showNotification(message, type = 'info', from = 'top', align = 'center') {
        if (typeof $.notify === 'function') {
            $.notify({
                message: '<span style="font-size:14px;">' + message + '</span>',
                title: '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{ __("settings.message") }}</span>',
                icon: 'fa fa-bell'
            }, {
                type: type,
                placement: {
                    from: from,
                    align: align
                },
                time: 3000
            });
        }
    }
</script>
@endpush
@endsection