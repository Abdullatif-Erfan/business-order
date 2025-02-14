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
                            </h4>
                        </div>

                        <form id="buyingForm">
                        @csrf
                        <input type="hidden" name="times" value="{{ $times; }}">
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                                <div class="row" style="padding: 10px 20px;">

                                     <div class="col-md-12">
                                        <div id="loader" style="display:none; text-align: center;">
                                            <i class="fa fa-spinner fa-spin" style="font-size:40px;"></i> 
                                        </div>
                                     </div>
                                     <div class="col-md-12">
                                       @if ($errors->any())
                                         <div class="col-md-12 border">
                                            <div class="row">
                                                <div class="alert alert-danger col-12">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                         <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                         </div>
                                         @endif
                                     </div>


                                    <!-- First Row -->
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <div class="form-group">
                                                    <label for="customer_account_id">انتخاب فروشنده <span class="danger">*</span></label>
                                                    <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" name="customer_account_id" id="customer_account_id" required>
                                                        <option value=""> انتخاب فروشنده </option>
                                                        @foreach($customers as $customer)
                                                            <option value="{{ $customer->id }}">  {{ $customer->name }} </option>
                                                        @endforeach
                                                    </select>
                                                    @error('customer_account_id')
                                                        <span style='color:red'>{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="todays_date">تاریخ <span class="danger">*</span></label>
                                                <div class="input-group mb-3" style="margin-top:10px" data-provide="datepicker">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click" data-targetselector="#exampleInput00" data-englishnumber="true">
                                                            <span class="fa fa-calendar"></span> 
                                                        </span>
                                                    </div>
                                                    <input class="form-control" name="todays_date" id="exampleInput00" value="{{ $todaysDate }}" required data-mddatetimepicker="true" placeholder="تاریخ ثبت" data-placement="right" data-englishnumber="true">
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="billno">نمبر بل <span class="danger">*</span></label>
                                                <div class="form-group">
                                                    <input type="number" class="form-control" name="billno" placeholder="نمبر بل" required>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="billno">  پرداخت کننده <span class="danger">*</span></label>
                                                <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="from_account_id" required>
                                                    <!-- <option value="">حساب پرداخت کننده</option> -->
                                                    @foreach($ownBanks as $acc)
                                                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>


                                        </div>
                                    </div>
                                    <!-- / first Row -->

                                    <!-- Second Row -->
                                    <div class="col-md-12">
                                        <div class="row">

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="customer_account_id">انتخاب جنس <span class="danger">*</span> </label>
                                                <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="pre_list_id" required>
                                                    <option value="0">انتخاب جنس</option>
                                                    @foreach($preLists as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="todays_date">تعداد <span class="danger">*</span> </label>
                                                <input class="form-control" name="amount" id="amount" type="number" oninput="recalculateEachTotal(this)" step="0.01" required>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="billno"> واحد <span class="danger">*</span> </label>
                                                <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="unit_id" required>
                                                    <option value="">واحد</option>
                                                    @foreach($units as $unitItem)
                                                        <option value="{{ $unitItem->id }}">{{ $unitItem->name }}</option>
                                                    @endforeach
                                                </select>
                                                
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="billno"> قیمت فی واحد <span class="danger">*</span> </label>
                                                <input class="form-control" name="bought_up" type="number" step="0.01"  oninput="recalculateEachTotal(this)" required>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- / Second Row -->

                                    <!-- Third Row -->
                                    <div class="col-md-12 m-t-10">
                                        <div class="row">
                                            
                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="note">    واحد پولی <span class="danger">*</span>  </label>
                                                <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="currency_id" required>
                                                    <!-- <option value="">حساب پرداخت کننده</option> -->
                                                    @foreach($currencies as $cur)
                                                        <option value="{{ $cur->id }}">{{ $cur->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-sm-4 col-xs-6">
                                                <label for="billno"> تاریخ انقضا  </label>
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
                                                <label for="trans_spend"> ترانسپورت </label>
                                                <input class="form-control" name="trans_spend" id="trans_spend" value="0" type="number" step="0.01">
                                                
                                            </div>

                                            <div class="col-md-2 col-sm-4 col-xs-6">
                                                <label for="note"> مقدار هشدار </label>
                                                <input class="form-control" name="notification_amount" id="notification_amount" type="number" value="0">
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


                                    <!-- Submit and Cancel Buttons -->
                                    <div class="col-md-8 m-t-20">
                                        <div class="row">
                                            <div class="col-3">
                                                <input type="submit" id="submit_button" name="submit" value="ثبت" class="form-control btn bg-blue pull-left">
                                            </div>
                                            <div class="col-3">
                                                <a href="{{ url('boughtList.index') }}">
                                                    <button type="button" class="form-control btn bg-danger">لغو</button>
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

<script type="text/javascript">
    $('[data-name="disable-button"]').click(function() {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', true);
    });

    $('[data-name="enable-button"]').click(function () {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', false);
    });
</script>
@endpush



<script>

function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> پیام </span>';
    
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


function submiteBuyingForm()
{
     // Serialize form data
     var formData = $('#buyingForm').serialize();

     console.log('formData',formData);

     // Show loading state
     $('#loading').show();

     // AJAX form submission
     $.ajax({
        url: '/boughtList/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: (response) => {
            $('#loading').hide();
            if (response.status === 'success') {
                showNotification('موفقانه ثبت گردید', 'success', 'top', 'right', 'withicon');
            } else {
                showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading').hide();
            showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
            // Handle validation errors
            // if (xhr.status === 422) { // Laravel validation error status code
            //     var errors = xhr.responseJSON.errors;
            //     if (errors?.name) {
            //         $('#currencyNameError').text(errors.name[0]);
            //     }
            //     if (errors?.symbols) {
            //         $('#symbolsError').text(errors.symbols[0]);
            //     }
            //     if (errors?.color) {
            //         $('#colorError').text(errors.color[0]);
            //     }
            //     if (errors?.is_base) {
            //         $('#isBaseError').text(errors.is_base[0]);
            //     }
            // } else {
            //     // General error handling
            //     showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
            // }
        }
    });

}
</script>

@endsection


