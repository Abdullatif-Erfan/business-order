@foreach($records as $record)
<a href="javascript:void(0);">
    <div class="notif-content">
        <span class="block p-10">
            <b class="col-blue">{{ $record->item_name ?? '' }}</b> در   ( {{ $record->wname ?? '' }} )
            به تعداد ({{$record->available_amount ?? ''}}) {{$record->unit_name ?? ''}} مانده است
        </span>
    </div>
</a>
@endforeach

