<br/>
<!-- Button to trigger the modal -->
<button type="button" name="add" class="btn btn-primary btn-sm m-l-10 m-b-10" data-toggle="modal" data-target="#addModal">
    <span class="btn-label"> <i class="fa fa-plus"></i> </span>
    <th>{{__('common.add')}}</th>
</button>


<div class="table-responsive table_responsive" style="padding:5px;">
    <div id="loading" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="example" class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>{{__('common.number')}}</th>
                <th>نام شعبه </th>
                <th>نام شعبه </th>
                <th>نام شعبه </th>
                <th>نام شعبه </th>
                <th>نام شعبه </th>
                <th>ویرایش </th>
                <th>حذف </th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div id="pagination" style="text-align: center;"></div>
</div>

<!-- Hidden Input Field in Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><th>{{__('common.add')}}</th></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <form id="branchForm">
                    <input type="hidden" id="branchId" value="">
                    <div class="form-group">
                        <label for="name">نام شعبه</label>
                        <input type="text" class="form-control" id="name" required placeholder="نام را وارد کنید">
                        <span id="nameError" class="text-danger"></span>
                    </div>
                </form>
                <div id="loading_modal" style="display:none;" class="text-center">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="button" class="btn btn-success btn-sm m-r-10" id="submitBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>

<script>
function fetchBranches(page = 1) {
    const loading = $('#loading');
    const exampleBody = $('#example tbody');
    const pagination = $('#pagination');

    // Show loading spinner
    loading.show();
    exampleBody.empty(); // Clear existing rows

    // ========================== Fetch branch data ===================
    $.ajax({
        url: "{{ route('branches.list') }}",
        type: 'GET',
        data: { page: page },
        success: function(response) {
            // Hide loading spinner
            loading.hide();

            // Populate table with data
            if (response.data.length > 0) {
                response.data.forEach((branch, index) => {
                    exampleBody.append(`
                        <tr>
                            <td>${response.from + index}</td>
                            <td>${branch.name}</td>
                            <td>${branch.name}</td>
                            <td>${branch.name}</td>
                            <td>${branch.name}</td>
                            <td>${branch.name}</td>
                            <td><i class="fas fa-pen-square editBranch" 
                            data-id="${branch.id}"  style="font-size:20px;" alt=""></i></td>
                            <td><i class="fas fa-trash-alt deleteBranch"  data-id="${branch.id}" style="font-size:20px;color:red;" alt="حذف"></i></td>
                            
                        </tr>
                    `);
                });
            } else {
                exampleBody.append('<tr><td colspan="4" style="text-align: center;">No records found</td></tr>');
            }

            // Generate pagination links
            pagination.html('');
            if (response.links) {
                response.links.forEach(link => {
                    const activeClass = link.active ? 'active' : '';
                    pagination.append(`
                        <button class="btn btn-sm ${activeClass}" data-page="${link.url ? new URL(link.url).searchParams.get('page') : null}">
                            ${link.label}
                        </button>
                    `);
                });
            }
        },
        error: function() {
            loading.hide();
            showNotification('Failed to load branches.', 'danger', 'top', 'right', 'withicon');
        }
    });
}

$(document).ready(function() {
    // Initial fetch
    fetchBranches();

    // Pagination click
    $('#pagination').on('click', 'button', function() {
        const page = $(this).data('page');
        if (page) {
            fetchBranches(page);
        }
    });

    
  

    // Handle add/edit form submission
    $('#submitBtn').click(function() {
        const branchId = $('#branchId').val();
        if(branchId > 0 ) {
            updateRecord();
        } else {
            addRecord();
        }
    });



    // =============== add form ====================
    function addRecord() {
        var name = $('#name').val();
        var nameError = $('#nameError');
        var loading_modal = $('#loading_modal');
        
        // Reset error messages
        nameError.text('');
        
        // Show loading spinner
        loading_modal.show();
        
        // Send AJAX request
        $.ajax({
            url: "{{ route('branches') }}",
            type: 'POST',
            data: {
                name: name,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Hide loading spinner
                loading_modal.hide();
                
                // Show success message
                // alert(response.message);
                
                // Close the modal
                $('#addModal').modal('hide');
                
                // Optionally, you can reset the form
                $('#branchForm')[0].reset();

                // Call the notification function
              showNotification(response.message, 'success', 'top', 'right', 'withicon');
              fetchBranches();

            },
            error: function(xhr) {
                // Hide loading spinner
                loading_modal.hide();
                
               // Handle validation errors
               var errors = xhr.responseJSON.errors;

                if (errors) {
                    if (errors.name) {
                        nameError.text(errors.name[0]); // Show validation error under input
                    }
                } else {
                    // Call the notification function for final error
                    showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
                }
            }
        });
    };

    // =============== show edit form ==============
    $('#example').on('click', '.editBranch', function() {
        const branchId = $(this).data('id');
        var loading_modal = $('#loading_modal');
        $('#addModal').modal('show');
        loading_modal.show();
        // Fetch branch details
        $.ajax({
            url: `/branches/${branchId}`,
            type: 'GET',
            success: function(branch) {
                // Populate modal fields
                // alert(branch.name);
                $('#branchId').val(branch.id);
                $('#name').val(branch.name);
                $('#addModal .modal-title').text('ویرایش شعبه');
                $('#submitBtn').text('ویرایش');
                loading_modal.hide();
            },
            error: function() {
                loading_modal.hide();
                showNotification('ریکارد موجود نیست', 'danger', 'top', 'right', 'withicon');
            }
        });
    });


    // =============== update form =================
    function updateRecord() {
        const branchId = $('#branchId').val();
        const name = $('#name').val();
        
        if (!name.trim()) {
            $('#nameError').text('نام شعبه الزامی است');
            return;
        }

        $('#nameError').text(''); // Clear previous error
        const url = branchId ? `/branches/${branchId}` : '/branches';
        const method = 'PATCH';

        $.ajax({
            url: url,
            type: method,
            data: {
                _token: '{{ csrf_token() }}',
                id: branchId,
                name: name
            },
            success: function(response) {
                $('#addModal').modal('hide');
                loading_modal.hide();
                name.val('');
                fetchBranches();
                showNotification(response.message, 'success', 'top', 'right', 'withicon');
            },
            error: function(xhr) {
                 // Hide loading spinner
                 loading_modal.hide();
                
                // Handle validation errors
                var errors = xhr.responseJSON.errors;
 
                 if (errors) {
                     if (errors.name) {
                         nameError.text(errors.name[0]); // Show validation error under input
                     }
                 } else {
                     // Call the notification function for final error
                     showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
                 }

            }
        });
    };


    // =============== delete a record =============
    $('#example').on('click', '.deleteBranch', function() {
        const branchId = $(this).data('id');
        if (confirm('آیا میخواهید حذف نمایید ؟')) {
            $.ajax({
                url: `/branches/${branchId}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    fetchBranches();
                },
                error: function() {
                    showNotification('حذف نگردید', 'danger', 'top', 'right', 'withicon');
                }
            });
        }
    });
});

</script>