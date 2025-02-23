<!-- For Persian Date Picker -->
<script src="{{ asset('assets/datepicker/jalaali.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $('#input1').change(function() {  
        var $this = $(this), value = $this.val();  
        alert(value);
    });

    $('#textbox1').change(function () {  
        var $this = $(this), value = $this.val(); 
        alert(value); 
    });

    $('[data-name="disable-button"]').click(function() {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', true);
    });

    $('[data-name="enable-button"]').click(function () {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', false);
    });
</script>


<script>
function showNotification(message, type = 'info', from = 'top', align = 'right', style = 'withicon') {
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
</script>

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