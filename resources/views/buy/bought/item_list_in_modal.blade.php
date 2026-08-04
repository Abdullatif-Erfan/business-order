<div class="table-responsive">
    <table class="table table-bordered new" style="width:100%">
        <thead>
            <tr>
                <th>{{__('common.number')}}</th>
                <th width="150">{{__('common.seller')}}</th>
                <th>{{__('buy.item')}}</th>
                <th>{{__('common.unit')}}</th>
                <th>{{__('buy.bought_amount')}}</th>
                <th>{{__('common.unit_price')}}</th>
                @if($saved_with_tax) 
                <th>{{__('buy.buy_tax_percentage_s')}}</th>
                <th>{{__('buy.buy_up_vat')}}</th>
                @endif
                <th>{{__('common.total_price')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($boughtDetails as $key => $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $detail->accountRelation->name ?? '-' }}</td>
                <td>{{ $detail->preListRelation->name ?? '-' }}</td>
                <td>{{ $detail->unitRelation->name ?? '-' }}</td>
                <td>{{ number_format($detail->amount, 2) }}</td>
                <td>
                    @if($saved_with_tax)
                        {{ number_format($detail->buy_up_vat ?? $detail->buy_up, 2) }}
                    @else
                        {{ number_format($detail->buy_up, 2) }}
                    @endif
                </td>
                @if($saved_with_tax) 
                <td>% {{ $detail->buy_tax_per ?? 0 }}</td>
                <td>{{ number_format($detail->buy_up_vat ?? $detail->buy_up, 2) }}</td>
                @endif
                <td>
                    @if($detail->buy_tax_per > 0)
                        {{ number_format($detail->total_vat, 2) }}  
                    @else 
                        {{ number_format($detail->total, 2) }} 
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>