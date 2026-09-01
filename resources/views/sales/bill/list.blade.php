@extends('layouts.app')

@section('content')
@php
  $not_col_for_print = $saved_with_tax ? "6":"3"; 
  $total_cols = $saved_with_tax ? "2":"2"; 

   // Customer Balance Calculations
   $customerLoans = $customer_balance['loans'] ?? 0;        // Company owes customer (طلبات)
   $customerTalabat = $customer_balance['talabat'] ?? 0;    // Customer owes company (قرض)
  
  // Net balance: positive = company owes customer, negative = customer owes customer
  $netCustomerBalance = $customerLoans - $customerTalabat;
  
  // Current bill remained
  $currentRemained = $warehouseSales->first()->remained ?? 0;
  
  // Previous balance (customer's balance before this bill)
  $previousBalance = $netCustomerBalance - $currentRemained;
  
  // Total due including previous balance
  $totalDue = $netCustomerBalance + $currentRemained;
  $paymentsTotal = 0;

@endphp

<style>
.price-section {
    background-color: #f6f6f6;
}
.final-total{
    background-color:#436fa7;
    color: #fff;
    font-size: 20px;
    font-weight:bolder;
}

/* Receipt Print Styles - 80mm Thermal Printer */
@media print {
    /* A4 Print Styles - using your existing function */
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
    
    body.print-receipt .text-center {
        text-align: center !important;
    }
    
    body.print-receipt .text-right {
        text-align: right !important;
    }
    
    body.print-receipt .text-left {
        text-align: left !important;
    }
    
    body.print-receipt .total-amount {
        font-size: 13px !important;
        font-weight: bold !important;
    }
    
    body.print-receipt .receipt-title {
        font-size: 15px !important;
        font-weight: bold !important;
        margin: 3px 0 !important;
    }
    
    body.print-receipt .receipt-logo {
        max-width: 60mm !important;
        height: auto !important;
    }
    
    body.print-receipt .no-border td,
    body.print-receipt .no-border th {
        border: none !important;
    }
    
    body.print-receipt .border-top-dashed {
        border-top: 1px dashed #000 !important;
        padding-top: 4px !important;
    }
    
    body.print-receipt .border-bottom-dashed {
        border-bottom: 1px dashed #000 !important;
        padding-bottom: 4px !important;
    }
    
    body.print-receipt .font-small {
        font-size: 9px !important;
    }
    
    body.print-receipt .font-bold {
        font-weight: bold !important;
    }
    
    /* Page settings for receipt printer */
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
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title"> {{__('sales.sales_details_title')}}
                                <span class="pull-left">
                                    <a href="{{ route('sales.index') }}">
                                        <button class="btn mybtn bg-default">{{__('common.back')}}</button>
                                    </a>
                                </span>

                                <!-- A4 Print Button - uses your existing function -->
                                <button onclick="print_page_with_image()" class="pull-left btn btn-success btn-sm btn-border m-l-10 hidden-print">
                                   <i class="fas fa-print"></i> {{__('sales.print_bill')}} A4 
                                </button>  
                                
                                <!-- Receipt Printer Button -->
                                <button onclick="printReceipt()" class="pull-left btn btn-info btn-sm btn-border m-l-10 hidden-print">
                                   <i class="fas fa-receipt"></i> چاپ Receipt (80mm)
                                </button> 

                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                    
                                <!-- ============================================ -->
                                <!-- A4 PRINT AREA (Keep your existing structure) -->
                                <!-- ============================================ -->
                                <div class="container col-md-12 col-sm-12 col-xs-12 print-area-a4" id="print_area">
                                    <p class="">{{__('common.print_date')}}‌ : {{ $todaysDate ?? '' }}</p>
                                    <table style="width:100%">
                                       <tr class="" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="2">
                                               <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> {{__('common.sales_date')}}‌ : {{ $warehouseSales->first()->idate ?? '' }}</td>
                                            <td> {{__('sales.billno')}}‌ : {{ 'SALES_' . ($warehouseSales->first()->billno ?? '') }}
                                                <br />
                                                 {{__('common.factor')}} : {{ ($warehouseSales->first()->factor ?? '') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> {{__('sales.customer')}}‌ : {{ $warehouseSales->first()->accountRelation->name ?? '' }}</td>
                                            <td> {{__('common.user')}}‌ : {{ $warehouseSales->first()->user_name ?? '' }}</td>
                                        </tr>
                                    </table>

                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                              <thead>
                                                <tr>
                                                    <th>{{__('common.number')}}</th>
                                                    <th>{{__('sales.item')}}</th>
                                                    <th>{{__('buy.sold_amount')}}</th>
                                                    <th>{{__('sales.unit')}}</th>
                                                    <th>{{__('common.unit_price')}}</th>
                                                    @if($saved_with_tax) 
                                                    <th>{{__('buy.sales_tax_percentage')}}</th>
                                                    <th>{{__('buy.sell_tax_price')}}</th>
                                                    <th>{{__('buy.sell_up_vat')}}</th>
                                                    @endif
                                                    <th style="width:15%">{{__('common.total_price')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach($salesDetails as $key => $detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->preListRelation->name ?? ' '}}</td>
                                                    <td>{{ $detail->amount  }}</td>
                                                    <td>{{ $detail->unitRelation->name }}</td>
                                                    @if($saved_with_tax) 
                                                    <td>{{ number_format($detail->sell_up_no_tax,2) }}</td>
                                                    @else 
                                                    <td>{{ number_format($detail->sell_up,2) }}</td>
                                                    @endif
                                                     @if($saved_with_tax) 
                                                    <td>% {{ $detail->sell_tax_per }}</td>
                                                    <td>{{ number_format($detail->sell_tax_price,2) }}</td>
                                                    <td>{{ number_format($detail->sell_up,2) }}</td>
                                                    @endif
                                                    <td>{{ number_format($detail->total,2) }}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="{{ $not_col_for_print }}" rowspan="8" style="padding: 40px;">
                                                        <div class="col-md-12" style="border:2px dotted #999; min-height:80px;background-color:#f8f8f8;border-top-right-radius:10px; border-bottom-left-radius:10px; padding: 10px;">
                                                            {{__('buy.note')}} : {{ $orgbios[0]->note_for_print }}
                                                        </div>
                                                         <div class="col-md-12 m-t-20">
                                                              <br>
                                                             <strong>
                                                                 <h3>{{__('sales.stamp')}} ---------------------</h3>
                                                             </strong>
                                                         </div>
                                                    </td>
                                                    <td colspan="{{ $total_cols }}" class="price-section">{{__('buy.total_bill_price')}}</td>
                                                    <td class="price-section">
                                                        {{ number_format($warehouseSales->first()->total,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">{{__('buy.cur_pay_yet')}}</td>
                                                    <td class="price-section">
                                                        {{ number_format($warehouseSales->first()->cur_pay,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">{{__('buy.remained')}}</td>
                                                    <td class="price-section">
                                                        {{ number_format($warehouseSales->first()->remained,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">{{__('buy.old_remained')}}</td>
                                                    <td class="price-section">
                                                        {{ number_format($previousBalance,2) }}
                                                        {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="{{ $total_cols }}" class="price-section">{{__('buy.balance')}}</td>
                                                    <td class="price-section">
                                                       {{ number_format($netCustomerBalance, 2) }}
                                                       {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <p style="margin-bottom:20px; padding-bottom:20px;"></p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                              <thead>
                                                <tr style="width:100%; background-color:#fff !important;color:#000 !important;">
                                                    <td colspan="7">{{__('common.bill_payments')}}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{__('common.number')}}</th>
                                                    <th>{{__('sales.billno')}}</th>
                                                    <th>{{__('journal.receiver_account')}}</th>
                                                    <th>{{__('common.total_payment')}}</th>
                                                    <th>{{__('common.date')}}</th>
                                                    <th class="hidden-print">{{__('common.journal_code')}}</th>
                                                    <th>{{__('common.note')}}</th>
                                                    <th>{{__('common.user')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach($salesBillPayments as $key => $pay)
                                               @php 
                                                 $paymentsTotal += $pay->cur_pay;
                                               @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ 'SALES_'.$pay->billno ?? ' '}}</td>
                                                    <td>{{ $pay->account->name ?? '' }}</td>
                                                    <td>{{ number_format($pay->cur_pay,2) }}</td>
                                                    <td>{{ $pay->payment_date }}</td>
                                                    <td class="hidden-print">{{ $pay->journal_code }}</td>
                                                    <td>{{ $pay->note }}</td>
                                                    <td>{{ $pay->user_name }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="background-color:#eee;">
                                                    <td colspan="3">{{__('common.total')}}</td>
                                                    <td>{{ number_format($paymentsTotal, 2) }}</td>
                                                    <td></td>
                                                    <td class="hidden-print"></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    
                                    <div class="col-md-12 m-t-20">
                                        <br>
                                        <strong>
                                            <h3>{{__('sales.sign_and_stamp')}} ---------------------</h3>
                                        </strong>
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
                                            <h3 class="receipt-title">تموینات هاني حسن ابو عبدالله للمواد الغذائية</h3>
                                            <p style="margin: 2px 0;">لصاحبه: هاني حسن رضي آل أبو عبدالله</p>
                                            <!-- <p style="margin: 2px 0;">{{ $orgbios[0]->phone ?? '' }}</p> -->
                                            <!-- <p style="margin: 2px 0;">{{__('common.print_date')}} : {{ $todaysDate ?? '' }}</p> -->
                                            <!-- <div class="receipt-divider"></div> -->

                                            <table style="width:100%;">
                                            <tr>
                                                <td style="width:50%;"><strong>فاتورة تسليم المواد الغذائية</strong></td>
                                                <td style="width:50%;"><strong> تاريخ الطباعة: {{ $todaysDate ?? '' }}</strong></td>
                                            </tr>
                                            </table>
                                        </div>

                                        <!-- Bill Info -->
                                        <div style="margin: 5px 0;">
                                            <table style="width:100%;">
                                                <tr>
                                                    <td style="width:50%;"><strong>رقم فاتورة:</strong> S{{ str_pad($warehouseSales->first()->billno ?? 0, 3, '0', STR_PAD_LEFT) }}</td>
                                                    <td style="width:50%; text-align:left;"><strong>تاريخ التسليم: </strong> {{ $warehouseSales->first()->idate ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>المکرم: </strong> {{ $warehouseSales->first()->accountRelation->name ?? '' }}</td>
                                                    <td style="text-align:left;"><strong> المستخدم: </strong> {{ $warehouseSales->first()->user_name ?? '' }}</td>
                                                </tr>
                                                @if(($warehouseSales->first()->factor ?? ''))
                                                <tr>
                                                    <td colspan="2"><strong>{{__('common.factor')}}:</strong> {{ $warehouseSales->first()->factor ?? '' }}</td>
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
                                                    <th style="text-align:right; padding:2px;">الأصناف</th>
                                                    <th style="text-align:center; padding:2px;">Qty</th>
                                                    <th style="text-align:left; padding:2px;">Price {{ $saved_with_tax ? ' VAT ' : '' }} </th>
                                                    <th style="text-align:left; padding:2px;">Total {{ $saved_with_tax ? ' VAT ' : '' }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($salesDetails as $detail)
                                                <tr>
                                                    <td style="text-align:center; padding:2px;">{{ $loop->iteration }}</td>
                                                    <td style="text-align:right; padding:2px;">{{ $detail->preListRelation->name ?? ' '}}</td>
                                                    <td style="text-align:center; padding:2px;">{{ $detail->amount }}</td>
                                                    <td style="text-align:left; padding:2px;">
                                                        {{ number_format($detail->sell_up,2) }}
                                                    </td>
                                                    <td style="text-align:left; padding:2px;">{{ number_format($detail->total,2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div class="receipt-divider"></div>

                                        <!-- Totals -->
                                        <table style="width:100%;">
                                            <tr>
                                                <td style="width:65%;"><strong>الإجمالي: </strong></td>
                                                <td style="width:35%; text-align:left;"><strong>{{ number_format($warehouseSales->first()->total,2) }} {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>المدفوعات: </td>
                                                <td style="text-align:left;">{{ number_format($warehouseSales->first()->cur_pay,2) }} {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>المتبقي: </strong></td>
                                                <td style="text-align:left;"><strong>{{ number_format($warehouseSales->first()->remained,2) }} {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>المتبقي من السابق: </td>
                                                <td style="text-align:left;">{{ number_format($previousBalance,2) }} {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}</td>
                                            </tr>
                                            <tr style="border-top: 1px dashed #000;">
                                                <td><strong>الرصيد:</strong></td>
                                                <td style="text-align:left;"><strong>{{ number_format($netCustomerBalance, 2) }} {{ $warehouseSales->first()->currencyRelation->symbols ?? '' }}</strong></td>
                                            </tr>
                                        </table>

                                        <div class="receipt-divider"></div>

                                        <!-- Payments Section -->
                                        @if(count($salesBillPayments) > 0)
                                        <table style="width:100%;">
                                            <thead>
                                                <tr style="border-bottom: 1px dashed #000;">
                                                    <th style="text-align:center; padding:2px;">#</th>
                                                    <th style="text-align:right; padding:2px;">المبلغ المدفوع:</th>
                                                    <th style="text-align:right; padding:2px;">تاریخ الدفع</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $receiptPaymentsTotal = 0; @endphp
                                                @foreach($salesBillPayments as $pay)
                                                @php $receiptPaymentsTotal += $pay->cur_pay; @endphp
                                                <tr>
                                                    <td style="text-align:center; padding:2px;">{{ $loop->iteration }}</td>
                                                    <td style="text-align:right; padding:2px;">{{ number_format($pay->cur_pay,2) }}</td>
                                                    <td style="text-align:right; padding:2px;">{{ $pay->payment_date }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="border-top: 1px dashed #000;">
                                                    <td style="padding:2px;"><strong>الإجمالي:</strong></td>
                                                    <td style="padding:2px; text-align:right;"><strong>{{ number_format($receiptPaymentsTotal, 2) }}</strong></td>
                                                    <td style="padding:2px;"></td>
                                                </tr>
                                            </tfoot>
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
</div>

<script>
/**
 * Receipt Print Function - 80mm Thermal Printer
 * This creates a separate print window with receipt formatting
 */
function printReceipt() {
    // Get the receipt content
    var receiptElement = document.getElementById("print_area_receipt");
    var data = receiptElement.innerHTML;
    
    // Create print window for receipt
    var printWindow = window.open("", "ReceiptPrintWindow", "width=400,height=600");
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Receipt Print</title>
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
                
                .receipt-logo {
                    max-width: 60mm !important;
                    height: auto !important;
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

// Keep your existing print_page_with_image function as is
// It already works perfectly for A4 printing
</script>

@endsection