<div class="table-responsive">
    <table class="display responsive nowrap table table-bordered" id="itemsTable">
        <thead>
            <tr style="background:#e9fffe">
                <th style="width:40%">{{__('wh.item_selection')}}</th>
                <th style="width:20%">{{__('common.amount')}}</th>
                <th style="width:30%">{{__('common.unit')}}</th>
                <th style="width:10%">{{__('common.action')}}</th>
            </tr>
        </thead>
        <tbody id="itemsBody">
            <tr class="item-row">
                <td>
                    <select class="form-control select2 item-select" name="buy_pre_list[]" style="width: 100%;" required>
                        <option value="">{{__('wh.item_selection')}}</option>
                        @foreach($preLists as $item)
                            <option value="{{ $item->id }}"
                                data-category-id="{{ $item->category_id ?? '' }}"
                                data-pre-list-id="{{ $item->id }}"
                                data-unit-id="{{ $item->unit_id ?? '' }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input name="amount[]" class="form-control amount" type="number" step="any" placeholder="{{__('common.amount')}}" required>
                </td>
                <td> 
                    <input name="category_id[]" class="form-control pre-category-id" type="hidden" readonly>
                    <input name="pre_list_id[]" class="form-control pre-list-id" type="hidden" readonly>
                    
                    <select class="form-control select2 unit-select" name="unit_id[]" style="width: 100%;" required>
                        <option value="">{{__('order.unit_selection')}}</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm removeRow" style="padding: 2px 8px !important; display: none;" title="{{ __('common.remove') }}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">
                    <button type="button" id="addNewRowBtn" class="add-row-btn">
                        <i class="fa fa-plus-circle"></i> {{ __('common.add_new_row') }}
                    </button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
$(document).ready(function () {
    // Store the template row HTML
    var templateRow = $('#itemsBody .item-row:first').clone();
    
    // =========================================
    // INITIALIZE SELECT2
    // =========================================
    $('.item-select, .unit-select').select2({
        dropdownParent: $('.table-responsive')
    });

    // =========================================
    // AUTO-SELECT UNIT ON PAGE LOAD
    // For existing rows with pre-selected items
    // =========================================
    $('.item-row').each(function() {
        var row = $(this);
        var selectedOption = row.find('.item-select').find(':selected');
        var unitId = selectedOption.data('unit-id') || '';
        
        if (unitId) {
            row.find('.unit-select').val(unitId).trigger('change');
        }
    });

    // =========================================
    // TOGGLE REQUIRED ATTRIBUTE
    // =========================================
    function toggleRequiredAttribute(row, isVisible) {
        row.find('.item-select, .amount, .unit-select').each(function () {
            if (isVisible) {
                $(this).attr('required', 'required');
            } else {
                $(this).removeAttr('required');
            }
        });
    }

    // =========================================
    // HANDLE ITEM SELECT CHANGE
    // =========================================
    $(document).on('change', '.item-select', function () {
        var selectedOption = $(this).find(':selected');
        var row = $(this).closest('tr');
        
        var categoryId = selectedOption.data('category-id') || '';
        var preListId = selectedOption.data('pre-list-id') || '';
        var unitId = selectedOption.data('unit-id') || '';

        row.find('.pre-category-id').val(categoryId);
        row.find('.pre-list-id').val(preListId);
        
        if (unitId) {
            row.find('.unit-select').val(unitId).trigger('change');
        } else {
            row.find('.unit-select').val('').trigger('change');
        }
    });

    // =========================================
    // ADD NEW ROW - Using the footer button
    // =========================================
    $('#addNewRowBtn').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $newRow = templateRow.clone();
        
        // Reset all inputs
        $newRow.find('input[type="text"], input[type="number"], input[type="hidden"]').val('');
        $newRow.find('select').each(function() {
            $(this).val('').removeAttr('data-select2-id');
            $(this).removeClass('select2-hidden-accessible');
        });
        
        // Remove any select2 containers
        $newRow.find('.select2-container').remove();
        
        // Show remove button
        $newRow.find('.removeRow').show();
        
        // Append new row
        $('#itemsBody').append($newRow);
        
        // Reinitialize select2 for new row
        $newRow.find('.item-select, .unit-select').select2({
            dropdownParent: $('.table-responsive')
        });
        
        // Add required attributes
        toggleRequiredAttribute($newRow, true);
        
        // Update remove buttons
        updateRemoveButtons();
    });

    // =========================================
    // REMOVE ROW
    // =========================================
    $(document).on('click', '.removeRow', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        var rows = $('#itemsBody .item-row');
        
        if (rows.length > 1) {
            var row = $(this).closest('tr');
            
            // Prevent deleting the first row
            if (row.index() !== 0) {
                // Destroy select2
                row.find('.item-select, .unit-select').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                row.find('.select2-container').remove();
                
                toggleRequiredAttribute(row, false);
                row.remove();
                updateRemoveButtons();
            } else {
                showNotification('{{ __("common.cant_delete_first_row") }}', 'warning', 'top', 'right', 'withicon');
            }
        } else {
            showNotification('{{ __("common.at_least_one_row") }}', 'warning', 'top', 'right', 'withicon');
        }
    });

    // =========================================
    // UPDATE REMOVE BUTTONS
    // =========================================
    function updateRemoveButtons() {
        var rows = $('#itemsBody .item-row');
        
        // Hide remove button on first row if only one row exists
        if (rows.length === 1) {
            rows.find('.removeRow').hide();
        } else {
            rows.find('.removeRow').show();
        }
    }

    // =========================================
    // VALIDATE AMOUNT
    // =========================================
    $(document).on('input', '.amount', function () {
        var amount = parseFloat($(this).val()) || 0;
        if (amount < 0) {
            $(this).val(0);
            showNotification('{{ __("common.amount_positive") }}', 'warning', 'top', 'right', 'withicon');
        }
    });

    // =========================================
    // AMOUNT ARROW KEY BEHAVIOR
    // Increase by 1 on arrow up, decrease by 1 on arrow down
    // =========================================
    $(document).on('keydown', '.amount', function(e) {
        var key = e.key || e.keyCode;
        
        // Arrow Up (38) or Arrow Down (40)
        if (key === 'ArrowUp' || key === 38) {
            e.preventDefault();
            var currentVal = parseFloat($(this).val()) || 0;
            $(this).val(currentVal + 1).trigger('input');
        } else if (key === 'ArrowDown' || key === 40) {
            e.preventDefault();
            var currentVal = parseFloat($(this).val()) || 0;
            var newVal = currentVal - 1;
            if (newVal < 0) newVal = 0;
            $(this).val(newVal).trigger('input');
        }
    });

    // =========================================
    // NOTIFICATION FUNCTION
    // =========================================
    function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
        var content = {
            message: '<span style="font-size:16px;">' + message + '</span>',
            title: '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{ __("settings.message") }}</span>',
            icon: style === 'withicon' ? 'fa fa-bell' : 'none',
            url: '#',
            target: '_blank'
        };

        $.notify(content, {
            type: type,
            placement: {
                from: from,
                align: align
            },
            time: 500
        });
    }

    // =========================================
    // INITIAL SETUP
    // =========================================
    updateRemoveButtons();
});
</script>