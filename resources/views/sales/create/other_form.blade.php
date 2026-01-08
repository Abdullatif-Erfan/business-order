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
                            <h4 class="card-title"> {{__('sales.pos_list_title')}} 
                                <span class="pull-left">
                                    <a href="{{ route('sales.index') }}">
                                        <button class="btn mybtn bg-default">  {{__('common.back')}}  </button>
                                    </a>
                                </span>
                                
                                 <small class="badge badge-info badge-sm"> <strong class="m-r-10"> 
                                  {{__('sales.note_description')}} : </strong>  </small>
                            </h4>
                        </div>

                        <form id="buyingForm" action="{{ route('sales.store') }}" method="POST">
                        @csrf

                        <!-- {{ json_encode(auth()->user()->full_name) }} -->
                        <!-- {{ json_encode(auth()->user()->id) }} -->
                        <input type="hidden" name="times" value="{{ $times ?? 0 }}">
                        <input type="hidden" name="code" value="{{ $journal_code ?? 0 }}">
                        

                        
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

                                     

                                    <!-- First Row -->
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="customer_account_id"> {{__('sales.customer_selection')}} <span class="danger">*</span></label>
                                                    <select class="form-control select2" tabindex="0" style="width: 100%; border:none !important; background-color:#ddd;" name="customer_account_id" id="customer_account_id" required>
                                                        <option value="">   {{__('sales.customer_selection')}} </option>
                                                        @foreach($customers as $customer)
                                                            <option value="{{ $customer->id }}">  {{ $customer->name }} </option>
                                                        @endforeach
                                                    </select>
                                                    @error('customer_account_id')
                                                        <span style='color:red'>{{ $message }}</span>
                                                    @enderror
                                            </div>


                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="todays_date"> {{__('common.date')}} <span class="danger">*</span></label>
                                                <div class="input-group " data-provide="datepicker">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click" data-targetselector="#todays_date" data-englishnumber="true">
                                                            <span class="fa fa-calendar"></span> 
                                                        </span>
                                                    </div>
                                                    <input class="form-control" tabindex="1" name="todays_date" id="todays_date" value="{{ $todaysDate }}" required data-mddatetimepicker="true" placeholder="{{__('common.date')}}" data-placement="right" data-englishnumber="true">
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="billno"> {{__('common.bill')}} <span class="danger">*</span></label>
                                                    <input type="number" tabindex="2" class="form-control" value="{{ $billno }}" name="billno" id="billno" placeholder="{{__('common.bill')}}" required readonly>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="factor">  {{__('common.factor')}} </label>
                                                    <input type="text" tabindex="2" class="form-control"  name="factor" id="factor" placeholder="{{__('common.factor')}}"  >
                                            </div>

                                            
                                        </div>
                                    </div>
                                    <!-- / first Row -->

                                    <!-- Second Row -->
                                    <div class="col-md-12 m-t-20">
                                        <div class="row">
                                           @include('sales.create.other_dynamic_item_list')
                                        </div>
                                    </div>
                                    <!-- / Second Row -->

                                    <hr />
                                 

                                    <!-- Second Row -->
                                     <div class="col-md-12 m-t-20">
                                        <div class="row">
                                           @include('sales.create.other_result_form')
                                        </div>
                                    </div>
                                    <!-- / Second Row -->


                                    <!-- Submit and Cancel Buttons -->
                                    <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                        <div class="row">
                                            <div class="col-3 col-xs-6">
                                            <input type="submit" id="submit_button" name="submit" value="{{__('common.save')}}" class="form-control btn bg-blue pull-left" >
                                            </div>
                                            <div class="col-3 col-xs-6">
                                            <a href="{{ route('sales.index') }}">
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
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> پیام  </span>';
    
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
        alert("{{__('buy.over_pay')}}");
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
        showNotification("{{__('common.over_pay')}}", 'danger', 'top', 'right', 'withicon');
        return false;
    } else if (sumWarehouseAmount < totalAmount) {
        showNotification("{{__('common.over_pay')}}", 'danger', 'top', 'right', 'withicon');
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


