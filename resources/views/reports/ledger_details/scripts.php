<script type="text/javascript">
window.onload = function(e){
	var table2; var target = [7];
// var table; var target = [];
// var is_admin = $('#is_admin').val();
// if(is_admin == 1) { target = [7,8,9]; } else { target = [7]; }
$(document).ready(function() {
    table2 = $('#table2').DataTable({ 
        "processing": true, 
        "serverSide": true, 
        "order": [], 
        dom: 'lBfrtip',             
        "ajax": {
            "url": "<?php echo site_url('ledger_details_list')?>",
            "type": "POST",
            "dataType": "json",
            "data": function ( data ) {
                
                data.default_account_id = $('#default_account_id').val();
                data.default_currency_id = $('#default_currency_id').val();

                data.currency_id = $('#currency_id').val();
                data.details = $('#details').val();
                data.start_date = $('#start_date').val();
                data.end_date = $('#end_date').val();
                data.year_search = $('#year_search').val();
                // alert(data.default_account_id);
            }
        },
        "columnDefs":[  
        {  
            // "targets":target,  
            "className": "hidden-print"
        },  
        ],
        "lengthMenu" : [[10, 25, 50, 100, -1 ], [10, 25, 50, 100, "همه"]], "pageLength" : 10,
		"footerCallback": function ( row, data, start, end, display ) 
        {
            var account_id = $('#default_account_id').val();
            var api = this.api(), data;
            // converting to interger to find total
            var intVal = function ( i ) {
                return typeof i === 'string' ? parseFloat(i.replace(/[\$,]/g, '')) :
                 typeof i === 'number' ? parseFloat(i) : 0;
            };
				
            var recieved_cache = api.ajax.json().recieved_cache;
            var recieved_loan  = api.ajax.json().recieved_loan;
            var payed_cache    = api.ajax.json().payed_cache;
            var payed_loan     = api.ajax.json().payed_loan;
            var parent_code    = api.ajax.json().parent_code;
            var cur_balance    = api.ajax.json().cur_balance;


            var balance = 0;
            var recieved = 0;
            var paid = 0;
			var label = '';
            
            if(parseInt(parent_code) === 1000)
            {
				label = 'نقد';
				paid = parseInt(payed_cache);
				recieved = parseInt(recieved_cache);

                balance = parseInt(recieved_cache) - parseInt(payed_cache);
            }
            else
            {
				label = 'قرض';
				paid = parseInt(payed_loan);
				recieved = parseInt(recieved_loan);

                balance = parseInt(payed_loan) - parseInt(recieved_loan);
            }
    
	        $( api.column( 0 ).footer() ).html('');
            $( api.column( 1 ).footer() ).html('<b> بردگی '+label+' </b>'+' &nbsp; '+'<span class="badge">'+addCommaSeparator(recieved)+'</span>');
            $( api.column( 2 ).footer() ).html('');
            $( api.column( 3 ).footer() ).html('<b> رسیدگی '+label+'  </b>'+' &nbsp; '+'<span class="badge">'+addCommaSeparator(paid)+'</span>');
            $( api.column( 4 ).footer() ).html('');
			$( api.column( 5 ).footer() ).html('<b>بیلانس</b>'+' &nbsp; '+'<span class="badge badge-info">'+addCommaSeparator(balance)+'</span>');
            $( api.column( 6 ).footer() ).html('');
            
            $( api.column( 7 ).footer() ).html('');

			$( api.column( 8 ).footer() ).html('');
			$( api.column( 9 ).footer() ).html('');
			$( api.column( 10 ).footer() ).html('');
            
        },
    });
    $('#btn-search').click(function(){ 
        table2.ajax.reload();  
    });
    $('#btn-reset').click(function(){ 
    });
    function addCommaSeparator(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
});
}
</script>
