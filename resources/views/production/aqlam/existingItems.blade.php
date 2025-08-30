

@if(count($oldRecords) > 0)
<form id="aqlamForm" action="{{ route('modelDetails.update') }}" method="POST">
    @csrf
    @method('PATCH')
    <input type="hidden" name="model_id" value="{{ $modelId }}" />

    <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
        <div class="form-body" style="padding-bottom:15px;">
            <div class="row" style="padding: 10px 20px;">

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="col-md-12">
                        <div class="alert alert-danger col-12" role="alert">
                            <button type="button" class="close pull-left" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Items Table --}}
                <div class="col-md-12 m-t-20">
                <div class="table-responsive">
                    <table class="table table-bordered new" id="itemsTable">
                
                            <tr style="background:#e9fffe">
                                <th style="width:30%">{{__('wh.item_selection')}}</th>
                                <th style="width:10%">{{__('common.amount')}}</th>
                                <th style="width:10%">{{__('common.unit')}}</th>
                                <th style="width:10%"> {{__('wh.average')}}</th>
                                <th style="width:15%">{{__('common.total')}}</th>
                                <th style="width:10%">{{__('common.add')}}</th>
                            </tr>
                        
                        
                            @foreach($oldRecords as $old)
                            <tr class="item-row">
                                <td>
                                    <select class="form-control select2 item-select" name="owarehouseItemId[]" style="width: 100%;" required>
                                        <option value="{{ $old->id }}">{{ $old->item_name }}</option>
                                    </select>
                                </td>

                                <td><input name="oamount[]" class="form-control" type="number" step="0.01" value="{{ $old->amount }}" required></td>
                                <td><input name="ounit_name[]" class="form-control" type="text" value="{{ $old->unit_name }}" readonly required></td>
                                <td><input name="oavg_up[]" class="form-control" type="number" value="{{ $old->price }}" step="0.01" required></td>
                                <td><input name="ototal[]" class="form-control" type="number" value="{{ $old->total_price }}" step="0.01" readonly required></td>

                                <!-- ✅ This hidden field is crucial -->
                                <input type="hidden" name="oldRecordId[]" value="{{ $old->id }}">

                                <td>
                                    <button type="button" class="btn btn-warning btn-sm removeRow">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach

                    </table>
                </div>

                </div>

                <hr />

                {{-- Submit / Cancel Buttons --}}
                <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                    <div class="row">
                        <div class="col-3 col-xs-6">
                            <input type="submit" id="submit_button" name="submit" value="{{ __('common.save') }}" class="form-control btn bg-blue pull-left">
                        </div>
                        <div class="col-3 col-xs-6">
                            <a href="{{ route('model.index') }}">
                                <button type="button" class="form-control btn bg-danger">{{ __('common.cancel') }}</button>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
@endif

<script>
$(document).ready(function() {
    // Function to recalculate total for a row
    function recalcRow(row) {
        var amount = parseFloat(row.find('input[name="oamount[]"]').val()) || 0;
        var avgUp = parseFloat(row.find('input[name="oavg_up[]"]').val()) || 0;
        var total = amount * avgUp;
        row.find('input[name="ototal[]"]').val(total.toFixed(2));
    }

    // Trigger recalculation when amount or avg_up changes
    $(document).on('input', 'input[name="oamount[]"], input[name="oavg_up[]"]', function() {
        var row = $(this).closest('tr');
        recalcRow(row);
    });

    // Optional: handle remove row button
    $(document).on('click', '.removeRow', function() {
        var rows = $('#itemsTable tbody tr.item-row');
        if(rows.length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert("You must have at least one row.");
        }
    });
});
</script>
