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
function showNotification(message, type = 'info', from = 'top', align = 'right', style = 'withicon') {
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
        $('#clearanceTable').DataTable().ajax.reload(null, false); // Reload data without resetting pagination
    });
});


function fetchList() {
    let clearanceTable = $('#clearanceTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(clearanceTable)) {
        clearanceTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {  
                url: '{{ route("clearance.sales.data") }}',
                // url: '{{ route("boughtList.data") }}',
                data: function (d) {
                    d.customer_name = $('#customer_name').val();
                    d.currency_id = $('#currency_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();  
                    // alert(d.warehouse_id);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'type', name: 'type' },
                { data: 'to_account.name', name: 'to_account.name' },
                { data: 'total', name: 'total' },
                { data: 'currency.name', name: 'currency.name' },
                { data: 'bill_numbers', name: 'bill_numbers'},
                { data: 'clearedBy', name: 'clearedBy' },
                { data: 'dates', name: 'dates' },
            ],
            drawCallback: function () 
            {
                var api = this.api();

                // Helper function for the modulo operation to check if it's an integer
                function fmod(a, b) {
                    return a - (b * Math.floor(a / b));
                }

                function sumColumn(index) {
                    return api
                        .column(index, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            var numA = parseFloat(a.toString().replace(/,/g, '')) || 0;
                            var numB = parseFloat(b.toString().replace(/,/g, '')) || 0;
                            var sum = numA + numB;

                            // Format the sum based on whether it has decimals
                            if (fmod(sum, 1) === 0) {
                                return sum.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                            } else {
                                return sum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }

                        }, 0)
                        .toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                $(api.column(3).footer()).html(sumColumn(3));
            }

        });

    } else {
        clearanceTable.DataTable().ajax.reload(null, false);
    }
}

function showSalesModal()
{
    $('#salesModal').modal('show');
}

function submitSalesClearanceForm() {
    var currency_id = parseInt($('#sales_currency_id').val()) || 0;
    var sales_to_account_id = parseInt($('#sales_to_account_id').val()) || 0;

    if (currency_id > 0 && sales_to_account_id > 0) {
        // Use template literals to correctly form the route
        var url = "{{ route('clearance.sales.create', ['currency_id' => '__CURRENCY_ID__', 'sales_to_account_id' => '__BUY_TO_ACCOUNT_ID__']) }}"
            .replace('__CURRENCY_ID__', currency_id)
            .replace('__BUY_TO_ACCOUNT_ID__', sales_to_account_id);

        window.location.href = url;
    } else {
        alert('لطفا مشتری  و واحد پولی را انتخاب نمایید');
    }
}

</script>