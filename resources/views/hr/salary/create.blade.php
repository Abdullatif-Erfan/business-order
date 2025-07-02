@extends('layouts.app')
@section('title', __('hr.salary'))
@section('content')
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">   
                            {{ __('hr.create_salary_form_title')}}
                                <span class="pull-left">
                                    <a href="{{  route('salary.index') }}">
                                        <button class="btn mybtn bg-default">{{ __('common.back')}}</button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <form action="{{ route('salary.store') }}" method="POST">
                                @csrf

                                <input type="hidden" name="from_account_id" value="{{ $ownBanks->first()->id }}">
                                <input type="hidden" name="from_account_name" value="{{ $ownBanks->first()->name }}">
                                              
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;margin-top:10px;">

                                        <!-- Row:1 - Col:1 -->
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                               <label for=""> {{__('journal.branch_selection')}} </label>
                                                <select class="form-control select2" name="branch_id" required>
                                                    @if ($branchs->count() > 1)
                                                        <option value="">--- {{__('journal.branch_selection')}} ---</option>
                                                    @endif
                                                    @foreach ($branchs as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('branch_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>


                                        <!-- Row:1 - Col:3  -->
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label for=""> {{__('common.save_date')}} </label>
                                            <div class="form-group" data-provide="datepicker">
                                                <input class="form-control" name="todays_date" id="todays_date" required
                                                data-targetselector="#todays_date" value="{{ $todaysDate }}" 
                                                data-mddatetimepicker="true"  placeholder="{{__('common.save_date')}}" 
                                                data-placement="right" data-englishnumber="true"  >
                                            </div>
                                            @error('todays_date')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>


                                        <!-- Row:2 - Col:1  -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                            <label for=""> {{__('hr.emp_selection')}} </label>
                                                <span class="typing-effect" id="to_account_id"></span>
                                                <select class="form-control select2" name="to_account_id" required>
                                                    <option value=""> {{__('hr.emp_selection')}}</option>
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('to_account_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>


                                        <!-- Row:2 - Col:2  -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                            <label for=""> {{__('hr.payable_amount')}} </label>
                                                <input class="form-control" id="amount" name="amount" type="number" step="0.01" required placeholder="{{__('hr.payable_amount')}}">
                                                @error('amount')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>


                                        <!-- Row:2 - Col:3  -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group form-floating-label">
                                            <label for=""> {{__('common.currency')}} </label>
                                                <select class="form-control select2" name="currency_id" required>
                                                    @foreach($currencies as $currency)
                                                        <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('currency_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>

                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                            <label for=""> {{__('common.year')}}  </label>
                                                <span class="typing-effect" id="year"></span>
                                                <select class="form-control select2" name="year" required>
                                                    <option value=""> {{__('common.year')}}</option>
                                                    @for($i=1400; $i<=1440; $i++)
                                                        <option value="{{ $i }}" {{ $i == $cur_year ? 'selected' : ''}} >{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                @error('year')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                            <label for=""> {{__('common.month')}} </label>
                                                <span class="typing-effect" id="month"></span>
                                                <select class="form-control select2" name="month" required>
                                                    <option value="">{{__('common.month')}}</option>
                                                    @foreach($months as $key => $month)
                                                        <option value="{{ $key }}" {{ $key == $cur_month ? 'selected' : ''}}>{{ $month }}</option>
                                                    @endforeach
                                                </select>
                                                @error('month')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row:3 - Col:1  -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                            <label for=""> {{__('common.details')}} </label>
                                                <input class="form-control" id="details" name="details" type="text" 
                                                placeholder="{{__('common.details')}}" >
                                                @error('details')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>


                                        <!-- Row:4 - Col:1  -->
                                        <div class="col-md-6 col-sm-6 col-xs-12 m-t-30">
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="submit" id="submit_button" name="submit" value="{{__('common.save')}}" class="form-control btn bg-blue">
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('salary.index') }}" class="btn bg-danger">{{__('common.cancel')}}</a>
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
@endsection
