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
        if ($.fn.DataTable.isDataTable('#warehouseItemTable')) {
            $('#warehouseItemTable').DataTable().ajax.reload(null, false);
        }
    });

    // Enter key search
    $('#item_name, #start_date, #end_date').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn-filter').click();
        }
    });
});

function fetchList() {
    var flag = parseInt($('#tax_activation').val()) || 0;
    var table = $('#warehouseItemTable');

    // =============================================
    // DEFINE COLUMNS BASED ON TAX ACTIVATION
    // =============================================
    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
        { data: 'prelist', name: 'prelist' },
        { data: 'in_amount', name: 'in_amount' },
        { data: 'out_amount', name: 'out_amount' }, 
        { data: 'available_amount', name: 'available_amount'},
        { data: 'unit', name: 'unit' },
        { data: 'buy_up', name: 'buy_up' }
    ];

    // Add tax columns if tax is enabled
    if (flag === 1) {
        columns.push(
            { data: 'buy_tax_per', name: 'buy_tax_per' },
            { data: 'buy_tax_price', name: 'buy_tax_price' },
            { data: 'buy_up_vat', name: 'buy_up_vat' }
        );
    }

    // Add remaining columns
    columns.push(
        { data: 'available_total', name: 'available_total' },
        { data: 'sell_up', name: 'sell_up' },
        { data: 'idate', name: 'idate', orderable: false, searchable: false }
    );

    // =============================================
    // INITIALIZE DATATABLE
    // =============================================
    if (!$.fn.DataTable.isDataTable(table)) {
        table.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'همه']
            ],
            ajax: {
                url: '{{ route("warehousesList.data") }}',
                data: function(d) {
                    d.tax_activation = $('#tax_activation').val();
                    d.item_name = $('#item_name').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
                complete: function () {
                    $('#warehouseItemTable_processing').hide();
                },
                error: function(xhr, status, error) {
                    $('#warehouseItemTable_processing').hide();
                    console.log(xhr.responseText);
                }
            },
            initComplete: function () {
                $('#warehouseItemTable_processing').hide();
            },
            columns: columns,
            order: [[1, 'desc']],
            
        });
    } else {
        table.DataTable().ajax.reload(null, false);
    }
}
</script>