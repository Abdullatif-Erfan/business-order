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

    // Move the filter button click event outside
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
        { data: 'buy_up', name: 'buy_up' },
        { data: 'available_total', name: 'available_total' }  // <-- MOVED HERE
    ];

    // Add tax columns if flag is 1
    if (flag === 1) {
        columns.push(
            { data: 'buy_tax_per', name: 'buy_tax_per' },
            { data: 'buy_tax_price', name: 'buy_tax_price' },
            { data: 'buy_up_vat', name: 'buy_up_vat' }
        );
    }

    // Add remaining columns
    columns.push(
        { data: 'sell_up', name: 'sell_up' },
        { data: 'idate', name: 'idate', orderable: false, searchable: false }
    );

    // Check if DataTable is already initialized
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
            drawCallback: function () {
                var api = this.api();

                function sumColumn(index) {
                    return api
                        .column(index, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            var numA = parseFloat(a.toString().replace(/,/g, '')) || 0;
                            var numB = parseFloat(b.toString().replace(/,/g, '')) || 0;
                            return numA + numB;
                        }, 0)
                        .toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                // Calculate column indices based on flag
                if (flag === 1) {
                    // With tax columns (11 total columns: 0-10)
                    // Index 0: DT_RowIndex
                    // Index 1: prelist
                    // Index 2: in_amount
                    // Index 3: out_amount
                    // Index 4: available_amount
                    // Index 5: unit
                    // Index 6: buy_up
                    // Index 7: available_total      <-- UPDATED
                    // Index 8: buy_tax_per
                    // Index 9: buy_tax_price
                    // Index 10: buy_up_vat
                    // Index 11: sell_up
                    // Index 12: idate
                    
                    $(api.column(6).footer()).html(sumColumn(6));  // buy_up
                    $(api.column(7).footer()).html(sumColumn(7));  // available_total
                    $(api.column(9).footer()).html(sumColumn(9));  // buy_tax_price
                    $(api.column(10).footer()).html(sumColumn(10)); // buy_up_vat
                    $(api.column(11).footer()).html(sumColumn(11)); // sell_up
                } else {
                    // Without tax columns (9 total columns: 0-8)
                    // Index 0: DT_RowIndex
                    // Index 1: prelist
                    // Index 2: in_amount
                    // Index 3: out_amount
                    // Index 4: available_amount
                    // Index 5: unit
                    // Index 6: buy_up
                    // Index 7: available_total      <-- UPDATED
                    // Index 8: sell_up
                    // Index 9: idate
                    
                    $(api.column(6).footer()).html(sumColumn(6));  // buy_up
                    $(api.column(7).footer()).html(sumColumn(7));  // available_total
                    $(api.column(8).footer()).html(sumColumn(8));  // sell_up
                }
            }
        });

    } else {
        warehouseItemTable.DataTable().ajax.reload(null, false);
    }
}
</script>