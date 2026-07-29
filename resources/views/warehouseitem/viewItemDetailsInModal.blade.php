<div class="container-fluid">
    <div class="table-responsive">
        <table class="table table-bordered" style="background: #fff;">
            <tbody>
                <!-- Row 1: Item & Bill -->
                <tr>
                    <td style="width: 25%; font-weight: bold; background: #f5f8ff;">{{__('common.item_name')}}</td>
                    <td style="width: 25%;">{{ $warehouseItems->preListRelation->name ?? 'N/A' }}</td>
                    <td style="width: 25%; font-weight: bold; background: #f5f8ff;">{{__('common.buy_bill')}}</td>
                    <td style="width: 25%;">{{ 'BUY_'.$warehouseItems->billno ?? 'N/A' }}</td>
                </tr>

                <!-- Row 2: Unit & Car -->
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">{{__('common.unit')}}</td>
                    <td>{{ $warehouseItems->unitRelation->name ?? 'N/A' }}</td>
                    <td style="font-weight: bold; background: #f5f8ff;">{{__('common.car')}}</td>
                    <td>{{ $warehouseItems->carRelation->name ?? 'N/A' }}</td>
                </tr>

                <!-- Row 3: Date & Status -->
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">{{__('common.date')}}</td>
                    <td>{{ $warehouseItems->idate ? \Carbon\Carbon::parse($warehouseItems->idate)->format('Y-m-d') : 'N/A' }}</td>
                    <td style="font-weight: bold; background: #f5f8ff;">{{__('common.status')}}</td>
                    <td>
                        @if(($warehouseItems->available_amount ?? 0) > 0)
                            <span style="color: #2e7d32; font-weight: bold;">✅ {{__('common.available')}}</span>
                        @else
                            <span style="color: #c62828; font-weight: bold;">❌ {{__('common.out_of_stock')}}</span>
                        @endif
                    </td>
                </tr>

                <!-- Row 4: In & Out -->
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">{{__('common.in')}}</td>
                    <td style="color: #0d47a1; font-weight: bold;">{{ number_format($warehouseItems->in_amount ?? 0, 2) }}
                        {{ $warehouseItems->unitRelation->name ?? 'N/A' }}
                    </td>
                    <td style="font-weight: bold; background: #f5f8ff;">{{__('common.out')}}</td>
                    <td style="color: #c62828;">{{ number_format($warehouseItems->out_amount ?? 0, 2) }} 
                        {{ $warehouseItems->unitRelation->name ?? 'N/A' }}</td>
                </tr>

                <!-- Row 5: Available & Total -->
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">{{__('common.available')}}</td>
                    <td style="color: #2e7d32; font-weight: bold;">{{ number_format($warehouseItems->available_amount ?? 0, 2) }}
                        {{ $warehouseItems->unitRelation->name ?? 'N/A' }}
                    </td>
                    <td style="font-weight: bold; background: #f5f8ff;">{{__('common.total')}}</td>
                    <td style="color: #0d47a1; font-weight: bold;">{{ number_format($warehouseItems->total ?? 0, 2) }}</td>
                </tr>

                <!-- Row 6: Buy Price & Sell Price -->
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">فی واحد خرید</td>
                    <td>{{ number_format($warehouseItems->buy_up ?? 0, 2) }}</td>
                    <td style="font-weight: bold; background: #f5f8ff;">فی واحد فروش</td>
                    <td>{{ number_format($warehouseItems->sell_up ?? 0, 2) }}</td>
                </tr>

                <!-- Row 7: Buy Price with VAT & Sell Price with VAT -->
                 @if($warehouseItems->buy_tax_per > 0)
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">فی واحد خرید با مالیات</td>
                    <td style="color: #2e7d32; font-weight: bold;">{{ number_format($warehouseItems->buy_up_vat ?? 0, 2) }}</td>
                    <td style="font-weight: bold; background: #f5f8ff;">فی واحد فروش با مالیات</td>
                    <td style="color: #2e7d32; font-weight: bold;">{{ number_format($warehouseItems->sell_up_vat ?? 0, 2) }}</td>
                </tr>

                <!-- Row 8: Available Total & Total -->
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">مبلغ موجودی اجناس</td>
                    <td style="color: #2e7d32; font-weight: bold;">{{ number_format($warehouseItems->available_total ?? 0, 2) }}</td>
                    <td style="font-weight: bold; background: #f5f8ff;">مبلغ مجموعی خرید</td>
                    <td style="color: #0d47a1; font-weight: bold;">{{ number_format($warehouseItems->total ?? 0, 2) }}</td>
                </tr>

                <!-- Row 9: Buy Tax % & Sell Tax % -->
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">فیصدی مالیات خرید</td>
                    <td>{{ number_format($warehouseItems->buy_tax_per ?? 0) }}%</td>
                    <td style="font-weight: bold; background: #f5f8ff;">فیصدی مالیات فروش</td>
                    <td>{{ number_format($warehouseItems->sell_tax_per ?? 0) }}%</td>
                </tr>

                <!-- Row 10: Buy Tax Price & Sell Tax Price -->
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">مبلغ مالیات خرید</td>
                    <td>{{ number_format($warehouseItems->buy_tax_price ?? 0, 2) }}</td>
                    <td style="font-weight: bold; background: #f5f8ff;">مبلغ مالیات فروش</td>
                    <td>{{ number_format($warehouseItems->sell_tax_price ?? 0, 2) }}</td>
                </tr>

                <!-- Row 11: Total Buy with VAT & Total Sell with VAT -->
                <tr>
                    <td style="font-weight: bold; background: #f5f8ff;">مجموع خرید با مالیات</td>
                    <td style="color: #0d47a1; font-weight: bold;">{{ number_format(($warehouseItems->buy_up_vat ?? 0) * ($warehouseItems->in_amount ?? 0), 2) }}</td>
                    <td style="font-weight: bold; background: #f5f8ff;">مجموع فروش با مالیات</td>
                    <td style="color: #2e7d32; font-weight: bold;">{{ number_format(($warehouseItems->sell_up_vat ?? 0) * ($warehouseItems->in_amount ?? 0), 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>