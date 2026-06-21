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
        padding: 10px 5px;
    }
    select.select2 {
        text-align: right !important;
        direction: rtl !important;
    }
    
    .order-status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: #fdcb6e; color: #2d3436; }
    .status-progress { background: #4facfe; color: #fff; }
    .status-completed { background: #00b894; color: #fff; }
    .status-cancelled { background: #e17055; color: #fff; }
    
    .order-card {
        border-left: 4px solid #4a6cf7;
        transition: all 0.3s ease;
    }
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .blink {
        animation: blink 1s linear infinite;
        color: red;
        font-size: 18px;
    }
    
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">{{ __('order.create_order') }}
                                <span class="pull-left">
                                    <a href="{{ route('orders.index') }}">
                                        <button class="btn mybtn bg-default">{{ __('common.back') }}</button>
                                    </a>
                                </span>
                                <small class="badge badge-info badge-sm">
                                    <strong class="m-r-10">{{ __('order.note') }}:</strong>
                                    {{ __('order.create_order_note') }}
                                </small>
                            </h4>
                        </div>

                        <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="times" value="{{ time() }}">

                            <div class="box-body animated fadeInRight" style="border-top: 2px solid #89b4ea;">
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;">

                                        <!-- Error Wrapper -->
                                        <div class="col-md-12">
                                            <div class="col-md-12" style="display:none" id="errorWrapper">
                                                <div class="row">
                                                    <div class="alert alert-danger col-12" id="validationErrors">
                                                        <span class="fa fa-times close-error" style="cursor: pointer; float: left; margin-left: 10px;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ========================================= -->
                                        <!-- FIRST ROW - Order Details -->
                                        <!-- ========================================= -->
                                        <div class="col-md-12">
                                            <div class="row">
                                                <!-- Order Number -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="ord_num">{{ __('order.order_number') }} <span class="danger">*</span></label>
                                                    <input type="text" class="form-control" name="ord_num" id="ord_num"
                                                        placeholder="{{ __('order.enter_order_number') }}" required>
                                                    <span id="ordNumError" class="text-danger"></span>
                                                </div>

                                                <!-- Item Selection -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="pre_list_id">{{ __('order.item_selection') }} <span class="danger">*</span></label>
                                                    <select class="form-control select2" style="width: 100%; background-color: #ddd;" 
                                                        name="pre_list_id" id="pre_list_id" required>
                                                        <option value="">{{ __('order.item_selection') }}</option>
                                                        @foreach($preLists as $item)
                                                            <option value="{{ $item->id }}" data-code="{{ $item->code }}">
                                                                @if(session('package_type') >= 4)
                                                                    ({{ __('common.code') }}: {{ $item->code }}) /
                                                                @endif
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span id="preListIdError" class="text-danger"></span>
                                                </div>

                                                <!-- Category -->
                                                <div class="col-md-2 col-sm-4 col-xs-6">
                                                    <label for="category_id">{{ __('order.category') }}</label>
                                                    <select class="form-control select2" style="width: 100%; background-color: #ddd;" 
                                                        name="category_id" id="category_id">
                                                        <option value="">{{ __('order.category_selection') }}</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Date -->
                                                <div class="col-md-2 col-sm-4 col-xs-6">
                                                    <label for="idate">{{ __('common.date') }} <span class="danger">*</span></label>
                                                    <div class="input-group" data-provide="datepicker">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" style="width: 40px !important;" 
                                                                data-mddatetimepicker="true" data-trigger="click" 
                                                                data-targetselector="#idate" data-englishnumber="true">
                                                                <span class="fa fa-calendar"></span>
                                                            </span>
                                                        </div>
                                                        <input class="form-control" name="idate" id="idate" 
                                                            value="{{ $todaysDate }}" required 
                                                            data-mddatetimepicker="true" 
                                                            placeholder="{{ __('common.date') }}" 
                                                            data-placement="right" data-englishnumber="true">
                                                    </div>
                                                    <span id="idateError" class="text-danger"></span>
                                                </div>

                                                <!-- Status -->
                                                <div class="col-md-2 col-sm-4 col-xs-6">
                                                    <label for="state">{{ __('order.status') }}</label>
                                                    <select class="form-control select2" style="width: 100%; background-color: #ddd;" 
                                                        name="state" id="state">
                                                        <option value="0">{{ __('order.pending') }}</option>
                                                        <option value="1">{{ __('order.in_progress') }}</option>
                                                        <option value="2">{{ __('order.completed') }}</option>
                                                        <option value="3">{{ __('order.cancelled') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ========================================= -->
                                        <!-- SECOND ROW - Quantity & Price -->
                                        <!-- ========================================= -->
                                        <div class="col-md-12 m-t-10">
                                            <div class="row">
                                                <!-- Amount -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="amount">{{ __('order.amount') }} <span class="danger">*</span></label>
                                                    <input class="form-control" name="amount" id="amount" type="number" 
                                                        step="0.01" placeholder="{{ __('order.enter_amount') }}" required>
                                                    <span id="amountError" class="text-danger"></span>
                                                </div>

                                                <!-- Unit -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="unit_id">{{ __('common.unit') }}</label>
                                                    <select class="form-control select2" style="width: 100%; background-color: #ddd;" 
                                                        name="unit_id" id="unit_id">
                                                        <option value="">{{ __('common.unit_selection') }}</option>
                                                        @foreach($units as $unitItem)
                                                            <option value="{{ $unitItem->id }}">{{ $unitItem->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Unit Price -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="unit_price">{{ __('order.unit_price') }}</label>
                                                    <input class="form-control" name="unit_price" id="unit_price" 
                                                        type="number" step="0.01" placeholder="{{ __('order.enter_unit_price') }}">
                                                </div>

                                                <!-- Total -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="total_price">{{ __('order.total_price') }}</label>
                                                    <input class="form-control" name="total_price" id="total_price" 
                                                        type="text" readonly placeholder="{{ __('order.total_price') }}" 
                                                        style="background-color: #f0f0f0; font-weight: bold;">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ========================================= -->
                                        <!-- THIRD ROW - Additional Info -->
                                        <!-- ========================================= -->
                                        <div class="col-md-12 m-t-10">
                                            <div class="row">
                                                <!-- Created By -->
                                                <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="iby">{{ __('order.created_by') }}</label>
                                                    <input class="form-control" name="iby" id="iby" type="text" 
                                                        placeholder="{{ __('order.enter_creator_name') }}" 
                                                        value="{{ auth()->user()->full_name ?? '' }}">
                                                </div>

                                                <!-- Done By -->
                                                <div class="col-md-3 col-sm-4 col-xs-6" id="doneByWrapper" style="display: none;">
                                                    <label for="done_by">{{ __('order.done_by') }}</label>
                                                    <input class="form-control" name="done_by" id="done_by" type="text" 
                                                        placeholder="{{ __('order.enter_done_by') }}">
                                                </div>

                                                <!-- Notes -->
                                                <div class="col-md-6 col-sm-4 col-xs-6">
                                                    <label for="notes">{{ __('order.notes') }}</label>
                                                    <input class="form-control" name="notes" id="notes" type="text" 
                                                        placeholder="{{ __('order.enter_notes') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ========================================= -->
                                        <!-- ORDER ITEMS SECTION -->
                                        <!-- ========================================= -->
                                        <div class="col-12 m-t-20">
                                            <div class="col-12" style="background-color: #f3f3f3; padding: 10px;">
                                                <strong><center>{{ __('order.order_items') }}</center></strong>
                                            </div>
                                        </div>

                                        <!-- Dynamic Items Form -->
                                        <div class="col-md-12 m-t-10">
                                            @include('order.dynamic_form')
                                        </div>

                                        <!-- Add to List Button -->
                                        <div class="col-12">
                                            <div class="col-12" style="margin-top: 10px; padding: 5px;">
                                                <button type="button" class="form-control btn btn-sm btn-info" onclick="addOrderItem()">
                                                    {{ __('order.add_item') }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Loader -->
                                        <div class="col-md-12">
                                            <div id="loader" style="display:none; text-align: center;">
                                                <i class="fa fa-spinner fa-spin" style="font-size: 40px;"></i>
                                            </div>
                                        </div>

                                        <!-- Inserted Result List -->
                                        <div class="col-12" id="insertedResult"></div>

                                        <!-- ========================================= -->
                                        <!-- SUBMIT BUTTON -->
                                        <!-- ========================================= -->
                                        <div class="col-md-12 m-t-20">
                                            <div class="row">
                                                <div class="col-12">
                                                    <input type="submit" id="submit_button" name="submit" 
                                                        value="{{ __('order.final_submit') }}" 
                                                        class="form-control btn bg-blue pull-left btn-sm">
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

<script>
// =========================================
// NOTIFICATION FUNCTION
// =========================================
function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{ __('settings.message') }}</span>';
    
    if (style === "withicon") {
        content.icon = 'fa fa-bell';
    } else {
        content.icon = 'none';
    }
    content.url = '#';
    content.target = '_blank';

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
// CLOSE ERROR
// =========================================
$(document).ready(function () {
    $(document).on('click', '.close-error', function () {
        $('#errorWrapper').fadeOut();
    });

    // Calculate total price
    $('#amount, #unit_price').on('input', function() {
        calculateTotal();
    });

    // Show/hide done_by field based on status
    $('#state').on('change', function() {
        if ($(this).val() == '2') {
            $('#doneByWrapper').show();
        } else {
            $('#doneByWrapper').hide();
        }
    });

    // Check order number duplication
    $('#ord_num').on('blur', function() {
        var ordNum = $(this).val();
        if (ordNum) {
            checkOrderNumberDuplication(ordNum);
        }
    });
});

// =========================================
// CALCULATE TOTAL
// =========================================
function calculateTotal() {
    var amount = parseFloat($('#amount').val()) || 0;
    var unitPrice = parseFloat($('#unit_price').val()) || 0;
    var total = amount * unitPrice;
    
    if (total > 0) {
        $('#total_price').val(total.toFixed(2));
    } else {
        $('#total_price').val('');
    }
}

// =========================================
// CHECK ORDER NUMBER DUPLICATION
// =========================================
function checkOrderNumberDuplication(ordNum) {
    $.ajax({
        url: "{{ route('orders.checkDuplication') }}",
        type: "GET",
        data: { ord_num: ordNum },
        success: function(response) {
            if (response.exists) {
                $('#ordNumError').text('{{ __("order.ord_num_exists") }}');
                $('#ordNumError').show();
                $('#submit_button').prop('disabled', true);
            } else {
                $('#ordNumError').text('');
                $('#ordNumError').hide();
                $('#submit_button').prop('disabled', false);
            }
        },
        error: function() {
            console.log("Error checking order number.");
        }
    });
}

// =========================================
// ADD ORDER ITEM
// =========================================
function addOrderItem() {
    // Serialize form data
    var formData = $('#orderForm').serialize();

    // Show loader
    $('#loader').show();

    // AJAX submission
    $.ajax({
        url: "{{ route('orders.addItem') }}",
        type: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            $('#loader').hide();
            if (response.status === 'failed') {
                showNotification(response.message, 'danger', 'top', 'right', 'withicon');
            } else {
                showNotification("{{ __('common.added_successfully') }}", 'success', 'top', 'right', 'withicon');
                $('#insertedResult').html(response);
                
                // Clear form fields
                $('#pre_list_id').val('').trigger('change');
                $('#category_id').val('').trigger('change');
                $('#amount').val('');
                $('#unit_id').val('').trigger('change');
                $('#unit_price').val('');
                $('#total_price').val('');
                $('#notes').val('');
                $('#iby').val('{{ auth()->user()->full_name ?? '' }}');
                
                // Reset dynamic rows
                $('.dynamic-row:not(:first)').remove();
                $('.dynamic-row:first').find('input').val('');
                $('.dynamic-row:first').find('select').val('').trigger('change');
            }
        },
        error: function(xhr) {
            $('#loader').hide();
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorList = '<ul>';
                
                $.each(errors, function(key, messages) {
                    errorList += '<li>' + messages[0] + '</li>';
                    if ($('#' + key + 'Error').length) {
                        $('#' + key + 'Error').text(messages[0]);
                    }
                });
                
                errorList += '</ul>';
                $('#validationErrors').html('<span class="fa fa-times close-error" style="cursor: pointer; float: left; margin-left: 10px;"></span>' + errorList).show();
                $('#errorWrapper').fadeIn(10);
                
                showNotification("{{ __('common.validation_errors') }}", 'danger', 'top', 'right', 'withicon');
            } else {
                showNotification("{{ __('common.error_occurred') }}", 'danger', 'top', 'right', 'withicon');
            }
        }
    });
}

// =========================================
// FORM SUBMISSION HANDLER
// =========================================
$(document).ready(function() {
    $('#orderForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        // Show loader
        $('#loader').show();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                $('#loader').hide();
                if (response.status === 'success') {
                    showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    // Redirect or reload
                    setTimeout(function() {
                        window.location.href = "{{ route('orders.index') }}";
                    }, 1000);
                } else {
                    showNotification(response.message, 'danger', 'top', 'right', 'withicon');
                }
            },
            error: function(xhr) {
                $('#loader').hide();
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorList = '<ul>';
                    
                    $.each(errors, function(key, messages) {
                        errorList += '<li>' + messages[0] + '</li>';
                    });
                    
                    errorList += '</ul>';
                    $('#validationErrors').html('<span class="fa fa-times close-error" style="cursor: pointer; float: left; margin-left: 10px;"></span>' + errorList).show();
                    $('#errorWrapper').fadeIn(10);
                    
                    showNotification("{{ __('common.validation_errors') }}", 'danger', 'top', 'right', 'withicon');
                } else {
                    showNotification("{{ __('common.error_occurred') }}", 'danger', 'top', 'right', 'withicon');
                }
            }
        });
    });
});
</script>

<!-- ========================================= -->
<!-- INCLUDE DYNAMIC FORM -->
<!-- ========================================= -->
@include('order.dynamic_form_js')

@endsection