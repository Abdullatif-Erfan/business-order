<div class="table-responsive" style="padding:5px;">
    <div id="loading2" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="warehouseTable" class="table table-bordered table-striped table-hover datatable2">
        <thead>
            <tr>
                <th>شماره</th>
                <th>نام</th>
                <th>شعبه مربوطه</th>
                <th>مسول گدام</th>
                <th>آدرس</th>
                <th>ویرایش</th>
                <th>حذف</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div id="pagination2" style="text-align: center;"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addWarehouse" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">افزودن / ویرایش گدام</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="warehouseFormWrapper"></div>
                <div id="loading_modal2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="addWarehouseBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditWarehouseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> ویرایش گدام</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditWarehouseFormWrapper"></div>
                <div id="loading_modal22" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditWarehouseBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>



<!-- ===================== Belongs to add ========================= -->
<script type="text/javascript">

    function showAddWarehouseForm()
    {
        $('#addWarehouse').modal('show');
        $('#loading_modal2').show();
        $.ajax({
                url: `/warehouses/create`,
                type: 'GET',
                success: (result) => {
                    $('#warehouseFormWrapper').html(result);
                    $('#loading_modal2').hide();

                    // Initialize Select2 after the form has been injected
                    $(".select2").select2();
                },
                error: () => {
                    $('#loading_modal2').hide();
                    alert('اطلاعات یافت نشد');
                }
        });

    }

    $('#addWarehouseBtn').on('click', function () {
    // Serialize form data
    var formData = $('#warehouseForm').serialize();

    // Show loading state
    $('#loading_modal2').show();

    // Clear previous error messages
    $('#wHnameError').text('');
    $('#branchError').text('');
    $('#responsibleError').text('');
    $('#addressError').text('');

    // AJAX form submission
    $.ajax({
        url: '/warehouses/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        success: (response) => {
            $('#loading_modal2').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchWarehouseList(); // Ensure this function exists in your code
                $('#addWarehouse').modal('hide');
                showNotification('موفقانه ثبت گردید', 'success', 'top', 'right', 'withicon');
            } else {
                showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal2').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#wHnameError').text(errors.name[0]);
                }
                if (errors?.branch_id) {
                    $('#branchError').text(errors.branch_id[0]);
                }
                if (errors?.responsible) {
                    $('#responsibleError').text(errors.responsible[0]);
                }
                if (errors?.address) {
                    $('#addressError').text(errors.address[0]);
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
    $('table').on('click', '.editWarehouse', function () {
        $('#EditWarehouseModal').modal('show');
        $('#loading_modal2').show();
        const warehouseId = $(this).data('id');
        $.ajax({
            url: `/warehouses/${warehouseId}`,
            type: 'GET',
            success: (result) => {
                $('#EditWarehouseFormWrapper').html(result);
                $('#loading_modal22').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_modal22').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    });
    
    // submit edit form 
    $('#EditWarehouseBtn').on('click', function () {
    // Serialize form data
    var formData = $('#warehouseEditForm').serialize();

    // Show loading state
    $('#loading_modal22').show();

    // Clear previous error messages
    $('#wHnameError').text('');
    $('#branchError').text('');
    $('#responsibleError').text('');
    $('#addressError').text('');

    // AJAX form submission
    $.ajax({
        url: '/warehouses/update', // The actual route for saving data
        type: 'PATCH',
        data: formData,
        success: (response) => {
            $('#loading_modal22').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchWarehouseList(); // Ensure this function exists in your code
                $('#EditWarehouseModal').modal('hide');
                showNotification('موفقانه ویرایش گردید', 'success', 'top', 'right', 'withicon');
            } else {
                showNotification('ویرایش نگردید', 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal22').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#wHnameError').text(errors.name[0]);
                }
                if (errors?.branch_id) {
                    $('#branchError').text(errors.branch_id[0]);
                }
                if (errors?.responsible) {
                    $('#responsibleError').text(errors.responsible[0]);
                }
                if (errors?.address) {
                    $('#addressError').text(errors.address[0]);
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
function fetchWarehouseList() {
    const warehouseTable = $('#warehouseTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(warehouseTable)) {
        // Initialize DataTable if not already initialized
        warehouseTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("warehouses.index") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'branch.name', name: 'branch.name' },
                { data: 'responsible', name: 'responsible' },
                { data: 'address', name: 'address' },
                { data: 'edit', name: 'edit', searchable: false, orderable: false },
                { data: 'delete', name: 'delete', searchable: false, orderable: false },
           ]
        });
    } else {
        // If already initialized, reload the data
        warehouseTable.DataTable().ajax.reload();
    }
}

// Delete Warehouse
$('table').on('click', '.deleteWarehouse', function () {
    const id = $(this).data('id');
        if (id && confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/warehouses/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchWarehouseList();
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
