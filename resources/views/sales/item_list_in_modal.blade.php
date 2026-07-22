@php
    $totalProfit = $salesDetails->sum('profit');
    $totalPrice = $salesDetails->sum('total');
    $totalAmount = $salesDetails->sum('amount');
    $colspan = $saved_with_tax ? 7 : 5;
@endphp

<div class="table-responsive">
    <table class="table table-bordered new" style="width:100%">
        <thead>
            <tr>
                <th>{{__('common.number')}}</th>
                <th>{{__('sales.item')}}</th>
                <th>{{__('buy.sold_amount')}}</th>
                <th>{{__('sales.unit')}}</th>
                @if($saved_with_tax) 
                <th>{{__('buy.sales_tax_percentage')}}</th>
                <th>{{__('buy.sell_tax_price')}}</th>
                @endif
                <th>{{__('common.unit_price')}}</th>
                <th>{{__('sales.profit')}}</th>
                <th>{{__('common.total_price')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesDetails as $key => $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $detail->preListRelation->name ?? ' ' }}</td>
                <td>{{ $detail->amount }}</td>
                <td>{{ $detail->unitRelation->name ?? ' ' }}</td>
                @if($saved_with_tax) 
                <td>% {{ $detail->sell_tax_per ?? 0 }}</td>
                <td>{{ number_format($detail->sell_tax_price ?? 0, 2) }}</td>
                @endif
                <td>{{ number_format($detail->sell_up ?? 0, 2) }}</td>
                <td>{{ number_format($detail->profit ?? 0, 2) }}</td>
                <td>{{ number_format($detail->total ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #eefcff; font-weight: bold;">
                <td colspan="{{ $colspan }}" style="text-align: right;">
                    <strong>{{ __('common.total') }}</strong>
                </td>
                <td>
                    <strong>{{ number_format($totalProfit, 2) }}</strong>
                </td>
                <td>
                    <strong>{{ number_format($totalPrice, 2) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
</div>