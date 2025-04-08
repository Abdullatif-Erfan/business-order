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


<style>

table.new thead tr th{background-color:#fff !important; color:#000 !important;text-align:center;}
table.my_table thead tr th{background-color:#3f7cc7  !important; color:#fff !important;text-align:center;}
.new tbody tr td{padding: 5px 5px;}
select.select2{text-align:right !important;direction:rtl !important;}

</style>
<style>
    .warehouse-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #ddd;
        transition: transform 0.2s ease-in-out;
        display: flex;
        flex-direction: column;
        height: 100%;
        cursor:pointer;
    }

    .warehouse-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        cursor:pointer;
    }

    .card-image {
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: #f8f8f8;
        cursor:pointer;
    }

    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        cursor:pointer;
    }

    .card-body {
        padding: 5px !important;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor:pointer;
    }
    .card-body .price, .amount {
        font-size:12px;
    }
    .item-title {
        font-size: 11px;
        font-weight: 600;
        color: #0513ad;
        line-height: 14px;
    }

    .w-100 {
        width: 100%;
    }

    .badge-group {
        display: flex;
        flex-direction: row;
        gap: 2px;
        align-items: center;
        justify-content: space-between;
    }

    .badge {
        font-size: 0.85rem;
        padding: 6px 10px;
        border-radius: 4px;
    }
    .bordered-badge {
        padding: 1px 3px;
        font-size: 10px;
        border: 1px solid #4791e2 !important;
        color: #0440d9;
        background: #fff;
        border-radius: 5px;
    }

    /* selected cards */
    .warehouse-selected-card {
        display: flex;
        flex-direction: row;
    }
    .selected-card-image {
        width: 100%;
        aspect-ratio: 4 / 1;
        overflow: hidden;
        cursor:pointer;
    }
    .selected-card-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        cursor:pointer;
        border-radius: 5px;
    }
    .border-bottom {
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }

    .custom-number-list {
    list-style: none;
    padding: 0;
    counter-reset: item;
    width: 100%;
}

.custom-number-list li {
    position: relative;
    margin-bottom: 20px;
}

.circle-number {
    position: absolute;
    top: 36%;
    right: 5px;
    transform: translateY(-50%);
    width: 25px;
    height: 25px;
    line-height: 24px;
    text-align: center;
    background-color: #068bd3;
    color: white;
    border-radius: 50%;
    font-weight: bold;
    font-size: 11px;
}

.total-prices-group {
    display:flex;
    align-items:center;
    justify-content: space-between;
    border-bottom: 1px solid #ddd;
    gap: 3px;
}

</style>



<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">فورم فروشات  
                                <span class="pull-left">
                                    <a href="{{ route('sales.index') }}">
                                        <button class="btn mybtn bg-default"> برگشت به لیست </button>
                                    </a>
                                </span>
                                
                            </h4>
                        </div>

                        <form id="buyingForm" action="{{ route('sales.store') }}" method="POST">
                        @csrf

                        <input type="hidden" tabindex="2" class="form-control" value="{{ $billno }}" name="billno" id="billno">

                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                                <div class="row" style="padding: 10px 20px;">
                                   
                                   <!-- right (list of items) -->
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                         <div class="col-xs-12">
                                            
                                            <div class="row">
                                                    <div class="col-md-8 col-sm-6 col-xs-12" >
                                                        <input type="text" name="search" placeholder="جستجو به اساس نام / بارکد" 
                                                         class="form-control m-t-10" oninput="searchByName()">
                                                    </div>

                                                    <div class="col-md-4 col-sm-6 col-xs-12" style="padding: 10px;">
                                                        <select class="form-control select2 col-12" tabindex="0" style="width: 100%; border:none !important; background-color:#ddd;" name="currency_id" id="currency_id" required>
                                                            @foreach($currencies as $currency)
                                                                <option value="{{ $currency->id }}">  {{ $currency->name }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                            </div>
                                           
                                         </div>

                                         <div class="col-sm-12 col-md-12 col-xs-12 border m-t-10" style="background:#f5f3f3">
                                         <div class="row m-t-20">
                                            @foreach($warehouseItems as $item)
                                                <div class="col-md-3 col-sm-6 mb-4 px-2" onclick="addItem(<?=$item->id?>)">
                                                    <div class="warehouse-card shadow-sm">
                                                        <div class="card-image">
                                                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->item_name }}" >
                                                        </div>
                                                        <div class="card-body">
                                                            <h5 class="item-title">{{ $item->item_name }}</h5>
                                                            <div class="badge-group">
                                                                <span class="amount bordered-badge">{{ $item->available_amount }} {{ $item->unit_name }}</span>
                                                                <span class="price">قیمت: {{ $item->avg_up }}</span>
                                                            </div>
                                                            <center><span class="badge badge-secondary w-100 m-t-10"> در {{ $item->warehouse_name }}</span></center>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                         </div>
                                    </div>
                                  <!-- / right -->

                                  <!-- left (added order items) -->
                                    <div class="col-md-4 col-sm-4 col-xs-12 border">
                                    <div class="row">
                                     <div class="col-md-12 col-sm-12 col-xs-12" style="background-color:#f3f8fa; padding: 10px; border-bottom: 1px solid #dcdcdc;">
                                         <select class="form-control select2 col-12" tabindex="0" style="width: 100%; border:none !important; background-color:#ddd;" name="customer_account_id" id="customer_account_id" required>
                                            <option value=""> انتخاب مشتری </option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">  {{ $customer->name }} </option>
                                            @endforeach
                                        </select>
                                      </div>    
                                    </div>
                                      <div class="row m-t-20">
                                      <ol class="custom-number-list">
                                        @foreach($warehouseItems as $index => $item)
                                            <li>
                                                <div class="col-md-12 col-sm-12 mb-2 px-2 border-bottom position-relative">
                                                    <div class="warehouse-selected-card">
                                                        <div class="selected-card-image col-sm-3 col-md-4 col-xs-12">
                                                            <img src="{{ asset('storage/'. $item->image_path) }}" alt="{{ $item->item_name }}">
                                                        </div>
                                                        <div class="col-sm-9 col-md-8 col-xs-12">
                                                            <h5 class="item-title">{{ $item->item_name }}
                                                            <span class="pull-left"><i class="fa fa-trash danger"></i></span>
                                                            </h5>
                                                            
                                                            <div class="badge-group">
                                                               
                                                                  <input type="number" name="" value="{{ $item->available_amount }}"
                                                                  style="width:50px;">
                                                                  <small class="text-info" style="font-size:10px">{{ $item->unit_name }}</small>

                                                                <span class="price">مجموع: {{ $item->avg_up }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="circle-number">{{ $index + 1 }}</div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ol>


                                       <div class="total col-sm-12 col-xs-12">
                                           <div class="total-prices-group">
                                                <div class="total">قیمت مجموعی:</div>
                                                <div class="price">12313.44</div>
                                           </div>
                                           <div class="total-prices-group">
                                                <div class="total">مفاد:</div>
                                                <div class="price">12313.44</div>
                                           </div>
                                           <div class="total-prices-group">
                                                <div class="total">تخفیف:</div>
                                                <div class="price">12313.44</div>
                                           </div>
                                           <div class="total-prices-group">
                                                <div class="total">قابل پرداخت:</div>
                                                <div class="price">12313.44</div>
                                           </div>
                                           <div class="total-prices-group">
                                                <div class="total">دریافت فعلی:</div>
                                                <div class="price">12313.44</div>
                                           </div>
                                           <div class="total-prices-group m-t-10">
                                                 <button class="btn btn-success btn-sm form-control">ثبت و پرنت</button>
                                                 <button class="btn btn-info btn-sm form-control">ثبت</button>
                                           </div>
                                       </div>


                                        </div>
                                    </div>
                                <!-- /left -->
                            

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
<script>
function searchByName()
{

}
function addItem(item_id)
{
    
}
</script>
@endsection


