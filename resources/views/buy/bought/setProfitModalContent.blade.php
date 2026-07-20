<div class="container-fluid">
    <form id="updateProfitForm">
        @csrf
        <input type="hidden" name="billno" value="{{ $boughtItemDetails->first()->billno ?? '' }}">
        <input type="hidden" name="times" value="{{ $boughtItemDetails->first()->times ?? '' }}">

        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr style="background: #e9fffe;">
                        <th style="width:5%">#</th>
                        <th style="width:15%">{{__('wh.item_selection')}}</th>
                        <th style="width:10%">{{__('common.amount')}}</th>
                        <th style="width:10%">{{__('common.unit')}}</th>
                        <th style="width:15%">{{__('buy.buy_up')}}</th>
                        <th style="width:25%">{{__('buy.profit')}}</th>
                        <th style="width:25%">{{__('sales.sold_up')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boughtItemDetails as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->preListRelation->name ?? 'Unknown' }}</strong>
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            <input type="hidden" name="items[{{ $index }}][buy_up]" value="{{ $item->buy_up }}">
                            <input type="hidden" name="items[{{ $index }}][pre_list_id]" value="{{ $item->pre_list_id }}">
                            <input type="hidden" name="items[{{ $index }}][unit_id]" value="{{ $item->unit_id }}">


                        </td>
                        <td>{{ number_format($item->amount, 2) }}</td>
                        <td>{{ $item->unitRelation->name ?? 'Unknown' }}</td>
                        <td>
                            <input type="number" step="any" min="0" 
                                   class="form-control buy-up-display" 
                                   value="{{ $item->buy_up }}" readonly style="background:#f5f5f5;">
                        </td>
                        <td>
                            <input type="number" step="any" min="0" 
                                   class="form-control profit-input" 
                                   name="items[{{ $index }}][profit]" 
                                   value="{{ $item->expected_profit ?? '' }}" 
                                   placeholder="{{__('common.profit')}}">
                        </td>
                        <td>
                            <input type="number" step="any" min="0" 
                                   class="form-control sell-up-display" 
                                   name="items[{{ $index }}][sell_up]" 
                                   value="{{ $item->sell_up ?? '' }}" 
                                   placeholder="{{__('sales.sell_up')}}" readonly>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // =========================================
    // RECALCULATE SELL UP ON PROFIT CHANGE
    // =========================================
    $(document).on('input', '.profit-input', function() {
        var row = $(this).closest('tr');
        var buyUp = parseFloat(row.find('.buy-up-display').val()) || 0;
        var profit = parseFloat($(this).val()) || 0;
        
        var sellUp = buyUp + profit;
        // Update the sell-up display
        row.find('.sell-up-display').val(sellUp.toFixed(2));
    });

    // =========================================
    // FORM SUBMISSION
    // =========================================
    $('#EditAccountBtn').off('click').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var originalText = $btn.text();
        $btn.prop('disabled', true).text('{{__("common.saving")}}...');
        
        var formData = $('#updateProfitForm').serialize();
        
        console.log('Form Data:', formData); // Debug: Check what's being sent
        
        $.ajax({
            url: '{{ url("/boughtList/updateProfit") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $btn.prop('disabled', false).text(originalText);
                if (response.status === 'success') {
                    showNotification(response.message || '{{__("common.updated_successfully")}}', 'success');
                    $('#EditRecordsModal').modal('hide');
                    if ($.fn.DataTable.isDataTable('#boughtItemTable')) {
                        $('#boughtItemTable').DataTable().ajax.reload(null, false);
                    }
                } else {
                    showNotification(response.message || '{{__("common.error_occurred")}}', 'danger');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    $.each(errors, function(key, messages) {
                        errorMessages.push(messages[0]);
                    });
                    showNotification(errorMessages.join('<br>'), 'danger');
                } else {
                    showNotification('{{__("common.error_occurred")}}', 'danger');
                }
            }
        });
    });
});
</script>