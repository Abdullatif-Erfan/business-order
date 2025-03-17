@foreach($records as $record)
    <a href="javascript:void(0);">  
        <div class="notif-content">  
            <span class="block p-10">  
                <b class="col-blue">{{ $record->preListRelation->name }}</b> در ({{ $record->warehouseRelation->name }})  

                @if($record->expired_days >= 0)  
                    <span class="badge badge-success"> {{ $record->expired_days }} روز تا تاریخ انقضای شان مانده است </span>  
                @else
                    <span class="badge badge-danger">  {{ abs($record->expired_days) }} روز تاریخ شان گذشته است </span>  
                @endif  
            </span>  
        </div>  
    </a>  
@endforeach
