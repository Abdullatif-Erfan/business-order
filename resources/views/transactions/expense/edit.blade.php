@extends('layouts.app')
@section('title', 'ویرایش ')
@section('content')
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">ویرایش   
                                <span class="pull-left">
                                    <a href="{{ route('expense.index') }}">
                                        <button class="btn mybtn bg-default"> برگشت به لیست </button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <form action="{{ route('expense.update', $expense->first()->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="id" value="{{ $expense->first()->id }}">
                                              
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;margin-top:10px;">

                                        <!-- Row:1 - Col:1 -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                               <label for=""> انتخاب شعبه </label>
                                                <select class="form-control select2" name="branch_id" required>
                                                    @if ($branchs->count() > 1)
                                                        <option value="">--- انتخاب شعبه ---</option>
                                                    @endif
                                                    @foreach ($branchs as $branch)
                                                        <option value="{{ $branch->id }}" 
                                                            {{ $expense->first()->branch_id == $branch->id ? 'selected' : '' }}>
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('branch_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row:1 - Col:2  -->
                                        <div class="col-md-4">
                                            <label for=""> بل نمبر </label>
                                           <div class="form-group">
                                                <input class="form-control" id="bill_no" name="bill_no" type="number" 
                                                placeholder="بل نمبر" value="{{ old('bill_no', $expense->first()->bill_no) }}">
                                                @error('bill_no')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>

                                        <!-- Row:1 - Col:3  -->
                                        <div class="col-md-4">
                                        <label for=""> تاریخ ثبت </label>
                                            <div class="form-group" data-provide="datepicker">
                                                <input class="form-control" name="todays_date" id="todays_date" required
                                                    value="{{ old('todays_date', $expense->first()->inserted_short_date) }}" 
                                                    data-mddatetimepicker="true" placeholder="تاریخ ثبت" data-englishnumber="true">
                                            </div>
                                            @error('todays_date')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>

                                        <!-- Row:2 - Col:1  -->
                                        <div class="col-md-4">
                                        <label for="">  نوع مصارف</label>
                                            <div class="form-group">
                                                <span class="typing-effect" id="dynamic_type"></span>
                                                <select class="form-control select2" name="dynamic_type" required>
                                                    <option value=""> انتخاب نوع مصارف</option>
                                                    @foreach($expenseTypes as $type)
                                                        <option value="{{ $type->id }}" 
                                                            {{ $expense->first()->dynamic_type == $type->id ? 'selected' : '' }}>
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('dynamic_type')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row:2 - Col:2  -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                        <label for=""> مبلغ </label>
                                            <div class="form-group">
                                                <input class="form-control" id="amount" name="amount" type="number" step="0.01" required 
                                                placeholder="مبلغ " value="{{ old('amount', $expense->first()->amount) }}">
                                                @error('amount')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>

                                        <!-- Row:2 - Col:3  -->
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                        <label for=""> واحد پولی </label>
                                            <div class="form-group form-floating-label">
                                                <select class="form-control select2" name="currency_id" required>
                                                    @foreach($currencies as $currency)
                                                        <option value="{{ $currency->id }}" 
                                                            {{ $expense->first()->currency_id == $currency->id ? 'selected' : '' }}>
                                                            {{ $currency->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('currency_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div> 
                                        </div>

                                        <!-- Row:3 - Col:1  -->
                                        <div class="col-md-6">
                                        <label for=""> تفصیلات </label>
                                            <div class="form-group">
                                                <input class="form-control" id="details" name="details" type="text" 
                                                placeholder="تفصیلات " required value="{{ old('details', $expense->first()->details) }}">
                                                @error('details')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row:3 - Col:2  -->
                                        <div class="col-md-6">
                                        <label for=""> سند </label>
                                            <div class="form-group">
                                                <input type="file" class="form-control" name="doc" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx">
                                                @error('doc')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>

                                        <!-- Row:4 - Col:1  -->
                                        <div class="col-md-6 m-t-30">
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="submit" id="submit_button" name="submit" value="بروزرسانی" class="form-control btn bg-blue">
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('expense.index') }}" class="btn bg-danger">لغو</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div> <!-- /.box-body -->
                    </div> <!-- /.card -->
                </div> <!-- /.col-md-12 -->
            </div> <!-- /.row -->
        </div> <!-- /.page-inner -->
    </div> <!-- /.content -->
</div> <!-- /.main-panel -->

<!-- For Persian Date Picker -->
<script src="{{ asset('assets/datepicker/jalaali.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js') }}" type="text/javascript"></script>
@endsection
