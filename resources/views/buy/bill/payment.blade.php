<div class="container-fluid">
    <form id="paymentForm" action="{{ route('boughtList.storePayment') }}" method="POST">
        @csrf
        <input type="hidden" name="billno" value="{{ $boughtItems->billno ?? '' }}">
        <input type="hidden" name="bought_item_id" value="{{ $boughtItems->id ?? '' }}">
        <input type="hidden" name="supplier_id" value="{{ $boughtItems->supplier_account_id ?? '' }}">
        <input type="hidden" name="current_remained" value="{{ $boughtItems->remained ?? 0 }}">
        <input type="hidden" name="currency_id" value="{{ $boughtItems->currency_id ?? 1 }}">
        
        <div class="row">
            <!-- Bill Info -->
            <div class="col-md-12">
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-2">
                            <strong>{{ __('common.bill') }}:</strong> {{ 'BUY_'.$boughtItems->billno ?? '' }}
                        </div>
                        <div class="col-md-4">
                            <strong>{{ __('sales.customer') }}:</strong> {{ $boughtItems->customer_name ?? '' }}
                        </div>
                        <div class="col-md-3">
                            <strong>{{ __('buy.total_price') }}:</strong> {{ number_format($boughtItems->total ?? 0, 2) }}
                        </div>
                        <div class="col-md-3">
                            <strong>{{ __('buy.remained') }}:</strong> 
                            <span style="color: #dc3545; font-weight: bold;">
                                {{ number_format($boughtItems->remained ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_amount">{{ __('sales.payment_amount') }} <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control" 
                           id="payment_amount" 
                           name="payment_amount" 
                           value="{{ number_format($boughtItems->remained ?? 0, 2) }}" 
                           max="{{ $boughtItems->remained ?? 0 }}"
                           min="0.01" step="any"
                           required>
                    <small class="text-muted">{{ __('sales.max_payment') }}: {{ number_format($boughtItems->remained ?? 0, 2) }}</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_date">{{ __('common.date') }} <span class="text-danger">*</span></label>
                    <div class="input-group date" id="paymentDatepicker">
                        <input type="text" class="form-control datepicker-input" name="payment_date" 
                               value="{{ date('Y-m-d') }}" placeholder="{{ __('common.date') }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text datepicker-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="account_id">{{ __('journal.payer_account') }} <span class="text-danger">*</span></label>
                    <select class="form-control select2" name="account_id" id="account_id" required>
                        <option value="">{{ __('journal.payer_account') }}</option>
                        @foreach($ownBanks ?? [] as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="supplier_account_id"> {{ __('journal.receiver_account') }} <span class="text-danger">*</span></label>
                    <select class="form-control select2" name="supplier_account_id" id="supplier_account_id" required readonly>
                        <option value="{{ $boughtItems->supplier_account_id }}">{{ $boughtItems->customer_name }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="note">{{ __('buy.comment') }}</label>
                    <textarea class="form-control" name="note" id="note" rows="2" 
                              placeholder="{{ __('buy.comment') }}"></textarea>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // =========================================
    // INITIALIZE DATEPICKER FOR DYNAMIC MODAL CONTENT
    // =========================================
    $('#paymentDatepicker .datepicker-input').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        clearBtn: true
    });

    // =========================================
    // DATEPICKER ICON CLICK HANDLER
    // =========================================
    $(document).on('click', '.datepicker-icon', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $input = $(this).closest('.input-group').find('.datepicker-input');
        if ($input.length) {
            $input.datepicker('show');
        }
    });

    // =========================================
    // INITIALIZE SELECT2
    // =========================================
    $('#account_id, #supplier_account_id').select2({
        width: '100%'
    });

    // =========================================
    // VALIDATE PAYMENT AMOUNT
    // =========================================
    $('#payment_amount').on('input', function() {
        var maxAmount = parseFloat($(this).attr('max')) || 0;
        var enteredAmount = parseFloat($(this).val()) || 0;
        
        if (enteredAmount > maxAmount) {
            $(this).val(maxAmount.toFixed(2));
            showNotification('{{ __("sales.payment_cannot_exceed_remained") }}', 'warning');
        }
        if (enteredAmount < 0) {
            $(this).val(0);
            showNotification('{{ __("common.amount_positive") }}', 'warning');
        }
    });

    // =========================================
    // FORM SUBMISSION
    // =========================================
    $('#paymentBill').off('click').on('click', function(e) {
        e.preventDefault();
        
        var amount = parseFloat($('#payment_amount').val()) || 0;
        var maxAmount = parseFloat($('#payment_amount').attr('max')) || 0;
        var accountId = parseFloat($('#account_id').val()) || 0;
        if(!accountId) {
            showNotification('انتخاب حساب ضروری است', 'danger');
            return;
        }
        
        if (amount <= 0) {
            showNotification('{{ __("sales.enter_valid_payment_amount") }}', 'danger');
            return;
        }
        
        if (amount > maxAmount) {
            showNotification('{{ __("sales.payment_cannot_exceed_remained") }}', 'danger');
            return;
        }
        
        var $btn = $(this);
        var originalText = $btn.text();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("common.saving") }}...');
        
        var formData = $('#paymentForm').serialize();
        
        $.ajax({
            url: '{{ route("boughtList.storePayment") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $btn.prop('disabled', false).html(originalText);
                if (response.status === 'success') {
                    showNotification(response.message || '{{ __("common.added_successfully") }}', 'success');
                    $('#billPaymentModal').modal('hide');
                    fetchList();
                    // if ($.fn.DataTable.isDataTable('#salesTable')) {
                    //     $('#salesTable').DataTable().ajax.reload(null, false);
                    // }
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 1500);
                } else {
                    showNotification(response.message || '{{ __("common.error_occurred") }}', 'danger');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    $.each(errors, function(key, messages) {
                        errorMessages.push(messages[0]);
                    });
                    showNotification(errorMessages.join('<br>'), 'danger');
                } else {
                    showNotification('{{ __("common.error_occurred") }}', 'danger');
                }
            }
        });
    });
});
</script>