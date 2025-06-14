<div class="table-responsive" style="padding:5px;">
    <div id="loading3" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="expenseTypeTable" class="table table-bordered table-striped table-hover datatable3">
        <thead>
            <tr>
                <th>{{__('common.number')}}</th>
                <th>نام</th>
                <th>{{__('common.edit')}}</th>
                <th>{{__('common.delete')}}</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div id="pagination3" style="text-align: center;"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addExpenseTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">افزودن </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="expenseTypeFormWrapper"></div>
                <div id="loading_modal_expense_type" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="addExpenseTypeBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditExpenseTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> ویرایش </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditExpenseTypeFormWrapper"></div>
                <div id="loading_modal_expense_type2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditExpenseTypeBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>



<!-- ===================== Belongs to add ========================= -->
<script type="text/javascript">

    function showAddExpenseTypeForm()
    {
        $('#addExpenseTypeModal').modal('show');
        $('#loading_modal_expense_type').show();
        $.ajax({
                url: `/etype/create`,
                type: 'GET',
                success: (result) => {
                    $('#expenseTypeFormWrapper').html(result);
                    $('#loading_modal_expense_type').hide();

                    // Initialize Select2 after the form has been injected
                    $(".select2").select2();
                },
                error: () => {
                    $('#loading_modal_expense_type').hide();
                    alert('اطلاعات یافت نشد');
                }
        });
    }

    $('#addExpenseTypeBtn').on('click', function () {
    // Serialize form data
    var formData = $('#expenseTypeForm').serialize();

    // Show loading state
    $('#loading_modal_expense_type').show();

    // Clear previous error messages
    $('#expenseTypeNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/etype/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        success: (response) => {
            $('#loading_modal_expense_type').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchExpenseTypeList(); // Ensure this function exists in your code
                $('#addExpenseTypeModal').modal('hide');
                showNotification('موفقانه ثبت گردید', 'success', 'top', 'right', 'withicon');
            } else {
                showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_expense_type').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#expenseTypeNameError').text(errors.name[0]);
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
    $('table').on('click', '.editExpenseType', function () {
        $('#EditExpenseTypeModal').modal('show');
        $('#loading_modal_expense_type').show();
        const typeId = $(this).data('id');
        $.ajax({
            url: `/etype/${typeId}`,
            type: 'GET',
            success: (result) => {
                $('#EditExpenseTypeFormWrapper').html(result);
                $('#loading_modal_expense_type2').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_modal_expense_type2').hide();
                $('#EditExpenseTypeModal').modal('hide');
                alert('اطلاعات یافت نشد / عدم صلاحیت ویرایش ریکاردهای دیگران');
            }
        });
    });
    
    // submit edit form 
    $('#EditExpenseTypeBtn').on('click', function () {
    // Serialize form data
    var formData = $('#expenseTypeEditForm').serialize();

    // Show loading state
    $('#loading_modal_expense_type2').show();

    // Clear previous error messages
    $('#expenseTypeNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/etype/update', // The actual route for saving data
        type: 'PATCH',
        data: formData,
        success: (response) => {
            $('#loading_modal_expense_type2').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchExpenseTypeList(); // Ensure this function exists in your code
                $('#EditExpenseTypeModal').modal('hide');
                showNotification('موفقانه ویرایش گردید', 'success', 'top', 'right', 'withicon');
            } else {
                showNotification('ویرایش نگردید', 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_expense_type2').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#expenseTypeNameError').text(errors.name[0]);
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
function fetchExpenseTypeList() {
    const expenseTypeTable = $('#expenseTypeTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(expenseTypeTable)) {
        // Initialize DataTable if not already initialized
        expenseTypeTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("etype.list") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'edit', name: 'edit', searchable: false, orderable: false },
                { data: 'delete', name: 'delete', searchable: false, orderable: false },
           ]
        });
    } else {
        // If already initialized, reload the data
        expenseTypeTable.DataTable().ajax.reload();
    }
}

// Delete Warehouse
$('table').on('click', '.deleteExpenseType', function () {
    const id = $(this).data('id');
        if (id && confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/etype/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchExpenseTypeList();
                        showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    } else {
                       showNotification('حذف نگردید', 'danger', 'top', 'right', 'withicon');
                       alert(response.message);
                    }
                },
                error: () => {
                    showNotification('حذف نگردید', 'danger', 'top', 'right', 'withicon');
                }
            });
        }
});
</script>
