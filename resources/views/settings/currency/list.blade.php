<div class="table-responsive" style="padding:5px;">
    <table id="currencyTable" class="table table-bordered table-striped table-hover datatable3">
        <thead>
            <tr>
                <th>شماره </th>
                <th> واحد پولی </th>										
                <th> سمبول / نماد </th>										
                <th> نمایش رنگ </th>		
                <th> بیس کرنسی </th>		
                <th>ویرایش </th>
                <th>حذف </th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCurrencyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">افزودن </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="currencyFormWrapper"></div>
                <div id="loading_modal_currency" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="addCurrencyBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditCurrencyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> ویرایش </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditCurrencyFormWrapper"></div>
                <div id="loading_modal_currency2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditCurrencyBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>



<!-- ===================== Belongs to add ========================= -->
<script type="text/javascript">

    // Show add modal 
    function showAddCurrencyForm()
    {
        $('#addCurrencyModal').modal('show');
        $('#loading_modal_currency').show();
        $.ajax({
                url: `/currency/create`,
                type: 'GET',
                success: (result) => {
                    $('#currencyFormWrapper').html(result);
                    $('#loading_modal_currency').hide();

                    // Initialize Select2 after the form has been injected
                    $(".select2").select2();
                },
                error: () => {
                    $('#loading_modal_currency').hide();
                    alert('اطلاعات یافت نشد');
                }
        });
    }

    // submit add form
    $('#addCurrencyBtn').on('click', function () 
    {
            // Serialize form data
            var formData = $('#currencyForm').serialize();

            // Show loading state
            $('#loading_modal_currency').show();

            // Clear previous error messages
            $('#currencyNameError').text('');
            $('#symbolsError').text('');
            $('#colorError').text('');
            $('#isBaseError').text('');


            // AJAX form submission
            $.ajax({
                url: '/currency/store', // The actual route for saving data
                type: 'POST',
                data: formData,
                success: (response) => {
                    $('#loading_modal_currency').hide();

                    if (response.status === 'success') {
                        // Call a function to refresh the warehouse list or update the UI
                        fetchCurrencyList(); // Ensure this function exists in your code
                        $('#addCurrencyModal').modal('hide');
                        showNotification('موفقانه ثبت گردید', 'success', 'top', 'right', 'withicon');
                    } else {
                        showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
                    }
                },
                error: (xhr) => {
                    $('#loading_modal_currency').hide();

                    // Handle validation errors
                    if (xhr.status === 422) { // Laravel validation error status code
                        var errors = xhr.responseJSON.errors;
                        if (errors?.name) {
                            $('#currencyNameError').text(errors.name[0]);
                        }
                        if (errors?.symbols) {
                            $('#symbolsError').text(errors.symbols[0]);
                        }
                        if (errors?.color) {
                            $('#colorError').text(errors.color[0]);
                        }
                        if (errors?.is_base) {
                            $('#isBaseError').text(errors.is_base[0]);
                        }
                    } else {
                        // General error handling
                        showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
                    }
                }
            });
  });
</script>



<!-- ===================== Belongs to Edit ========================= -->
<script type="text/javascript">
    // Open Modal for Editing
    $('table').on('click', '.editCurrency', function () {
        $('#EditCurrencyModal').modal('show');
        $('#loading_modal_currency').show();
        const currencyId = $(this).data('id');
        $.ajax({
            url: `/currency/${currencyId}`,
            type: 'GET',
            success: (result) => {
                $('#EditCurrencyFormWrapper').html(result);
                $('#loading_modal_currency2').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_modal_currency2').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    });
    
    // submit edit form 
    $('#EditCurrencyBtn').on('click', function () 
    {
        // Serialize form data
        var formData = $('#currencyEditForm').serialize();

        // Show loading state
        $('#loading_modal_currency2').show();

        // Clear previous error messages
        $('#unitNameError').text('');

        // AJAX form submission
        $.ajax({
            url: '/currency/update', // The actual route for saving data
            type: 'PATCH',
            data: formData,
            success: (response) => {
                $('#loading_modal_currency2').hide();

                if (response.status === 'success') {
                    // Call a function to refresh the warehouse list or update the UI
                    fetchCurrencyList(); // Ensure this function exists in your code
                    $('#EditCurrencyModal').modal('hide');
                    showNotification('موفقانه ویرایش گردید', 'success', 'top', 'right', 'withicon');
                } else {
                    showNotification('ویرایش نگردید', 'danger', 'top', 'right', 'withicon');
                }
            },
            error: (xhr) => {
                $('#loading_modal_currency2').hide();

                // Handle validation errors
                if (xhr.status === 422) { // Laravel validation error status code
                    var errors = xhr.responseJSON.errors;

                    if (errors?.name) {
                            $('#currencyNameError').text(errors.name[0]);
                        }
                        if (errors?.symbols) {
                            $('#symbolsError').text(errors.symbols[0]);
                        }
                        if (errors?.color) {
                            $('#colorError').text(errors.color[0]);
                        }
                        if (errors?.is_base) {
                            $('#isBaseError').text(errors.is_base[0]);
                        }
                } else {
                    // General error handling
                    showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
                }

            }
        });
  });
</script>



<!-- ===================== Belongs to Display and Delete ========================= -->
<script type="text/javascript">
// Fetch Warehouses List
function fetchCurrencyList() {
    const currencyTable = $('#currencyTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(currencyTable)) {
        // Initialize DataTable if not already initialized
        currencyTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("currency.list") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'name', name: 'name'},
                { data: 'symbols', name: 'symbols' },
                { data: 'color', name: 'color' },
                { data: 'is_base', name: 'is_base' },
                { data: 'edit', name: 'edit', searchable: false, orderable: false },
                { data: 'delete', name: 'delete', searchable: false, orderable: false },
           ]
        });
    } else {
        // If already initialized, reload the data
        currencyTable.DataTable().ajax.reload();
    }
}

// Delete Warehouse
$('table').on('click', '.deleteCurrency', function () {
    const id = $(this).data('id');
        if (id && confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/currency/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchCurrencyList();
                        showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    } else {
                       showNotification(response.message, 'danger', 'top', 'right', 'withicon');
                    //    alert(response.message);
                    }
                },
                error: () => {
                    showNotification(response.message, 'danger', 'top', 'right', 'withicon');
                }
            });
        }
});
</script>
