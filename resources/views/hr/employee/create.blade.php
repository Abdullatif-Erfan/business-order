@extends('layouts.app')

@section('content')

@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
    // Show the notification using the data from the session
    $(document).ready(function(){
        showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
    });
</script>
@endif


<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px; text-align:center;">
                            <a href="{{ route('employee.index') }}" class="pull-left">
                                <button type="button" class="btn btn-sm mybtn">
                                    <i class="fas fa-arrow-left"></i>  
                                </button>
                            </a>
                            <span class="card-title pull-right">  فورم ایجاد کارمند جدید : <strong>همزمان حساب نیز ساخته میشود</strong> </span>
                        </div>


                        <div class="card-body">
                          <div class="col-12">
                          <form action="{{ route('employee.store') }}" method="POST">
                            @csrf
                                <div class="col-xs-12">
                                    <div class="row">

                                        <div class="form-group col-sm-4">
                                            <label for="name"> نام حساب  </label>
                                            <input type="text" class="form-control" name="name" required placeholder="نام حساب را بنویسید">
                                            <span id="accountNameError" class="text-danger"></span>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label for="phone"> شماره تماس </label>
                                            <input type="text" class="form-control" name="phone"  placeholder="شماره تماس ...">
                                            <span id="phoneError" class="text-danger"></span>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label for="address"> آدرس </label>
                                            <input type="text" class="form-control" name="address"  placeholder=" آدرس ...">
                                            <span id="addressError" class="text-danger"></span>
                                        </div>


                                            <div class="form-group col-sm-4" id="net_salary" >
                                                <label for="percent"> معاش خالص ماهانه </label>
                                                <input type="number" class="form-control" name="net_salary"  required placeholder="معاش خالص ماهانه را بنویسید">
                                                <span id="netSalaryError" class="text-danger"></span>
                                            </div>

                                            <div class="form-group col-sm-4" id="salary_currency" >
                                                <label for="percent">  پرداخت معاش به کرنسی </label>
                                                <select class="form-control" name="salary_currency" required>
                                                    <option value=""> انتخاب کرنسی </option>
                                                    @foreach($currencies as $currency)
                                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>


                                        @if(count($branchs) >= 2)
                                        <div class="form-group col-sm-4">
                                            <label for="account_type_id"> انتخاب شعبه </label>
                                            <select class="form-control"  name="branch_id" required>
                                                <option value="">انتخاب  شعبه</option>
                                                @foreach($branchs as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                            <span id="branchError" class="text-danger"></span>
                                        </div>
                                        @elseif(count($branchs) == 1) 
                                            <input type="hidden" value="{{ $branchs[0]->id }}" name="branch_id" required>
                                        @endif

                            
                                        <div class="col-md-6 m-t-30 m-b-30">
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="submit" id="submit_button" name="submit" value="ثبت" class="form-control btn bg-blue">
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('employee.index') }}" class="btn bg-danger">لغو</a>
                                                </div>
                                            </div>
                                        </div>
                                        

                                    </div>
                                </div>

                                
                            </form>
                            </div>
                        </div> <!-- /card-body -->
                    </div> <!-- /card -->
                </div> <!-- /col-md-12 -->
            </div> <!-- /row -->
        </div> <!-- /page-inner -->
    </div> <!-- /content -->
</div> <!-- /main content -->


@endsection

