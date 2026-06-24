@extends('layouts.app')

@section('content')
<style>
    .invoice-container {
        max-width: 1100px;
        margin: 0 auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    }
    .invoice-header {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }
    .invoice-title {
        font-size: 28px;
        font-weight: 700;
        color: #2d3436;
    }
    .invoice-info {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }
    .invoice-info td {
        padding: 5px 10px;
        font-size: 14px;
    }
    .invoice-table th {
        background: #f1f3f5;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        padding: 12px 10px;
    }
    .invoice-table td {
        padding: 10px;
        vertical-align: middle;
    }
    .summary-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-top: 25px;
    }
    .summary-section td {
        padding: 8px 15px;
        font-size: 15px;
    }
    .total-amount {
        font-size: 22px;
        font-weight: 700;
        color: #2d3436;
    }
    .grand-total {
        background: #2d3436;
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        padding: 12px 20px;
        border-radius: 8px;
    }
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 13px;
    }
    .status-draft { background: #dfe6e9; color: #2d3436; }
    .status-pending { background: #fdcb6e; color: #2d3436; }
    .status-partial { background: #4facfe; color: #fff; }
    .status-paid { background: #00b894; color: #fff; }
    .status-cancelled { background: #e17055; color: #fff; }
    .payment-section {
        margin-top: 25px;
        padding: 20px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
    }
    .payment-table th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
    }
    @media print {
        .no-print { display: none !important; }
        .invoice-container { box-shadow: none !important; padding: 10px !important; }
        .payment-section { border: none !important; }
    }
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">
                                {{ __('buy.invoice_details') }}
                                <span class="pull-left">
                                    <a href="{{ route('boughtList.invoices') }}">
                                        <button class="btn mybtn bg-default"> {{ __('common.back') }} </button>
                                    </a>
                                    <button class="btn btn-success btn-sm no-print" onclick="window.print()">
                                        <i class="fas fa-print"></i> {{ __('common.print') }}
                                    </button>
                                </span>
                            </h4>
                        </div>

                        <div class="card-body">
                            <div class="invoice-container" id="invoice-area">

                                <!-- Header -->
                                <div class="invoice-header text-center">
                                    @if(isset($orgbios[0]->header))
                                        <img src="{{ asset($orgbios[0]->header) }}" alt="Logo" style="max-height:80px; margin-bottom:10px;">
                                    @endif
                                    <h2 class="invoice-title">{{ $orgbios[0]->name ?? 'Company Name' }}</h2>
                                    <p style="color: #636e72; font-size:14px;">
                                        {{ $orgbios[0]->address ?? '' }} | {{ __('common.phone') }}: {{ $orgbios[0]->phone ?? '' }}
                                    </p>
                                </div>

                                <!-- Invoice Info -->
                                <div class="invoice-info">
                                    <table style="width:100%">
                                        <tr>
                                            <td style="width:20%"><strong>{{ __('buy.invoice_number') }}:</strong></td>
                                            <td style="width:30%">{{ $invoice->invoice_number }}</td>
                                            <td style="width:20%"><strong>{{ __('order.supplier_name') }}:</strong></td>
                                            <td style="width:30%">{{ $invoice->supplier->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('buy.invoice_date') }}:</strong></td>
                                            <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                            <td><strong>{{ __('buy.due_date') }}:</strong></td>
                                            <td>{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('common.currency') }}:</strong></td>
                                            <td>{{ $invoice->currency->name ?? '' }}</td>
                                            <td><strong>{{ __('order.status') }}:</strong></td>
                                            <td>
                                                @php
                                                    $statusClasses = [
                                                        0 => 'status-draft',
                                                        1 => 'status-pending',
                                                        2 => 'status-partial',
                                                        3 => 'status-paid',
                                                        4 => 'status-cancelled'
                                                    ];
                                                    $statusLabels = [
                                                        0 => __('order.draft'),
                                                        1 => __('order.pending'),
                                                        2 => __('order.partial'),
                                                        3 => __('order.paid'),
                                                        4 => __('order.cancelled')
                                                    ];
                                                @endphp
                                                <span class="status-badge {{ $statusClasses[$invoice->status] ?? 'status-draft' }}">
                                                    {{ $statusLabels[$invoice->status] ?? __('order.unknown') }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Items Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered invoice-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('buy.item') }}</th>
                                                <th>{{ __('common.amount') }}</th>
                                                <th>{{ __('common.unit_price') }}</th>
                                                <th>{{ __('buy.tax') }} %</th>
                                                <th>{{ __('buy.tax_amount') }}</th>
                                                <th>{{ __('common.total_price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->items as $key => $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->preList->name ?? '' }}</td>
                                                <td>{{ number_format($item->amount, 2) }}</td>
                                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ $item->tax_percentage ?? 0 }}%</td>
                                                <td>{{ number_format($item->tax_amount ?? 0, 2) }}</td>
                                                <td>{{ number_format($item->total, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Summary -->
                                <div class="summary-section">
                                    <table style="width:100%">
                                        <tr>
                                            <td style="width:50%">
                                                <strong>{{ __('buy.notes') }}:</strong>
                                                <p style="margin-top:5px; color:#636e72;">{{ $invoice->notes ?? __('common.no_notes') }}</p>
                                            </td>
                                            <td style="width:50%">
                                                <table style="width:100%">
                                                    <tr>
                                                        <td><strong>{{ __('common.total_price') }}</strong></td>
                                                        <td style="text-align:right">{{ number_format($invoice->total_amount, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('buy.paid_amount') }}</strong></td>
                                                        <td style="text-align:right; color:#00b894;">{{ number_format($invoice->paid_amount, 2) }}</td>
                                                    </tr>
                                                    <tr style="font-size:18px; font-weight:700;">
                                                        <td><strong>{{ __('buy.remaining_amount') }}</strong></td>
                                                        <td style="text-align:right; color:#e17055;">
                                                            {{ number_format($invoice->remaining_amount, 2) }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Payment Section -->
                                <div class="payment-section no-print">
                                    <h5>{{ __('buy.payments') }}</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered payment-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('common.date') }}</th>
                                                    <th>{{ __('common.amount') }}</th>
                                                    <th>{{ __('buy.payment_method') }}</th>
                                                    <th>{{ __('buy.reference_number') }}</th>
                                                    <th>{{ __('buy.notes') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($invoice->payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->payment_date }}</td>
                                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                                    <td>
                                                        @php
                                                            $methods = [
                                                                1 => __('buy.cash'),
                                                                2 => __('buy.bank'),
                                                                3 => __('buy.loan')
                                                            ];
                                                        @endphp
                                                        {{ $methods[$payment->payment_method] ?? '-' }}
                                                    </td>
                                                    <td>{{ $payment->reference_number ?? '-' }}</td>
                                                    <td>{{ $payment->notes ?? '-' }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('buy.no_payments_recorded') }}</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Add Payment Form -->
                                    @if($invoice->status != 3 && $invoice->status != 4)
                                    <div class="row no-print">
                                        <div class="col-md-12">
                                            <h6>{{ __('buy.add_payment') }}</h6>
                                            <form id="paymentForm">
                                                @csrf
                                                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" step="0.01" class="form-control" name="amount" placeholder="{{ __('common.amount') }}" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <select class="form-control" name="payment_method" required>
                                                            <option value="1">{{ __('buy.cash') }}</option>
                                                            <option value="2">{{ __('buy.bank') }}</option>
                                                            <option value="3">{{ __('buy.loan') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" name="reference_number" placeholder="{{ __('buy.reference_number') }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-plus"></i> {{ __('common.add') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control" name="notes" placeholder="{{ __('buy.notes') }}">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Payment Form Submit
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '{{ route("boughtList.addPayment") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    showNotification(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    $.each(errors, function(key, messages) {
                        errorMessages.push(messages[0]);
                    });
                    showNotification(errorMessages.join('<br>'), 'danger');
                } else {
                    showNotification('{{ __("common.error_occurred") }}', 'danger');
                }
            }
        });
    });
});

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
@endsection