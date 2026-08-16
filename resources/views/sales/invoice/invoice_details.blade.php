@extends('layouts.app')

@php 
  $billNumbers = json_decode($invoice->sales_bill_numbers, true);
  $total = 0;
  $total_cur_pay = 0;
  $total_remained = 0;
@endphp

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
    
    /* Receipt Print Styles - 80mm Thermal Printer */
    @media print {
        .no-print { display: none !important; }
        .invoice-container { box-shadow: none !important; padding: 10px !important; }
        .payment-section { border: none !important; }
        
        /* A4 Print Styles */
        body.print-a4 .print-area-a4 {
            display: block !important;
        }
        body.print-a4 .print-area-receipt {
            display: none !important;
        }
        
        /* Receipt Print Styles - 80mm */
        body.print-receipt .print-area-receipt {
            display: block !important;
        }
        body.print-receipt .print-area-a4 {
            display: none !important;
        }
        
        /* Receipt specific styling */
        body.print-receipt {
            direction: rtl !important;
            text-align: right !important;
            margin: 0mm !important;
            padding: 2mm !important;
        }
        
        body.print-receipt .receipt-content {
            max-width: 80mm !important;
            margin: 0 auto !important;
            padding: 2mm !important;
            font-family: 'Courier New', monospace !important;
            font-size: 11px !important;
            background: white !important;
        }
        
        body.print-receipt table {
            width: 100% !important;
            font-size: 10px !important;
            border-collapse: collapse !important;
        }
        
        body.print-receipt table td, 
        body.print-receipt table th {
            padding: 2px 3px !important;
            font-size: 10px !important;
            border: none !important;
            text-align: right !important;
        }
        
        body.print-receipt .receipt-header {
            text-align: center !important;
            border-bottom: 1px dashed #000 !important;
            padding-bottom: 8px !important;
            margin-bottom: 8px !important;
        }
        
        body.print-receipt .receipt-footer {
            text-align: center !important;
            border-top: 1px dashed #000 !important;
            padding-top: 8px !important;
            margin-top: 8px !important;
            font-size: 9px !important;
        }
        
        body.print-receipt .receipt-divider {
            border-top: 1px dashed #000 !important;
            margin: 4px 0 !important;
        }
        
        body.print-receipt .receipt-title {
            font-size: 12px !important;
            font-weight: bold !important;
            margin: 3px 0 !important;
        }
        
        body.print-receipt .receipt-logo {
            max-width: 60mm !important;
            height: auto !important;
        }
        
        body.print-receipt .text-center { text-align: center !important; }
        body.print-receipt .text-right { text-align: right !important; }
        body.print-receipt .text-left { text-align: left !important; }
        body.print-receipt .font-bold { font-weight: bold !important; }
        
        body.print-receipt .invoice-info-receipt {
            background: none !important;
            padding: 0 !important;
            margin-bottom: 5px !important;
        }
        
        body.print-receipt .summary-receipt {
            background: none !important;
            padding: 0 !important;
            margin-top: 5px !important;
        }
        
        body.print-receipt .status-badge {
            display: none !important;
        }
        
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        @page {
            size: 80mm auto !important;
            margin: 0mm !important;
        }
    }
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px;">
                            <input type="hidden" id="remained_amount_yet" value="{{ $invoice->remaining }}">
                            <h4 class="card-title">
                                {{ __('buy.invoice_details') }}
                                <span class="pull-left">
                                    <!-- A4 Print Button -->
                                    <button class="btn btn-success btn-sm no-print" onclick="print_page_with_image()">
                                        <i class="fas fa-print"></i> A4
                                    </button>
                                    <!-- Receipt Print Button -->
                                    <button class="btn btn-info btn-sm no-print" onclick="printReceipt()">
                                        <i class="fas fa-receipt"></i> Receipt
                                    </button>
                                    <a href="{{ route('sales.invoices') }}">
                                        <button class="btn mybtn bg-default"> {{ __('common.back') }} </button>
                                    </a>
                                </span>
                            </h4>
                        </div>

                        <div class="card-body">
                            <!-- ============================================ -->
                            <!-- A4 PRINT AREA -->
                            <!-- ============================================ -->
                            <div class="invoice-container print-area-a4" id="print_area">

                                <!-- Header -->
                                <div class="invoice-header text-center border">
                                    @if(isset($orgbios[0]->header))
                                        <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" 
                                                style="width: 100% !important;">
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
                                            <td style="width:20%"><strong>{{ __('buy.customer') }}:</strong></td>
                                            <td style="width:30%">{{ $invoice->customer->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('buy.invoice_date') }}:</strong></td>
                                            <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                            <td><strong>{{ __('sales.billno') }}:</strong></td>
                                            <td>
                                                @if($billNumbers && is_array($billNumbers))
                                                    @foreach($billNumbers as $bill)
                                                        <span class="badge">{{ 'SALES_'.$bill }}</span> &nbsp;&nbsp;
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
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
                                                <th>{{ __('sales.billno') }}</th>
                                                <th>{{ __('buy.invoice_date') }}</th>
                                                <th>{{ __('common.total_price') }}</th>
                                                <th>{{ __('buy.paid_amount') }} {{ __('common.bill') }}</th>
                                                <th>{{ __('buy.remaining_amount') }}</th>
                                                <th>{{ __('common.user') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->items as $key => $item)
                                            @php 
                                                $total += $item->total;
                                                $total_cur_pay += $item->cur_pay;
                                                $total_remained += $item->remained;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ 'SALES_'.$item->billno ?? '' }}</td>
                                                <td>{{ $item->invoice_date ?? '' }}</td>
                                                <td>{{ number_format($item->total, 2) }}</td>
                                                <td>{{ number_format($item->cur_pay ?? 0, 2) }}</td>
                                                <td>{{ number_format($item->remained ?? 0, 2) }}</td>
                                                <td>{{ $item->user_name ?? '' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr style="background-color:#f9f9f9">
                                                <td colspan="3">{{__('common.total')}}</td>
                                                <td>{{number_format($total,2)}}</td>
                                                <td style="text-align:right; color:#00b894;">{{number_format($total_cur_pay,2)}}</td>
                                                <td style="text-align:right; color:#e17055;">{{number_format($total_remained,2)}}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
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
                                                        <td style="text-align:right">
                                                            {{ number_format($invoice->total, 2) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('buy.paid_amount_bill_invoice') }}</strong></td>
                                                        <td style="text-align:right; color:#00b894;">
                                                            {{ number_format($invoice->paid_amount, 2) }}</td>
                                                    </tr>
                                                    <tr style="font-size:18px; font-weight:700;">
                                                        <td><strong>{{ __('buy.remaining_amount') }}</strong></td>
                                                        <td style="text-align:right; color:#e17055;">
                                                             {{ number_format($invoice->remaining, 2) }}
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
                                                    <th>دریافت کننده</th>
                                                    <th>{{ __('common.amount') }}</th>
                                                    <th>{{ __('buy.payment_method') }}</th>
                                                    <th>{{ __('buy.reference_number') }}</th>
                                                    <th>{{ __('buy.notes') }}</th>
                                                    <th class="hidden-print">{{ __('common.journal_code') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($invoice->payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->payment_date }}</td>
                                                    <td>{{ $payment->account->name ?? "" }}</td>
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
                                                    <td class="hidden-print">{{ $payment->journal_code ?? '-' }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">{{ __('buy.no_payments_recorded') }}</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Add Payment Form -->
                                    @if($invoice->status != 3 && $invoice->status != 4)
                                    <div class="row no-print hidden-print">
                                        <div class="col-md-12">
                                            <h6><strong>{{ __('buy.add_payment') }}</strong></h6>
                                            <form id="paymentForm">
                                                @csrf
                                                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                                <input type="hidden" name="times" value="{{ $times }}">
                                                <input type="hidden" name="code" value="{{ $newJournalCode }}">
                                                <input type="hidden" name="tax_activation" value="{{ $orgbios[0]->tax_activation }}">
                                                <input type="hidden" name="customer_account_id" value="{{ $invoice->customer->id }}">
                                                
                                                <div class="row">
                                                    <div class="col-md-3 col-sm-6 col-xs-6 m-b-4">
                                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_id" id="account_id">
                                                            @foreach($ownBanks as $bank)
                                                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 col-sm-6 col-xs-6 m-b-4">
                                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="customer_account_id" disabled>
                                                            @foreach($customers as $customer)
                                                                <option value="{{ $customer->id }}" {{$customer->id  === $invoice->customer->id ? 'selected': ''}} >{{ $customer->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" step="0.01" class="form-control" name="amount"
                                                         placeholder="{{ __('common.amount') }}" oninput="checkRemaining(this.value)" required>
                                                    </div>
                                                    <div class="col-md-2 mt-2">
                                                        <select class="form-control" name="payment_method" required>
                                                            <option value="1">{{ __('buy.cash') }}</option>
                                                            <option value="2">{{ __('buy.bank') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 col-sm-6 col-xs-6  mt-2">
                                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency_id">
                                                            @foreach($currencies as $currency)
                                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2  mt-2">
                                                        <input type="text" class="form-control" name="reference_number" placeholder="{{ __('buy.reference_number') }}">
                                                    </div>
                                                     <div class="col-md-3  mt-2">
                                                        <input type="text" class="form-control" name="notes" placeholder="{{ __('buy.notes') }}">
                                                    </div>
                                                    <div class="col-md-2  mt-2">
                                                        <button type="submit" class="btn btn-primary btn-sm" id="submit">
                                                            <i class="fas fa-plus"></i> {{ __('common.add') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                            </div>
                            <!-- END A4 PRINT AREA -->

                            <!-- ============================================ -->
                            <!-- RECEIPT PRINT AREA - 80mm Thermal Printer -->
                            <!-- ============================================ -->
                            <div class="print-area-receipt" id="print_area_receipt" style="display: none;">
                                <div class="receipt-content">
                                    
                                    <!-- Receipt Header -->
                                    <div class="receipt-header">
                                        <h3 class="receipt-title">{{ $orgbios[0]->name ?? 'Company Name' }}</h3>
                                        <table style="width:100%;">
                                            <tr>
                                                <td style="width:50%;"><strong>تلفن:</strong> {{ $orgbios[0]->phone ?? '' }}</td>
                                                <td style="width:50%; text-align:left;"><strong>{{__('common.print_date')}}:</strong> {{ $todaysDate ?? '' }}</td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Invoice Info -->
                                    <div style="margin: 3px 0;">
                                        <table style="width:100%;">
                                            <tr>
                                                <td style="width:50%;"><strong>{{__('buy.invoice_number')}}:</strong> {{ $invoice->invoice_number }}</td>
                                                <td style="width:50%; text-align:left;"><strong>{{__('buy.invoice_date')}}:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{__('buy.customer')}}:</strong> {{ $invoice->customer->name ?? '' }}</td>
                                                <td style="text-align:left;">
                                                    @php
                                                        $statusLabels = [
                                                            0 => __('order.draft'),
                                                            1 => __('order.pending'),
                                                            2 => __('order.partial'),
                                                            3 => __('order.paid'),
                                                            4 => __('order.cancelled')
                                                        ];
                                                    @endphp
                                                    <strong>{{__('order.status')}}:</strong> {{ $statusLabels[$invoice->status] ?? __('order.unknown') }}
                                                </td>
                                            </tr>
                                            @if($billNumbers && is_array($billNumbers))
                                            <tr>
                                                <td colspan="2"><strong>{{__('sales.billno')}}:</strong> 
                                                    @foreach($billNumbers as $bill)
                                                        SALES_{{ $bill }} &nbsp;
                                                    @endforeach
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>

                                    <div class="receipt-divider"></div>

                                    <!-- Items Table -->
                                    <table style="width:100%;">
                                        <thead>
                                            <tr style="border-bottom: 1px dashed #000;">
                                                <th style="text-align:center; padding:2px;">#</th>
                                                <th style="text-align:right; padding:2px;">{{__('sales.billno')}}</th>
                                                <th style="text-align:right; padding:2px;">{{__('common.total_price')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->items as $item)
                                            <tr>
                                                <td style="text-align:center; padding:2px;">{{ $loop->iteration }}</td>
                                                <td style="text-align:right; padding:2px;">SALES_{{ $item->billno ?? '' }}</td>
                                                <td style="text-align:left; padding:2px;">{{ number_format($item->total, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="receipt-divider"></div>

                                    <!-- Summary -->
                                    <table style="width:100%;">
                                        <tr>
                                            <td style="width:65%;"><strong>{{__('common.total_price')}}</strong></td>
                                            <td style="width:35%; text-align:left;"><strong>{{ number_format($invoice->total, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>{{__('buy.paid_amount_bill_invoice')}}</td>
                                            <td style="text-align:left;">{{ number_format($invoice->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr style="border-top: 1px dashed #000;">
                                            <td><strong>{{__('buy.remaining_amount')}}</strong></td>
                                            <td style="text-align:left;"><strong>{{ number_format($invoice->remaining, 2) }}</strong></td>
                                        </tr>
                                    </table>

                                    <div class="receipt-divider"></div>

                                    <!-- Payments Section (compact) -->
                                    @if($invoice->payments->count() > 0)
                                    <table style="width:100%;">
                                        <thead>
                                            <tr style="border-bottom: 1px dashed #000;">
                                                <th style="text-align:center; padding:2px;">#</th>
                                                <th style="text-align:right; padding:2px;">{{__('common.amount')}}</th>
                                                <th style="text-align:right; padding:2px;">{{__('common.date')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->payments as $payment)
                                            <tr>
                                                <td style="text-align:center; padding:2px;">{{ $loop->iteration }}</td>
                                                <td style="text-align:right; padding:2px;">{{ number_format($payment->amount, 2) }}</td>
                                                <td style="text-align:right; padding:2px;">{{ $payment->payment_date }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="receipt-divider"></div>
                                    @endif


                                </div>
                            </div>
                            <!-- END RECEIPT PRINT AREA -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkRemaining(cur_pay) {
    var remained = parseFloat($('#remained_amount_yet').val()) || 0;
    var curPay = parseFloat(cur_pay) || 0;
    remained = Math.round(remained * 100) / 100;
    curPay = Math.round(curPay * 100) / 100;
    
    if (curPay > remained) {
        alert("{{__('buy.over_pay_invoice')}}");
        $('#submit').hide();
        return false;
    } else if (curPay <= 0) {
        $('#submit').hide();
        alert("{{__('buy.empty_pay')}}");
    } else {
        $('#submit').show();
        return true;
    }
}

/**
 * Receipt Print Function - 80mm Thermal Printer
 */
function printReceipt() {
    var receiptElement = document.getElementById("print_area_receipt");
    var data = receiptElement.innerHTML;
    
    var printWindow = window.open("", "ReceiptPrintWindow", "width=400,height=600");
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Invoice Receipt</title>
            <style>
                body {
                    direction: rtl !important;
                    text-align: right !important;
                    margin: 0mm !important;
                    padding: 2mm !important;
                    font-family: 'Courier New', monospace !important;
                    background: white !important;
                }
                
                .receipt-content {
                    max-width: 80mm !important;
                    margin: 0 auto !important;
                    padding: 2mm !important;
                    font-size: 11px !important;
                }
                
                table {
                    width: 100% !important;
                    font-size: 10px !important;
                    border-collapse: collapse !important;
                }
                
                table td, table th {
                    padding: 2px 3px !important;
                    font-size: 10px !important;
                    border: none !important;
                    text-align: right !important;
                }
                
                .receipt-header {
                    text-align: center !important;
                    border-bottom: 1px dashed #000 !important;
                    padding-bottom: 8px !important;
                    margin-bottom: 8px !important;
                }
                
                .receipt-footer {
                    text-align: center !important;
                    border-top: 1px dashed #000 !important;
                    padding-top: 8px !important;
                    margin-top: 8px !important;
                    font-size: 9px !important;
                }
                
                .receipt-divider {
                    border-top: 1px dashed #000 !important;
                    margin: 4px 0 !important;
                }
                
                .receipt-title {
                    font-size: 12px !important;
                    font-weight: bold !important;
                    margin: 3px 0 !important;
                }
                
                .text-center { text-align: center !important; }
                .text-right { text-align: right !important; }
                .text-left { text-align: left !important; }
                
                * {
                    -webkit-print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }
                
                @page {
                    size: 80mm auto !important;
                    margin: 0mm !important;
                }
            </style>
        </head>
        <body>
            <div class="receipt-content">${data}</div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}

$(document).ready(function() {
    // Payment Form Submit
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '{{ route("sales.addPayment") }}',
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