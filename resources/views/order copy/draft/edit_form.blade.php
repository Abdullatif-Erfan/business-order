@extends('layouts.app')

@section('content')

<style>
    table.new thead tr th {
        background-color: #fff !important;
        color: #000 !important;
        text-align: center;
    }
    table.my_table thead tr th {
        background-color: #3f7cc7 !important;
        color: #fff !important;
        text-align: center;
    }
    .new tbody tr td {
        padding: 5px 5px;
    }
    select.select2 {
        text-align: right !important;
        direction: rtl !important;
    }
    .form-control {
        padding-right: 3px !important;
    }
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">{{ __('order.edit_draft_order') }} ( {{ $draftOrder->dord_num }} )
                                <span class="pull-left">
                                    <a href="{{ route('draftOrders.index') }}">
                                        <button class="btn mybtn bg-default">{{ __('common.back') }}</button>
                                    </a>
                                </span>
                            </h4>
                        </div>

                        <form id="orderEditForm" action="{{ route('draftOrders.update', $draftOrder->dord_num) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="dord_num" value="{{ $draftOrder->dord_num }}">
                            <input type="hidden" name="times" value="{{ $draftOrder->times ?? time() }}">
                            <input type="hidden" name="deleted_items" id="deleted_items" value="">

                            <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;">

                                        <!-- Validation Errors -->
                                        <div id="errorContainer" class="col-md-12" style="display: none;">
                                            <div class="alert alert-danger col-12" role="alert">
                                                <button type="button" class="close pull-left" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <ul id="errorList"></ul>
                                            </div>
                                        </div>

                                        <!-- ========================================= -->
                                        <!-- FIRST ROW -->
                                        <!-- ========================================= -->
                                        <div class="col-md-12">
                                            <div class="row">
                                                <!-- Customer -->
                                                <div class="col-md-4 col-sm-4 col-xs-6">
                                                    <label for="customer_id">{{ __('order.customer_selection') }} <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" style="width: 100%; background-color:#ddd;" 
                                                            name="customer_id" id="customer_id" required>
                                                        <option value="">{{ __('order.customers') }}</option>
                                                        @foreach($customers as $customer)
                                                            <option value="{{ $customer->id }}" {{ $draftOrder->customer_id == $customer->id ? 'selected' : '' }}>
                                                                {{ $customer->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('customer_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <!-- Date Picker -->
                                                <div class="col-md-4 col-sm-4 col-xs-6">
                                                    <label for="idate">{{ __('order.date') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group date" id="datepicker">
                                                        <input type="text" class="form-control" name="idate" id="idate" 
                                                            value="{{ $draftOrder->idate ? \Carbon\Carbon::parse($draftOrder->idate)->format('Y-m-d') : date('Y-m-d') }}" 
                                                            placeholder="{{ __('common.date') }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text datepicker-icon">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @error('idate')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <!-- Status -->
                                                <div class="col-md-4 col-sm-4 col-xs-6">
                                                    <label for="state">{{ __('order.status') }}</label>
                                                    <select class="form-control" name="state" id="state">
                                                        <option value="1" {{ $draftOrder->state == 1 ? 'selected' : '' }}>{{ __('order.new') }}</option>
                                                        <option value="2" {{ $draftOrder->state == 2 ? 'selected' : '' }}>{{ __('order.pending') }}</option>
                                                        <option value="3" {{ $draftOrder->state == 3 ? 'selected' : '' }}>{{ __('order.completed') }}</option>
                                                        <option value="4" {{ $draftOrder->state == 4 ? 'selected' : '' }}>{{ __('order.cancelled') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ========================================= -->
                                        <!-- SECOND ROW - Item Details -->
                                        <!-- ========================================= -->
                                        <div class="col-md-12 m-t-20">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered new my_table" id="itemsTable">
                                                            <thead>
                                                                <tr style="background:#e9fffe">
                                                                    <th style="width:5%">#</th>
                                                                    <th style="width:35%">{{ __('wh.item_selection') }}</th>
                                                                    <th style="width:15%">{{ __('common.category') }}</th>
                                                                    <th style="width:20%">{{ __('common.amount') }}</th>
                                                                    <th style="width:15%">{{ __('common.unit') }}</th>
                                                                    <th style="width:10%">{{ __('common.add') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="itemsBody">
                                                                @foreach($orderItems as $index => $item)
                                                                <tr class="item-row" data-id="{{ $item->id }}">
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>
                                                                        <select class="form-control select2 item-select" name="items[{{ $index }}][pre_list_id]" style="width: 100%;" required>
                                                                            <option value="">{{ __('wh.item_selection') }}</option>
                                                                            @foreach($preLists as $preList)
                                                                                <option value="{{ $preList->id }}"
                                                                                    data-category-id="{{ $preList->category_id ?? '' }}"
                                                                                    {{ $item->pre_list_id == $preList->id ? 'selected' : '' }}>
                                                                                    {{ $preList->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control select2 category-select" name="items[{{ $index }}][category_id]" style="width: 100%;">
                                                                            <option value="">{{ __('order.select_category') }}</option>
                                                                            @foreach($categories as $category)
                                                                                <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                                                                    {{ $category->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input name="items[{{ $index }}][amount]" class="form-control amount" type="number" step="0.01" 
                                                                               value="{{ $item->amount }}" placeholder="{{ __('common.amount') }}" required>
                                                                    </td>
                                                                    <td>
                                                                        <input name="items[{{ $index }}][id]" type="hidden" value="{{ $item->id }}">
                                                                        <select class="form-control select2 unit-select" name="items[{{ $index }}][unit_id]" style="width: 100%;" required>
                                                                            <option value="">{{ __('order.unit_selection') }}</option>
                                                                            @foreach($units as $unit)
                                                                                <option value="{{ $unit->id }}" {{ $item->unit_id == $unit->id ? 'selected' : '' }}>
                                                                                    {{ $unit->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-info btn-sm addRow" style="padding: 2px 8px !important;" title="{{ __('common.add') }}">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-warning btn-sm removeRow" style="padding: 2px 8px !important;" title="{{ __('common.remove') }}">
                                                                            <i class="fa fa-minus"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                       


                                        <!-- ========================================= -->
                                        <!-- SUBMIT BUTTONS -->
                                        <!-- ========================================= -->
                                        <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                            <div class="row">
                                                <div class="col-3 col-xs-6">
                                                    <button type="submit" id="submit_button" class="form-control btn bg-blue">
                                                        {{ __('order.update') }}
                                                    </button>
                                                </div>
                                                <div class="col-3 col-xs-6">
                                                    <a href="{{ route('draftOrders.index') }}">
                                                        <button type="button" class="form-control btn bg-danger">
                                                            {{ __('common.cancel') }}
                                                        </button>
                                                    </a>
                                                </div>
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

@push('scripts')
<script>
$(document).ready(function() {
   
    // =========================================
    // INITIALIZE SELECT2
    // =========================================
    $('.item-select, .unit-select, .category-select').select2({
        dropdownParent: $('.table-responsive')
    });

    // =========================================
    // DATE PICKER
    // =========================================
    $('#datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
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
        
        if (categoryId) {
            row.find('.category-select').val(categoryId).trigger('change');
        }
    });

    // =========================================
    // ADD NEW ROW
    // =========================================
    $(document).on('click', '.addRow', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $lastRow = $('#itemsBody .item-row:last');
        var $newRow = $lastRow.clone();
        var rowCount = $('#itemsBody .item-row').length;
        
        // Reset values
        $newRow.find('input[type="text"], input[type="number"]').val('');
        $newRow.find('input[type="hidden"]').val('');
        $newRow.find('select').each(function() {
            $(this).val('').removeAttr('data-select2-id');
            $(this).removeClass('select2-hidden-accessible');
        });
        $newRow.find('.select2-container').remove();
        $newRow.find('.removeRow').show();
        
        // Update name attributes with new index
        $newRow.find('[name]').each(function() {
            var name = $(this).attr('name');
            if (name && name.includes('[')) {
                var newName = name.replace(/items\[\d+\]/, 'items[' + rowCount + ']');
                $(this).attr('name', newName);
            }
        });
        
        // Update serial number
        $newRow.find('td:first').text(rowCount + 1);
        
        // Update index in data-id
        $newRow.attr('data-id', '');
        
        $('#itemsBody').append($newRow);
        $newRow.find('.item-select, .unit-select, .category-select').select2({
            dropdownParent: $('.table-responsive')
        });
        toggleRequiredAttribute($newRow, true);
        updateRemoveButtons();
    });

    // =========================================
    // REMOVE ROW
    // =========================================
    $(document).on('click', '.removeRow', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        var rows = $('#itemsBody .item-row');
        var row = $(this).closest('tr');
        
        if (rows.length > 1) {
            var itemId = row.find('input[name*="[id]"]').val();
            if (itemId) {
                var deletedItems = $('#deleted_items').val();
                if (deletedItems) {
                    $('#deleted_items').val(deletedItems + ',' + itemId);
                } else {
                    $('#deleted_items').val(itemId);
                }
            }
            
            toggleRequiredAttribute(row, false);
            row.remove();
            updateRemoveButtons();
            updateSerialNumbers();
        } else {
            showNotification('{{ __("common.at_least_one_row") }}', 'warning');
        }
    });

    // =========================================
    // UPDATE SERIAL NUMBERS
    // =========================================
    function updateSerialNumbers() {
        $('#itemsBody .item-row').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

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
            showNotification('{{ __("common.amount_positive") }}', 'warning');
        }
    });

    // =========================================
    // SHOW ERRORS
    // =========================================
    function showErrors(errors) {
        var errorContainer = $('#errorContainer');
        var errorList = $('#errorList');
        errorList.empty();
        $.each(errors, function(key, messages) {
            errorList.append('<li>' + messages[0] + '</li>');
        });
        errorContainer.show();
    }

    // =========================================
    // FORM SUBMISSION
    // =========================================
    $('#orderEditForm').on('submit', function(e) {
        e.preventDefault();
        
        $('#errorContainer').hide();
        
        var isValid = true;
        var errors = [];
        
        $('#itemsBody .item-row').each(function() {
            var row = $(this);
            var preListId = row.find('.item-select').val();
            var amount = row.find('.amount').val();
            var unitId = row.find('.unit-select').val();
            
            if (!preListId) {
                isValid = false;
                row.find('.item-select').css('border-color', 'red');
                errors.push('{{ __("wh.select_item") }}');
            } else {
                row.find('.item-select').css('border-color', '');
            }
            
            if (!amount || parseFloat(amount) <= 0) {
                isValid = false;
                row.find('.amount').css('border-color', 'red');
                errors.push('{{ __("wh.enter_valid_amount") }}');
            } else {
                row.find('.amount').css('border-color', '');
            }
            
            if (!unitId) {
                isValid = false;
                row.find('.unit-select').css('border-color', 'red');
                errors.push('{{ __("wh.select_unit") }}');
            } else {
                row.find('.unit-select').css('border-color', '');
            }
        });
        
        if (!isValid) {
            showErrors(errors);
            return;
        }
        
        var $submitBtn = $('#submit_button');
        var originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('{{ __("common.saving") }}...');
        
        var formData = $(this).serialize();
        var actionUrl = $(this).attr('action');
        
        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData + '&_method=PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $submitBtn.prop('disabled', false).text(originalText);
                if (response.status === 'success') {
                    showNotification(response.message || '{{ __("common.updated_successfully") }}', 'success');
                    setTimeout(function() {
                        window.location.href = '{{ route("draftOrders.index") }}';
                    }, 1500);
                } else {
                    showNotification(response.message || '{{ __("common.error_occurred") }}', 'danger');
                }
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).text(originalText);
                
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    $.each(errors, function(key, messages) {
                        errorMessages.push(messages[0]);
                    });
                    showNotification(errorMessages.join('<br>'), 'danger');
                } else if (xhr.status === 500) {
                    showNotification('{{ __("common.server_error") }}', 'danger');
                } else {
                    showNotification('{{ __("common.error_occurred") }}', 'danger');
                }
            }
        });
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
@endpush

@endsection