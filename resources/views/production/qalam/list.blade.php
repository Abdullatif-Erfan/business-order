@extends('layouts.app')

@section('content')


<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px;">
                              
                          @if(auth()->user()->hasAccess('production','create_records'))
                                <a href="{{ route('qalam.create') }}">
                                    <button type="button" class="btn btn-sm mybtn">
                                        <i class="fas fa-plus"></i> {{ __('common.add')}}
                                    </button>
                                </a>
                            @endif

                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                        </div>


                        <div class="filterForm" id="searchWrapper1">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-5 col-sm-6 col-xs-6">
                                        <input type="text" id="item_name" placeholder="{{__('common.item_name')}}" class="form-control">
                                    </div>

                                    <div class="col-md-5 col-sm-6 col-xs-6 m-b-4">
                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id">
                                            <!-- <option value="">  واحد پولی </option> -->
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <button class="btn mybtn search_btn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /filter_form -->


                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <span class="pull-left visible-print">{{__('common.print_date')}} : {{ $todaysDate }}</span>
                                <table id="qalamTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                            <center> {{__('wh.existing_list')}}  ???  </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{__('common.number')}}     </th>
                                            <th> قلم   </th>
                                            <th> مقدار</th>
                                            <th> واحد </th>
                                            <th> واحد پولی </th>
                                            <th> قیمت فی واحد </th>
                                            <th> قیمت مجموعی </th>
                                            <th> تاریخ </th>
                                            <th> کاربر </th>
                                            <th>{{__('common.edit')}}       </th>
                                            <th>{{__('common.delete')}}     </th>
                                        </tr>
                                    </thead>
                                    <!-- <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="3">{{__('common.total')}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>   -->
                                </table>
                            </div> <!-- /table responsive -->
                        </div> <!-- /card-body -->
                    </div> <!-- /card -->
                </div> <!-- /col-md-12 -->
            </div> <!-- /row -->
        </div> <!-- /page-inner -->
    </div> <!-- /content -->
</div> <!-- /main content -->



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
    const qalamTable = $('#qalamTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(qalamTable)) {
        // Initialize DataTable if not already initialized
        qalamTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("qalam.data") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'model_relation.name', name: 'model_relation.name' },
                { data: 'amount', name: 'amount' },
                { data: 'unit_relation.name', name: 'unit_relation.name' },
                { data: 'currency_relation.name', name: 'currency_relation.name' },
                { data: 'unit_price', name: 'unit_price' },
                { data: 'total_price', name: 'total_price' },
                { data: 'dates', name: 'dates' },
                { data: 'user', name: 'user' },
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

