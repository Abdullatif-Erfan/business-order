<script>
$(document).ready(function() {
    fetchList();

    // Filter button (if exists)
    $('#btn-filter').click(function() {
        $('#userTable').DataTable().ajax.reload(null, false);
    });
});

function fetchList() {
    let userTable = $('#userTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(userTable)) {
        userTable.DataTable({
            serverSide: true,
            processing: true,
            pageLength: 10,   
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'همه']
            ],
            responsive: true,
            autoWidth: false,
            ajax: {  
                url: '{{ route("user.data") }}',
                data: function (d) {
                    // Add any filter data here if needed
                },
                error: function(xhr, status, error) {
                    console.log('DataTable Error:', error);
                    console.log('Response:', xhr.responseText);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'full_name', name: 'full_name' },
                { data: 'user_name', name: 'user_name' },
                { data: 'email', name: 'email' },
                { data: 'photo', name: 'photo', orderable: false, searchable: false },
                { data: 'priviledge', name: 'priviledge', orderable: false, searchable: false },
                { data: 'link', name: 'link', orderable: false, searchable: false },
                { data: 'relogin', name: 'relogin', orderable: false, searchable: false },
                { data: 'view', name: 'view', orderable: false, searchable: false },
                { data: 'edit', name: 'edit', orderable: false, searchable: false },
                { data: 'delete', name: 'delete', orderable: false, searchable: false }
            ],
            // language: {
            //     processing: "{{ __('common.loading') }}...",
            //     search: "{{ __('common.search') }}:",
            //     lengthMenu: "{{ __('common.show') }} _MENU_ {{ __('common.records') }}",
            //     info: "{{ __('common.showing') }} _START_ {{ __('common.to') }} _END_ {{ __('common.of') }} _TOTAL_ {{ __('common.records') }}",
            //     infoEmpty: "{{ __('common.no_records') }}",
            //     infoFiltered: "({{ __('common.filtered_from') }} _MAX_ {{ __('common.records') }})",
            //     zeroRecords: "{{ __('common.no_records_found') }}",
            //     emptyTable: "{{ __('common.no_data_available') }}",
            //     paginate: {
            //         first: "{{ __('common.first') }}",
            //         previous: "{{ __('common.previous') }}",
            //         next: "{{ __('common.next') }}",
            //         last: "{{ __('common.last') }}"
            //     }
            // },
            drawCallback: function() {
                // Any post-draw logic
            }
        });
    } else {
        userTable.DataTable().ajax.reload(null, false);
    }
}

// Delete confirmation
function doConfirm() {
    return confirm("{{ __('common.delete_confirm') }}");
}


// Set Profit
   $('table').on('click', '.viewUser', function () {
        $('#userViewModal').modal('show');
        $('#userViewModalLoader').show();
        var id = $(this).data('id');
        $.ajax({
            url: `/user/${id}`,
            type: 'GET',
            success: (result) => {
                $('#userViewModalContent').html(result);
                $('#userViewModalLoader').hide();
            },
            error: () => {
                $('#userViewModalLoader').hide();
                alert('اطلاعات یافت نشد');
            }
        });
});


// Notification function if needed
function showNotification(message, type = 'info') {
    if (typeof $.notify === 'function') {
        $.notify({
            message: '<span style="font-size:14px;">' + message + '</span>',
            title: '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{ __("settings.message") }}</span>',
            icon: 'fa fa-bell'
        }, {
            type: type,
            placement: {
                from: 'top',
                align: 'center'
            },
            time: 3000
        });
    }
}
</script>