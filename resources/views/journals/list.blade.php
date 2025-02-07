@extends('layouts.app')
@section('title', 'روزنامچه')

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





<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                        <div class="card-header" style="padding: 11px 20px !important;">
                            
                            @if(auth()->user()->hasAccess('journal','create_records'))
                                <a href="{{ route('journal.create') }}">
                                    <button type="button" class="btn btn-sm mybtn">
                                        <i class="fas fa-plus"></i> ثبت ژورنال جدید
                                    </button>
                                </a>
                            @else
                                <button type="button" onclick="alert('صلاحیت ندارید')" class="btn btn-sm mybtn">
                                    <i class="fas fa-plus"></i> ثبت جدید
                                </button>
                            @endif

                            <button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button>

                            <button type="button" class="btn btn-sm mybtn visible-xs" onclick="show_search_form(1)">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>

                        {{-- Filter Form --}}
                        <div class="filterForm" id="searchWrapper1">  
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <select class="form-control select2" id="account_id">
                                            <option value=""> حساب </option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control select2" id="currency_id">
                                            <option value=""> واحد پولی </option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select> 
                                    </div>

                                    
                                    <div class="col-md-2">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#start_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="start_date" id="start_date"
                                            data-targetselector="#start_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ شروع"  data-placement="right" data-englishnumber="true"  >
                                        </div>
							     	</div>
                                


                                     <div class="col-md-3">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#end_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="end_date" id="end_date"
                                            data-targetselector="#end_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ ختم / الی امروز"  data-placement="right" data-englishnumber="true" >
                                        </div>
							     	</div>

                                  

                                    <div class="col-md-1">
                                        <input class="form-control" id="code_number" placeholder="کد">
                                    </div>

                                    <div class="col-md-1">
                                        <input class="form-control" id="bill_number" placeholder="بل">
                                    </div>

                                    <div class="col-md-1">
                                        <button class="btn mybtn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div> 
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body">
                            <div class="table-responsive" id="print_area">
                                <span class="pull-left visible-print">تاریخ چاپ: {{ now()->format('Y-m-d') }}</span>
                                <table id="journalTable" class="display responsive nowrap table table-bordered" width="100%">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="9">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="9">
                                                <center>
                                                    روزنامچه   
                                                </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> شماره </th>
                                            <th> کد </th>
                                            <th> حساب </th>
                                            <th> جزییات </th>
                                            <th> بردگی (آمد)  </th>
                                            <th> رسیدگی (رفت)  </th>
                                            <th>واحد</th>
                                            <th>تاریخ</th>
                                            <th>جزییات</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="4">مجموع</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div> 
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
function showNotification(message, type = 'info', from = 'top', align = 'left', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> پیام </span>';
    
    if (style === "withicon") {
        content.icon = 'fa fa-bell';
    } else {
        content.icon = 'none';
    }
    content.url = '#';
    content.target = '_blank';

    $.notify(content, {
        type: type, // Default, Primary, Secondary, Info, Success, Warning, Danger
        placement: {
            from: from, // top, bottom
            align: align // right, center, left
        },
        time: 500
    });
}
</script>

<script>
    $(document).ready(function() {
        let table = $('#journalTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('journal.data') }}',
                data: function (d) {
                    d.account_id = $('#account_id').val();
                    d.currency_id = $('#currency_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.code_number = $('#code_number').val();
                    d.bill_number = $('#bill_number').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'code', name: 'code' },
                { data: 'accountRelation', name: 'accountRelation' },
                { data: 'details', name: 'details' },
                { data: 'transaction_type_1', name: 'transaction_type_1' },
                { data: 'transaction_type_2', name: 'transaction_type_2' },
                { data: 'currency', name: 'currency' },
                { data: 'inserted_short_date', name: 'inserted_short_date' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });

        // When the filter button is clicked, refresh the table
        $('#btn-filter').click(function() {
            table.draw(); // Refresh DataTable with new filters
        });
    });

    function viewDetails(id) {
        alert("جزییات برای آی دی " + id);
    }
</script>


@endsection('content')

