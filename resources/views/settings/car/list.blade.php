<div class="table-responsive" style="padding:5px;">
    <div id="loading3" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="carTable" class="table table-bordered table-striped table-hover datatable3">
        <thead>
            <tr>
                <th> {{__('common.number')}}</th>
                <th> {{__('common.name')}}</th>
                <th> {{__('common.edit')}}</th>
                <th> {{__('common.delete')}}</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div id="pagination3" style="text-align: center;"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{__('common.add')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="carFormWrapper"></div>
                <div id="loading_modal_car" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i>  {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> {{__('common.close')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="addCarBtn"> {{__('common.save')}}</button>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditCarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">  {{__('common.edit')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditCarFormWrapper"></div>
                <div id="loading_modal_car2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i>  {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> {{__('common.close')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditCarBtn"> {{__('common.save')}}</button>
            </div>
        </div>
    </div>
</div>



<!-- ===================== Belongs to add ========================= -->
<script type="text/javascript">

    function showAddCarForm()
    {
        $('#addCarModal').modal('show');
        $('#loading_modal_car').show();
        $.ajax({
                url: `/cars/create`,
                type: 'GET',
                success: (result) => {
                    $('#carFormWrapper').html(result);
                    $('#loading_modal_car').hide();

                    // Initialize Select2 after the form has been injected
                    $(".select2").select2();
                },
                error: () => {
                    $('#loading_modal_car').hide();
                    alert('اطلاعات یافت نشد');
                }
        });
    }

    $('#addCarBtn').on('click', function () {
    // Serialize form data
    var formData = $('#carForm').serialize();

    // Show loading state
    $('#loading_modal_car').show();

    // Clear previous error messages
    $('#carNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/cars/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        success: (response) => {
            $('#loading_modal_car').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchCarList(); // Ensure this function exists in your code
                $('#addCarModal').modal('hide');
                showNotification("{{ __('common.added_successfully') }}", 'success', 'top', 'right', 'withicon');
            } else {
                showNotification("{{ __('common.add_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_car').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#carNameError').text(errors.name[0]);
                }
            } else {
                // General error handling
                showNotification("{{ __('common.add_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        }
    });
  });
</script>



<!-- ===================== Belongs to Edit ========================= -->
<script type="text/javascript">
    // Open Modal for Editing
    $('table').on('click', '.editCar', function () {
        $('#EditCarModal').modal('show');
        $('#loading_modal_car').show();
        const unitId = $(this).data('id');
        $.ajax({
            url: `/cars/${unitId}`,
            type: 'GET',
            success: (result) => {
                $('#EditCarFormWrapper').html(result);
                $('#loading_modal_car2').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_modal_car2').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    });
    
    // submit edit form 
    $('#EditCarBtn').on('click', function () {
    // Serialize form data
    var formData = $('#carEditForm').serialize();

    // Show loading state
    $('#loading_modal_car2').show();

    // Clear previous error messages
    $('#carNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/cars/update', // The actual route for saving data
        type: 'PATCH',
        data: formData,
        success: (response) => {
            $('#loading_modal_car2').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchCarList(); // Ensure this function exists in your code
                $('#EditCarModal').modal('hide');
                showNotification("{{ __('common.updated_successfully') }}", 'success', 'top', 'right', 'withicon');
            } else {
                showNotification("{{ __('common.update_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_car2').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#carNameError').text(errors.name[0]);
                }
            } else {
                // General error handling
                showNotification("{{ __('common.add_failed') }}", 'danger', 'top', 'right', 'withicon');
            }

        }
    });
  });
</script>



<!-- ===================== Belongs to Display and Delete ========================= -->
<script type="text/javascript">
// Fetch Warehouses List
function fetchCarList() {
    const carTable = $('#carTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(carTable)) {
        // Initialize DataTable if not already initialized
        carTable.DataTable({
            serverSide: true,
            processing: true,
            lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'همه']
                ],
            ajax: {
                url: '{{ route("cars.list") }}',
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
        carTable.DataTable().ajax.reload();
    }
}

// Delete Warehouse
$('table').on('click', '.deleteCar', function () {
    const id = $(this).data('id');
        if (id && confirm("{{ __('common.delete_confirm') }}")) {
            $.ajax({
                url: `/cars/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchCarList();
                        showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    } else {
                       showNotification("{{ __('common.delete_failed') }}", 'danger', 'top', 'right', 'withicon');
                       alert(response.message);
                    }
                },
                error: () => {
                    showNotification("{{ __('common.delete_failed') }}", 'danger', 'top', 'right', 'withicon');
                }
            });
        }
});
</script>
