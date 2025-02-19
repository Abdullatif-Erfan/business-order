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
    /* When input fields are readonly or disabled, make them white */
    input[readonly], input:disabled {
        background-color: white !important; /* Set background color to white */
        color: black !important; /* Set text color to black */
        border: 1px solid #ccc; /* Optional: Add a border to match non-disabled fields */
    }

    .form-control:disabled, .form-control[readonly], select:disabled
    {
        background: #ffffff !important;
        border-color: #ffffff !important;
    }
    td {
        padding: 5px 10px !important; /* Adds 10px padding to all td elements */
    }
    input[type="text"], input[type="number"] {
    border-bottom: 2px solid #1b96cd;
}
</style>

<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px; text-align:center;">
                            
                            <span class="card-title pull-right">    جزییات  {{ $warehouseItems->first()->preListRelation->name ?? ''}} در  {{ $warehouse->name }} </span>

                            <!-- <button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button> -->
                            <a href="{{ route('warehousesList.index') }}?id={{ $warehouseItems->first()->warehouse_id }}">
                               <button class="btn btn-default btn-sm pull-left">برگشت به لست</button>
                            </a>
                        </div>


                        <div class="card-body">

                        <div class="col-md-12">
                                @if ($errors->any())
                                <div class="col-md-12 m-t-10">
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


                        <form id="editWarehouseForm" action="{{ route('warehousesList.update') }}" method="POST">
                        <input type="hidden" name="id" id="whid" value={{ $warehouseItems->first()->warehouse_id }} >
                        <input type="hidden"  id="deleteId" name="updateId" value={{ $warehouseItems->first()->id }} >

                        @csrf
                        @method('PATCH') 
                        <div class="table-responsive" id="print_area" style="padding:5px;">
                            <span class="pull-left visible-print">تاریخ چاپ : {{ $todaysDate }}</span>
                            <table id="warehouseTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <tr>
                                    <th>نام جنس</th>
                                    <td><input type="text" id="name" name="name" class="form-control" value="{{ $warehouseItems->first()->preListRelation->name ?? '' }}" readonly disabled></td>
                                    <th>واحد جنس</th>
                                    <td>
                                       <select class="form-control" style="width: 100%; border:none !important;   background-color:#ddd;" aria-hidden="true" id="unit_id" name="unit_id" readonly>
                                            <!-- <option value="">  واحد پولی </option> -->
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ $unit->id  == $warehouseItems->first()->unit_id ? 'selected': ''}} >{{ $unit->name }}</option>
                                            @endforeach
                                        </select>

                                    </td>
                                </tr>
                                <tr>
                                    <th>واحد پولی</th>
                                    <td>
                                        <select class="form-control" style="width: 100%; border:none !important;   background-color:#ddd;" aria-hidden="true" id="currency_id" name="currency_id" readonly>
                                            <!-- <option value="">  واحد پولی </option> -->
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}" {{ $currency->id  == $warehouseItems->first()->currency_id ? 'selected': ''}} >{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <th>مقدار موجود</th>
                                    <td><input type="number" step="0.01" id="available_amount" name="available_amount" class="form-control" value="{{ $warehouseItems->first()->available_amount ?? '' }}" readonly></td>
                                </tr>
                                <tr>
                                    <th> مقدار ورود</th>
                                    <td><input type="number"  step="0.01" id="in_amount" name="in_amount" class="form-control" value="{{ $warehouseItems->first()->in_amount ?? '' }}" readonly></td>
                                    <th> مقدار خروج</th>
                                    <td><input type="number"  step="0.01" id="out_amount" name="out_amount" class="form-control" value="{{ $warehouseItems->first()->out_amount ?? '' }}" readonly></td>
                                </tr>

                                <tr>
                                    <th> خرید آخر فی واحد</th>
                                    <td><input type="number"  step="0.01" id="bought_up" name="bought_up" class="form-control" value="{{ $warehouseItems->first()->bought_up ?? '' }}" readonly></td>
                                    <th> قیمت فروش فی واحد</th>
                                    <td><input type="number" id="sell_up"  step="0.01" name="sell_up" class="form-control" value="{{ $warehouseItems->first()->sell_up ?? '' }}" readonly></td>
                                </tr>
                                
                                <tr>
                                    <th> نرخ اوسط فی واحد</th>
                                    <td><input type="number" id="avg_up"  step="0.01" name="avg_up" class="form-control" value="{{ $warehouseItems->first()->avg_up ?? '' }}" readonly oninput="updateAvailAbleTotal(this.value)"></td>
                                    <th> مجموع ارزش جنس موجود</th>
                                    <td><input type="number"  step="0.01" id="available_total" name="available_total" class="form-control" value="{{ $warehouseItems->first()->available_total ?? '' }}" readonly></td>
                                </tr>
                                
                                <tr>
                                    <th> مقدار هشدار</th>
                                    <td><input type="number" id="notification_amount" name="notification_amount" class="form-control"  value="{{ $warehouseItems->first()->notification_amount ?? '' }}" readonly></td>
                                    <th> تاریخ انقضا</th>
                                    <td>
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                            <input class="form-control" name="expire_date" id="expire_date"
                                            data-targetselector="#expire_date" value="{{ $warehouseItems->first()->expire_date ?? '' }}"  readonly data-mddatetimepicker="true"  placeholder="تاریخ انقضا "  data-placement="right" data-englishnumber="true" >
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                     <th>ضایعات</th>
                                    <td><input type="number" id="wastage_amount" name="wastage_amount" class="form-control" value="{{ $warehouseItems->first()->wastage_amount ?? '' }}" readonly></td>
                                    <th>قیمت ضایعات</th>
                                    <td><input type="number" step="0.01" id="wastage_total" name="wastage_total" class="form-control" value="{{ $warehouseItems->first()->wastage_total ?? '' }}" readonly></td>
                                </tr>

                                <tr>
                                    <th>آخرین تاریخ ثبت / ویرایش</th>
                                    <td>
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                            <input class="form-control" name="inserted_short_date" id="inserted_short_date"
                                            data-targetselector="#inserted_short_date" value="{{ $warehouseItems->first()->inserted_short_date ?? '' }}"  readonly
                                            data-mddatetimepicker="true"  placeholder="تاریخ ختم / الی امروز"  data-placement="right" data-englishnumber="true" >
                                        </div>
                                    </td>
                                    <th>ثبت توسط</th>
                                    <td>{{ $warehouseItems->first()->inserted_by ?? '' }}</td>
                                </tr>

                            </table>
                        </div>

                        <hr>

                        <div class="col-md-12">
                            <!-- print button -->
                            <button type="button" onclick="transferItems()" class="btn btn-success btn-sm btn-border m-r-10 hidden-print">
                                <i class="fas fa-exchange-alt"></i> انتقال
                            </button>

                            <!-- Edit button -->
                            <button type="button" onclick="toggleEdit()" class="btn btn-primary btn-sm m-r-10" id="editBtn">
                                <i class="fas fa-pen"></i> ویرایش
                            </button>

                            <!-- Save button -->
                            <button type="submit" class="btn btn-success btn-sm m-r-10" id="saveBtn" style="display: none;">
                                <i class="fas fa-save"></i> ذخیره تغییرات
                            </button>

                            <button type="button" onclick="deleteAnItem()" class="btn btn-danger btn-sm m-r-10" id="editBtn">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </div>
                        </form>
                        </div> <!-- /card-body -->
                    </div> <!-- /card -->
                </div> <!-- /col-md-12 -->
            </div> <!-- /row -->
        </div> <!-- /page-inner -->
    </div> <!-- /content -->
</div> <!-- /main content -->

<div class="modal fade" id="transferModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:800px !important">
            <form action="{{ route('warehousesList.updateTransfer')}}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"> انتقال از گدام به گدام </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="ModalContent"></div>
                <div id="loading" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin font-20"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="submitTransfer" >ثبت</button>
            </div>
            </form>
        </div>
    </div>
</div>


@include('warehouseitem.scripts')

<!-- JavaScript for Edit Mode -->

<script>

    function transferItems()
    {
        var id = $('#whid').val();
        $('#transferModal').modal('show');   
        $('#loading').show();
        $.ajax({
            url: `/warehousesList/getWarehouseItemForTransfer/${id}`,
            type: 'GET',
            success: (result) => {
                $('#ModalContent').html(result);
                $('#loading').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    }

    // Function to toggle between edit and view modes
    function toggleEdit() {
        var isReadonly = document.querySelector('#name').hasAttribute('readonly');
        
        // Toggle readonly on all input fields
        document.querySelectorAll('input').forEach(function(input) {
            if (isReadonly) {
                input.removeAttribute('readonly');
            } else {
                input.setAttribute('readonly', 'true');
            }
        });

        // Toggle visibility of Edit and Save buttons
        // document.getElementById('editBtn').style.display = isReadonly ? 'none' : 'inline-block';
        document.getElementById('saveBtn').style.display = isReadonly ? 'inline-block' : 'none';
    }

    function updateAvailAbleTotal(new_avg_price) 
    {
        var available_amount = parseFloat($('#available_amount').val()) || 0; // Get value and handle NaN
        var avg_price = parseFloat(new_avg_price) || 0; // Ensure new_avg_price is a valid number
        var result = avg_price * available_amount;

        $('#available_total').val(result.toFixed(2)); // Ensure result is formatted correctly
    }



 //  delete an item
 function deleteAnItem()
 {
    var whid = $('#whid').val();
    var deleteId = $('#deleteId').val();

    if (deleteId && confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/warehousesList/delete/${deleteId}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => 
                {
                    if (response.status === 'success') 
                    {
                        showNotification(response.message, 'success', 'top', 'right', 'withicon');
                
                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = `/warehousesList?id=${whid}`;
                        }, 1000);
                    } 
                    else 
                    {
                        showNotification(response.message , 'danger', 'top', 'right', 'withicon');
                    }
                },
                error: () => {
                    showNotification('حذف نگردید', 'danger', 'top', 'right', 'withicon');
                }
            });
        }
 }

</script>

@endsection

