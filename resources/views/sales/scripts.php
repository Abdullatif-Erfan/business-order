<script type="text/javascript">
window.onload = function(e){
	var table; var target = [10];
$(document).ready(function() {
    table = $('#table').DataTable({ 
        "processing": true, 
        "serverSide": true, 
        "order": [], 
        dom: 'lBfrtip',             
        "ajax": {
            "url": "<?php echo site_url('sales/sales/salesLists')?>",
            "type": "POST",
            "dataType": "json",
            "data": function ( data ) 
            {
                data.customer_name = $('#customer_name').val();
                data.item_name = $('#item_name').val();
                data.currency_id = $('#currency_id').val();
                data.idate = $('#idate').val();
            }
        },
        "columnDefs":[  
        {  
            "targets":target,  
            "className": "hidden-print"
        },  
        ],
        "lengthMenu" : [[10, 25, 50, 100, -1 ], [10, 25, 50, 100, "همه"]], "pageLength" : 10,
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // converting to interger to find total
            var intVal = function ( i ) {
                return typeof i === 'string' ? parseFloat(i.replace(/[\$,]/g, '')) :
                 typeof i === 'number' ? parseFloat(i) : 0;
            };

            var total = api.column(8).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0).toFixed(2);

	        $( api.column( 0 ).footer() ).html('');
            $( api.column( 1 ).footer() ).html('');
            $( api.column( 2 ).footer() ).html('');
            $( api.column( 3 ).footer() ).html('');
            $( api.column( 4 ).footer() ).html('');
            $( api.column( 5 ).footer() ).html('');
            $( api.column( 6 ).footer() ).html('');
            $( api.column( 7 ).footer() ).html('مجموع');
            $( api.column( 8 ).footer() ).html('<span style="color:blue;">'+addCommaSeparator(total)+'</span>');
            $( api.column( 9 ).footer() ).html('');
        },

    });
    $('#btn-filter').click(function(){ 
        table.ajax.reload();  
    });
    $('#btn-reset').click(function(){ 
    });
});
}

function addCommaSeparator(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
</script>