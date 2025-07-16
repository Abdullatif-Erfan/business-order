@extends('layouts.app')
@section('content')

<style>

table.new thead tr th{background-color:#fff !important; color:#000 !important;text-align:center;}
table.my_table thead tr th{background-color:#3f7cc7  !important; color:#fff !important;text-align:center;}
.new tbody tr td{padding: 5px 5px;}
select.select2{text-align:right !important;direction:rtl !important;}
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
                            <h4 class="card-title">{{__('wh.wastage_create_title')}}  
                                <span class="pull-left">
                                    <a href="{{ route('warehousesList.wastage') }}">
                                        <button class="btn mybtn bg-default"> {{__('common.back')}} </button>
                                    </a>
                                </span>
                            </h4>
                        </div>

                        <form id="buyingForm" action="{{ route('warehousesList.wastage.store') }}" method="POST">
                        @csrf

                        <!-- {{ json_encode(auth()->user()->full_name) }} -->
                        <!-- {{ json_encode(auth()->user()->id) }} -->
                        <input type="hidden" name="todays_date" value="{{ $todays_date ?? 0 }}">
                        

                        
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                                <div class="row" style="padding: 10px 20px;">

                                @if ($errors->any())
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <div class="row">
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
                                        </div>
                                    </div>
                                @endif

                                     

                                   

                                    <!-- Second Row -->
                                    <div class="col-md-12 m-t-20">
                                        <div class="row">
                                           @include('warehouseitem.wastage.dynamic_form')
                                        </div>
                                    </div>
                                    <!-- / Second Row -->

                                    <hr />
                                 

                                 


                                    <!-- Submit and Cancel Buttons -->
                                    <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                        <div class="row">
                                            <div class="col-3 col-xs-6">
                                            <input type="submit" id="submit_button" name="submit" value="{{__('common.save')}}" class="form-control btn bg-blue pull-left" >
                                            </div>
                                            <div class="col-3 col-xs-6">
                                            <a href="{{ route('warehousesList.wastage') }}">
                                            <button type="button"  class="form-control btn bg-danger">{{__('common.cancel')}}</button>
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

<script src="{{ asset('assets/datepicker/jalaali.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js') }}" type="text/javascript"></script>

@endpush






<script>
function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> {{ __('settings.message') }}  </span>';
    
    if (style === "withicon") {
        content.icon = 'fa fa-bell';
    } else {
        content.icon = 'none';
    }
    content.url = '#';
    content.target = '_blank';

    $.notify(content, {
        type: type, // Default, Primary, Secondary, Info, Success, Warning, Danger
        placement: {
            from: from, // top, bottom
            align: align // right, center, left
        },
        time: 500
    });
}



function updateRemainOnCurPay(cur_pay) {
    var payable = parseFloat($('#payable').val()) || 0;
    cur_pay = parseFloat(cur_pay) || 0;

    // Ensure cur_pay is not more than payable
    if (cur_pay > payable) {
        cur_pay = payable;
        $('#cur_pay').val(payable.toFixed(2)); // Update the input field to the max value
    }

    var result = payable - cur_pay;
    $('#remained').val(result.toFixed(2));
}

function updateCurPay(curPay) {
    var payable = parseFloat($('#payable').val()) || 0;
    var curPayVal = parseFloat(curPay) || 0;
    
    var result = payable - curPayVal;
    $('#remained').val(Math.max(result, 0).toFixed(2)); // Prevent negative values

    // Hide submit button if curPay is greater than payable
    if (curPayVal > payable) {
        $('#submit_button').hide(); // Hides the submit button
        alert("{{__('buy.over_pay')}}")
    } else {
        $('#submit_button').show(); // Shows the submit button
    }
}

function submiteBuyingForm()
{
    if (!validateWarehouseAmounts()) {
         event.preventDefault(); // Prevent form submission
    }
    else 
    {
     // Serialize form data
     var formData = $('#buyingForm').serialize();

    //  console.log('formData', JSON.parse(formData));

     // Show loader state
     $('#loader').show();

     // AJAX form submission
     $.ajax({
        url: '/boughtList/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: (response) => {
            if(response.status ==='failed') 
            {
                showNotification(response.message, 'danger', 'top', 'right', 'withicon');
                $('#loader').hide();
            }
            else 
            {
                $('#loader').hide();
                $('#errorWrapper').fadeOut(10);
                showNotification("{{__('common.added_successfully')}}", 'success', 'top', 'right', 'withicon');
                $('#insertedResult').html(response);

                // ✅ Clear the form after successful submission
                //  $('#buyingForm')[0].reset();
                // ✅ Clear form fields manually except specific ones
                    $('#pre_list_id').val('');
                    $('#amount').val('');
                    $('#unit_id').val('');
                    $('#bought_up').val('');
                    $('#expire_date').val('');
                    $('#discount').val('0');
                    $('#transport').val('0');
                    $('#notification_amount').val('0');
                    $('.dynamic-row').find('input, select').val('');
                // ✅ Optionally, remove validation error messages
                // $('.error-message').text('');
                // $('#validationErrors').hide();
            }

        },
        error: (xhr) => {
            $('#loader').hide();
            // $('#insertedResult').html('خطا رخ داد'); // General error message

            if (xhr.status === 422) { // Laravel validation error response
                var errors = xhr.responseJSON.errors;
                var errorList = '<ul>';
                
                $.each(errors, function (key, messages) {
                    errorList += '<li>' + messages[0] + '</li>'; // Show only the first error for each field
                    $('#' + key + 'Error').text(messages[0]); // Display inline error message if an element exists
                });

                errorList += '</ul>';

                // Add close button dynamically inside the validationErrors div
                var errorHtml = `
                    <span class="fa fa-times close-error" style="cursor: pointer; float: left; margin-left: 10px;"></span>
                    ${errorList}
                `;

                $('#validationErrors').html(errorHtml).show();
                $('#errorWrapper').fadeIn(10);

                showNotification("{{__('buy.fill_all_fields')}}", 'danger', 'top', 'right', 'withicon');
            } else {
                showNotification("{{__('buy.all_fields_required')}}", 'danger', 'top', 'right', 'withicon');
            }
        }

    });
 }
}
</script>

<script>
// Function to check sum validation
function validateWarehouseAmounts() 
{
    let totalAmount = parseFloat($('#amount').val()) || 0;
    let sumWarehouseAmount = 0;

    $('input[name="warehouse_amount[]"]').each(function () {
         sumWarehouseAmount += parseFloat($(this).val()) || 0;
    });

    console.log('totalAmount', totalAmount);
    console.log('warehouse_amount', sumWarehouseAmount);

    if (sumWarehouseAmount > totalAmount) {
        showNotification("{{__('buy.invalid_amount')}}", 'danger', 'top', 'right', 'withicon');
        return false;
    } else if (sumWarehouseAmount < totalAmount) {
        showNotification("{{__('buy.invalid_amount')}}", 'danger', 'top', 'right', 'withicon');
        return false;
    } else {
        $('#warehouseAmountError').text(''); // Clear error if valid
        return true;
    }
}

$(document).ready(function () {
    $(document).on('click', '.close-error', function () {
        $('#errorWrapper').fadeOut();
    });
});


function checkBillNoDuplication(billNo) 
{
    if (billNo == 0) {
        $("#failurMsg").show();
        $("#successMsg").hide();
        return;
    }

    $.ajax({
        url: "{{ route('boughtList.checkBillNoDuplication') }}", // Define this route in web.php
        type: "GET",
        data: { billno: billNo },
        success: function(response) {
            if (response.exists) {
                $("#failurMsg").show();
                $("#successMsg").hide();
            } else {
                $("#successMsg").show();
                $("#failurMsg").hide();
            }
        },
        error: function() {
            console.log("Error checking bill number.");
        }
    });
}

</script>

@endsection


