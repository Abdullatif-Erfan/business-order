<script>

    $(document).on('show.bs.dropdown', '.dropdown', function() {
    var $menu = $(this).find('.dropdown-menu');
    var $button = $(this).find('.dropdown-toggle');
    var buttonBottom = $button.offset().top + $button.outerHeight();
    var windowHeight = $(window).height();
    var menuHeight = $menu.outerHeight();

    // If not enough space below, open upward
    if (buttonBottom + menuHeight > windowHeight) {
        $(this).addClass('dropup');
    } else {
        $(this).removeClass('dropup');
    }
});

$(document).ready(function () {
       // Initialize datepicker
    $('.datepicker-input').datepicker({
        format: 'yyyy-mm-dd', // Match your database format
        autoclose: true,
        todayHighlight: true,
        clearBtn: true
    });
});


$(document).on('click', '.datepicker-icon', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $input = $(this).closest('.input-group').find('input');
    if ($input.length) {
        $input.datepicker('show');
    }
});
</script>
<script>
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

    // =========================================
    // ENTER KEY SEARCH
    // =========================================
    $('.filter-section input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#btn-filter').click();
        }
    });

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
                        var remained = parseInt(row.remained) || 0;
                        if (hasInvoice === 1) {
                            return '<span class="badge badge-success" title="{{ __("buy.invoice_generated") }}"><i class="fas fa-check"></i></span>';
                        } 
                        if(remained <= 0) {
                            return '<span class="badge" title="{{ __("buy.invoice_generated") }}"><i class="fas fa-check"></i></span>';
                        }
                        return '<input type="checkbox" class="row-checkbox" value="' + data + '">';
                    }
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'billno', name: 'billno' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'total', name: 'total' },
                { data: 'cur_pay', name: 'cur_pay' },
                { data: 'remained', name: 'remained' },
                { data: 'currency_name', name: 'currency_name' },
                { data: 'idate', name: 'idate' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className:"hidden-print" }
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
                $(api.column(4).footer()).html(sumColumn(4)); // total
                $(api.column(5).footer()).html(sumColumn(5)); // cur_pay
                $(api.column(6).footer()).html(sumColumn(6)); // remained
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


// Show Items in modal
   $('table').on('click', '.itemList', function () {
        $('#itemListModal').modal('show');
        $('#itemListModalLoader').show();
        const billno = $(this).data('id');
        $.ajax({
            url: `/sales/getListOfItemsToShowInModal/${billno}`,
            type: 'GET',
            success: (result) => {
                $('#itemListModalContent').html(result);
                $('#itemListModalLoader').hide();
            },
            error: () => {
                $('#itemListModalLoader').hide();
                alert('اطلاعات یافت نشد');
            }
        });
});

// Sales Bill
$('table').on('click', '.billPayment', function () {
    var billno = $(this).data('id');
    var hasInvoice = $(this).data('id2');
    var remained = $(this).data('id3');
    
    // Check if invoice exists 
    if (hasInvoice == 1) {
        showNotification('از این فروش انوایس ایجاد گردیده است و پرداخت از طریق بل صورت نمیگیرد', 'danger');
        return; // Exit the function
    }

    if (remained <= 0) {
        showNotification('پرداخت تکمیل گردیده است', 'success');
        return; // Exit the function
    }
    
    $('#billPaymentModal').modal('show');
    $('#billPaymentModalLoader').show();
    $('#billPaymentModalContent').html('');
    
    $.ajax({
        url: `/sales/billPayment/${billno}`,
        type: 'GET',
        success: function(result) {
            $('#billPaymentModalContent').html(result);
            $('#billPaymentModalLoader').hide();
        },
        error: function() {
            $('#billPaymentModalLoader').hide();
            alert('اطلاعات یافت نشد');
        }
    });
});

</script>