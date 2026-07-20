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
    $('#car_name, #item_name, #start_date, #end_date').on('keypress', function(e) {
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
        { data: 'carName', name: 'carName' },
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
        { data: 'total', name: 'total' },
        { data: 'sell_up', name: 'sell_up' },
        { data: 'idate', name: 'idate', orderable: false, searchable: false },
        { data: 'transfer', name: 'transfer', orderable:false, searchable:false}
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
                    d.car_name = $('#car_name').val();
                    d.tax_activation = $('#tax_activation').val();
                    d.item_name = $('#item_name').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.availability_options = $('#availability_options').val();
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

                $(api.column(8).footer()).html(sumColumn(8));
                $(api.column(9).footer()).html(sumColumn(9));
            },
            
        });
    } else {
        table.DataTable().ajax.reload(null, false);
    }
}

// transferItems
$(document).on('click', '.transferItems', function() {
    var $this = $(this);
    var id = $this.data('id');
    $('#editModal').modal('show');
    $('#loading').show();
    $.ajax({
        url: `/warehousesList/getWarehouseItemForTransfer/${id}`,
        type: 'GET',
        success: (result) => {
            $('#ModalContent').html(result);
            $('#loading').hide();

            // Initialize Select2 after the form has been injected
            $(".select2").select2();
        },
        error: () => {
            $('#loading').hide();
            alert('اطلاعات یافت نشد');
        }
    });
});



$(document).ready(function() {
    $('#submitTransfer').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{__("common.loading")}}...');
        
        var formData = {
            id: $('input[name="id"]').val(),
            source_warehouse_id: $('input[name="source_warehouse_id"]').val(),
            car_id: $('#car_id').val(),
            amount: $('#amount').val(),
            unit_id: $('input[name="unit_id"]').val(),
            item_name: $('#item_name').val(),
            _token: '{{ csrf_token() }}'
        };
        
        // Validate
        if (!formData.car_id) {
            alert('{{__("buy.select_car")}}');
            $btn.prop('disabled', false).html(originalText);
            return;
        }
        
        if (!formData.amount || parseFloat(formData.amount) <= 0) {
            alert('{{__("wh.enter_valid_amount")}}');
            $btn.prop('disabled', false).html(originalText);
            return;
        }
        
        $.ajax({
            url: '{{ route("warehousesList.updateTransfer") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $btn.prop('disabled', false).html(originalText);
                if (response.status === 'success') {
                    showNotification(response.message, 'success');
                     $('#editModal').modal('hide');
                     $('#loading').hide();
                      fetchList();
                    // Reload page or redirect
                    // setTimeout(function() {
                    //     window.location.href = '{{ route("warehousesList.details", $warehouseItems->id ?? 0) }}';
                    // }, 1500);
                } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    $.each(errors, function(key, messages) {
                        errorMessages.push(messages[0]);
                    });
                    alert(errorMessages.join('\n'));
                } else {
                    showNotification('{{__("common.error_occurred")}}', 'danger');
                }
            }
        });
    });
});
</script>