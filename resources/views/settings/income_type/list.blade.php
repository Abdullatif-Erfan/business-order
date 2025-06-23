<div class="table-responsive" style="padding:5px;">
    <div id="loading3" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="incomeTypeTable" class="table table-bordered table-striped table-hover datatable3">
        <thead>
            <tr>
                <th>{{__('common.number')}}</th>
                <th>{{__('common.name')}}</th>
                <th>{{__('common.edit')}}</th>
                <th>{{__('common.delete')}}</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div id="pagination3" style="text-align: center;"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addIncomeTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('common.add')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="incomeTypeFormWrapper"></div>
                <div id="loading_modal_income_type" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{__('common.close')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="addIncomeTypeBtn">{{__('common.save')}}</button>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditIncomeTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{__('common.edit')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditIncomeTypeFormWrapper"></div>
                <div id="loading_modal_income_type2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{__('common.close')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditIncomeTypeBtn">{{__('common.save')}}</button>
            </div>
        </div>
    </div>
</div>



<!-- ===================== Belongs to add ========================= -->
<script type="text/javascript">

    function showAddIncomeTypeForm()
    {
        $('#addIncomeTypeModal').modal('show');
        $('#loading_modal_income_type').show();
        $.ajax({
                url: `/itype/create`,
                type: 'GET',
                success: (result) => {
                    $('#incomeTypeFormWrapper').html(result);
                    $('#loading_modal_income_type').hide();

                    // Initialize Select2 after the form has been injected
                    $(".select2").select2();
                },
                error: () => {
                    $('#loading_modal_income_type').hide();
                    alert('اطلاعات یافت نشد');
                }
        });
    }

    $('#addIncomeTypeBtn').on('click', function () {
    // Serialize form data
    var formData = $('#incomeTypeForm').serialize();

    // Show loading state
    $('#loading_modal_income_type').show();

    // Clear previous error messages
    $('#incomeTypeNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/itype/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        success: (response) => {
            $('#loading_modal_income_type').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchIncomeTypeList(); // Ensure this function exists in your code
                $('#addIncomeTypeModal').modal('hide');
                showNotification("{{ __('common.added_successfully') }}", 'success', 'top', 'right', 'withicon');
            } else {
                showNotification("{{ __('common.add_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_income_type').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#incomeTypeNameError').text(errors.name[0]);
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
    $('table').on('click', '.editIncomeType', function () {
        $('#EditIncomeTypeModal').modal('show');
        $('#loading_modal_income_type').show();
        const unitId = $(this).data('id');
        $.ajax({
            url: `/itype/${unitId}`,
            type: 'GET',
            success: (result) => {
                $('#EditIncomeTypeFormWrapper').html(result);
                $('#loading_modal_income_type2').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_modal_income_type2').hide();
                $('#EditIncomeTypeModal').modal('hide');
                alert("{{__('common.not_authorized')}}");
            }
        });
    });
    
    // submit edit form 
    $('#EditIncomeTypeBtn').on('click', function () {
    // Serialize form data
    var formData = $('#incomeTypeEditForm').serialize();

    // Show loading state
    $('#loading_modal_income_type2').show();

    // Clear previous error messages
    $('#incomeTypeNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/itype/update', // The actual route for saving data
        type: 'PATCH',
        data: formData,
        success: (response) => {
            $('#loading_modal_income_type2').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchIncomeTypeList(); // Ensure this function exists in your code
                $('#EditIncomeTypeModal').modal('hide');
                 showNotification("{{ __('common.updated_successfully') }}", 'success', 'top', 'right', 'withicon');
            } else {
                showNotification("{{ __('common.update_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_income_type2').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#incomeTypeNameError').text(errors.name[0]);
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
function fetchIncomeTypeList() {
    const incomeTypeTable = $('#incomeTypeTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(incomeTypeTable)) {
        // Initialize DataTable if not already initialized
        incomeTypeTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("itype.list") }}',
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
        incomeTypeTable.DataTable().ajax.reload();
    }
}

// Delete Warehouse
$('table').on('click', '.deleteIncomeType', function () {
    const id = $(this).data('id');
        if (id && confirm("{{ __('common.delete_confirm') }}")) {
            $.ajax({
                url: `/itype/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchIncomeTypeList();
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
