@props([
    'name' => 'date',
    'id' => null,
    'label' => 'Date',
    'value' => null,
    'required' => false,
    'placeholder' => 'Select Date',
    'format' => 'yyyy-mm-dd',
    'startDate' => null,
    'endDate' => null,
    'autoclose' => true,
    'todayHighlight' => true,
    'weekStart' => 1,
    'clearBtn' => true,
    'todayBtn' => true,
    'orientation' => 'bottom'
])

@php
    $id = $id ?? $name;
    $value = $value ?? old($name, date('Y-m-d'));
    $requiredAttr = $required ? 'required' : '';
    
    // Build data attributes
    $dataAttrs = "data-provide='datepicker'";
    $dataAttrs .= " data-date-format='{$format}'";
    $dataAttrs .= $startDate ? " data-date-start-date='{$startDate}'" : '';
    $dataAttrs .= $endDate ? " data-date-end-date='{$endDate}'" : '';
    $dataAttrs .= $autoclose ? " data-date-autoclose='true'" : '';
    $dataAttrs .= $todayHighlight ? " data-date-today-highlight='true'" : '';
    $dataAttrs .= " data-date-week-start='{$weekStart}'";
    $dataAttrs .= $clearBtn ? " data-date-clear-btn='true'" : '';
    $dataAttrs .= $todayBtn ? " data-date-today-btn='true'" : '';
    $dataAttrs .= " data-date-orientation='{$orientation}'";
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $id }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div class="input-group date" id="{{ $id }}_wrapper">
        <input type="text" class="form-control" id="{{ $id }}" 
               name="{{ $name }}" value="{{ $value }}" 
               placeholder="{{ $placeholder }}" {{ $requiredAttr }}
               {!! $dataAttrs !!}>
        <div class="input-group-append">
            <span class="input-group-text datepicker-icon" style="cursor:pointer;">
                <i class="fas fa-calendar-alt"></i>
            </span>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Manual initialization if needed
    @if(!$autoclose)
    $('#{{ $id }}_wrapper').datepicker({
        format: '{{ $format }}',
        autoclose: {{ $autoclose ? 'true' : 'false' }},
        todayHighlight: {{ $todayHighlight ? 'true' : 'false' }},
        weekStart: {{ $weekStart }},
        clearBtn: {{ $clearBtn ? 'true' : 'false' }},
        todayBtn: {{ $todayBtn ? 'true' : 'false' }},
        orientation: '{{ $orientation }}'
        @if($startDate)
        ,startDate: '{{ $startDate }}'
        @endif
        @if($endDate)
        ,endDate: '{{ $endDate }}'
        @endif
    });
    @endif
});
</script>
@endpush