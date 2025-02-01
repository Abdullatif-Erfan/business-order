<br />
<!-- Button to trigger the modal -->
<button type="button" class="btn btn-primary btn-sm m-l-10 m-b-10" data-toggle="modal" data-target="#addModal">
    <span class="btn-label"><i class="fa fa-plus"></i></span> ثبت جدید
</button>

<div class="table-responsive" style="padding:5px;">
    <div id="loading" style="display: none; text-align: center;">
        <span>Loading...</span>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="example" class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>شماره</th>
                <th>نام شعبه</th>
                <th>نام شعبه</th>
                <th>نام شعبه</th>
                <th>نام شعبه</th>
                <th>نام شعبه</th>
                <th>ویرایش</th>
                <th>حذف</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div id="pagination" style="text-align: center;"></div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addModal33" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ثبت جدید</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="branchForm">
                    <input type="hidden" id="branchId">
                    <div class="form-group">
                        <label for="name">نام شعبه</label>
                        <input type="text" class="form-control" id="name" required placeholder="نام را وارد کنید">
                        <span id="nameError" class="text-danger"></span>
                    </div>
                </form>
                <div id="loading_modal" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm " data-dismiss="modal">بستن</button>
                <button type="button" class="btn btn-success btn-sm m-r-10" id="submitBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>

<script>

$(document).ready(function () {
    const fetchBranches = (page = 1) => {
        $('#loading').show();
        $('#example tbody').empty();

        $.ajax({
            url: "{{ route('branches.list') }}",
            type: 'GET',
            data: { page },
            success: (response) => {
                $('#loading').hide();

                if (response.data.length) {
                    response.data.forEach((branch, index) => {
                        $('#example tbody').append(`
                            <tr>
                                <td>${response.from + index}</td>
                                <td>${branch.name}</td>
                                <td>${branch.name}</td>
                                <td>${branch.name}</td>
                                <td>${branch.name}</td>
                                <td>${branch.name}</td>
                                <td><i class="fas fa-pen-square editBranch" data-id="${branch.id}" style="font-size:20px;"></i></td>
                                <td><i class="fas fa-trash-alt deleteBranch" data-id="${branch.id}" style="font-size:20px; color:red;"></i></td>
                            </tr>
                        `);
                    });
                } else {
                    $('#example tbody').append('<tr><td colspan="8" class="text-center">No records found</td></tr>');
                }

                $('#pagination').html('');
                if (response.links) {
                    response.links.forEach(link => {
                        const activeClass = link.active ? 'active' : '';
                        $('#pagination').append(`
                            <button class="btn btn-sm ${activeClass}" data-page="${link.url ? new URL(link.url).searchParams.get('page') : null}">${link.label}</button>
                        `);
                    });
                }
            },
            error: () => {
                $('#loading').hide();
                showNotification('Failed to load branches.', 'danger', 'top', 'right', 'withicon');
            }
        });
    };

    const addOrUpdateBranch = () => {
        const branchId = $('#branchId').val();
        const name = $('#name').val();
        const url = branchId ? `/branches/${branchId}` : "{{ route('branches') }}";
        const method = branchId ? 'PATCH' : 'POST';
        
        $('#loading_modal').show();
        $('#nameError').text('');

        $.ajax({
            url,
            type: method,
            data: { name, _token: '{{ csrf_token() }}', id: branchId },
            success: (response) => {
                $('#loading_modal').hide();
                $('#addModal').modal('hide');
                $('#branchForm')[0].reset();
                showNotification(response.message, 'success', 'top', 'right', 'withicon');
                fetchBranches();
            },
            error: (xhr) => {
                $('#loading_modal').hide();
                const errors = xhr.responseJSON?.errors;
                if (errors?.name) {
                    $('#nameError').text(errors.name[0]);
                } else {
                    showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
                }
            }
        });
    };

    const deleteBranch = (branchId) => {
        if (confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/branches/${branchId}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    fetchBranches();
                },
                error: () => {
                    showNotification('حذف نگردید', 'danger', 'top', 'right', 'withicon');
                }
            });
        }
    };

    // Initialize fetch
    fetchBranches();

    // Pagination
    $('#pagination').on('click', 'button', function () {
        const page = $(this).data('page');
        if (page) fetchBranches(page);
    });

    // Submit button
    $('#submitBtn').click(() => addOrUpdateBranch());

    // Edit branch
    $('#example').on('click', '.editBranch', function () {
        const branchId = $(this).data('id');
        $('#addModal').modal('show');
        $('#loading_modal').show();

        $.ajax({
            url: `/branches/${branchId}`,
            type: 'GET',
            success: (branch) => {
                $('#branchId').val(branch.id);
                $('#name').val(branch.name);
                $('#addModal .modal-title').text('ویرایش شعبه');
                // $('#submitBtn').text('ویرایش');
                $('#loading_modal').hide();
            },
            error: () => {
                $('#loading_modal').hide();
                showNotification('ریکارد موجود نیست', 'danger', 'top', 'right', 'withicon');
            }
        });
    });

    // Delete branch
    $('#example').on('click', '.deleteBranch', function () {
        deleteBranch($(this).data('id'));
    });
});
</script>
