@extends('layouts.app')

@section('content')


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
                            <span class="card-title pull-right"> 
                               {{__('hr.emp_create_title')}} : <strong>{{__('hr.emp_create_title_note')}}</strong>
                            </span>
                        </div>


                        <div class="card-body">
                          <div class="col-12">
                          <form action="{{ route('employee.store') }}" method="POST">
                            @csrf
                                <div class="col-xs-12">
                                    <div class="row">

                                        <div class="form-group col-sm-4">
                                            <label for="name">  {{__('settings.account')}} {{__('common.name')}}  </label>
                                            <input type="text" class="form-control" name="name" required>
                                            <span id="accountNameError" class="text-danger"></span>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label for="phone"> {{__('common.phone')}} </label>
                                            <input type="text" class="form-control" name="phone"  
                                            placeholder="{{__('common.phone')}} ...">
                                            <span id="phoneError" class="text-danger"></span>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label for="address"> {{__('common.address')}} </label>
                                            <input type="text" class="form-control" name="address"  placeholder=" {{__('common.address')}} ...">
                                            <span id="addressError" class="text-danger"></span>
                                        </div>


                                            <div class="form-group col-sm-4" id="net_salary" >
                                                <label for="percent"> {{__('hr.net_salary')}} </label>
                                                <input type="number" class="form-control" name="net_salary"  required >
                                                <span id="netSalaryError" class="text-danger"></span>
                                            </div>

                                            <div class="form-group col-sm-4" id="salary_currency" >
                                                <label for="percent">  {{__('hr.salary_currency')}} </label>
                                                <select class="form-control" name="salary_currency" required>
                                                    <!-- <option value=""> {{__('common.currency')}} </option> -->
                                                    @foreach($currencies as $currency)
                                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                             <div class="form-group col-sm-4" id="emp_car_id" >
                                                <label for="car">  {{__('settings.car')}} </label>
                                                <select class="form-control" name="emp_car_id" required>
                                                    <option value=""> --- {{__('settings.car_selection')}} --- </option>
                                                    <!-- <option value=""> {{__('common.currency')}} </option> -->
                                                    @foreach($cars as $car)
                                                    <option value="{{ $car->id }}">{{ $car->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                             <div class="form-group col-sm-4">
                                                <div class="filter-group" style="min-width: 120px;">
                                                    <label for="car">  {{__('common.start_date')}} </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control datepicker-input" name="emp_start_date"  placeholder="{{__('common.start_date')}}" required>
                                                        <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                            
                                        <div class="col-md-6 m-t-30 m-b-30">
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="submit" id="submit_button" name="submit" value="{{__('common.save')}}" class="form-control btn bg-blue">
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('employee.index') }}" class="btn bg-danger">
                                                    {{__('common.cancel')}}</a>
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

<script>
$(document).ready(function () {
       // Initialize datepicker
    $('.datepicker-input').datepicker({
        format: 'yyyy-mm-dd', // Match your database format
        autoclose: true,
        todayHighlight: true,
        clearBtn: true
    });
});
$(document).on('click', '.datepicker-icon', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $input = $(this).closest('.input-group').find('input');
    if ($input.length) {
        $input.datepicker('show');
    }
});
</script>
@endsection

