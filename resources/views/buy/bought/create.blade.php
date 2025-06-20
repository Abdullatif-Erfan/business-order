@extends('layouts.app')

@section('content')

<style>

table.new thead tr th{background-color:#fff !important; color:#000 !important;text-align:center;}
table.my_table thead tr th{background-color:#3f7cc7  !important; color:#fff !important;text-align:center;}
.new tbody tr td{padding: 10px 5px;}
select.select2{text-align:right !important;direction:rtl !important;}


@keyframes blink {
  0% { opacity: 1; }
  50% { opacity: 0; }
  100% { opacity: 1; }
}

.blink {
  animation: blink 1s linear infinite;
  color: red;
  font-size: 18px;
}
.blink {
  color: red;
  font-size: 18px;
}

</style>


<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">فورم خریداری  
                                <span class="pull-left">
                                    <a href="{{ url('boughtList') }}">
                                        <button class="btn mybtn bg-default"> برگشت به لیست </button>
                                    </a>
                                </span>
                                
                                 <small class="badge badge-info badge-sm"> <strong class="m-r-10"> نوت:</strong>دریک بل نمبر صرف خرید یک مشتری را ثبت نمایید</small>
                            </h4>
                        </div>

                        <form id="buyingForm" action="{{ route('boughtList.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="times" value="{{ $times; }}"> 
                        <input type="hidden" name="journal_code" value="{{ $newJournalCode; }}"> 

                        <!-- {{ json_encode(auth()->user()->full_name) }} -->
                        <!-- {{ json_encode(auth()->user()->id) }} -->

                        
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                                <div class="row" style="padding: 10px 20px;">

                                     <div class="col-md-12">
                                         <div class="col-md-12" style="display:none" id="errorWrapper">
                                            <div class="row">
                                                <!-- <div class="alert alert-danger col-12 " id="validationErrors"></div> -->
                                                <div class="alert alert-danger col-12" id="validationErrors">
                                                    <span class="fa fa-times close-error" style="cursor: pointer; float: left; margin-left: 10px;"></span>
                                                </div>
                                            </div>
                                         </div>
                                     </div>

                                     


                                    <!-- First Row -->
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                    <label for="customer_account_id">انتخاب فروشنده <span class="danger">*</span></label>
                                                    <select class="form-control select2" tabindex="0" style="width: 100%; border:none !important; background-color:#ddd;" name="customer_account_id" id="customer_account_id" required>
                                                        <option value=""> انتخاب فروشنده </option>
                                                        @foreach($customers as $customer)
                                                            <option value="{{ $customer->id }}">  {{ $customer->name }} </option>
                                                        @endforeach
                                                    </select>
                                                    @error('customer_account_id')
                                                        <span style='color:red'>{{ $message }}</span>
                                                    @enderror
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="from_account_id">   حساب شرکت <span class="danger">*</span></label>
                                                <select class="form-control select2" tabindex="3" style="width: 100%; background-color:#ddd;" name="from_account_id" required>
                                                    <!-- <option value="">حساب پرداخت کننده</option> -->
                                                    @foreach($ownBanks as $acc)
                                                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>


                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="todays_date">تاریخ <span class="danger">*</span></label>
                                                <div class="input-group " data-provide="datepicker">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click" data-targetselector="#todays_date" data-englishnumber="true">
                                                            <span class="fa fa-calendar"></span> 
                                                        </span>
                                                    </div>
                                                    <input class="form-control" tabindex="1" name="todays_date" id="todays_date" value="{{ $todaysDate }}" required data-mddatetimepicker="true" placeholder="تاریخ ثبت" data-placement="right" data-englishnumber="true">
                                                </div>
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="billno"> بل نمبر <span class="danger">*</span></label>
                                                <input type="number" tabindex="2" onkeyup="checkBillNoDuplication(this.value)" class="form-control" value="{{ $billno }}" name="billno" id="billno"
                                                    placeholder=" بل نمبر" required readonly>
                                                 <span id="successMsg" style="display:none"><div style="color:green">تایید است</div></span>
                                                 <span id="failurMsg" style="display:none"><div style="color:red"> بل نمبر تکراری است</div></span>
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="factor">  فاکتور </label>
                                                <input type="text" tabindex="2" class="form-control"  name="factor" id="factor" placeholder="شماره فاکتور" >
                                            </div>

                                            
                                        </div>
                                    </div>
                                    <!-- / first Row -->

                                    <!-- Second Row -->
                                    <div class="col-md-12 m-t-10">
                                        <div class="row">

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="pre_list_id">انتخاب جنس <span class="danger">*</span> </label>
                                                <select class="form-control select2" tabindex="4" style="width: 100%; background-color:#ddd;" name="pre_list_id" id="pre_list_id">
                                                    <option value="0">انتخاب جنس</option>
                                                    @foreach($preLists as $item)
                                                        <option value="{{ $item->id }}" data-code="{{ $item->code }}" data-name="{{ $item->name }}">
                                                        @if(session('package_type') == 4)
                                                         ( کد = {{ $item->code }}  )  /
                                                         @endif
                                                          {{ $item->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="amount">تعداد <span class="danger">*</span> </label>
                                                <input class="form-control" name="amount" id="amount" type="number" step="0.01" >
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="unit_id"> واحد <span class="danger">*</span> </label>
                                                <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="unit_id" id="unit_id" >
                                                    <option value="">واحد</option>
                                                    @foreach($units as $unitItem)
                                                        <option value="{{ $unitItem->id }}">{{ $unitItem->name }}</option>
                                                    @endforeach
                                                </select>
                                                
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="bought_up"> قیمت فی واحد <span class="danger">*</span> </label>
                                                <input class="form-control" name="bought_up" id='bought_up' type="number" step="0.01"  oninput="recalculateEachTotal(this)" >
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="note">    واحد پولی <span class="danger">*</span>  </label>
                                                <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="currency_id" required>
                                                    <!-- <option value="">حساب پرداخت کننده</option> -->
                                                    @foreach($currencies as $cur)
                                                        <option value="{{ $cur->id }}">{{ $cur->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- / Second Row -->

                                    <!-- Third Row -->
                                    <div class="col-md-12 m-t-10">
                                        <div class="row">

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="expire_date"> تاریخ انقضا  </label>
                                                <div class="input-group" style="margin-top:2px" data-provide="datepicker">&nbsp;&nbsp;
                                                    <input class="form-control"  name="expire_date" id="expire_date"  
                                                    data-targetselector="#expire_date" value="" 
                                                    data-mddatetimepicker="true"  placeholder="تاریخ ختم"  data-placement="right" data-englishnumber="true"  >
                                                </div>
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                               <label for="discount">  تخفیف </label>
                                                <input class="form-control" name="discount" id="discount" type="number" value="0" step="0.01">
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="transport"> ترانسپورت </label>
                                                <input class="form-control" name="transport" id="transport" value="0" type="number" step="0.01">
                                                
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="notification_amount"> مقدار هشدار </label>
                                                <input class="form-control" name="notification_amount" id="notification_amount" type="number" value="0">
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="note"> کمنت </label>
                                                <input class="form-control" name="note" id="note" type="text" placeholder="کمنت">
                                            </div>
                                            

                                        </div>
                                    </div>
                                    <!-- / Third Row -->

                                    <!-- Fourth Row -->
                                     <div class="col-12">
                                        <div class="col-12" style="background-color:#f3f3f3; margin-top:10px;padding: 5px;">
                                           <strong><center>بخش انتقالات</center></strong>
                                        </div>
                                     </div>
                                    <!-- / Fourth Row -->


                                   <!-- fifth Row -->
                                    <hr />
                                    <div class="col-md-12 m-t-20">
                                        @include('buy.bought.dynamic_form')
                                    </div>
                                   <!-- / fifth Row -->

                                     <!-- Add to list button  -->
                                     <div class="col-12">
                                        <div class="col-12" style="margin-top:10px;padding: 5px;">
                                           <button type="button" class="form-control btn btn-sm btn-info" onclick="submiteBuyingForm()">افزودن به لیست خرید</button>
                                        </div>
                                     </div>
                                    <!-- /  Add to list button  -->

                                    <div class="col-md-12">
                                        <div id="loader" style="display:none; text-align: center;">
                                            <i class="fa fa-spinner fa-spin" style="font-size:40px;"></i> 
                                        </div>
                                     </div>


                                    <!-- inserted result list -->
                                        <div class="col-12" id="insertedResult"> </div>
                                    <!-- /inserted result list -->


                                    <!-- Submit and Cancel Buttons -->
                                    <div class="col-md-8 m-t-20">
                                        <div class="row">
                                            <div class="col-12">
                                                <input type="submit" id="submit_button" name="submit" value="ثبت نهایی" class="form-control btn bg-blue pull-left btn-sm">
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

<script type="text/javascript">
    $('[data-name="disable-button"]').click(function() {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', true);
    });

    $('[data-name="enable-button"]').click(function () {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', false);
    });

});


</script>
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

function updateCurPay(curPay) {
    var payable = parseFloat($('#payable').val()) || 0;
    var curPayVal = parseFloat(curPay) || 0;
    
    var result = payable - curPayVal;
    $('#remained').val(Math.max(result, 0).toFixed(2)); // Prevent negative values

    // Hide submit button if curPay is greater than payable
    if (curPayVal > payable) {
        $('#submit_button').hide(); // Hides the submit button
        alert('پرداخت فعلی بیشتر از مبلغ قابل پرداخت نادرست میباشد')
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
                showNotification('موفقانه ثبت گردید', 'success', 'top', 'right', 'withicon');
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
                    // $('#note').val('');
                    $('.dynamic-row').find('input, select').val('');
                // ✅ Optionally, remove validation error messages
                // $('.error-message').text('');
                // $('#validationErrors').hide();


                // Remove all dynamic rows except the first one
                $('.dynamic-row:not(:first)').remove();
                
                // Optional: Reset the first row's inputs if needed
                $('.dynamic-row:first').find('input').val('');
                $('.dynamic-row:first').find('select').val('').trigger('change');

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

                showNotification('ثبت نگردید، لطفاً تمام فیلدهای ضروری را خانه پری کنید', 'danger', 'top', 'right', 'withicon');
            } else {
                showNotification('ثبت نگردید، لطفاً تمام فیلدهای ضروری را خانه پری کنید', 'danger', 'top', 'right', 'withicon');
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
        showNotification('مجموع تعداد انتقال به گدام بیشتر از مقدار اصلی است!', 'danger', 'top', 'right', 'withicon');
        return false;
    } else if (sumWarehouseAmount < totalAmount) {
        showNotification('مجموع تعداد انتقال به گدام کمتر از مقدار اصلی است!', 'danger', 'top', 'right', 'withicon');
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


