<!-- For Persian Date Picker -->
<script src="{{ asset('assets/datepicker/jalaali.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $('#input1').change(function() {  
        var $this = $(this), value = $this.val();  
        alert(value);
    });

    $('#textbox1').change(function () {  
        var $this = $(this), value = $this.val(); 
        alert(value); 
    });

    $('[data-name="disable-button"]').click(function() {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', true);
    });

    $('[data-name="enable-button"]').click(function () {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', false);
    });
</script>


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
        type: type, // Default, Primary, Secondary, Info, Success, Warning, Danger
        placement: {
            from: from, // top, bottom
            align: align // right, center, left
        },
        time: 500
    });
}
</script>

<script>
$(document).ready(function() {
    fetchList();

    // Move the filter button click event outside
    $('#btn-filter').click(function() {
        $('#warehouseItemTable').DataTable().ajax.reload(null, false); // Reload data without resetting pagination
    });
});


function fetchList() {
    let warehouseItemTable = $('#warehouseItemTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(warehouseItemTable)) {
        warehouseItemTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {  
                url: '{{ route("warehousesList.data") }}',
                // url: '{{ route("boughtList.data") }}',
                data: function (d) {
                    d.warehouse_id = $('#warehouse_id').val();
                    d.item_name = $('#item_name').val();
                    d.currency_id = $('#currency_id').val();
                    // alert(d.warehouse_id);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'prelist', name: 'prelist' },
                { data: 'currency', name: 'currency' },
                { data: 'unit', name: 'unit' },
                { data: 'in_amount', name: 'in_amount' },
                { data: 'out_amount', name: 'out_amount' }, 
                { data: 'available_amount', name: 'available_amount'},
                { data: 'bought_up', name: 'bought_up' },
                { data: 'avg_up', name: 'avg_up' },
                { data: 'sell_up', name: 'sell_up' },
                { data: 'available_total', name: 'available_total' },
                { data: 'view', name: 'view', orderable: false, searchable: false }
            ],
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
                $(api.column(7).footer()).html(sumColumn(7));
                $(api.column(8).footer()).html(sumColumn(8));
                $(api.column(9).footer()).html(sumColumn(9));
                $(api.column(10).footer()).html(sumColumn(10));
            }
        });

    } else {
        warehouseItemTable.DataTable().ajax.reload(null, false);
    }
}
</script>