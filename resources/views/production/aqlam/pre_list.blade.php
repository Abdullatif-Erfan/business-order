
<form id="buyingForm" action="{{ route('modelDetails.store') }}" method="POST">
    @csrf
    <input type="hidden" name="model_id" value="{{ $modelId }}" />

    <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">

    <div class="container" style="background: #d7f7ff;padding: 5px 35px;">
      <h4>ثبت جنس جدید در این مودل </h4>
    </div>
        <div class="form-body" style="padding-bottom:1px;">
            <div class="row" style="padding: 10px 20px;">

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="col-md-12">
                        <div class="alert alert-danger col-12" role="alert">
                            <button type="button" class="close pull-left" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Items Table --}}
                <div class="col-md-12 m-t-20">
                    <div class="table-responsive">
                        <table class="table table-bordered new" id="itemsTable">
                            <thead>
                                <tr style="background:#e9fffe">
                                    <th style="width:30%">{{ __('wh.item_selection') }}</th>
                                    <th style="width:10%">{{ __('common.amount') }}</th>
                                    <th style="width:10%">{{ __('common.unit') }}</th>
                                    <th style="width:10%">{{ __('wh.average') }}</th>
                                    <th style="width:15%">{{ __('common.total') }}</th>
                                    <th style="width:10%">{{ __('common.add') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td>
                                        <select class="form-control select2 item-select" name="warehouseItemId[]" style="width:100%;" required>
                                            <option value="">{{ __('wh.item_selection') }}</option>
                                            @foreach($warehouseItems as $item)
                                                <option value="{{ $item->id }}"
                                                    data-available-amount="{{ $item->available_amount }}"
                                                    data-unit-name="{{ $item->unit_name }}"
                                                    data-unit-id="{{ $item->unit_id }}"
                                                    data-avg-up="{{ $item->avg_up }}"
                                                    data-branch-id="{{ $item->branch_id }}"
                                                    data-warehouse-id="{{ $item->warehouse_id }}"
                                                    data-pre-list-id="{{ $item->pre_list_id }}"
                                                    data-currency-id="{{ $item->currency_id }}">
                                                    {{ $item->item_name }} ({{ $item->available_amount }} {{ $item->unit_name }}) - {{ $item->warehouse_name }}
                                                    @if(session('package_type') == 4)
                                                        / ( کد = {{ $item->code }} )
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input name="amount[]" class="form-control amount" type="number" step="0.01" placeholder="{{ __('common.amount') }}" required></td>
                                    <td>
                                        <input name="unit_id[]" class="form-control unit-id" type="hidden" readonly required>
                                        <input name="branch_id[]" class="form-control branch-id" type="hidden" readonly required>
                                        <input name="warehouse_id[]" class="form-control warehouse-id" type="hidden" readonly required>
                                        <input name="pre_list_id[]" class="form-control pre-list-id" type="hidden" readonly required>
                                        <input name="unit_name[]" class="form-control unit-name" type="text" readonly required>
                                        <input name="currency_id[]" class="form-control currency-id" type="hidden" readonly required>
                                    </td>
                                    <td><input name="avg_up[]" class="form-control avg-up" type="number" step="0.01" required></td>
                                    <td><input name="total[]" class="form-control total" value="0" type="number" step="0.01" readonly required></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm addRow" style="padding:2px 8px;"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-warning btn-sm removeRow" style="padding:2px 8px;"><i class="fa fa-minus"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr />

                {{-- Submit / Cancel Buttons --}}
                <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                    <div class="row">
                        <div class="col-3 col-xs-6">
                            <input type="submit" id="submit_button" name="submit" value="{{ __('common.save') }}" class="form-control btn bg-blue pull-left">
                        </div>
                        <div class="col-3 col-xs-6">
                            <a href="{{ route('model.index') }}">
                                <button type="button" class="form-control btn bg-danger">{{ __('common.cancel') }}</button>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function () {

    function toggleRequiredAttribute(row, isVisible) {
        row.find('.item-select, .amount').each(function () {
            isVisible ? $(this).attr('required', 'required') : $(this).removeAttr('required');
        });
    }

    function recalculate(row) {
        var avgUp = parseFloat(row.find('.avg-up').val()) || 0;
        var enteredAmount = parseFloat(row.find('.amount').val()) || 1;
        if (enteredAmount < 0) { row.find('.amount').val(0); enteredAmount = 0; }
        row.find('.total').val((avgUp * enteredAmount).toFixed(2));
        updateTotalPrice();
        updateGeneralDiscount();
    }

    function updateTotalPrice() {
        var totalPrice = 0;
        $('.total').each(function () { totalPrice += parseFloat($(this).val()) || 0; });
        $('#total_price').val(totalPrice.toFixed(2));
        updatePayableAmount();
    }

    // Item select change
    $(document).on('change', '.item-select', function () {
        var selected = $(this).find(':selected');
        var row = $(this).closest('tr');
        row.find('.unit-name').val(selected.data('unit-name'));
        row.find('.unit-id').val(selected.data('unit-id'));
        row.find('.branch-id').val(selected.data('branch-id'));
        row.find('.warehouse-id').val(selected.data('warehouse-id'));
        row.find('.pre-list-id').val(selected.data('pre-list-id'));
        row.find('.currency-id').val(selected.data('currency-id'));
        row.find('.avg-up').val(selected.data('avg-up'));
        row.find('.amount').data('max', selected.data('available-amount'));
    });

    // Amount input change
    $(document).on('input', '.amount', function () { recalculate($(this).closest('tr')); });

    // Add new row
    $(document).on('click', '.addRow', function () {
        var newRow = $('#itemsTable tbody .item-row:first').clone();
        newRow.find('input').val('');
        newRow.find('.item-select').val('').trigger('change');
        newRow.find('.select2-container').remove();
        newRow.find('.item-select').removeClass('select2-hidden-accessible').show();
        $('#itemsTable tbody').append(newRow);
        newRow.find('.item-select').select2();
        toggleRequiredAttribute(newRow, true);
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        var rows = $('#itemsTable tbody tr.item-row');
        if (rows.length > 1) {
            var row = $(this).closest('tr');
            if (row.index() !== 0) {
                toggleRequiredAttribute(row, false);
                row.remove();
                updateTotalPrice();
                updateGeneralDiscount();
            } else { alert("You must have at least one row."); }
        } else { alert("You must have at least one row."); }
    });

    // Initialize select2
    $('.item-select').select2();
});
</script>
