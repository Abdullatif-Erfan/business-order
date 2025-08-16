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
                            <h4 class="card-title"> {{__('wh.old_item_title')}}  
                                 <small class="badge badge-info badge-sm"> <strong class="m-r-10">  
                                 {{__('buy.note')}}   : </strong> {{__('wh.note_desc')}}</small>
                            </h4>
                        </div>

                        <form id="buyingForm" action="{{ route('qalam.store') }}" method="POST">
                        @csrf

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

                                

                                     <input type="hidden" name="branch_id" value="{{$branch_id}}" />

                                     <div class="col-md-4 col-sm-6 col-xs-6">
                                       <label for="">انتخاب مودل</label>
                                        <select class="form-control select2 item-select" name="model_id" style="width:100%;" required>
                                            <option value=""> انتخاب مودل</option>
                                            @foreach($models as $model)
                                                <option value="{{ $model->id }}">
                                                    {{ $model->name }} 
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>	

                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                      <label for="">مقدار تولید</label>
                                       <div class="form-group">
                                            <input class="form-control" id="amount" name="amount" type="number" step="0.01" required
                                             placeholder="مقدار تولید" >
                                            <span id="amountError" class="text-danger"></span>
                                        </div> 
                                    </div>

                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                      <label for="">انتخاب واحد</label>
                                        <select class="form-control select2 item-select" name="unit_id" style="width:100%;" required>
                                            <option value=""> انتخاب واحد</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}">
                                                    {{ $unit->name }} 
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                      <label for="">قیمت فی واحد تمام شده</label>
                                       <div class="form-group">
                                            <input class="form-control" id="price" name="price" type="number" step="0.01" required
                                             placeholder="فیات تمام شده" >
                                            <span id="priceError" class="text-danger"></span>
                                        </div> 
                                    </div>

                                    <div class="col-md-4 col-sm-6 col-xs-6">
                                      <label for="">انتخاب واحد پولی</label>
                                        <select class="form-control select2 item-select" name="currency_id" style="width:100%;" required>
                                            <option value=""> انتخاب واحد پولی </option>
                                            @foreach($currency as $cur)
                                                <option value="{{ $cur->id }}">
                                                    {{ $cur->name }} 
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                     <!-- Add to list button  -->
                                     <div class="col-4 m-t-20">
                                        <div class="col-12" style="margin-top:10px;padding: 5px;">
                                           <button type="submit" class="form-control btn btn-sm btn-info"> 
                                           <i class="fa fa-save"></i>  &nbsp; {{__('common.save')}}  </button>
                                        </div>
                                     </div>
                                    <!-- /  Add to list button  -->
                                   
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
@endpush


<script>
@endsection


