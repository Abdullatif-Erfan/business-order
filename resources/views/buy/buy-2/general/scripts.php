<script type="text/javascript">
window.onload = function(e){
	var table; var target = [11,12];
// var table; var target = [];
// var is_admin = $('#is_admin').val();
// if(is_admin == 1) { target = [7,8,9]; } else { target = [7]; }
$(document).ready(function() {
    table = $('#table').DataTable({ 
        "processing": true, 
        "serverSide": true, 
        "order": [], 
        dom: 'lBfrtip',             
        "ajax": {
            "url": "<?php echo site_url('buy/buying/buyingList')?>",
            "type": "POST",
            "dataType": "json",
            "data": function ( data ) {

                data.customer_name = $('#customer_name').val();
                data.pre_list_name = $('#pre_list_name').val();
                data.currency_id = $('#currency_id').val();
                data.idate = $('#idate').val();
                data.bill_number = $('#bill_number').val();
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

            var total = api.column(7).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0).toFixed(2);

			var recieved = api.column(8).data().reduce(function(a, b) {
				return intVal(a) + intVal(b);
			}, 0).toFixed(2);

			var remained = api.column(9).data().reduce(function(a, b) {
				return intVal(a) + intVal(b);
			}, 0).toFixed(2);


	        $( api.column( 0 ).footer() ).html('');
            $( api.column( 1 ).footer() ).html('');
            $( api.column( 2 ).footer() ).html('');
            $( api.column( 3 ).footer() ).html('');
            $( api.column( 4 ).footer() ).html('');
            $( api.column( 5 ).footer() ).html('');
            $( api.column( 6 ).footer() ).html('مجموع');
            $( api.column( 7 ).footer() ).html('<span style="color:blue;">'+addCommaSeparator(total)+'</span>');
			$( api.column( 8 ).footer() ).html('<span style="color:blue;">'+addCommaSeparator(recieved)+'</span>');
			$( api.column( 9 ).footer() ).html('<span style="color:blue;">'+addCommaSeparator(remained)+'</span>');
            $( api.column( 10 ).footer() ).html('');
            $( api.column( 11 ).footer() ).html('');


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
<script>
$(document).on('click','#journalDetails',function(e){
    var account_id = $(this).data('id');
    var currency = $(this).data('id2');

    var url = 'ledgerDetails/'+ account_id + '/' + currency;
    // Open the URL in a new tab
    window.open(url, '_blank');
})


$(document).on('click','#deleteJournals',function(e){
	e.preventDefault();
	var delete_id = $(this).data('id');
    $.ajax({
        type:'POST',
        data:{delete_id: delete_id},
        url:"<?php echo base_url() . 'deleteJournals'; ?>",
        success: function(result)
        {
            if($result=1)
              {
                tempAlert(" موفقانه حذف گردید ",1000);
                window.setTimeout(function()
                {
                    document.location.reload();
                }, 1000);
              } else { tempAlert("  حذف نگردید ",1000);}
        }
    });
})		
</script>