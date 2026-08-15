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
                            <a href="{{ route('customer.index') }}" class="pull-left">
                                <button type="button" class="btn btn-sm mybtn">
                                    <i class="fas fa-arrow-left"></i>  
                                </button>
                            </a>
                            <span class="card-title pull-right"> 
                               ویرایش مشتری
                            </span>
                        </div>

                        <div class="card-body">
                            <div class="col-12">
                                <form action="{{ route('customer.update', ['id' => $account->id]) }}" method="POST" id="customerForm">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-xs-12">
                                        <div class="row">

                                            <!-- Hidden ID -->
                                            <input type="hidden" name="id" value="{{ $account->id }}">

                                            <!-- Account Type -->
                                            <div class="form-group col-sm-4">
                                                <label for="account_type_id">{{__('settings.account_type_selection')}}</label>
                                                <select class="form-control" name="account_type_id" id="account_type_id" required>
                                                    @foreach($accountTypes as $accountType)
                                                        <option value="{{ $accountType->id }}" 
                                                            {{ old('account_type_id', $account->account_type_id) == $accountType->id ? 'selected' : '' }}>
                                                            {{ $accountType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span id="accountTypeIdError" class="text-danger"></span>
                                            </div>

                                            <!-- Name -->
                                            <div class="form-group col-sm-4">
                                                <label for="name">{{__('settings.account_name')}}</label>
                                                <input type="text" class="form-control" name="name" value="{{ old('name', $account->name) }}" required>
                                                <span id="accountNameError" class="text-danger"></span>
                                            </div>

                                            <!-- Phone -->
                                            <div class="form-group col-sm-4" id="phoneGroup">
                                                <label for="phone">{{__('common.phone')}}</label>
                                                <input type="text" class="form-control" name="phone" value="{{ old('phone', $account->phone) }}" placeholder="{{__('common.phone')}} ...">
                                                <span id="phoneError" class="text-danger"></span>
                                            </div>

                                            <!-- Address -->
                                            <div class="form-group col-sm-4" id="addressGroup">
                                                <label for="address">{{__('common.address')}}</label>
                                                <input type="text" class="form-control" name="address" value="{{ old('address', $account->address) }}" placeholder="{{__('common.address')}} ...">
                                                <span id="addressError" class="text-danger"></span>
                                            </div>

                                            <!-- Loan Limit -->
                                            <div class="form-group col-sm-4" id="loanLimitGroup">
                                                <label for="loan_limit">{{__('settings.loan_limit')}}</label>
                                                <input type="number" step="any" class="form-control" name="loan_limit" value="{{ old('loan_limit', $account->loan_limit ?? 0) }}">
                                                <span id="loanLimitError" class="text-danger"></span>
                                            </div>

                                            <!-- Loan Limit Option -->
                                            <div class="form-group col-sm-4" id="loanLimitOptionGroup">
                                                <label for="loan_limit_option">{{__('settings.loan_limit_option')}}</label>
                                                <select class="form-control" name="loan_limit_option">
                                                    <option value="0" {{ old('loan_limit_option', $account->loan_limit_option ?? 0) == 0 ? 'selected' : '' }}>{{__('settings.no')}}</option>
                                                    <option value="1" {{ old('loan_limit_option', $account->loan_limit_option ?? 0) == 1 ? 'selected' : '' }}>{{__('settings.yes')}}</option>
                                                </select>
                                                <span id="loanLimitOptionError" class="text-danger"></span>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="col-md-6 m-t-30 m-b-30">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="submit" id="submit_button" name="submit" value="{{__('common.edit')}}" class="form-control btn bg-blue">
                                                    </div>
                                                    <div class="col-6">
                                                        <a href="{{ route('customer.index') }}" class="btn bg-danger" style="width:100%;">
                                                            {{__('common.cancel')}}
                                                        </a>
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