<div class="table-responsive" style="padding:5px;">
    <div id="loading3" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="preListTable" class="table table-bordered table-striped table-hover datatable3">
       <thead>
            <tr>
                <th>{{__('common.number')}}     </th>
                <th>{{__('buy.category')}}     </th>
                <th>{{__('common.item_name')}}  </th>
                <th>{{__('common.default_unit')}}  </th>
                <th>{{__('common.edit')}}       </th>
                <th>{{__('common.delete')}}     </th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div id="pagination3" style="text-align: center;"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addPrelistModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 900px !important; max-width: 95vw !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{__('common.add')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="preListFormWrapper"></div>
                <div id="loading_modal_PreList" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i>  {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm m-l-10" id="addPreListBtnAndResume"> {{__('buy.save_and_resume')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10 m-l-10" id="addPreListBtn"> {{__('common.save')}}</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> {{__('common.close')}}</button>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditPreListModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width: 900px !important; max-width: 95vw !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">  {{__('common.edit')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditPreListFormWrapper"></div>
                <div id="loading_modal_PreList2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i>  {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> {{__('common.close')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditPreListBtn"> {{__('common.save')}}</button>
            </div>
        </div>
    </div>
</div>



<!-- ===================== Belongs to add ========================= -->
<script type="text/javascript">

    function showAddPreListForm()
    {
        $('#addPrelistModal').modal('show');
        $('#loading_modal_PreList').show();
        $.ajax({
                url: `/buyprelist/create`,
                type: 'GET',
                success: (result) => {
                    $('#preListFormWrapper').html(result);
                    $('#loading_modal_PreList').hide();

                    // Initialize Select2 after the form has been injected
                    $(".select2").select2();
                },
                error: () => {
                    $('#loading_modal_PreList').hide();
                    alert('اطلاعات یافت نشد');
                }
        });
    }

    $('#addPreListBtn').on('click', function () {
    // Serialize form data
    var formData = $('#preListForm').serialize();

    // Show loading state
    $('#loading_modal_PreList').show();

    // Clear previous error messages
    $('#preListNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/buyprelist/store', // The actual route for saving data
        type: 'POST',
        data: formData,
        success: (response) => {
              $('#loading_modal_PreList').hide();
              $('#preListNameError').text('');
            if (response.status === 'success') {
                fetchPreListItems(); // Ensure this function exists in your code
                $('#addPrelistModal').modal('hide');
                showNotification("{{ __('common.added_successfully') }}", 'success', 'top', 'right', 'withicon');
            } else {
                showNotification("{{ __('common.add_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_PreList').hide();

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


  // =============================================
// SAVE AND RESUME BUTTON - Keep Modal Open
// =============================================
$('#addPreListBtnAndResume').on('click', function (e) {
    e.preventDefault(); // Prevent default form submission
    
    // Serialize form data
    var formData = $('#preListForm').serialize();

    // Show loading state
    $('#loading_modal_PreList').show();

    // Clear previous error messages
    $('#preListNameError').text('');

    // AJAX form submission
    $.ajax({
        url: '/buyprelist/store',
        type: 'POST',
        data: formData,
        success: function (response) {
            $('#loading_modal_PreList').hide();
            $('#preListNameError').text('');
            
            if (response.status === 'success') {
                //  Refresh the table
                fetchPreListItems();
                
                //  Clear the name field only (keep modal open)
                $('#preListForm input[name="name"]').val('');
                // $('#preListForm select[name="category_id"]').val('').trigger('change');
                
                //  Focus on the name field for next entry
                $('#preListForm input[name="name"]').focus();
                
                //  Show success message
                showNotification("{{ __('common.added_successfully') }}", 'success', 'top', 'right', 'withicon');
                
                //  Reset any error messages
                $('#preListNameError').text('');
                
            } else {
                showNotification("{{ __('common.add_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        },
        error: function (xhr) {
            $('#loading_modal_PreList').hide();

            // Handle validation errors
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                if (errors?.name) {
                    $('#preListNameError').text(errors.name[0]);
                }
            } else {
                showNotification("{{ __('common.add_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        }
    });
});

</script>




<!-- ===================== Belongs to Edit ========================= -->
<script type="text/javascript">
    // Open Modal for Editing
    $('table').on('click', '.editIcon', function () {
        $('#EditPreListModal').modal('show');
        $('#loading_modal_PreList').show();
        const id = $(this).data('id');
        $.ajax({
             url: `/buyprelist/${id}`,
            type: 'GET',
            success: (result) => {
                $('#EditPreListFormWrapper').html(result);
                $('#loading_modal_PreList2').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_modal_PreList2').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    });
    
    // ======================= submit edit form  ========================
    $('#EditPreListBtn').on('click', function () {
    // Serialize form data
    var formData = $('#preListEditForm').serialize();

    // Show loading state
    $('#loading_modal_PreList2').show();

    // Clear previous error messages
    $('#preListNameError2').text('');

    // AJAX form submission
    $.ajax({
        url: '/buyprelist/update', 
        type: 'POST',
        data: formData,
        success: (response) => {
            $('#loading_modal_PreList2').hide();

            if (response.status === 'success') {
                // Call a function to refresh the warehouse list or update the UI
                fetchPreListItems(); // Ensure this function exists in your code
                $('#EditPreListModal').modal('hide');
                showNotification("{{ __('common.updated_successfully') }}", 'success', 'top', 'right', 'withicon');
            } else {
                showNotification("{{ __('common.update_failed') }}", 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading_modal_PreList2').hide();

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
function fetchPreListItems() {
    const preListTable = $('#preListTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(preListTable)) {
        // Initialize DataTable if not already initialized
        preListTable.DataTable({
            serverSide: true,
            processing: true,
            lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'همه']
                ],
            ajax: {
                url: '{{ route("buyprelist.data") }}',
            },
             columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'category', name: 'category'},
                { data: 'name', name: 'name' },
                { data: 'unit_name', name: 'unit_name' },
                { data: 'edit', name: 'edit', orderable: false, searchable: false }, 
                { data: 'delete', name: 'delete', orderable: false, searchable: false }
            ]
        });
    } else {
        // If already initialized, reload the data
        preListTable.DataTable().ajax.reload();
    }
}

// Delete Warehouse
$('table').on('click', '.deleteIcon', function () {
    const id = $(this).data('id');
        if (id && confirm("{{ __('common.delete_confirm') }}")) {
            $.ajax({
                 url: `/buyprelist/destroy/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchPreListItems();
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
