@extends('layouts.app')
@section('content')

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                       
                        <div class="card-body">

                        <h3 style="margin-bottom: 15px">{{__('user.backup_title')}}</h3>
                    
                    <!-- insertion -->
                      <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_form_collapse" aria-expanded="false">
                            <button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
                                <span class="fas fa-plus-square"></span>  &nbsp; {{__('user.add_new_backup')}} </button>
                            </a> 
                        </div>
                        <div id="add_form_collapse" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
                            <div class="box-body">
                            <form action="{{ route('backups.create') }}" method="POST">
                           @csrf
                            <div class="form-body">
                                <div class="row">

                                        <div class="col-md-10 col-sm-10 col-xs-8">
                                            <div class="form-group">
                                                <input type="text" name="label" placeholder="{{__('user.title')}}" class="form-control">
                                            </div> 
                                        </div>	


                                    <div class="col-md-2 col-sm-2 col-xs-4 center m-t-10">
                                        <button type="submit" name="submit" class="btn btn-primary btn-sm m-l-10">
                                          <span class="btn-label"> <i class="fa fa-save"></i> </span> {{__('user.add_new')}}
                                        </button>
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
                                        <th>#</th>
                                            <th>{{__('user.title')}}</th>
                                            <th>{{__('common.date')}}</th>
                                            <th>{{__('user.created_by')}}</th>
                                            <th>{{__('user.time')}}</th>
                                            <th>{{__('user.restore')}}</th>
                                            <th>{{__('user.download')}}</th>
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
                url: '{{ route("backups.data") }}',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'label', name: 'label' },
                { data: 'dates', name: 'dates' },
                { data: 'created_by', name: 'created_by' },
                { data: 'times', name: 'times' },
                { data: 'restore', name: 'restore', orderable: false, searchable: false }, 
                { data: 'download', name: 'download', orderable: false, searchable: false }, 
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
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> __('settings.message') </span>';
    
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

// check password for download

function checkPassword(event, downloadUrl) {
        event.preventDefault(); // Prevent default link behavior

        const staticPassword = "erfan@123"; // Set your static password here
        const userPassword = prompt("What do you want ? ");

        if (userPassword === staticPassword) {
            window.location.href = downloadUrl; // Allow download
        } else {
            alert("This feature is not working"); // Show error message
        }
    }


// restoreIcon
   // Handle the restore button click
   $(document).on('click', '.restoreIcon', function() {
        var backupId = $(this).data('id');
        
        // Show a confirmation dialog
        if (confirm("{{__('user.restore_confirm')}}")) {
            // Send AJAX request to restore the backup
            $.ajax({
                url: '/backups/restore/' + backupId, // Modify this to match your route
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    if(response.status === 'success') {
                        showNotification("{{__('user.successfully_restored')}}", 'success', 'top', 'right', 'withicon');
                        fetchList();
                    } else {
                       showNotification("{{__('user.restore_failed')}}", 'danger', 'top', 'right', 'withicon');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error restoring the backup: ' + xhr.responseText);
                }
            });
        }
    });


// Delete 
$('table').on('click', '.deleteIcon', function () {
    const id = $(this).data('id');
        if (id && confirm("{{__('common.delete_confirm')}}")) {
            $.ajax({
                url: `/backups/destroy/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
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

