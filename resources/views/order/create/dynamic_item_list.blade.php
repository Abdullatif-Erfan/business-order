<div class="table-responsive">
    <table class="display responsive nowrap table table-bordered"  id="itemsTable">
        <thead>
            <tr style="background:#e9fffe">
                <th style="width:40%">{{__('wh.item_selection')}}</th>
                <th style="width:20%">{{__('common.amount')}}</th>
                <th style="width:30%">{{__('common.unit')}}</th>
                <th style="width:10%">{{__('common.add')}}</th>
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
                    <input name="amount[]" class="form-control amount" type="number" step="0.01" 
                           placeholder="{{__('common.amount')}}" required>
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
                    <button type="button" class="btn btn-info btn-sm addRow" style="padding: 2px 8px !important;" title="{{ __('common.add') }}">
                        <i class="fa fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-warning btn-sm removeRow" style="padding: 2px 8px !important; display: none;" title="{{ __('common.remove') }}">
                        <i class="fa fa-minus"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {
    // Store the template row HTML
    var templateRow = $('#itemsBody .item-row:first').clone();
    
    // Initialize select2 on existing rows
    $('.item-select, .unit-select').select2({
        dropdownParent: $('.table-responsive')
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
    // ADD NEW ROW - UNBIND AND REBIND TO PREVENT DUPLICATES
    // =========================================
    // Remove any existing click handlers and add new one
    $('.addRow').off('click').on('click', function (e) {
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
                showNotification('{{ __("common.at_least_one_row") }}', 'warning', 'top', 'right', 'withicon');
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