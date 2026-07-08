@extends('layouts.app')

@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin == 1;
    $permissions = [
        'settings' => $user->hasAccess('dashboard', 'total_access'),
    ];
@endphp

@section('content')
<style>
    a {
        color: #555 !important;
    }
    a:hover {
        text-decoration: none;
    }
    .form-control {
        border-radius: 25px;
    }

    .filter-group .input-group .form-control {
        height: 38px;
        font-size: 13px;
        border-radius: 4px 0 0 4px;
        border: 1px solid #ddd;
        padding: 6px 12px;
        background: #fff;
        border-radius: 25px;
    }
    .filter-group .input-group .input-group-text {
        border-radius: 0 4px 4px 0;
        background: #fff;
        border: 1px solid #ddd;
        border-left: none;
        cursor: pointer;
        padding: 0 12px;
        height: 34px;
        color: #636e72;
        font-size: 14px;
        display: flex;
        align-items: center;
        border-radius: 25px !important;
    }
    .filter-group .input-group .input-group-text:hover {
        color: #4a6cf7;
    }
    .btn-search {
        background: #dddddd;
        color: #1673ca;
        border: none;
        padding: 9px 12px;
        border-radius: 25px;
        cursor: pointer;
        height: 38px;
        font-size: 14px;
    }
    .btn-search:hover {
        background: #127a48;
        color: #fff;
    }
    .btn-reset {
        background: #dddddd;
        color: #be16c4;
        border: none;
        padding: 9px 12px;
        border-radius: 25px;
        cursor: pointer;
        height: 38px;
        font-size: 14px;
        margin-left: 5px;
    }
    .btn-reset:hover {
        background: #2a7bc8;
        color: #fff;
    }
    /* Loader */

.dots-loader {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
}

.dots-loader span {
    width: 12px;
    height: 12px;
    background: #4a6cf7;
    border-radius: 50%;
    animation: bounce 1.4s infinite ease-in-out both;
}

.dots-loader span:nth-child(1) { animation-delay: -0.32s; }
.dots-loader span:nth-child(2) { animation-delay: -0.16s; }
.dots-loader span:nth-child(3) { animation-delay: 0s; }

@keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

</style>

<div class="main-panel">
    <input type="hidden" id="todays_date" value="{{ date('Y-m-d') }}">
    <div class="content">
        <div class="panel-header bg-primary-gradient">
            <div class="page-inner">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h1 class="text-white pb-2 fw-bold main_title">
                            {{ $orgBio->name ?? 'Dashboard' }}
                        </h1>
                    </div>
                </div>

                <!-- ================================= Search Area ============================================= -->
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <form id="filterForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 col-sm-4 col-xs-6">
                                <select class="form-control mt-1 mb-1" id="supplier_id"
                                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="supplier_id">
                                    <option value=""> -- {{ __('order.supplier_name') }} -- </option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-sm-4 col-xs-6">
                                <select class="form-control mt-1 mb-1" id="driver_id"
                                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="driver_id">
                                    <option value=""> -- {{ __('order.employee_name') }} -- </option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6">
                                <div class="filter-group" style="min-width: 120px;">
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker-input" value="{{ $search['start_date'] ?? '' }}" id="start_date" placeholder="YYYY-MM-DD">
                                        <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6">
                                <div class="filter-group" style="min-width: 120px;">
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker-input"  id="end_date" placeholder="YYYY-MM-DD">
                                        <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6">
                                <button type="button" class="btn btn-search" id="btn-filter"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-reset" id="btn-reset" title="{{ __('common.reset') }}"><i class="fas fa-undo"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- ================================= / Search Area ============================================= -->

            </div>
        </div>
   
        <!-- tab -->
        <div class="col-12 tab-wrapper">
            @if($permissions['settings'] || $isAdmin)             
                @include('dashboard.steps')
            @else
                <h3>No Permission</h3>
            @endif
        </div>
        <!-- / tab -->

    </div>
    @include('component.footer-text')
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // =========================================
    // DATE PICKER
    // =========================================
    $(document).on('click', '.datepicker-icon', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $input = $(this).closest('.input-group').find('input');
        if ($input.length) {
            $input.datepicker('show');
        }
    });

    // =========================================
    // FILTER BUTTON
    // =========================================
    $('#btn-filter').click(function() {
        fetchDashboardData();
    });

    // =========================================
    // RESET BUTTON
    // =========================================
    $('#btn-reset').click(function() {
        // Show loader, hide button text
        $('#filter-loader').show();
        $('#btn-reset').prop('disabled', true);

        $('#supplier_id').val('');
        $('#driver_id').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        fetchDashboardData();
    });

    // =========================================
    // ENTER KEY SEARCH
    // =========================================
    $('#filterForm input, #filterForm select').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn-filter').click();
        }
    });

    // =========================================
    // INITIAL LOAD
    // =========================================
    fetchDashboardData();
});

// =========================================
// FETCH DASHBOARD DATA (Orders & Buy)
// =========================================
function fetchDashboardData() {
    var supplierId = $('#supplier_id').val();
    var driverId = $('#driver_id').val();
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();

    // Show loader
    $('#filter-loader').show();
    $('#btn-filter').prop('disabled', true);
    $('#btn-filter i').hide();
    $('#btn-filter').append('<span id="filter-loading-text"> جستجو ...</span>');

    // Fetch Orders
    $.ajax({
        url: '{{ route("home.orders") }}',
        type: 'GET',
        data: {
            supplier_id: supplierId,
            driver_id: driverId,
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#orders').html(response.data);
            }
        },
        error: function(xhr) {
            console.log('Orders Error:', xhr);
        }
    });

    // Fetch Buy (Bought) Data
    $.ajax({
        url: '{{ route("home.bought") }}',
        type: 'GET',
        data: {
            supplier_id: supplierId,
            driver_id: driverId,
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            // Hide loader
            $('#filter-loader').hide();
            $('#btn-filter').prop('disabled', false);
            $('#btn-filter i').show();
            $('#filter-loading-text').remove();

            if (response.status === 'success') {
                $('#buy').html(response.data);
            } else {
                showNotification(response.message || 'خطا در بارگذاری داده‌های خرید', 'danger');
            }
        },
        error: function(xhr) {
            // Hide loader
            $('#filter-loader').hide();
            $('#btn-filter').prop('disabled', false);
            $('#btn-filter i').show();
            $('#filter-loading-text').remove();
            
            console.log('Buy Error:', xhr);
            showNotification('خطا در بارگذاری داده‌های خرید', 'danger');
        }
    });

    // Fetch Sales Data
    $.ajax({
        url: '{{ route("home.sales") }}',
        type: 'GET',
        data: {
            supplier_id: supplierId,
            driver_id: driverId,
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            // Hide loader
            $('#filter-loader').hide();
            $('#btn-filter').prop('disabled', false);
            $('#btn-filter i').show();
            $('#filter-loading-text').remove();

            if (response.status === 'success') {
                $('#sales').html(response.data);
            } else {
                showNotification(response.message || 'خطا در بارگذاری داده‌های فروش', 'danger');
            }
        },
        error: function(xhr) {
            // Hide loader
            $('#filter-loader').hide();
            $('#btn-filter').prop('disabled', false);
            $('#btn-filter i').show();
            $('#filter-loading-text').remove();
            
            console.log('Sales Error:', xhr);
            showNotification('خطا در بارگذاری داده‌های فروش', 'danger');
        }
    });
}

// =========================================
// NOTIFICATION FUNCTION
// =========================================
function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
    var content = {
        message: '<span style="font-size:16px;">' + message + '</span>',
        title: '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{ __("settings.message") }}</span>',
        icon: style === 'withicon' ? 'fa fa-bell' : 'none',
        url: '#',
        target: '_blank'
    };

    $.notify(content, {
        type: type,
        placement: {
            from: from,
            align: align
        },
        time: 500
    });
}
</script>
@endpush

@endsection 