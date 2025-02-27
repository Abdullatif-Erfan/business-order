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

                        <h3 style="margin-bottom: 15px">لیست بک اپ</h3>
                    
                    <!-- insertion -->
                      <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_form_collapse" aria-expanded="false">
                            <button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
                                <span class="fas fa-plus-square"></span>  &nbsp; ایجاد بک اپ جدید </button>
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
                                                <input type="text" name="label" placeholder="عنوان" class="form-control">
                                            </div> 
                                        </div>	


                                    <div class="col-md-2 col-sm-2 col-xs-4 center m-t-10">
                                        <button type="submit" name="submit" class="btn btn-primary btn-sm m-l-10">
                                          <span class="btn-label"> <i class="fa fa-save"></i> </span> ایجاد جدید
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
                                            <th>عنوان</th>
                                            <th>تاریخ</th>
                                            <th>زمان</th>
                                            <th>ریستور</th>
                                            <th>دانلود</th>
                                            <th>حذف</th>
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
        if (confirm('آیا میخواهید ریستور نمایید ؟')) {
            // Send AJAX request to restore the backup
            $.ajax({
                url: '/backups/restore/' + backupId, // Modify this to match your route
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    if(response.status === 'success') {
                        showNotification('موفقانه ریستور گردید', 'success', 'top', 'right', 'withicon');
                        fetchList();
                    } else {
                       showNotification('ریستور نگردید', 'danger', 'top', 'right', 'withicon');
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
        if (id && confirm('آیا میخواهید حذف نمایید؟')) {
            $.ajax({
                url: `/backups/destroy/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (response) => {
                    if(response.status === 'success') {
                        showNotification('موفقانه حذف گردید', 'success', 'top', 'right', 'withicon');
                        fetchList();
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

