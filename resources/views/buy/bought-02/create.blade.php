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

                        <form action="{{ route('boughtList.store') }}" method="POST" >
                        @csrf
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                                <div class="row" style="padding: 10px 20px;">

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
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="customer_account_id">انتخاب فروشنده</label>
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

                                            <div class="col-md-4">
                                                <label for="todays_date">تاریخ</label>
                                                <div class="input-group mb-3" style="margin-top:10px" data-provide="datepicker">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click" data-targetselector="#exampleInput00" data-englishnumber="true">
                                                            <span class="fa fa-calendar"></span> 
                                                        </span>
                                                    </div>
                                                    <input class="form-control" name="todays_date" id="exampleInput00" value="{{ $todaysDate }}" required data-mddatetimepicker="true" placeholder="تاریخ ثبت" data-placement="right" data-englishnumber="true">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="billno">نمبر بل</label>
                                                <div class="form-group">
                                                    <input type="number" class="form-control" name="billno" placeholder="نمبر بل" required>
                                                </div>
                                                <span id="successMsg" style="display:none"><div style="color:green">تایید است</div></span>
                                                <span id="failurMsg" style="display:none"><div style="color:red"> بل نمبر تکراری است</div></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second Row -->
                                    <hr />
                                    <div class="col-md-12 m-t-20">
                                        <div class="row">
                                            @include('buy.bought.dynamic_form')
                                        </div>
                                    </div>

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

@endpush


@endsection


