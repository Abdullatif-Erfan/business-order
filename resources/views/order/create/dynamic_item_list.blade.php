<div class="table-responsive">
    <table class="display responsive nowrap table table-bordered" id="itemsTable">
        <thead>
            <tr style="background:#e9fffe">
                <th style="width:5%">#</th>
                <th style="width:20%">{{ __('order.category') }}</th>
                <th style="width:30%">{{ __('wh.item_selection') }}</th>
                <th style="width:15%">{{ __('common.amount') }}</th>
                <th style="width:20%">{{ __('common.unit') }}</th>
                <th style="width:10%">{{ __('common.delete') }}</th>
            </tr>
        </thead>
        <tbody id="itemsBody">
            @if(isset($groupedItems) && count($groupedItems) > 0)
                @php $rowIndex = 0; @endphp
                @foreach($groupedItems as $item)
                    @php $rowIndex++; @endphp
                    <tr class="item-row" data-category-id="{{ $item['category_id'] }}">
                        <td>{{ $rowIndex }}</td>
                        <td>
                            <strong>{{ $item['category_name'] }}</strong>
                            @if($item['count'] > 1)
                                <span class="badge badge-info">x{{ $item['count'] }}</span>
                            @endif
                        </td>
                        <td>
                            <select class="form-control select2 item-select" name="items[{{ $rowIndex }}][pre_list_id]" style="width: 100%;" required>
                                <option value="">{{ __('wh.item_selection') }}</option>
                                @foreach($preLists as $preItem)
                                    <option value="{{ $preItem->id }}"
                                        data-category-id="{{ $preItem->category_id ?? '' }}"
                                        data-supplier-id="{{ $preItem->supplier_id ?? '' }}"
                                        data-unit-id="{{ $preItem->unit_id ?? '' }}"
                                        {{ $item['pre_list_id'] == $preItem->id ? 'selected' : '' }}>
                                        {{ $preItem->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="items[{{ $rowIndex }}][category_id]" class="pre-category-id" value="{{ $item['category_id'] }}">
                            <input type="hidden2" name="items[{{ $rowIndex }}][supplier_id]" class="pre-supplier-id" value="{{ $item['supplier_id'] }}">
                        </td>
                        <td>
                            <input name="items[{{ $rowIndex }}][amount]" class="form-control amount" type="number" step="any" min="0.1"
                                   value="{{ $item['amount'] }}" placeholder="{{ __('common.amount') }}" required>
                        </td>
                        <td>
                            <select class="form-control select2 unit-select" name="items[{{ $rowIndex }}][unit_id]" style="width: 100%;" required>
                                <option value="">{{ __('order.unit_selection') }}</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ $item['unit_id'] == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-item" style="padding: 2px 8px !important;" title="{{ __('common.delete') }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="fas fa-plus-circle"></i> {{ __('order.no_items_added') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Add Item Button -->
<!-- <div class="row m-t-10">
    <div class="col-md-12">
        <button type="button" id="addItemBtn" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> {{ __('order.add_item') }}
        </button>
    </div>
</div> -->

<script>
$(document).ready(function () {
    // Store all items data
    var allItems = {!! json_encode($groupedItems ?? []) !!};
    var categories = {!! json_encode($categories ?? []) !!};
    
    // Render the items grouped by category
    function renderItems() {
        var html = '';
        var rowIndex = 0;
        
        // Group items by category
        var groupedItems = {};
        allItems.forEach(function(item) {
            var categoryId = item.category_id || 'uncategorized';
            if (!groupedItems[categoryId]) {
                groupedItems[categoryId] = [];
            }
            groupedItems[categoryId].push(item);
        });
        
        // Render each category group
        for (var categoryId in groupedItems) {
            var items = groupedItems[categoryId];
            var categoryName = categories.find(c => c.id == categoryId)?.name || 'دسته بندی نشده';
            
            // Category header row
            html += `
                <tr class="category-header" data-category-id="${categoryId}">
                    <td colspan="6">
                        <div class="d-flex justify-content-between align-items-center" style="background-color:#ddd">
                            <strong><i class="fas fa-folder-open"></i> ${categoryName}</strong>
                            <span>{{ __('common.totally') }}: ${items.length}  {{ __('common.records') }}  </span>
                        </div>
                    </td>
                </tr>
            `;
            
            // Item rows for this category
            items.forEach(function(item, index) {
                rowIndex++;
                html += `
                    <tr class="item-row" data-category-id="${categoryId}" data-item-index="${index}">
                        <td>${rowIndex}</td>
                        <td>${categoryName}</td>
                        <td>
                            <select class="form-control select2 item-select" name="items[${rowIndex}][pre_list_id]" style="width: 100%;" required>
                                <option value="">{{ __('wh.item_selection') }}</option>
                                @foreach($preLists as $preItem)
                                    <option value="{{ $preItem->id }}"
                                        data-category-id="{{ $preItem->category_id ?? '' }}"
                                        data-unit-id="{{ $preItem->unit_id ?? '' }}"
                                        ${item.pre_list_id == {{ $preItem->id }} ? 'selected' : ''}>
                                        {{ $preItem->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="items[${rowIndex}][category_id]" class="pre-category-id" value="${item.category_id || ''}">
                             <input type="hidden" name="items[${rowIndex}][supplier_id]" class="pre-supplier-id" value="${item.supplier_id || ''}">
                        </td>
                        <td>
                            <input name="items[${rowIndex}][amount]" class="form-control amount" type="number" step="any"  min="0.1"
                                   value="${item.amount || ''}" placeholder="{{ __('common.amount') }}" required>
                        </td>
                        <td>
                            <select class="form-control select2 unit-select" name="items[${rowIndex}][unit_id]" style="width: 100%;" required>
                                <option value="">{{ __('order.unit_selection') }}</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" ${item.unit_id == {{ $unit->id }} ? 'selected' : ''}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-item" style="padding: 2px 8px !important;" title="{{ __('common.delete') }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        
        // Empty state
        if (rowIndex === 0) {
            html += `
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="fas fa-info-circle"></i> {{ __('order.item_doesnot_exist') }}
                    </td>
                </tr>
            `;
        }
        
        $('#itemsBody').html(html);
        
        // Reinitialize select2
        $('.item-select, .unit-select').select2({
            dropdownParent: $('.table-responsive')
        });
        
        // Update remove buttons
        updateRemoveButtons();
    }
    
    // Add new item
    function addItem() {
        var newItem = {
            category_id: '',
            supplier_id: '',
            pre_list_id: '',
            unit_id: '',
            amount: ''
        };
        allItems.push(newItem);
        renderItems();
    }
    
    // Delete item
    function deleteItem(index) {
        if (confirm('{{ __("common.delete_confirm") }}')) {
            allItems.splice(index, 1);
            renderItems();
        }
    }
    
    // Update remove buttons
    function updateRemoveButtons() {
        var rows = $('#itemsBody .item-row');
        if (rows.length === 0) {
            // If no items, show empty state
        }
    }
    
    // =========================================
    // EVENT HANDLERS
    // =========================================
    
    // Add new item button
    $(document).on('click', '#addItemBtn', function() {
        addItem();
    });
    
    // Delete item
    $(document).on('click', '.delete-item', function() {
        var row = $(this).closest('.item-row');
        var index = row.data('item-index');
        var categoryId = row.data('category-id');
        
        
        // Find and remove the item from allItems
        var items = allItems.filter(function(item, i) {
            return item.category_id == categoryId;
        });
        
        if (items.length > 0) {
            var itemIndex = items[index];
            var globalIndex = allItems.indexOf(itemIndex);
            if (globalIndex !== -1) {
                allItems.splice(globalIndex, 1);
                renderItems();
            }
        }
    });
    
    // Handle item select change
    $(document).on('change', '.item-select', function () {
        var selectedOption = $(this).find(':selected');
        var row = $(this).closest('tr');
        
        var categoryId = selectedOption.data('category-id') || '';
        var unitId = selectedOption.data('unit-id') || '';
        
        row.find('.pre-category-id').val(categoryId);
        
        if (unitId) {
            row.find('.unit-select').val(unitId).trigger('change');
        }
    });


    renderItems();
});
</script>