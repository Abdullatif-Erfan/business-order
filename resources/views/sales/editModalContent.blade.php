@php 
   $maxAmount = $warehouseAmount->available_amount ?? 0;
   $curAmount = $salesDetails->amount ?? 0;
   $maxLimit = $maxAmount + $curAmount;
@endphp

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-6">
        <label for="item_name">{{__('sales.item')}}</label>

        <input class="form-control" name="id" type="hidden" 
        value="{{ $salesDetails->id ?? 0 }}">

        <input class="form-control" name="pre_list_id" type="hidden"
        value="{{ $salesDetails->pre_list_id ?? 0}}">

        <input class="form-control" name="billno" type="hidden"  
        value="{{ $salesDetails->billno ?? 0}}">

        <input class="form-control" name="warehouse_id" type="hidden"  
        value="{{ $salesDetails->warehouse_id ?? 0 }}">

        <input class="form-control" name="max_available_amount" id="max_available_amount" type="hidden"  
        value="{{ $warehouseAmount->available_amount ?? 0 }}">

        <input class="form-control" name="item_name" id="item_name" type="text" readonly value="{{ $salesDetails->preListRelation->name ?? ''}}">
    </div>

    <div class="col-md-4 col-sm-4 col-xs-6">
        <label for="amount"> {{__('common.sold_amount')}} </label>
        <input name="old_amount" type="hidden" step="0.01" value="{{ $salesDetails->amount ?? ''}}">
        <input class="form-control" name="amount" type="number" step="any" min="1" max="{{ $maxLimit }}"
        value="{{ $salesDetails->amount ?? 0}}" required 
        oninput="updateMaxLimitLabel(this.value, '{{ $maxAmount }}', '{{ $curAmount }}')">
        <span id="max_limit_info">
            <span id="max_limit_label" style="display:none">{{ $maxAmount }}</span>
            <span id="remaining_label" style="margin-left: 15px;">
                {{__('common.remaining')}}: 
                <span id="remaining_amount">{{ $maxAmount }}</span>
            </span>
        </span>
        <div id="warning_message" style="display:none; color: #ff6b6b; font-weight: bold; margin-top: 5px;">
            <i class="fas fa-exclamation-triangle"></i> 
            {{__('common.exceeds_max_limit')}}
        </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-6">
        <label for="amount"> {{__('common.unit')}} </label>
        <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="unit_id" id="unit_id">
            <option value=""> --- {{__('wh.unit_selection')}} ---</option>
            @foreach($units as $unitItem)
                <option value="{{ $unitItem->id }}" {{ $salesDetails->unit_id == $unitItem->id ? 'selected' : '' }}>{{ $unitItem->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-6">
        <label for="sell_up"> {{__('sales.sold_up')}} </label>
        <input class="form-control" name="sell_up" id="sell_up" type="number" step="0.01" 
        value="{{ $salesDetails->sell_up ?? 0}}" required>
    </div>
</div>

<script>
function updateMaxLimitLabel(curAmount, maxAmount, oldAmount) {
    // Parse values to float
    let currentValue = parseFloat(curAmount) || 0;
    let maxAvailable = parseFloat(maxAmount) || 0;
    let originalAmount = parseFloat(oldAmount) || 0;
    
    // Calculate remaining amount
    let remaining = maxAvailable + originalAmount - currentValue;
    
    // Get elements
    let maxLimitLabel = document.getElementById('max_limit_label');
    let remainingLabel = document.getElementById('remaining_amount');
    let warningDiv = document.getElementById('warning_message');
    let amountInput = document.querySelector('input[name="amount"]');
    
    // Update display
    maxLimitLabel.textContent = maxAvailable;
    remainingLabel.textContent = remaining.toFixed(2);
    
    // Color coding for remaining amount
    if (remaining < 0) {
        // Exceeded limit
        remainingLabel.style.color = '#ff6b6b';
        remainingLabel.style.fontWeight = 'bold';
        warningDiv.style.display = 'block';
        
        // Show remaining as negative with parentheses
        remainingLabel.textContent = '(' + remaining.toFixed(2) + ')';
        
        // Add red border to input
        amountInput.style.borderColor = '#ff6b6b';
        amountInput.style.backgroundColor = '#fff5f5';
        
    } else if (remaining < 10) {
        // Low stock warning
        remainingLabel.style.color = '#ffc107';
        remainingLabel.style.fontWeight = 'bold';
        warningDiv.style.display = 'none';
        amountInput.style.borderColor = '#ffc107';
        amountInput.style.backgroundColor = '#fff9e6';
        
        // Optional: Show warning message for low stock
        let lowStockMsg = document.getElementById('low_stock_warning');
        if (!lowStockMsg) {
            let msg = document.createElement('div');
            msg.id = 'low_stock_warning';
            msg.style.cssText = 'color: #ffc107; font-size: 12px; margin-top: 2px;';
            msg.innerHTML ='';
            amountInput.parentNode.appendChild(msg);
        }
        
    } else {
        // Normal state
        remainingLabel.style.color = '#28a745';
        remainingLabel.style.fontWeight = 'normal';
        warningDiv.style.display = 'none';
        amountInput.style.borderColor = '#28a745';
        amountInput.style.backgroundColor = '#f8fff8';
        
        // Remove low stock warning if exists
        let lowStockMsg = document.getElementById('low_stock_warning');
        if (lowStockMsg) {
            lowStockMsg.remove();
        }
    }
    
    // Update max attribute for validation
    let maxLimit = maxAvailable + originalAmount;
    amountInput.max = maxLimit.toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    let amountInput = document.querySelector('input[name="amount"]');
    let maxAmount = document.getElementById('max_available_amount').value;
    let oldAmount = document.querySelector('input[name="old_amount"]').value;
    
    if (amountInput) {
        updateMaxLimitLabel(amountInput.value, maxAmount, oldAmount);
    }
});

// Additional validation on form submit
document.querySelector('form')?.addEventListener('submit', function(e) {
    let amountInput = document.querySelector('input[name="amount"]');
    let maxAvailable = parseFloat(document.getElementById('max_available_amount').value) || 0;
    let oldAmount = parseFloat(document.querySelector('input[name="old_amount"]').value) || 0;
    let currentValue = parseFloat(amountInput.value) || 0;
    let remaining = maxAvailable + oldAmount - currentValue;
    
    if (remaining < 0) {
        e.preventDefault();
        alert('{{__('common.amount_exceeds_max_limit')}}');
        amountInput.focus();
        return false;
    }
    return true;
});
</script>