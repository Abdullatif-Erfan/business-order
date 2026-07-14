<script>
// In sales.scripts.blade.php
$(document).on('click', '.datepicker-icon', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $input = $(this).closest('.input-group').find('input');
    if ($input.length) {
        $input.datepicker('show');
    }
});

function showNotification(message, type = 'info', from = 'top', align = 'right', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> {{__('settings.message')}} </span>';
    
    if (style === "withicon") {
        content.icon = 'fa fa-bell';
    } else {
        content.icon = 'none';
    }
    content.url = '#';
    content.target = '_blank';

    $.notify(content, {
        type: type,
        placement: {
            from: from,
            align: align
        },
        time: 500
    });
}

$(document).ready(function() {
    fetchList();

    $('#btn-filter').click(function() {
        $('#salesTable').DataTable().ajax.reload(null, false);
    });

    $('#selectAll').on('click', function() {
        $('.row-checkbox').prop('checked', this.checked);
        toggleGenerateButton();
    });

    $(document).on('change', '.row-checkbox', function() {
        toggleGenerateButton();
    });
});

function toggleGenerateButton() {
    var checked = $('.row-checkbox:checked').length;
    if (checked > 0) {
        $('#generateInvoiceBtn').show();
    } else {
        $('#generateInvoiceBtn').hide();
    }
}

function fetchList() {
    let salesTable = $('#salesTable');

    if (!$.fn.DataTable.isDataTable(salesTable)) {
        salesTable.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'همه']
            ],
            ajax: {  
                url: '{{ route("sales.data") }}',
                data: function (d) {
                    d.customer_name = $('#customer_name').val();
                    d.currency_id = $('#currency_id').val();
                    d.bill_number = $('#bill_number').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
                error: function(xhr, status, error) {
                    console.error('DataTable Error:', error);
                    showNotification('خطا در بارگذاری داده‌ها', 'danger');
                }
            },
            columns: [
                { 
                    data: 'id', 
                    name: 'id',
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        var hasInvoice = parseInt(row.has_invoice) || 0;
                        if (hasInvoice === 1) {
                            return '<span class="badge badge-success" title="{{ __("buy.invoice_generated") }}"><i class="fas fa-check"></i></span>';
                        }
                        return '<input type="checkbox" class="row-checkbox" value="' + data + '">';
                    }
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'billno', name: 'billno' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'factor', name: 'factor' },
                { data: 'total', name: 'total' },
                { data: 'cur_pay', name: 'cur_pay' },
                { data: 'remained', name: 'remained' },
                { data: 'currency_name', name: 'currency_name' },
                { data: 'idate', name: 'idate' },
                { data: 'view', name: 'view', orderable: false, searchable: false }
            ],
            drawCallback: function () {
                var api = this.api();

                function sumColumn(index) {
                    return api
                        .column(index, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            var numA = parseFloat(a?.toString().replace(/,/g, '')) || 0;
                            var numB = parseFloat(b?.toString().replace(/,/g, '')) || 0;
                            return numA + numB;
                        }, 0)
                        .toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                // Column indices: 0:checkbox, 1:DT_RowIndex, 2:billno, 3:customer_name, 4:factor
                // 5:total, 6:cur_pay, 7:remained, 8:currency_name, 9:idate, 10:view
                $(api.column(5).footer()).html(sumColumn(5)); // total
                $(api.column(6).footer()).html(sumColumn(6)); // cur_pay
                $(api.column(7).footer()).html(sumColumn(7)); // remained
            }
        });
    } else {
        salesTable.DataTable().ajax.reload(null, false);
    }
}

// Generate Invoice
$('#generateInvoiceBtn').on('click', function() {
    var selectedIds = [];
    $('.row-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    
    if (selectedIds.length === 0) {
        showNotification('{{ __("buy.select_at_least_one") }}', 'warning');
        return;
    }
    
    if (confirm('{{ __("buy.confirm_generate_invoice") }}')) {
        $.ajax({
            url: '{{ route("sales.generateInvoice") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                sold_item_ids: selectedIds
            },
            success: function(response) {
                if (response.status === 'success') {
                    showNotification(response.message, 'success');
                    window.location.href = '{{ url("sales/invoice") }}/' + response.invoice_id;
                } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function(xhr) {
                showNotification('{{ __("common.error_occurred") }}', 'danger');
            }
        });
    }
});

</script>