<div class="table-responsive" style="padding:5px;">
    <table id="accountTable" class="table table-bordered table-striped table-hover datatable3">
        <thead>
            <tr>
                <th>شماره </th>
                <th> نوع حساب </th>										
                <th>  نام حساب </th>										
                <th>  شماره تماس </th>		
                <th> آدرس  </th>		
                <th> نمایش  </th>		
                <th>ویرایش </th>
                <th>حذف </th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:900px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">افزودن </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="accountFormWrapper"></div>
                <div id="loading_modal_account" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="addAccountBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditAccountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:900px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> ویرایش </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditAccountFormWrapper"></div>
                <div id="loading_modal_account2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="EditAccountBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>


<!-- Details Modal -->
<div class="modal fade" id="DetailsAccountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:900px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> جزییات </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="ViewFormWrapper"></div>
                <div id="loading_modal_account3" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
            </div>
        </div>
    </div>
</div>




<!-- ===================== Belongs to add ========================= -->
<script type="text/javascript">

    // Show add modal 
    function showAddAccountForm()
    {
        //  resetForm();
        $('#addAccountModal').modal('show');
        $('#loading_modal_account').show();
        $.ajax({
                url: `/account/create`,
                type: 'GET',
                success: (result) => {
                    $('#accountFormWrapper').html(result);
                    $('#loading_modal_account').hide();

                    // Initialize Select2 after the form has been injected
                    $(".select2").select2();
                },
                error: () => {
                    $('#loading_modal_account').hide();
                    alert('اطلاعات یافت نشد');
                }
        });
    }

    // submit add form
    $('#addAccountBtn').on('click', function () 
    {
            // Serialize form data
            var formData = $('#accountForm').serialize();

            // Show loading state
            $('#loading_modal_account').show();

            // Clear previous error messages
            $('#accountTypeIdError').text('');
            $('#accountNameError').text('');

            // AJAX form submission
            $.ajax({
                url: '/account/store', // The actual route for saving data
                type: 'POST',
                data: formData,
                success: (response) => {
                    $('#loading_modal_account').hide();
                    if (response.status === 'success') {
                        // Call a function to refresh the warehouse list or update the UI
                        fetchAccountList(); // Ensure this function exists in your code
                        $('#addAccountModal').modal('hide');
                        showNotification('موفقانه ثبت گردید', 'success', 'top', 'right', 'withicon');
                    } else {
                        showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
                    }
                },
                error: (xhr) => {
                    $('#loading_modal_account').hide();

                    // Handle validation errors
                    if (xhr.status === 422) { // Laravel validation error status code
                        var errors = xhr.responseJSON.errors;
                        if (errors?.name) {
                            $('#accountNameError').text(errors.name[0]);
                        }
                        if (errors?.account_type_id) {
                            $('#accountTypeIdError').text(errors.account_type_id[0]);
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
    $('table').on('click', '.editAccount', function () {
        $('#EditAccountModal').modal('show');
        $('#loading_modal_account').show();
        const accountId = $(this).data('id');
        $.ajax({
            url: `/account/${accountId}`,
            type: 'GET',
            success: (result) => {
                $('#EditAccountFormWrapper').html(result);
                $('#loading_modal_account2').hide();

                // Initialize Select2 after the form has been injected
                $(".select2").select2();
            },
            error: () => {
                $('#loading_modal_account2').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    });
    
    // submit edit form 
    $('#EditAccountBtn').on('click', function () 
    {
        // Serialize form data
        var formData = $('#accountEditForm').serialize();

        // Show loading state
        $('#loading_modal_account2').show();

        // Clear previous error messages
        $('#accountTypeIdError').text('');
        $('#accountNameError').text('');


        // AJAX form submission
        $.ajax({
            url: '/account/update', // The actual route for saving data
            type: 'PATCH',
            data: formData,
            success: (response) => {
                $('#loading_modal_account2').hide();

                if (response.status === 'success') {
                    // Call a function to refresh the warehouse list or update the UI
                    fetchAccountList(); // Ensure this function exists in your code
                    $('#EditAccountModal').modal('hide');
                    showNotification('موفقانه ویرایش گردید', 'success', 'top', 'right', 'withicon');
                } else {
                    showNotification('ویرایش نگردید', 'danger', 'top', 'right', 'withicon');
                }
            },
            error: (xhr) => {
                $('#loading_modal_account2').hide();

                // Handle validation errors
                if (xhr.status === 422) { // Laravel validation error status code
                    var errors = xhr.responseJSON.errors;
                        if (errors?.name) {
                            $('#accountNameError').text(errors.name[0]);
                        }
                        if (errors?.account_type_id) {
                            $('#accountTypeIdError').text(errors.account_type_id[0]);
                        }
                } else {
                    // General error handling
                    showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
                }

            }
        });
  });
</script>



<!-- ===================== Belongs to Display, ViewDetails and Delete ========================= -->
<script type="text/javascript">
// Fetch Warehouses List
function fetchAccountList() {
    const accountTable = $('#accountTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(accountTable)) {
        // Initialize DataTable if not already initialized
        accountTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("account.list") }}',
            },
            
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'account_type', name: 'account_type'},
                { data: 'name', name: 'name'},
                { data: 'phone', name: 'phone' },
                { data: 'address', name: 'address' },
                { data: 'view', name: 'view' },
                { data: 'edit', name: 'edit', searchable: false, orderable: false },
                { data: 'delete', name: 'delete', searchable: false, orderable: false },
           ]
        });
    } else {
        // If already initialized, reload the data
        accountTable.DataTable().ajax.reload();
    }
}


// view Details
$('table').on('click', '.viewAccount', function () {
        $('#DetailsAccountModal').modal('show');
        $('#loading_modal_account3').show();
        const accountId = $(this).data('id');
        $.ajax({
            url: `/account/show/${accountId}`,
            type: 'GET',
            success: (result) => {
                $('#ViewFormWrapper').html(result);
                $('#loading_modal_account3').hide();
            },
            error: () => {
                $('#loading_modal_account3').hide();
                alert('اطلاعات یافت نشد');
            }
        });
    });


// Delete Warehouse
$('table').on('click', '.deleteAccount', function () {
    const id = $(this).data('id');
        if (id && confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/account/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchAccountList();
                        showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    } else {
                       showNotification(response.message, 'danger', 'top', 'right', 'withicon');
                    }
                },
                error: () => {
                    showNotification(response.message, 'danger', 'top', 'right', 'withicon');
                }
            });
        }
});
</script>


<script>
$(document).ready(function() {
    // Add new form row
    $(document).on('click', '.add-more', function() {
        let newRow = $('.repeatable-form:first').clone();
        newRow.find('input, select').val(''); // Clear inputs
        newRow.find('.add-more').hide(); // Hide "Add More" button in cloned row
        newRow.find('.remove').show(); // Show "Remove" button
        $('#formContainer').append(newRow);
    });

    // Remove form row
    $(document).on('click', '.remove', function() {
        $(this).closest('.repeatable-form').remove();
    });

    // Reset form when modal is closed
    $('#addAccountModal').on('hidden.bs.modal', function () {
        resetForm();
    });

    // // Reset form on submit
    // $('#accountForm').submit(function(e) {
    //     e.preventDefault(); // Prevent actual form submission
    //     // alert('Form submitted successfully!');
    //     resetForm();
    //     $('#addAccountModal').modal('hide'); // Hide modal after submit
    // });

    // Function to reset the form
    function resetForm() {
        $('#formContainer').html($('.repeatable-form:first').clone()); // Keep only the first row
        $('#formContainer .repeatable-form input, #formContainer .repeatable-form select').val(''); // Clear values
        $('#formContainer .repeatable-form .remove').hide(); // Hide "Remove" button
        $('#formContainer .repeatable-form .add-more').show(); // Show "Add More" button
    }
});
</script>