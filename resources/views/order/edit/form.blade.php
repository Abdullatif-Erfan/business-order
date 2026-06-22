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
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">{{ __('order.edit_order') }}
                                <span class="pull-left">
                                    <a href="{{ route('orders.index') }}">
                                        <button class="btn mybtn bg-default">{{ __('common.back') }}</button>
                                    </a>
                                </span>
                            </h4>
                        </div>

                        <form id="orderEditForm" action="{{ route('orders.update', $order->ord_num) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="ord_num" value="{{ $order->ord_num }}">
                            <input type="hidden" name="times" value="{{ $order->times }}">
                            <input type="hidden" name="delete_items" id="delete_items" value="">

                            <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;">

                                        <!-- Validation Errors -->
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

                                        <!-- ========================================= -->
                                        <!-- FIRST ROW -->
                                        <!-- ========================================= -->
                                        <div class="col-md-12">
                                            <div class="row">
                                                <!-- Supplier -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="supplier_id">{{ __('order.supplier_name') }} <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" style="width: 100%; background-color:#ddd;" 
                                                            name="supplier_id" id="supplier_id" required>
                                                        <option value="">{{ __('order.supplier_selection') }}</option>
                                                        @foreach($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}" {{ $order->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                                {{ $supplier->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('supplier_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <!-- Employee/Driver -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="employee_id">{{ __('order.employee_name') }} <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" style="width: 100%; background-color:#ddd;" 
                                                            name="employee_id" id="employee_id" required>
                                                        <option value="">{{ __('order.employee_selection') }}</option>
                                                        @foreach($employees as $employee)
                                                            <option value="{{ $employee->id }}" {{ $order->employee_id == $employee->id ? 'selected' : '' }}>
                                                                {{ $employee->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('employee_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <!-- Date Picker -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="date">{{ __('order.date') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group date" id="datepicker">
                                                        <input type="text" class="form-control" name="date" id="date" 
                                                            value="{{ $order->idate ? \Carbon\Carbon::parse($order->idate)->format('Y-m-d') : date('Y-m-d') }}" 
                                                            placeholder="{{ __('common.date') }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text datepicker-icon">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @error('date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <!-- Status -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="state">{{ __('order.status') }}</label>
                                                    <select class="form-control" name="state" id="state">
                                                        <option value="0" {{ $order->state == 0 ? 'selected' : '' }}>{{ __('order.draft') }}</option>
                                                        <option value="1" {{ $order->state == 1 ? 'selected' : '' }}>{{ __('order.new') }}</option>
                                                        <option value="2" {{ $order->state == 2 ? 'selected' : '' }}>{{ __('order.done') }}</option>
                                                        <option value="3" {{ $order->state == 3 ? 'selected' : '' }}>{{ __('order.cancelled') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ========================================= -->
                                        <!-- SECOND ROW - Dynamic Items -->
                                        <!-- ========================================= -->
                                        <div class="col-md-12 m-t-20">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered new" id="itemsTable">
                                                            <thead>
                                                                <tr style="background:#e9fffe">
                                                                    <th style="width:40%">{{ __('wh.item_selection') }}</th>
                                                                    <th style="width:20%">{{ __('common.amount') }}</th>
                                                                    <th style="width:30%">{{ __('common.unit') }}</th>
                                                                    <th style="width:10%">{{ __('common.add') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="itemsBody">
                                                                @foreach($orderItems as $index => $item)
                                                                <tr class="item-row" data-id="{{ $item->id }}">
                                                                    <td>
                                                                        <select class="form-control select2 item-select" name="buy_pre_list[]" style="width: 100%;" required>
                                                                            <option value="">{{ __('wh.item_selection') }}</option>
                                                                            @foreach($preLists as $preList)
                                                                                <option value="{{ $preList->id }}"
                                                                                    data-category-id="{{ $preList->category_id ?? '' }}"
                                                                                    data-pre-list-id="{{ $preList->id }}"
                                                                                    {{ $item->pre_list_id == $preList->id ? 'selected' : '' }}>
                                                                                    {{ $preList->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input name="amount[]" class="form-control amount" type="number" step="0.01" 
                                                                               value="{{ $item->amount }}" placeholder="{{ __('common.amount') }}" required>
                                                                    </td>
                                                                    <td>
                                                                        <input name="category_id[]" class="form-control pre-category-id" value="{{ $item->category_id ?? '' }}" type="hidden" readonly>
                                                                        <input name="item_id[]" class="form-control item-id" type="hidden" value="{{ $item->id }}">
                                                                        
                                                                        <select class="form-control select2 unit-select" name="unit_id[]" style="width: 100%;" required>
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

                                        <hr />

                                        <!-- ========================================= -->
                                        <!-- SUBMIT BUTTONS -->
                                        <!-- ========================================= -->
                                        <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                            <div class="row">
                                                <div class="col-3 col-xs-6">
                                                    <button type="submit" id="submit_button" class="form-control btn bg-blue">
                                                        {{ __('common.edit') }}
                                                    </button>
                                                </div>
                                                <div class="col-3 col-xs-6">
                                                    <a href="{{ route('orders.index') }}">
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
    $('.item-select, .unit-select').select2();

    // Store the template row HTML
    var templateRow = $('#itemsBody .item-row:first').clone();

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

        row.find('.pre-category-id').val(categoryId);
        row.find('.pre-list-id').val(preListId);
    });

    // =========================================
    // ADD NEW ROW
    // =========================================
    $(document).on('click', '.addRow', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $newRow = templateRow.clone();
        
        // Reset values
        $newRow.find('input[type="text"], input[type="number"]').val('');
        $newRow.find('.item-id').val('');
        $newRow.find('select').each(function() {
            $(this).val('').removeAttr('data-select2-id');
            $(this).removeClass('select2-hidden-accessible');
        });
        $newRow.find('.select2-container').remove();
        $newRow.find('.removeRow').show();
        
        $('#itemsBody').append($newRow);
        $newRow.find('.item-select, .unit-select').select2();
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
            // Check if this is an existing item (has ID)
            var itemId = row.find('.item-id').val();
            if (itemId) {
                // Mark for deletion
                var deleteItems = $('#delete_items').val();
                if (deleteItems) {
                    $('#delete_items').val(deleteItems + ',' + itemId);
                } else {
                    $('#delete_items').val(itemId);
                }
            }
            
            toggleRequiredAttribute(row, false);
            row.remove();
            updateRemoveButtons();
        } else {
            showNotification('{{ __("common.at_least_one_row") }}', 'warning');
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
    // FORM SUBMISSION
    // =========================================
    $('#orderEditForm').on('submit', function(e) {
        e.preventDefault();
        
        var isValid = true;
        var errorMessages = [];
        
        $('#itemsBody .item-row').each(function() {
            var row = $(this);
            var preListId = row.find('.item-select').val();
            var amount = row.find('.amount').val();
            var unitId = row.find('.unit-select').val();
            
            if (!preListId) {
                isValid = false;
                errorMessages.push('{{ __("wh.select_item") }}');
            }
            if (!amount || parseFloat(amount) <= 0) {
                isValid = false;
                errorMessages.push('{{ __("wh.enter_valid_amount") }}');
            }
            if (!unitId) {
                isValid = false;
                errorMessages.push('{{ __("wh.select_unit") }}');
            }
        });
        
        if (!isValid) {
            showNotification(errorMessages.join('<br>'), 'danger');
            return;
        }
        
        var $submitBtn = $('#submit_button');
        var originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('{{ __("common.saving") }}...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $submitBtn.prop('disabled', false).text(originalText);
                if (response.status === 'success') {
                    showNotification(response.message, 'success');
                    setTimeout(function() {
                        window.location.href = '{{ route("orders.index") }}';
                    }, 1000);
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