@extends('layouts.app')
@section('content')

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                       
                        <div class="card-body">

                        <h3 style="margin-bottom: 15px">
                        {{__('buy.list_title')}} </h3>
                    
                    <!-- insertion -->
                      <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_form_collapse" aria-expanded="false">
                            <button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
                                <span class="fas fa-plus-square"></span>  &nbsp; {{__('common.add')}} </button>
                            </a> 
                        </div>
                        <div id="add_form_collapse" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
                            <div class="box-body">
                            <form  id="buyPreListForm">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                                    
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="form-group">
                                           <select name="category_id" class="form-control select2" style="width:100%">
                                                <option value="">{{__('buy.select_category')}}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div>
                                                    
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="form-group">
                                            <input class="form-control" id="name" name="name" type="text" required placeholder="{{__('common.item_name')}}" >
                                            <span id="nameError" class="text-danger"></span>
                                        </div> 
                                    </div>	


                                    <div class="col-md-2 col-sm-4 col-xs-12 center m-t-10">
                                        <button type="button" name="submit" class="btn btn-info btn-sm m-l-10" onclick="addNewRecord(1)"  >
                                          <span class="btn-label"> <i class="fa fa-save"></i> </span> {{__('buy.save_and_resume')}}
                                        </button>
                                    </div>

                                    <div class="col-md-2 col-sm-4 col-xs-12 center m-t-10">
                                        <button type="button" name="submit" class="btn btn-primary btn-sm m-l-10" onclick="addNewRecord(2)"  >
                                          <span class="btn-label"> <i class="fa fa-save"></i> </span> {{__('common.save')}}
                                        </button>
                                    </div>

                                    <div class="col-12">
                                        <div id="loading" style="display:none; text-align: center;">
                                            <i class="fa fa-spinner fa-spin"></i>{{__('common.loading')}}
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
                                            <th>{{__('common.number')}}     </th>
                                            <th>{{__('buy.category')}}     </th>
                                            <th>{{__('common.item_name')}}  </th>
                                            <th>{{__('common.edit')}}       </th>
                                            <th>{{__('common.delete')}}     </th>
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
                <h5 class="modal-title"> {{__('common.edit')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="EditFormWrapper"></div>
                <div id="edit_loader" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i>  {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> {{__('common.close')}}</button>
                <button type="submit" class="btn btn-success btn-sm m-r-10" id="updateSubmitBtn"> {{__('common.save')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
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
                showNotification("{{__('common.added_successfully')}}", 'success', 'top', 'right', 'withicon');
            } else {
                showNotification("{{__('common.add_failed')}}", 'danger', 'top', 'right', 'withicon');
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
                showNotification("{{__('common.add_failed')}}", 'danger', 'top', 'right', 'withicon');
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
    
    // Include CSRF token
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    // Show loading state
    $('#loading2').show();

    // Clear previous error messages
    $('#branchIdError2').text('');
    $('#nameError2').text('');
    $('#imageError2').text('');

    // AJAX form submission
    $.ajax({
        url: '{{ route("buyprelist.update") }}', // Use the named route
        type: 'POST', // Matches your route definition
        data: formData,
        contentType: false,
        processData: false,
        success: (response) => {
            $('#loading2').hide();
            if (response.status === 'success') {
                fetchList();
                showNotification("{{__('common.updated_successfully')}}", 'success', 'top', 'right', 'withicon');
                $('#EditModal').modal('hide');
            } else {
                showNotification("{{__('common.update_failed')}}", 'danger', 'top', 'right', 'withicon');
            }
        },
        error: (xhr) => {
            $('#loading2').hide();
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                if (errors?.name) {
                    $('#nameError2').text(errors.name[0]);
                }
                if (errors?.image) {
                    $('#imageError2').text(errors.image[0]);
                }
            } else {
                showNotification("{{__('common.update_failed')}}", 'danger', 'top', 'right', 'withicon');
            }
        }
    });
});

</script>

<script>
function showNotification(message, type = 'info', from = 'top', align = 'left', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> {{ __('settings.message') }} </span>';
    
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
            pageLength: 10,   
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

// Delete 
$('table').on('click', '.deleteIcon', function () {
    const id = $(this).data('id');
        if (id && confirm("{{ __('common.delete_confirm') }}")) {
            $.ajax({
                url: `/buyprelist/destroy/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status == 'success') {
                        showNotification("{{__('common.deleted_successfully')}}", 'success', 'top', 'right', 'withicon');
                        fetchList();
                    } else {
                       showNotification("{{__('common.delete_failed')}}", 'danger', 'top', 'right', 'withicon');
                    }
                },
                error: () => {
                    showNotification("{{__('common.delete_failed')}}", 'danger', 'top', 'right', 'withicon');
                }
            });
        }
});

</script>



@endsection

