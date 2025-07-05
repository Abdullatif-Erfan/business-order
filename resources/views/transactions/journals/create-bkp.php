@extends('layouts.app')
@section('title', 'روزنامچه')
@section('content')

<style>
    @keyframes blink {
  0% { opacity: 1; }
  50% { opacity: 0; }
  100% { opacity: 1; }
}

.blink {
  animation: blink 1s linear infinite;
  color: red;
  font-size: 20px;
}
.blink {
  color: red;
  font-size: 20px;
}

@keyframes bold_normal {  
    0%, 100% {  
    font-weight: bold; /* Start and end with bold */  
  }  
  50% {  
    font-weight: normal; /* Transition to normal */  
  }  
}  

.typing-effect {  
  animation: bold_normal 1s linear infinite; /* Apply the animation */  
  color: green; /* Set the text color */  
  font-size: 18px; /* Set the font size */  
  margin-bottom: 10px;
} 

</style>
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">{{ __('journal.form_title') }}
                                <span class="pull-left">
                                    <a href="{{  route('journal.index') }}">
                                        <button class="btn mybtn bg-default"> {{ __('journal.back_to_list') }} </button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="col-md-12 m-t-10">
                            @if(session('notification'))
                                <div class="alert alert-{{ session('notification.type') }}">
                                    {{ session('notification.message') }}
                                </div>
                            @endif
                            </div>
                            <form action="{{ route('journal.store') }}" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="branch_id" value="{{ $branchs->first()->id }}" required >
                            <input type="hidden" name="conversion_flag" id="conversion_flag" value="0" >
                            <input type="hidden" id="default_currency_id" name="default_currency" value="{{ $default_currency->id }}" >
                            <input type="hidden"  name="default_currency_symbol" value="{{ $default_currency->symbols }}">
                            
                                @csrf
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;margin-top:10px;">


                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group form-floating-label">
                                                <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="options" id="options" required 
                                                onchange="selectAccountsLabel(this.value)" > 
                                                    <option value="">  {{ __('journal.transaction_type') }} </option>
                                                    <option value="1">{{ __('journal.cash_transaction') }}</option>
                                                    <option value="2">{{ __('journal.credit_transaction') }}</option>
                                                 </select> 
                                            </div> 
                                        </div>

                                                                                       
                                        <div class="col-md-3">
                                           <div class="form-group">
                                                <input class="form-control" id="bill_no" name="bill_no" type="number"
                                                 placeholder="{{ __('journal.bill_no') }}" >
                                                @error('bill_no')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>


                                        <div class="col-md-3">
                                                <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                                <div class="input-group-append">
                                                <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                                    data-targetselector="#todays_date" data-englishnumber="true">
                                                    <span class="fa fa-calendar"></span> 
                                                </span>
                                                </div>
                                                    <input class="form-control" name="todays_date" id="todays_date" required
                                                    data-targetselector="#todays_date" value="{{ $todaysDate }}" 
                                                    data-mddatetimepicker="true"  placeholder="{{ __('journal.register_date') }}"  data-placement="right" data-englishnumber="true"  >
                                                </div>
                                                @error('todays_date')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <span class="typing-effect" id="from_account_label"></span>
                                                <select class="form-control select2" name="from_account_id" required>
                                                    <option value="">{{ __('journal.payer_account') }}</option>
                                                    @foreach($accounts as $account)
                                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('from_account_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <span class="typing-effect" id="to_account_label"></span>
                                                <select class="form-control select2" name="to_account_id" required>
                                                    <option value="">{{ __('journal.receiver_account') }}</option>
                                                    @foreach($accounts as $account)
                                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('to_account_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                              

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                       <div class="form-group">
                                                            <input class="form-control" id="from_amount" name="from_amount" type="text" required placeholder="{{__('journal.payer_amount')}}" oninput="updateToAmountWithThisValue(this.value)">
                                                            @error('from_amount')<span class="text-danger">{{ $message }}</span>@enderror
                                                        </div> 
                                                    </div>
                                                    
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="form-group form-floating-label">
                                                            <select class="form-control select2" name="from_currency_id" required 
                                                            id="from_currency_id" onchange="currencyConverter()">
                                                                @foreach($currencies as $currency)
                                                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('from_currency_id')<span class="text-danger">{{ $message }}</span>@enderror
                                                        </div> 
                                                    </div>

                                                </div>
                                            </div>


                                       
                                          <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                       <div class="form-group">                                                          <input class="form-control" id="to_amount" name="to_amount" type="text" required placeholder="{{__('journal.receiver_amount')}}">
                                                           @error('to_amount')<span class="text-danger">{{ $message }}</span>@enderror
                                                        </div> 
                                                        <div class="badge badge-info" id="rate"></div>
                                                        <div id="exchange_error" class="error danger"></div>

                                                    </div>
                                                    
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="form-group form-floating-label">
                                                            <select class="form-control select2" name="to_currency_id" required
                                                            id="to_currency_id" onchange="currencyConverter()">
                                                                @foreach($currencies as $currency)
                                                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('to_currency_id')<span class="text-danger">{{ $currency }}</span>@enderror
                                                        </div> 
                                                    </div>

                                                </div>
                                            </div>



                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <input class="form-control" id="from_details" name="from_details" type="text" 
                                                placeholder="{{__('journal.payer_details')}}" required oninput="checkPrevCode()" >
                                                @error('from_details')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <input class="form-control" id="to_details" name="to_details" type="text"  
                                                placeholder="{{__('journal.receiver_details')}}" required>
                                                @error('to_details')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>


                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label>{{__('journal.attachment')}}</label>
                                                <input type="file" class="form-control" name="doc" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx">
                                                @error('doc')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12 m-t-30">
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="submit" id="submit_button" name="submit" value="{{__('journal.submit')}}" class="form-control btn bg-blue">
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('journal.index') }}" class="btn bg-danger">{{__('journal.cancel')}}</a>
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
</div>


<!-- For Persian Date Picker -->
<script src="{{ asset('assets/datepicker/jalaali.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $('#input1').change(function() {  
        var $this = $(this), value = $this.val();  
        alert(value);
    });

    $('#textbox1').change(function () {  
        var $this = $(this), value = $this.val(); 
        alert(value); 
    });

    $('[data-name="disable-button"]').click(function() {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', true);
    });

    $('[data-name="enable-button"]').click(function () {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', false);
    });
</script>

<script>
   function checkPrevCode()
   {
      var options = $('#options').val();
      if(parseInt(options) === 4)
      {
          $('#msg').val('need prev_code');
      }
      else 
      {
        $('#msg').val('');
      }
   }

    function updateToAmountWithThisValue(from_amount)
    {
        const rawAmount = from_amount.replace(/,/g, '');   
        $('#to_amount').val(formatNumberWithCommas(rawAmount));  
   }


   function selectAccountsLabel(paymentType)
   {
    /**
    *  نقد به نقد
    * پرداخ نقد یک ریکارد و دریافت کننده نقد ریکارد دیگر ثبت شود
    */
     if(parseInt(paymentType) === 1) 
     {
         $('#from_account_label').html(@json(__('journal.payer')));
         $('#to_account_label').html(@json(__('journal.reciever')));
     } 
     /**
     *   نسیه به نسیه
     *  یک ریکارد طلب و یک ریکارد قرضدار ثبت گردد
     */
     else if(parseInt(paymentType) === 2) 
     {
        $('#from_account_label').html(@json(__('journal.credit_debtor_label')));
        $('#to_account_label').html(@json(__('journal.credit_creditor_label')));
     }
     /**
     * نقد به نسیه
     * دو ریکارد برای پرداخت کننده ثبت شود که یکی شان از حساب نقده کم شود ویکی شان طلب ثبت گردد
     * یک ریکارد قرضداری دریافت کننده ثبت گرد 
     */
     else if(parseInt(paymentType) === 3) 
     {
        $('#from_account_label').html(@json(__('journal.cash_payer_label')));
        $('#to_account_label').html(@json(__('journal.mixed_receiver_label')));
     }
    
    /**
     * نسیه به نقد
     * باید همین مبلغ در جمع  رسیدگی قرض مشتری علاوه شود تا از قرضه شان کم شود
     * باید همین مبلغ در حساب خزانه جمع شود زیرا نقد دریافت کرده وباید حساب شان افزایش یابد
     */
     else if(parseInt(paymentType) === 4) 
     {
        $('#from_account_label').html(@json(__('journal.reverse_payer_label')));
        $('#to_account_label').html(@json(__('journal.reverse_receiver_label')));
     }
   }
</script>


<script>
    document.getElementById('to_amount').addEventListener('input', function() {
        let to_amount = this.value.replace(/,/g, ''); // Remove existing commas
        to_amount = to_amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
        to_amount = to_amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
        this.value = to_amount;
    });

    // Function to format a number with commas  
    function formatNumberWithCommas(number) {  
        const parts = number.split('.');  
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas for thousands  
        return parts.join('.'); // Join back the integer and decimal parts  
    }  

    function updateToAmountWithThisValue(from_amount)
    {
          // Remove commas before processing to get the raw value  
          const rawAmount = from_amount.replace(/,/g, '');  
        // Format the amount to with commas and set it to the to_amount field  
        $('#to_amount').val(formatNumberWithCommas(rawAmount));  

   }

</script>
<script>
    document.getElementById('from_amount').addEventListener('input', function() {
        let from_amount = this.value.replace(/,/g, ''); // Remove existing commas
        from_amount = from_amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
        from_amount = from_amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
        this.value = from_amount;
    });

    function currencyConverter() 
    {
        let from_currency = parseFloat($('#from_currency_id').val()) || 0;
        let to_currency = parseFloat($('#to_currency_id').val()) || 0;
        let fromAmount = $('#from_amount').val().replace(/,/g, '') || "0";
        if(from_currency > 0 && to_currency > 0)
        {
            if (from_currency !== to_currency) 
            {
                $('#conversion_flag').val(1);
                let formData = {
                    from_currency: from_currency,
                    to_currency: to_currency,
                    fromAmount: fromAmount,
                    _token: $('meta[name="csrf-token"]').attr('content') // Get CSRF token dynamically
                };

                $.ajax({
                    url: '/home/currencyConverter',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',  
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    success: function (result) 
                    {
                        if (result.convertedAmount !== undefined && result.exchangeRate !== undefined) 
                        {
                            // $('#to_amount').val(parseFloat(result.convertedAmount).toFixed(2));
                            $('#to_amount').val(number_format(parseFloat(result.convertedAmount), 2));
                            $('#rate').text(' نرخ ' + result.exchangeRate).toFixed(2);
                        } 
                        else 
                        {
                            alert('Conversion failed. Invalid response.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error: ", error);
                        console.error("Response Text: ", xhr.responseText.error);
                        console.error("Status: ", status);

                        // alert("An error occurred: " + error + "\nDetails: " + xhr.responseText);
                        try {
                                let response = JSON.parse(xhr.responseText);
                                if (response.error) {
                                    $('#exchange_error').text(response.error); // Show error message
                                    alert(response.error); // Optional: Show error as an alert
                                } else {
                                    $('#exchange_error').text("An unknown error occurred.");
                                }
                            } 
                            catch (e) 
                            {
                                $('#exchange_error').text("Failed to parse error response.");
                                console.error("JSON Parse Error: ", e);
                            }

                    }
                });
            }
            else 
            {
                $('#conversion_flag').val(0);
                $('#to_amount').val(fromAmount);
                $('#rate').text('');
                $('#exchange_error').text('');
            }
        }
    }

    function number_format(num, decimals = 2) {
       return num.toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'); 
    }

</script>

@endsection
