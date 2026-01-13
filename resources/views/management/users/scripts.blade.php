<!-- For Persian Date Picker -->
<script src="{{ asset('assets/datepicker/jalaali.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function() {
    fetchList();

    // Move the filter button click event outside
    $('#btn-filter').click(function() {
        $('#userTable').DataTable().ajax.reload(null, false); // Reload data without resetting pagination
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
            ajax: {  
                url: '{{ route("user.data") }}',
                // url: '{{ route("boughtList.data") }}',
                data: function (d) {
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'full_name', name: 'full_name' },
                { data: 'user_name', name: 'user_name' },
                { data: 'branch_relation.name', name: 'branch_relation.name' },
                { data: 'email', name: 'email' },
                { data: 'photo', name: 'photo',orderable: false, searchable: false  },
                { data: 'priviledge', name: 'priviledge', orderable: false, searchable: false },
                { data: 'relogin', name: 'relogin', orderable: false, searchable: false },
                { data: 'edit', name: 'edit', orderable: false, searchable: false  },
                { data: 'delete', name: 'delete', orderable: false, searchable: false }
            ],
        });

    } else {
        userTable.DataTable().ajax.reload(null, false);
    }
}
</script>