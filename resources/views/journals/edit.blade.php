@extends('layouts.app')
@section('title', 'روزنامچه')
@section('content')
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">فورم ثبت ژورنال ویا روزنامچه
                                <span class="pull-left">
                                    <a href="{{  route('journal.index') }}">
                                        <button class="btn mybtn bg-default"> برگشت به لیست </button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <form action="{{ route('journal.update') }}" method="POST" enctype="multipart/form-data">
                               <input type="hidden" name="times" value="{{ $journals[0]->times }}">
                                @csrf
                                @method('PATCH') 
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;margin-top:10px;">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <select class="form-control select2" name="branch_id" required>
                                                    @if ($branchs->count() > 1)
                                                       <option value="{{ $journals[0]->branch_id }}">{{ $journals[0]->branchRelation->name }}</option>
                                                        <option value="">--- انتخاب شعبه ---</option>
                                                    @endif
                                                    @foreach ($branchs as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('branch_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>


                                        <div class="col-md-4">
                                           <div class="form-group">
                                                <input class="form-control" id="bill_no" name="bill_no" type="number" placeholder="بل نمبر" value="{{ $journals[0]->bill_no > 0 ? $journals[0]->bill_no : ''  }}" >
                                                @error('bill_no')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>


                                        <div class="col-md-4">
                                                <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                                <div class="input-group-append">
                                                <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                                    data-targetselector="#todays_date" data-englishnumber="true">
                                                    <span class="fa fa-calendar"></span> 
                                                </span>
                                                </div>
                                                    <input class="form-control" name="todays_date" id="todays_date" required
                                                    data-targetselector="#todays_date"  value="{{ $journals[0]->inserted_short_date }}" 
                                                    data-mddatetimepicker="true"  placeholder="تاریخ ثبت"  data-placement="right" data-englishnumber="true"  >
                                                </div>
                                                @error('todays_date')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <span class="typing-effect" id="from_account_label"></span>
                                                <select class="form-control select2" name="from_account_id" required>
                                                <option value="{{ $journals[0]->account_id }}">{{ $journals[0]->accountRelation->name }}</option>
                                                    <option value="">حساب پرداخت کننده</option>
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
                                                <option value="{{ $journals[1]->account_id }}">{{ $journals[1]->accountRelation->name }}</option>
                                                    <option value="">حساب دریافت کننده</option>
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
                                                            <input class="form-control" id="from_amount" name="from_amount" type="text" required placeholder="مبلغ پرداخت کننده" oninput="updateToAmountWithThisValue(this.value)" value="{{ $journals[0]->amount }}">
                                                            @error('from_amount')<span class="text-danger">{{ $message }}</span>@enderror
                                                        </div> 
                                                    </div>
                                                    
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="form-group form-floating-label">
                                                            <select class="form-control select2" name="from_currency_id" required>
                                                            <option value="{{ $journals[0]->currency_id }}">{{ $journals[0]->currencyRelation->name }}</option>
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
                                                       <div class="form-group">
                                                          <input class="form-control" id="to_amount" name="to_amount" type="text" required placeholder="مبلغ دریافت کننده" value="{{ $journals[1]->amount }}">
                                                           @error('to_amount')<span class="text-danger">{{ $message }}</span>@enderror
                                                        </div> 
                                                    </div>
                                                    
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="form-group form-floating-label">
                                                            <select class="form-control select2" name="to_currency_id" required>
                                                            <option value="{{ $journals[1]->currency_id }}">{{ $journals[1]->currencyRelation->name }}</option>
                                                                @foreach($currencies as $currency)
                                                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('to_currency_id')<span class="text-danger">{{ $currency }}</span>@enderror
                                                        </div> 
                                                    </div>

                                                </div>
                                            </div>



                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input class="form-control" id="from_details" name="from_details" type="text" placeholder="تفصیلات دریافت کننده" required value="{{ $journals[0]->details }}">
                                                @error('from_details')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input class="form-control" id="to_details" name="to_details" type="text" placeholder="تفصیلات  پرداخت کننده" required value="{{ $journals[1]->details }}">
                                                @error('to_details')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>اسناد</label>
                                                <input type="file" class="form-control" name="doc" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx">
                                                @error('doc')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 m-t-30">
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="submit" id="submit_button" name="submit" value="ثبت" class="form-control btn bg-blue">
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('journal.index') }}" class="btn bg-danger">لغو</a>
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
</script>

@endsection
