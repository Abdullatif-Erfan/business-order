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
            "url": "<?php echo site_url('warehouseItem/warehouseItem/itemList')?>",
            "type": "POST",
            "dataType": "json",
            "data": function ( data ) 
            {
                data.warehouse_id = $('#warehouse_id').val();
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

            var boughtTotal = api.column(7).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0).toFixed(2);

			var reminedTotal = api.column(8).data().reduce(function(a, b) {
				return intVal(a) + intVal(b);
			}, 0).toFixed(2);

	        $( api.column( 0 ).footer() ).html('');
            $( api.column( 1 ).footer() ).html('');
            $( api.column( 2 ).footer() ).html('');
            $( api.column( 3 ).footer() ).html('');
            $( api.column( 4 ).footer() ).html('');
            $( api.column( 5 ).footer() ).html('');
            $( api.column( 6 ).footer() ).html('مجموع');
            $( api.column( 7 ).footer() ).html('<span style="color:blue;">'+addCommaSeparator(boughtTotal)+'</span>');
            $( api.column( 8 ).footer() ).html('<span style="color:blue;">'+addCommaSeparator(reminedTotal)+'</span>');
            $( api.column( 9 ).footer() ).html('');
            $( api.column( 10 ).footer() ).html('');


        },

    });
    $('#btn-filter').click(function(){ 
        table.ajax.reload();  
    });
    $('#btn-reset').click(function(){ 
        data.full_name = '';
        data.country_id = '';
        data.nation_id = '';
        data.village_id = '';
        // $('#form-filter')[0].reset();
        // table.ajax.reload();  
    });
});
}

function addCommaSeparator(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
</script>