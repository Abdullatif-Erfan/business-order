<script>
$(document).ready(function() {
    // =========================================
    // ADD MORE ROW
    // =========================================
    $(document).on('click', '.add-more', function() {
        var $row = $(this).closest('.dynamic-row');
        var $newRow = $row.clone();
        
        // Clear values in new row
        $newRow.find('input').val('');
        $newRow.find('select').val('').trigger('change');
        $newRow.find('.item-total').val('');
        
        // Show remove button on all rows except first
        $newRow.find('.remove-row').show();
        
        // Append new row
        $row.after($newRow);
        
        // Reinitialize select2 for new row
        $newRow.find('.select2').select2();
    });

    // =========================================
    // REMOVE ROW
    // =========================================
    $(document).on('click', '.remove-row', function() {
        var $row = $(this).closest('.dynamic-row');
        if ($('.dynamic-row').length > 1) {
            $row.remove();
        } else {
            showNotification("{{ __('order.at_least_one_item') }}", 'warning', 'top', 'right', 'withicon');
        }
    });

    // =========================================
    // CALCULATE ITEM TOTAL
    // =========================================
    $(document).on('input', '.price-input, input[name="quantity[]"]', function() {
        var $row = $(this).closest('.dynamic-row');
        var quantity = parseFloat($row.find('input[name="quantity[]"]').val()) || 0;
        var price = parseFloat($row.find('.price-input').val()) || 0;
        var total = quantity * price;
        
        if (total > 0) {
            $row.find('.item-total').val(total.toFixed(2));
        } else {
            $row.find('.item-total').val('');
        }
    });
});
</script>