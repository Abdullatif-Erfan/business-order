<!-- For Persian Date Picker -->
<script>
function showNotification(message, type = 'info', from = 'top', align = 'left', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> پیام </span>';
    
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
</script>
<script>
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
$(document).ready(function() {
    fetchList();

    $('#btn-filter').click(function() {
        $('#warehouseItemTable').DataTable().ajax.reload(null, false);
    });
});

function fetchList() {
    var flag = parseInt($('#tax_activation').val()) || 0;
    var warehouseItemTable = $('#warehouseItemTable');

    // Define columns based on flag
    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
        { data: 'prelist', name: 'prelist' },
        { data: 'in_amount', name: 'in_amount' },
        { data: 'out_amount', name: 'out_amount' }, 
        { data: 'available_amount', name: 'available_amount'},
        { data: 'unit', name: 'unit' },
        { data: 'buy_up', name: 'buy_up' }
    ];

    if (flag === 1) {
        columns.push(
            { data: 'buy_tax_per', name: 'buy_tax_per' },
            { data: 'buy_tax_price', name: 'buy_tax_price' },
            { data: 'buy_up_vat', name: 'buy_up_vat' }
        );
    }

    columns.push(
        { data: 'available_total', name: 'available_total' },
        { data: 'sell_up', name: 'sell_up' },
        { data: 'idate', name: 'idate', orderable: false, searchable: false }
    );

    if (!$.fn.DataTable.isDataTable(warehouseItemTable)) {
        warehouseItemTable.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,   
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'همه']
            ],
            ajax: {  
                url: '{{ route("warehousesList.data") }}',
                data: function (d) {
                    d.tax_activation = $('#tax_activation').val();
                    d.item_name = $('#item_name').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: columns,
            // ✅ FIX: Ensure processing is hidden on load
            processing: true,
            drawCallback: function () {
                var api = this.api();
                
                // ✅ FIX: Check if data exists
                var pageData = api.rows({ page: 'current' }).data();
                if (!pageData || pageData.length === 0) {
                    // Hide processing and clear footer
                    $('#warehouseItemTable_processing').hide();
                    return;
                }

                function safeSum(index) {
                    var data = api.column(index, { page: 'current' }).data();
                    if (!data || data.length === 0) return '0.00';
                    
                    var sum = 0;
                    data.each(function(value) {
                        // ✅ FIX: Handle null/undefined/empty
                        if (value !== null && value !== undefined && value !== '') {
                            var num = parseFloat(value.toString().replace(/,/g, '')) || 0;
                            sum += num;
                        }
                    });
                    
                    return sum.toLocaleString(undefined, { 
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2 
                    });
                }

                // ✅ FIX: Clear and update footer
                if (flag === 1) {
                    $(api.column(6).footer()).html(safeSum(6));
                    $(api.column(7).footer()).html(safeSum(7));
                    $(api.column(8).footer()).html(safeSum(8));
                    $(api.column(9).footer()).html(safeSum(9));
                    $(api.column(10).footer()).html(safeSum(10));
                    $(api.column(11).footer()).html(safeSum(11));
                } else {
                    $(api.column(6).footer()).html(safeSum(6));
                    $(api.column(7).footer()).html(safeSum(7));
                    $(api.column(8).footer()).html(safeSum(8));
                }
                
                // ✅ FIX: Hide processing
                $('#warehouseItemTable_processing').hide();
            },
            language: {
                emptyTable: 'داده‌ای موجود نیست',
                zeroRecords: 'داده‌ای یافت نشد',
                processing: '<i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...'
            }
        });

    } else {
        warehouseItemTable.DataTable().ajax.reload(null, false);
    }
}
</script>