<div class="table-responsive" style="padding:5px;">
    <div id="loading" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="branchTable"  class="table table-bordered table-striped table-hover datatable">
        <thead>
            <tr>
                <th>{{ __('common.number')}}</th>
                <th>{{ __('settings.branch_name')}} </th>
                <th>{{ __('settings.branch_resp')}} </th>
                <th>{{ __('settings.branch_phone')}}</th>
                <th>{{ __('settings.branch_email')}}</th>
                <th>{{ __('settings.branch_address')}}</th>
                <th>{{ __('common.edit')}}</th>
                <th>{{ __('common.delete')}}</th>
            </tr>
        </thead>
    </table>
    <div id="pagination" style="text-align: center;"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{ __('common.add')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="branchFormWrapper"></div>
                <div id="loading_modal" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm " data-dismiss="modal">{{ __('common.close')}}</button>
                <button type="button" class="btn btn-success btn-sm m-r-10" id="submitBtnBranch">{{ __('common.save')}}</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="EditBranchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:900px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{ __('common.edit')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditBranchFormWrapper"></div>
                <div id="loading_modal2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditBranchBtn">{{ __('common.save')}}</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

function showAddBranchForm()
{
    $('#addModal').modal('show');
    $('#loading_modal').show();
    $.ajax({
            url: `/branches/create`,
            type: 'GET',
            success: (result) => {
                $('#branchFormWrapper').html(result);
                $('#loading_modal').hide();
            },
            error: () => {
                $('#loading_modal').hide();
                alert('اطلاعات یافت نشد');
            }
    });
}

// submit add form
$('#submitBtnBranch').on('click', function () 
    {
        // Serialize form data
        var formData = $('#branchForm').serialize();

        // Show loading state
        $('#loading_modal').show();

        // // Clear previous error messages
        // $('#accountTypeIdError').text('');
        // $('#accountNameError').text('');

        // showNotification("{{ __('common.add_failed') }}" , 'danger', 'top', 'right', 'withicon');

        // AJAX form submission
        $.ajax({
            url: '/branches/store', 
            type: 'POST',
            data: formData,
            success: (response) => {
                $('#loading_modal').hide();
                if (response.status === 'success') {
                    fetchBranchList(); // Ensure this function exists in your code
                    $('#addModal').modal('hide');
                    showNotification(response.message, 'success', 'top', 'right', 'withicon');
                } else {
                    showNotification("{{ __('common.add_failed') }}" , 'danger', 'top', 'right', 'withicon');
                }
            },
            error: (xhr) => {
                $('#loading_modal').hide();
                // Handle validation errors
                 if (xhr.status === 422) 
                 { // Laravel validation error status code
                    var errors = xhr.responseJSON.errors;
                    if (errors?.name) {
                        $('#bNameError').text(errors.name[0]);
                    }
                    if (errors?.responsible) {
                        $('#bResponsibleError').text(errors.responsible[0]);
                    }
                    if (errors?.phone) {
                        $('#bPhoneError').text(errors.phone[0]);
                    }
                    if (errors?.address) {
                        $('#bAddressError').text(errors.address[0]);
                    }
                    
                } else {
                    // General error handling
                    showNotification("{{ __('common.add_failed') }}", 'danger', 'top', 'right', 'withicon');
                }
            }
        });
  });


// Fetch Branch List
function fetchBranchList() {
    const branchTable = $('#branchTable'); // Replace 'branchTable' with the ID of your branch table

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(branchTable)) {
        branchTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("branches.list") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'responsible', name: 'responsible' },
                { data: 'phone', name: 'phone' },
                { data: 'email', name: 'email' },
                { data: 'address', name: 'address' },
                { data: 'edit', name: 'edit', searchable: false, orderable: false },
                { data: 'delete', name: 'delete', searchable: false, orderable: false },
            ]
        });
    } else {
        // Reload the data if already initialized
        branchTable.DataTable().ajax.reload();
    }
}



</script>

<!-- ===================== Belongs to Edit ========================= -->
<script type="text/javascript">
    // Open Modal for Editing
    $('table').on('click', '.editBranch', function () {
        $('#EditBranchModal').modal('show');
        $('#loading_modal2').show();
        const bId = $(this).data('id');
        $.ajax({
            url: `/branches/show/${bId}`,
            type: 'GET',
            success: (result) => {
                $('#EditBranchFormWrapper').html(result);
                $('#loading_modal2').hide();
            },
            error: () => {
                $('#loading_modal2').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    });
    
    // submit edit form 
    $('#EditBranchBtn').on('click', function () 
    {
        // Serialize form data
        var formData = $('#editBranchForm').serialize();

        // Show loading state
        $('#loading_modal2').show();

        // Clear previous error messages
        // $('#accountTypeIdError').text('');
        // $('#accountNameError').text('');


        // AJAX form submission
        $.ajax({
            url: '/branches/update', // The actual route for saving data
            type: 'PATCH',
            data: formData,
            success: (response) => {
                $('#loading_modal2').hide();

                if (response.status === 'success') {
                    fetchBranchList(); 
                    $('#EditBranchModal').modal('hide');
                    showNotification("{{ __('common.updated_successfully') }}", 'success', 'top', 'right', 'withicon');
                } else {
                    showNotification("{{ __('common.update_failed') }}", 'danger', 'top', 'right', 'withicon');
                }
            },
            error: (xhr) => {
                $('#loading_modal2').hide();

                // Handle validation errors
                if (xhr.status === 422) 
                { // Laravel validation error status code
                    var errors = xhr.responseJSON.errors;
                    if (errors?.name) {
                        $('#bNameError').text(errors.name[0]);
                    }
                    if (errors?.responsible) {
                        $('#bResponsibleError').text(errors.responsible[0]);
                    }
                    if (errors?.phone) {
                        $('#bPhoneError').text(errors.phone[0]);
                    }
                    if (errors?.address) {
                        $('#bAddressError').text(errors.address[0]);
                    }
                } else {
                    // General error handling
                    showNotification("{{ __('common.update_failed') }}", 'danger', 'top', 'right', 'withicon');
                }

            }
        });
  });

 // Delete Branch
 $('table').on('click', '.deleteBranch', function () {
    const id = $(this).data('id');
    if (id && confirm("{{ __('common.delete_confirm') }}")) {
        $.ajax({
            url: `/branches/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: (response) => {
                if(response.status === 'success') {
                    // table.ajax.reload(null, false); // callaback, boolean
                    fetchBranchList();
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