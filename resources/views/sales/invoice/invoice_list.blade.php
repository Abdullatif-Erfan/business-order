@extends('layouts.app')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px; text-align:center;">
                            <input type="hidden" id="tax_activation" value="{{ $tax->tax_activation }}">
                            <span class="card-title">{{ __('buy.invoices') }}</span>
                            <span class="pull-left">
                                <a href="{{ url('sales') }}">
                                    <button class="btn mybtn bg-default"> {{ __('common.back') }} </button>
                                </a>
                            </span>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="invoiceTable" class="display responsive nowrap table table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('common.number') }}</th>
                                            <th>{{ __('buy.invoice_number') }}</th>
                                            <th>{{ __('order.supplier_name') }}</th>
                                            <th>{{ __('common.total_price') }}</th>
                                            <th>{{ __('buy.paid_amount') }}</th>
                                            <th>{{ __('buy.remaining_amount') }}</th>
                                            <th>{{ __('order.status') }}</th>
                                            <th>{{ __('buy.invoice_date') }}</th>
                                            <th>{{ __('common.details') }}</th>
                                        </tr>
                                    </thead>
                                </table>
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
    fetchInvoices();
});

function fetchInvoices() {
    $('#invoiceTable').DataTable({
        serverSide: true,
        processing: true,
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'همه']
        ],
        responsive: true,
        autoWidth: false,
        ajax: {
            url: '{{ route("sales.invoiceData") }}',
            type: 'GET',
            data: function (d) {
                d.tax_activation = $('#tax_activation').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
            { data: 'invoice_number', name: 'invoice_number' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'total', name: 'total' },
            { data: 'paid_amount', name: 'paid_amount' },
            { data: 'remaining', name: 'remaining' },
            { data: 'status', name: 'status' },
            { data: 'invoice_date', name: 'invoice_date' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
}
</script>
@endsection