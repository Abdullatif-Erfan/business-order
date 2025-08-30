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
                            {{__('production.model_list')}}
                        </h3>
                    
                    <!-- insertion -->
                      <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_form_collapse" aria-expanded="false">
                            <button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
                                <span class="fas fa-plus-square"></span>  &nbsp; {{__('common.add')}} </button>
                            </a> 
                        </div>
                        <div id="add_form_collapse" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
                            <div class="box-body">
                            <form  id="productionForm">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <input type="hidden" name="branch_id" value="{{$branch_id}}" />
                                    <div class="col-md-8 col-sm-8 col-xs-6">
                                        <div class="form-group">
                                            <input class="form-control" id="name" name="name" type="text" required
                                             placeholder="{{__('common.item_name')}}" >
                                            <span id="nameError" class="text-danger"></span>
                                        </div> 
                                    </div>	


                                    <div class="col-md-2 col-sm-4 col-xs-12 center m-t-10">
                                        <button type="button" name="submit" class="btn btn-primary btn-sm m-l-10" 
                                        onclick="addNewModalRecord()"  >
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
                            <table id="modelTable"  class="table table-bordered table-striped table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th>{{__('common.number')}}     </th>
                                            <th>{{__('common.item_name')}}  </th>
                                            <th>{{__('production.unit_price')}}</th>
                                            <th>{{__('production.unit')}}</th>
                                            <th>{{__('production.add_sub_items')}}  </th>
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

 function addNewModalRecord() {
    var form = document.getElementById('productionForm');
    // console.log("Form element:", form);
    // console.log("Tag name:", form ? form.tagName : "not found");

    if (!form) {
        alert("Form not found!");
        return;
    }

    var formData = new FormData(form);
    // console.log([...formData]); // see form values

    $('#loading').show();
    $('#nameError').text('');

    $.ajax({
        url: '/model/store',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: (response) => {
            $('#loading').hide();
            fetchList();
            if (response.status === 'success') {
                $('#name').val('');
                $('#add_form_collapse').collapse('hide');
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
            } else {
                showNotification("{{__('common.add_failed')}}", 'danger', 'top', 'right', 'withicon');
            }
        }
    });
}

</script>


<script>
// =============================== edit the record ======================
$('table').on('click', '.editIcon', function () {
    $('#EditModal').modal('show');
    $('#edit_loader').show();
    const id = $(this).data('id');
    $.ajax({
        url: `/model/${id}`,
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
    // var formData = new FormData($('#updateModelForm')[0]);

    var formData = $('#updateModelForm').serialize();

    $('#loading2').show();
    $('#nameError2').text('');

    $.ajax({
        // url: '{{ route("model.update") }}',
        url: '/model/update',
        type: 'PATCH',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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
    const modelTable = $('#modelTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(modelTable)) {
        // Initialize DataTable if not already initialized
        modelTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("model.data") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'model_details_total_price', name: 'model_details_total_price' },
                { data: 'currency_name', name: 'currency_name' },
                { data: 'addItem', name: 'addItem', orderable: false, searchable: false }, 
                { data: 'edit', name: 'edit', orderable: false, searchable: false }, 
                { data: 'delete', name: 'delete', orderable: false, searchable: false }
            ]
        });
    } else {
        // If already initialized, reload the data
        modelTable.DataTable().ajax.reload();
    }
}
</script>



<script>

// Delete 
$('table').on('click', '.deleteIcon', function () {
    const id = $(this).data('id');
        if (id && confirm("{{ __('common.delete_confirm') }}")) {
            $.ajax({
                url: `/model/destroy/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        fetchList();
                        showNotification("{{__('common.deleted_successfully')}}", 'success', 'top', 'right', 'withicon');
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

