<script type="text/javascript">
window.onload = function(e){
	var table; var target = [1];
$(document).ready(function() {
    table = $('#table').DataTable({ 
        "processing": true, 
        "serverSide": true, 
        "order": [], 
        dom: 'lBfrtip',             
        "ajax": {
            "url": "<?php echo site_url('example/mine/list')?>",
            "type": "POST",
            "dataType": "json",
            "data": function ( data ) {

                data.name = $('#name').val();
            }
        },
        "columnDefs":[  
        {  
            "targets":target,  
            "className": "hidden-print"
        },  
        ],
        "lengthMenu" : [[10, 25, 50, 100, -1 ], [10, 25, 50, 100, "همه"]], "pageLength" : 10
    });
    $('#btn-filter').click(function(){ 
        table.ajax.reload();  
    });
});
}
</script>

