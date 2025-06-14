@extends('layouts.app')

@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
    // Show the notification using the data from the session
    $(document).ready(function(){
        showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
    });
</script>
@endif

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                       
                        <div class="card-body">

                        <h3 style="margin-bottom: 15px">
                        لیست اجناس برای نمایش در ثبت </h3>
                    
                    <!-- insertion -->
                      <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_form_collapse" aria-expanded="false">
                            <button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
                                <span class="fas fa-plus-square"></span>  &nbsp; <th>{{__('common.add')}}</th> </button>
                            </a> 
                        </div>
                        <div id="add_form_collapse" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
                            <div class="box-body">
                            <form  id="buyPreListForm">
                            @csrf
                            <input type="hidden" name="branch_id" value="{{ $branchs->first()->id }}">
                            <div class="form-body">
                                <div class="row">
                                
                                    <div class="col-md-8 col-sm-8 col-xs-6">
                                        <div class="form-group">
                                            <input class="form-control" id="name" name="name" type="text" required placeholder="{{__('common.item_name')}}" >
                                            <span id="nameError" class="text-danger"></span>
                                        </div> 
                                    </div>	


                                    <div class="col-md-2 col-sm-4 col-xs-12 center m-t-10">
                                        <button type="button" name="submit" class="btn btn-info btn-sm m-l-10" onclick="addNewRecord(1)"  >
                                          <span class="btn-label"> <i class="fa fa-save"></i> </span> ثبت و ماندن
                                        </button>
                                    </div>

                                    <div class="col-md-2 col-sm-4 col-xs-12 center m-t-10">
                                        <button type="button" name="submit" class="btn btn-primary btn-sm m-l-10" onclick="addNewRecord(2)"  >
                                          <span class="btn-label"> <i class="fa fa-save"></i> </span> ثبت
                                        </button>
                                    </div>

                                    <div class="col-12">
                                        <div id="loading" style="display:none; text-align: center;">
                                            <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                                        </div>
                                    </div>

                                </div>
                                </div>  <!-- /form-body -->
                            </form>
                        </div> <!-- box-body -->
                    </div>  <!-- /id="add_form" -->	
            <!-- /insertion -->


                            <div class="table_responsive m-t-20" id="print_area">
                            <table id="preListTable"  class="table table-bordered table-striped table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th>{{__('common.number')}}</th>
                                            <th> شعبه</th>
                                            <th>{{__('common.item_name')}}</th>
                                            <th>{{__('common.edit')}}</th>
                                            <th>{{__('common.delete')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div> 
                        </div> 
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" id="EditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> ویرایش </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditFormWrapper"></div>
                <div id="edit_loader" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="updateSubmitBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>


<script>

$(document).ready(function() {
    fetchList();
});

function fetchList() {
    const preListTable = $('#preListTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(preListTable)) {
        // Initialize DataTable if not already initialized
        preListTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("buyprelist.data") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'branch', name: 'branch' },
                { data: 'name', name: 'name' },
                { data: 'edit', name: 'edit', orderable: false, searchable: false }, 
                { data: 'delete', name: 'delete', orderable: false, searchable: false }
            ]
        });
    } else {
        // If already initialized, reload the data
        preListTable.DataTable().ajax.reload();
    }
}
</script>



<script>
function showNotification(message, type = 'info', from = 'top', align = 'left', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> پیام </span>';
    
    if (style === "withicon") {
        content.icon = 'fa fa-bell';
    } else {
        content.icon = 'none';
    }
    content.url = '#';
    content.target = '_blank';

    $.notify(content, {
        type: type, // Default, Primary, Secondary, Info, Success, Warning, Danger
        placement: {
            from: from, // top, bottom
            align: align // right, center, left
        },
        time: 500
    });
}

// ====================== add new record =====================

function addNewRecord(id) {
    var form = $('#buyPreListForm')[0];
    var formData = new FormData(form);

    $('#loading').show();
    $('#nameError').text('');
    $('#imageError').text('');

    $.ajax({
        url: '/buyprelist/store',
        type: 'POST',
        data: formData,
        processData: false, // Required for FormData
        contentType: false, // Required for FormData
        success: (response) => {
            $('#loading').hide();

            if (response.status === 'success') {
                fetchList(); 
                if (id === 2) {
                    $('#add_form_collapse').collapse('hide');
                }
                $('#name').val('');
                $('input[name="image"]').val(''); // clear file input
                showNotification('موفقانه ثبت گردید', 'success', 'top', 'right', 'withicon');
            } else {
                showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading').hide();
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                if (errors?.name) {
                    $('#nameError').text(errors.name[0]);
                }
                if (errors?.image) {
                    $('#imageError').text(errors.image[0]);
                }
            } else {
                showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
            }
        }
    });
}

// =============================== edit the record ======================
$('table').on('click', '.editIcon', function () {
    $('#EditModal').modal('show');
    $('#edit_loader').show();
    const id = $(this).data('id');
    $.ajax({
        url: `/buyprelist/${id}`,
        type: 'GET',
        success: (result) => {
            $('#EditFormWrapper').html(result);
            $('#edit_loader').hide();

            // Initialize Select2 after the form has been injected
            $(".select2").select2();
        },
        error: () => {
            $('#edit_loader').hide();
            alert('اطلاعات یافت نشد');
        }
    });
});


$('#updateSubmitBtn').on('click', function () {

     // Create FormData object
     var formData = new FormData($('#updatePreListForm')[0]);
     // Include CSRF token manually
     formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    // Show loading state
    $('#loading2').show();

    // Clear previous error messages
    $('#branchIdError2').text('');
    $('#nameError2').text('');
    $('#imageError2').text('');

    // AJAX form submission
    $.ajax({
        url: '/buyprelist/update', // The actual route for updating data
        type: 'POST', // Laravel supports PATCH, but some servers require POST + _method
        data: formData ,
        contentType: false,  // Important for file upload
        processData: false,  // Prevent jQuery from processing the data
        success: (response) => {
            $('#loading2').hide();
            if (response.status === 'success') {
                fetchList();
                showNotification('موفقانه ویرایش گردید', 'success', 'top', 'right', 'withicon');
                $('#EditModal').modal('hide');
            } else {
                showNotification('ویرایش نگردید', 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading2').hide();
            if (xhr.status === 422) { // Laravel validation error status code
                var errors = xhr.responseJSON.errors;
                if (errors?.branch_id) {
                    $('#branchIdError2').text(errors.branch_id[0]);
                }
                if (errors?.name) {
                    $('#nameError2').text(errors.name[0]);
                }
                if (errors?.image) {
                    $('#imageError2').text(errors.image[0]);
                }
            } else {
                showNotification('ثبت نگردید', 'danger', 'top', 'right', 'withicon');
            }
        }
    });
});

// Delete 
$('table').on('click', '.deleteIcon', function () {
    const id = $(this).data('id');
        if (id && confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/buyprelist/destroy/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchList();
                        showNotification(response.message, 'success', 'top', 'right', 'withicon');
                    } else {
                       showNotification('حذف نگردید', 'danger', 'top', 'right', 'withicon');
                    }
                },
                error: () => {
                    showNotification('حذف نگردید', 'danger', 'top', 'right', 'withicon');
                }
            });
        }
});

</script>



@endsection

