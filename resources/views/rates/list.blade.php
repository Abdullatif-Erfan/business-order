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
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 mt-3">
                    <div class="card">
                        <div class="card-header" style="padding:10px">
                            <h4 class="card-title">   نرخ ارزها
                            </h4>
                        </div>

                        <div class="card-body">
                            
                    <!-- panel -->
                    <div class="col-md-12"  id="print_area">
                        <div class="panel-group" id="accordion">
                            <div class="col-xs-12">
                                <div class="row">

                                  @include('rates.add')
                                    <div class="table-responsive" style="padding:5px;">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>شماره</th>
                                                    <th>از</th>
                                                    <th>معادل</th>
                                                    <!-- <th width="30%">عکس آن</th> -->
                                                    <th>ویرایش</th>
                                                    <th>حذف</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($rates as $key => $rate)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <label class="label success-label">1</label> {{ $rate->fromCurrency->name ?? 'Unknown' }}
                                                        </td>
                                                        <td>
                                                            <label class="label success-label">{{ $rate->to_currency_amount }}</label>
                                                            {{ $rate->toCurrency->name ?? 'Unknown' }}
                                                        </td>
                                                        <!-- <td>
                                                            <label class="label success-label">1</label>
                                                            {{ $rate->toCurrency->name ?? 'Unknown' }}
                                                            <label class="label success-label">{{ $rate->reverse_amount }}</label>
                                                            {{ $rate->fromCurrency->name ?? 'Unknown' }}
                                                        </td> -->
                                                        <td>
                                                            <a href="#" data-toggle="modal" data-target="#editModal{{ $rate->id }}">
                                                                <i class="fas fa-pen-square" style="font-size:20px;"></i>
                                                            </a>
                                                            
                                                            <!-- Edit Modal -->
                                                            <div id="editModal{{ $rate->id }}" class="modal fade" tabindex="-1" role="dialog">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-blue3">
                                                                            <button type="button" class="close" data-dismiss="modal">×</button>
                                                                            <h5 class="modal-title">ویرایش</h5>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="{{ route('rate.update') }}" method="POST">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <input type="hidden" name="id" value="{{ $rate->id }}">
                                                                                <div class="form-group">
                                                                                    <select class="form-control" name="from_currency_id" required>
                                                                                        <option value="{{ $rate->from_currency_id }}"> یک {{ $rate->fromCurrency->name ?? 'Unknown' }}</option>
                                                                                        @foreach($currencies as $currency)
                                                                                            <option value="{{ $currency->id }}">یک {{ $currency->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <select class="form-control" name="to_currency_id" required>
                                                                                        <option value="{{ $rate->to_currency_id }}"> معادل به {{ $rate->toCurrency->name ?? 'Unknown' }}</option>
                                                                                        @foreach($currencies as $currency)
                                                                                            <option value="{{ $currency->id }}">معادل به {{ $currency->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <input class="form-control" id="to_currency_amount{{ $rate->id }}" name="to_currency_amount" 
                                                                                    type="number" step="0.00001" onkeyup="calculateReverseAmountEdit({{ $rate->id }})" required value="{{ $rate->to_currency_amount }}">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <input class="form-control" id="reverse_amount{{ $rate->id }}" name="reverse_amount" type="hidden" required value="{{ $rate->reverse_amount }}">
                                                                                </div>
                                                                                <!-- <div class="form-group">
                                                                                    <select class="form-control" onchange="changePointsEdit(this.value, {{ $rate->id }})">
                                                                                        <option value="">تعداد خانه اعشاریه</option>
                                                                                        @for($i = 10; $i >= 1; $i--)
                                                                                            <option value="{{ $i }}">{{ $i }} خانه</option>
                                                                                        @endfor
                                                                                    </select>
                                                                                </div> -->
                                                                                <div class="modal-footer">
                                                                                    <button type="submit" class="btn btn-info btn-sm m-l-10">
                                                                                        <span class="btn-label"><i class="fa fa-save"></i></span> ثبت
                                                                                    </button>
                                                                                    <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">لغو</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if(auth()->user()->isAdmin)
                                                                <form action="{{ route('rate.destroy', $rate->id) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn outline btn-sm">
                                                                        <i class="fas fa-trash-alt" style="font-size:20px;color:red;"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>


                                </div>
                            </div>
                         </div>
                      </div>
                    </div> <!-- End card-body -->
                    </div> <!-- End main card -->
                </div>
            </div>
        </div>
    </div>
</div>

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
    function calculateReverseAmountEdit(id) {
        var toCurrencyAmount = parseFloat(document.getElementById('to_currency_amount' + id).value);
        var result = (1 / toCurrencyAmount).toFixed(10);
        document.getElementById('reverse_amount' + id).value = result;
    }

    function changePointsEdit(value, id) {
        var toCurrencyAmount = parseFloat(document.getElementById('to_currency_amount' + id).value);
        var result = (1 / toCurrencyAmount).toFixed(parseFloat(value));
        document.getElementById('reverse_amount' + id).value = result;
    }
</script>
@endsection
