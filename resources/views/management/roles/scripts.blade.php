<script>
$(document).ready(function() {
    fetchList();

    // Move the filter button click event outside
    $('#btn-filter').click(function() {
        $('#roleTable').DataTable().ajax.reload(null, false); // Reload data without resetting pagination
    });
});


function fetchList() {
    let roleTable = $('#roleTable');

    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable(roleTable)) {
        roleTable.DataTable({
            serverSide: true,
            processing: true,
            ajax: {  
                url: '{{ route("roles.data") }}',
                // url: '{{ route("boughtList.data") }}',
                data: function (d) {
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'role', name: 'role' },
                { data: 'status', name: 'status' },
                { data: 'add', name: 'add', orderable: false, searchable: false },
                { data: 'edit', name: 'edit', orderable: false, searchable: false  },
                { data: 'delete', name: 'delete', orderable: false, searchable: false }
            ],
        });

    } else {
        roleTable.DataTable().ajax.reload(null, false);
    }
}
</script>