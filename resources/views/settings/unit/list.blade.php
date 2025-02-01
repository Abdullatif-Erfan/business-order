<div class="table-responsive" style="padding:5px;">
    <div id="loading3" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="unitTable" class="table table-bordered table-striped table-hover datatable3">
        <thead>
            <tr>
                <th>شماره</th>
                <th>نام</th>
                <th>ویرایش</th>
                <th>حذف</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div id="pagination3" style="text-align: center;"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">افزودن </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="unitFormWrapper"></div>
                <div id="loading_modal_unit" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="addUnitBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditUnitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> ویرایش </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditUnitFormWrapper"></div>
                <div id="loading_modal_unit2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditUnitBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>



<!-- ===================== Belongs to add ========================= -->
<script type="text/javascript">

    function showAddUnitForm()
    {
        $('#addUnitModal').modal('show');
        $('#loading_modal_unit').show();
        $.ajax({
                url: `/units/create`,
                type: 'GET',
                success: (result) => {
                    $('#unitFormWrapper').html(result);
                    $('#loading_modal_unit').hide();

                    // Initialize Select2 after the form has been injected
                    $(".select2").select2();
                },
                error: () => {
                    $('#loading_modal_unit').hide();
                    alert('اطلاعات یافت نشد');
                }
        });
    }

    $('#addUnitBtn').on('click', function () {
    // Serialize form data
    var formData = $('#unitForm').serialize();

    // Show loading state
    $('#loading_modal_unit').show();

    // Clear previous error messages
    $('#unitNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/units/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        success: (response) => {
            $('#loading_modal_unit').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchUnitList(); // Ensure this function exists in your code
                $('#addUnitModal').modal('hide');
                showNotification('موفقانه ثبت گردید', 'success', 'top', 'right', 'withicon');
            } else {
                showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_unit').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#unitNameError').text(errors.name[0]);
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
    $('table').on('click', '.editUnit', function () {
        $('#EditUnitModal').modal('show');
        $('#loading_modal_unit').show();
        const unitId = $(this).data('id');
        $.ajax({
            url: `/units/${unitId}`,
            type: 'GET',
            success: (result) => {
                $('#EditUnitFormWrapper').html(result);
                $('#loading_modal_unit2').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_modal_unit2').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    });
    
    // submit edit form 
    $('#EditUnitBtn').on('click', function () {
    // Serialize form data
    var formData = $('#unitEditForm').serialize();

    // Show loading state
    $('#loading_modal_unit2').show();

    // Clear previous error messages
    $('#unitNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/units/update', // The actual route for saving data
        type: 'PATCH',
        data: formData,
        success: (response) => {
            $('#loading_modal_unit2').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchUnitList(); // Ensure this function exists in your code
                $('#EditUnitModal').modal('hide');
                showNotification('موفقانه ویرایش گردید', 'success', 'top', 'right', 'withicon');
            } else {
                showNotification('ویرایش نگردید', 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_unit2').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#unitNameError').text(errors.name[0]);
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
function fetchUnitList() {
    const unitTable = $('#unitTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(unitTable)) {
        // Initialize DataTable if not already initialized
        unitTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("units.list") }}',
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
        unitTable.DataTable().ajax.reload();
    }
}

// Delete Warehouse
$('table').on('click', '.deleteUnit', function () {
    const id = $(this).data('id');
        if (id && confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/units/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchUnitList();
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
