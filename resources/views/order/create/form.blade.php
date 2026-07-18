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
                            <h4 class="card-title">{{ __('order.create_order') }}
                                <span class="pull-left">
                                    <a href="{{ route('orders.index') }}">
                                        <button class="btn mybtn bg-default">{{ __('common.back') }}</button>
                                    </a>
                                </span>
                            </h4>
                        </div>

                        <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="times" value="{{ $times ?? 0 }}">

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

                                                <!-- Date Picker - Using Reusable Component -->
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    <label for="date">{{__('order.date')}} <span class="text-danger">*</span></label>
                                                    <div class="input-group date" id="datepicker">
                                                        <input type="text" class="form-control" name="date" 
                                                            value="{{ date('Y-m-d') }}" placeholder="Select date">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Status -->
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    <label for="state">{{ __('order.status') }}</label>
                                                    <select class="form-control" name="state" id="state" readonly>
                                                        <option value="1">{{ __('order.new') }}</option>
                                                        <!-- <option value="2">{{ __('order.cancelled') }}</option> -->
                                                        <!-- <option value="3">{{ __('order.completed') }}</option> -->
                                                    </select>
                                                </div>
                                                

                                            </div>
                                        </div>

                                        <!-- <div class="col-md-12 m-t-10">
                                            <button type="button" id="addItemBtn" class="btn btn-success btn-sm">
                                                <i class="fa fa-plus"></i> {{ __('order.add_item') }}
                                            </button>
                                        </div> -->

                                        <!-- ========================================= -->
                                        <!-- SECOND ROW - Dynamic Items -->
                                        <!-- ========================================= -->
                                        <div class="col-md-12 m-t-20">
                                            <div class="rows">
                                                @include('order.create.dynamic_item_list')
                                            </div>
                                        </div>

                                        <hr />

                                        <!-- ========================================= -->
                                        <!-- SUBMIT BUTTONS -->
                                        <!-- ========================================= -->
                                        <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                            @if(count($groupedItems) > 0) 
                                            <div class="row">
                                                <div class="col-3 col-xs-6">
                                                    <button type="submit" id="submit_button" class="form-control btn bg-blue">
                                                        {{ __('common.save') }}
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
                                            @endif
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
        
        // Get data from selected option
        var categoryId = selectedOption.data('category-id') || '';
        var preListId = selectedOption.data('pre-list-id') || '';
        var unitId = selectedOption.data('unit-id') || '';

        // Set values in the row
        row.find('.pre-category-id').val(categoryId);
        row.find('.pre-list-id').val(preListId);
        
        // Auto-select unit if available
        if (unitId) {
            row.find('.unit-select').val(unitId).trigger('change');
        } else {
            row.find('.unit-select').val('').trigger('change');
        }
    });

    // =========================================
    // CAPTURE UNIT SELECTION CHANGE
    // =========================================
    $(document).on('change', '.unit-select', function () {
        var row = $(this).closest('tr');
        var selectedUnitId = $(this).val();
        console.log('Row:', row.index(), 'Unit selected:', selectedUnitId);
    });

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
    // ADD NEW ROW
    // =========================================
    $(document).on('click', '.addRow', function () {
        var $lastRow = $('.item-row:last');
        var $newRow = $lastRow.clone();
        var rowCount = $('.item-row').length;

        // Reset input values
        $newRow.find('input[type="text"], input[type="number"], input[type="hidden"]').val('');
        $newRow.find('.item-select, .unit-select').val('').trigger('change');
        
        // Remove select2 and reinitialize
        $newRow.find('.select2-container').remove();
        $newRow.find('.item-select, .unit-select').removeClass('select2-hidden-accessible').show();
        
        // Append new row
        $lastRow.after($newRow);
        
        // Reinitialize select2 for new row
        $newRow.find('.item-select, .unit-select').select2();
        
        // Show remove button for all rows except first
        if (rowCount > 0) {
            $newRow.find('.removeRow').show();
        }
        
        // Add required attributes
        toggleRequiredAttribute($newRow, true);
    });

    // =========================================
    // REMOVE ROW
    // =========================================
    $(document).on('click', '.removeRow', function () {
        var rows = $('.item-row');
        
        if (rows.length > 1) {
            var row = $(this).closest('tr');
            
            // Prevent deleting the first row
            if (row.index() !== 0) {
                toggleRequiredAttribute(row, false);
                row.remove();
                
                // Hide remove button if only one row left
                if ($('.item-row').length === 1) {
                    $('.removeRow').hide();
                }
            } else {
                showNotification('{{ __("common.at_least_one_row") }}', 'warning', 'top', 'right', 'withicon');
            }
        } else {
            showNotification('{{ __("common.at_least_one_row") }}', 'warning', 'top', 'right', 'withicon');
        }
    });

    // =========================================
    // FORM SUBMISSION
    // =========================================
    $('#orderForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var items = [];
        var isValid = true;
        var errorMessages = [];
        
        $('.item-row').each(function(index) {
            var row = $(this);
            var preListId = row.find('.item-select').val();
            var amount = row.find('.amount').val();
            var unitId = row.find('.unit-select').val();
            var categoryId = row.find('.pre-category-id').val();
            
            // Validate item selection
            if (!preListId) {
                isValid = false;
                row.find('.item-select').css('border-color', 'red');
                errorMessages.push('{{ __("wh.select_item") }}');
            } else {
                row.find('.item-select').css('border-color', '');
            }
            
            // Validate amount
            if (!amount || parseFloat(amount) <= 0) {
                isValid = false;
                row.find('.amount').css('border-color', 'red');
                errorMessages.push('{{ __("wh.enter_valid_amount") }}');
            } else {
                row.find('.amount').css('border-color', '');
            }
            
            // Validate unit
            if (!unitId) {
                isValid = false;
                row.find('.unit-select').css('border-color', 'red');
                errorMessages.push('{{ __("wh.select_unit") }}');
            } else {
                row.find('.unit-select').css('border-color', '');
            }
            
            if (preListId && amount && parseFloat(amount) > 0 && unitId) {
                items.push({
                    pre_list_id: preListId,
                    amount: parseFloat(amount),
                    unit_id: unitId,
                    category_id: categoryId
                });
            }
        });
        
        if (!isValid || items.length === 0) {
            showNotification(errorMessages.join('<br>'), 'danger', 'top', 'right', 'withicon');
            return;
        }
        
        var $submitBtn = $('#submit_button');
        var originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('{{ __("common.saving") }}...');
        
        // Get form data with items
        var formData = $(this).serialize();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $submitBtn.prop('disabled', false).text(originalText);
                if (response.status === 'success') {
                    showNotification(response.message || '{{ __("common.added_successfully") }}', 
                                   'success', 'top', 'right', 'withicon');
                    
                    setTimeout(function() {
                        window.location.href = '{{ route("orders.index") }}';
                    }, 1000);
                } else {
                    showNotification(response.message || '{{ __("common.error_occurred") }}', 
                                   'danger', 'top', 'right', 'withicon');
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
                    
                    showNotification(errorMessages.join('<br>'), 'danger', 'top', 'right', 'withicon');
                } else if (xhr.status === 500) {
                    showNotification('{{ __("common.server_error") }}', 'danger', 'top', 'right', 'withicon');
                } else {
                    showNotification('{{ __("common.error_occurred") }}', 'danger', 'top', 'right', 'withicon');
                }
            }
        });

         return false;
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
    // HANDLE CLOSE ERROR
    // =========================================
    $(document).on('click', '.close-error', function() {
        $('#errorWrapper').fadeOut();
    });

    // =========================================
    // INITIAL SETUP
    // =========================================
    // Hide remove button for first row initially
    if ($('.item-row').length === 1) {
        $('.removeRow').hide();
    }
});
</script>

@endpush

@endsection