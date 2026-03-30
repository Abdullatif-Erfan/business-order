<div class="table-responsive" style="padding:5px;">
    <div id="loading3" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="orgProfileTable" class="table table-bordered table-striped table-hover datatable3">
        <thead>
            <tr>
                <th>{{__('common.number')}}</th>
                <th>{{__('settings.profile_name')}} </th>
                <th>{{__('settings.phone')}} </th>
                <th>{{__('settings.address')}} </th>
                <th>{{__('settings.logo')}} </th>
                <th>{{__('settings.header')}} </th>											
                <th>{{__('common.edit')}}  </th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div id="pagination3" style="text-align: center;"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addOrgProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('common.add') }}  </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="orgProfileFormWrapper"></div>
                <div id="loading_modal_org_profile" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading') }} 
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.add') }} </button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="addOrgProfileBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditOrgProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" >
        <div class="modal-content" style="width:800px !important">
            <div class="modal-header">
                <h5 class="modal-title"> {{ __('common.edit') }}  </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditOrgProfileFormWrapper"></div>
                <div id="loading_modal_org_profile2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('common.loading') }} 
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('common.close') }} </button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditOrgProfileBtn">{{ __('common.save') }} </button>
            </div>
        </div>
    </div>
</div>



<!-- ===================== Belongs to add ========================= -->
<script type="text/javascript">

    function showAddProfileForm()
    {
        $('#addOrgProfileModal').modal('show');
        $('#loading_modal_org_profile').show();
        $.ajax({
                url: `/orgprofile/create`,
                type: 'GET',
                success: (result) => {
                    $('#orgProfileFormWrapper').html(result);
                    $('#loading_modal_org_profile').hide();

                    // Initialize Select2 after the form has been injected
                    $(".select2").select2();
                },
                error: () => {
                    $('#loading_modal_org_profile').hide();
                    alert('اطلاعات یافت نشد');
                }
        });
    }

    $('#addOrgProfileBtn').on('click', function () {
    // Serialize form data
    var formData = $('#orgProfileForm').serialize();

    // Show loading state
    $('#loading_modal_org_profile').show();

    // Clear previous error messages
    $('#orgProfileNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/orgprofile/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        success: (response) => {
            $('#loading_modal_org_profile').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchProfileList(); // Ensure this function exists in your code
                $('#addOrgProfileModal').modal('hide');
                showNotification("{{ __('common.added_successfully') }}", 'success', 'top', 'right', 'withicon');
                } else {
                showNotification("{{ __('common.add_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_org_profile').hide();

            // Handle validation errors
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#orgProfileNameError').text(errors.name[0]);
                }

                if (errors?.expired_after_days) {
                    $('#expired_after_daysError').text(errors.expired_after_days[0]);
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
    $('table').on('click', '.editOrgBio', function () {
        $('#EditOrgProfileModal').modal('show');
        $('#loading_modal_org_profile').show();
        const id = $(this).data('id');
        $.ajax({
            url: `/orgprofile/${id}`,
            type: 'GET',
            success: (result) => {
                $('#EditOrgProfileFormWrapper').html(result);
                $('#loading_modal_org_profile2').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_modal_org_profile2').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    });
    
    // submit edit form 
    $('#EditOrgProfileBtn').on('click', function () {
    // Create FormData object
    var formData = new FormData($('#orgProfileEditForm')[0]);
     // Include CSRF token manually
     formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    // Show loading state
    $('#loading_modal_org_profile2').show();

    // Clear previous error messages
    $('#orgProfileNameError').text('');
    $('#orgProfilePhoneError').text('');
    $('#orgProfileAddressError').text('');
    $('#orgProfileHeaderError').text('');
    $('#orgProfileLogosError').text('');

    // AJAX form submission
    $.ajax({
        url: '/orgprofile/update', // Adjust the route if necessary
        type: 'POST', // Laravel supports PATCH, but some servers require POST + _method
        data: formData ,
        contentType: false,  // Important for file upload
        processData: false,  // Prevent jQuery from processing the data
        success: (response) => {
            $('#loading_modal_org_profile2').hide();

            if (response.status === 'success') {
                fetchProfileList(); // Refresh the list if needed
                $('#EditOrgProfileModal').modal('hide');
                showNotification("{{ __('common.updated_successfully') }}", 'success', 'top', 'right', 'withicon');
                } else {
                    showNotification("{{ __('common.update_failed') }}", 'danger', 'top', 'right', 'withicon');
                }
        },
        error: (xhr) => {
            $('#loading_modal_org_profile2').hide();

            if (xhr.status === 422) { // Laravel validation error
                var errors = xhr.responseJSON.errors;

                if (errors?.name) {
                    $('#orgProfileNameError').text(errors.name[0]);
                }
                if (errors?.phone) {
                    $('#orgProfilePhoneError').text(errors.phone[0]);
                }
                if (errors?.address) {
                    $('#orgProfileAddressError').text(errors.address[0]);
                }
                if (errors?.header) {
                    $('#orgProfileHeaderError').text(errors.header[0]);
                }
                if (errors?.logos) {
                    $('#orgProfileLogosError').text(errors.logos[0]);
                }
                if (errors?.expired_after_days) {
                    $('#expired_after_daysError').text(errors.expired_after_days[0]);
                }
            } else {
                showNotification("{{__('common.try_again')}}", 'danger', 'top', 'right', 'withicon');
            }
        }
    });
});

</script>



<!-- ===================== Belongs to Display and Delete ========================= -->
<script type="text/javascript">
// Fetch Warehouses List
function fetchProfileList() {
    const orgProfileTable = $('#orgProfileTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(orgProfileTable)) {
        // Initialize DataTable if not already initialized
        orgProfileTable.DataTable({
            serverSide: true,
            processing: true,
            lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'همه']
                ],
            ajax: {
                url: '{{ route("orgprofile.list") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'phone', name: 'phone' },
                { data: 'address', name: 'address' },
                { data: 'logos', name: 'logos' },
                { data: 'header', name: 'header' },
                { data: 'edit', name: 'edit', searchable: false, orderable: false },
           ]
        });
    } else {
        // If already initialized, reload the data
        orgProfileTable.DataTable().ajax.reload();
    }
}

// Delete Warehouse
$('table').on('click', '.deleteOrgProfile', function () {
    const id = $(this).data('id');
        if (id && confirm("{{ __('common.delete_confirm') }}")) {
            $.ajax({
                url: `/orgprofile/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchProfileList();
                        showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    } else {
                       showNotification(response.message, 'danger', 'top', 'right', 'withicon');
                       alert(response.message);
                    }
                },
                error: () => {
                    showNotification(response.message, 'danger', 'top', 'right', 'withicon');
                }
            });
        }
});
</script>
