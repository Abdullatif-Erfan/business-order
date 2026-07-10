@extends('layouts.app')
@section('title', __('journal.expense_title'))
@section('content')
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">{{__('common.edit')}} {{__('journal.expense_title')}}  
                                <span class="pull-left">
                                    <a href="{{ route('expense.index') }}">
                                        <button class="btn mybtn bg-default">  {{__('common.back')}}  </button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <form action="{{ route('expense.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="id" value="{{ $expense->id }}">
                                              
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;margin-top:10px;">

                                        <!-- Row 1: Bill No & Date -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="bill_no">{{ __('common.bill') }}</label>
                                                <input class="form-control" id="bill_no" name="bill_no" type="number" 
                                                    placeholder="{{ __('common.bill') }}" 
                                                    value="{{ old('bill_no', $expense->bill_no) }}">
                                                @error('bill_no')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>

                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="todays_date">{{ __('common.save_date') }}</label>
                                                <div class="input-group date" id="datepicker">
                                                    <input type="text" class="form-control" name="todays_date" id="todays_date" required
                                                        value="{{ old('todays_date', $expense->idate ?? date('Y-m-d')) }}" 
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

                                        <!-- Row 2: Expense Type & Amount -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="dynamic_type">{{ __('journal.expense_type_selection') }}</label>
                                                <select class="form-control select2" name="dynamic_type" id="dynamic_type" required>
                                                    <option value=""> {{ __('journal.expense_type_selection') }}</option>
                                                    @foreach($expenseTypes as $type)
                                                        <option value="{{ $type->id }}" 
                                                            {{ old('dynamic_type', $expense->dynamic_type) == $type->id ? 'selected' : '' }}>
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('dynamic_type')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="amount">{{ __('common.amount') }}</label>
                                                <input class="form-control" id="amount" name="amount" type="number" step="0.01" required 
                                                    placeholder="{{ __('common.amount') }}" 
                                                    value="{{ old('amount', $expense->amount) }}">
                                                @error('amount')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>

                                        <!-- Row 3: Currency & Account -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="currency_id">{{ __('common.currency') }}</label>
                                                <select class="form-control select2" name="currency_id" id="currency_id" required>
                                                    @foreach($currencies as $currency)
                                                        <option value="{{ $currency->id }}" 
                                                            {{ old('currency_id', $expense->currency_id) == $currency->id ? 'selected' : '' }}>
                                                            {{ $currency->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('currency_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>

                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="reciever_account_id">{{ __('journal.account') }}</label>
                                                <select class="form-control select2" name="reciever_account_id" id="reciever_account_id" required>
                                                    <option value=""> {{ __('journal.select_account') }}</option>
                                                    @if(isset($ownBanks))
                                                        @foreach($ownBanks as $account)
                                                            <option value="{{ $account->id }}" 
                                                                {{ old('reciever_account_id', $expense->account_id) == $account->id ? 'selected' : '' }}>
                                                                {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('reciever_account_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row 4: Details & Document -->
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label for="details">{{ __('common.details') }}</label>
                                                <input class="form-control" id="details" name="details" type="text" 
                                                    placeholder="{{ __('common.details') }}" required 
                                                    value="{{ old('details', $expense->details) }}">
                                                @error('details')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label for="doc">{{ __('common.document') }}</label>
                                                <input type="file" class="form-control" name="doc" id="doc" 
                                                    accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx">
                                                @if($expense->doc)
                                                    <small class="text-muted">
                                                        <a href="{{ asset('storage/' . $expense->doc) }}" target="_blank">
                                                            <i class="fas fa-file"></i> {{ __('common.current_document') }}
                                                        </a>
                                                    </small>
                                                @endif
                                                @error('doc')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row 5: Submit Buttons -->
                                        <div class="col-md-12 col-sm-12 col-xs-12 m-t-20">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <button type="submit" id="submit_button" class="btn btn-success btn-block">
                                                        <i class="fas fa-save"></i> {{ __('common.save') }}
                                                    </button>
                                                </div>
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <a href="{{ route('expense.index') }}" class="btn btn-danger btn-block">
                                                        <i class="fas fa-times"></i> {{ __('common.cancel') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div> <!-- /.box-body -->
                    </div> <!-- /.card -->
                </div> <!-- /.col-md-12 -->
            </div> <!-- /.row -->
        </div> <!-- /.page-inner -->
    </div> <!-- /.content -->
</div> <!-- /.main-panel -->

@push('scripts')
<script>
    $(document).ready(function() {
        // Select2 initialization
        $('.select2').select2({
            width: '100%'
        });

        // Amount input validation
        $('#amount').on('input', function() {
            var value = parseFloat($(this).val());
            if (value < 0) {
                $(this).val(0);
                showNotification('{{ __("common.amount_positive") }}', 'warning');
            }
        });

        // Bill number validation
        $('#bill_no').on('input', function() {
            var value = parseInt($(this).val());
            if (value < 0) {
                $(this).val(0);
            }
        });

        // File validation
        $('#doc').on('change', function() {
            var file = this.files[0];
            if (file) {
                var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf', 
                                   'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                                   'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
                var maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!allowedTypes.includes(file.type)) {
                    showNotification('{{ __("common.invalid_file_type") }}', 'danger');
                    $(this).val('');
                } else if (file.size > maxSize) {
                    showNotification('{{ __("common.file_too_large") }}', 'danger');
                    $(this).val('');
                }
            }
        });

        // Form submit validation
        $('form').on('submit', function(e) {
            var amount = parseFloat($('#amount').val());
            if (isNaN(amount) || amount <= 0) {
                e.preventDefault();
                showNotification('{{ __("common.enter_valid_amount") }}', 'danger');
                $('#amount').focus();
                return false;
            }
            
            var details = $('#details').val().trim();
            if (details === '') {
                e.preventDefault();
                showNotification('{{ __("common.enter_details") }}', 'danger');
                $('#details').focus();
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