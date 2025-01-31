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
    <table id="branchTable"  class="table table-bordered table-striped table-hover datatable">
        <thead>
            <tr>
                <th>شماره</th>
                <th>نام شعبه</th>
                <th>ویرایش</th>
                <th>حذف</th>
            </tr>
        </thead>
        <tbody>
          {{-- @foreach($branchs as $branch)
            <tr>
                <td>{{ $branch->id }}</td>
                <td>{{ $branch->name }}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach --}}
        </tbody>
    </table>
    <div id="pagination" style="text-align: center;"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
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

<script type="text/javascript">
$(document).ready(function () {
    // Initialize DataTable
    // var table = fetchBranchList();

    // Add or Update Branch
    $('#submitBtn').click(function () {
        addOrUpdateBranch(table);
    });

    /**
     * When clicked on editBranch, get the value based on id and show in the modal
     */
    $('table').on('click', '.editBranch', function () {
        const branchId = $(this).data('id');
        $('#addModal').modal('show');
        $('#loading_modal').show();

        $.ajax({
            url: `/branches/${branchId}`,
            type: 'GET',
            success: (branch) => {
                $('#branchId').val(branch.id);
                $('#name').val(branch.name);
                $('#loading_modal').hide();
            },
            error: () => {
                $('#loading_modal').hide();
                showNotification('ریکارد موجود نیست', 'danger', 'top', 'right', 'withicon');
            }
        });
    });

    // Delete Branch
    $('table').on('click', '.deleteBranch', function () {
        const id = $(this).data('id');
        if (id && confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/branches/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        table.ajax.reload(null, false); // callaback, boolean
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
                url: '{{ route("branches") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'edit', name: 'edit', searchable: false, orderable: false },
                { data: 'delete', name: 'delete', searchable: false, orderable: false },
            ]
        });
    } else {
        // Reload the data if already initialized
        branchTable.DataTable().ajax.reload();
    }
}

// Add or Update Branch
const addOrUpdateBranch = (table) => {
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
                $('#branchId').val('');
                table.ajax.reload(null, false);
                showNotification(response.message, 'success', 'top', 'right', 'withicon');
            },
            error: (xhr) => {
                $('#loading_modal').hide();
                const errors = xhr.responseJSON?.errors;
                if (errors?.name) {
                    $('#nameError').text(errors.name[0]);
                } else {
                    $('#branchId').val('');
                    showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
                }
            }
        });
    };

</script>
