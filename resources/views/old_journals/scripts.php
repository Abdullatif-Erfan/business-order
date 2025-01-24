<script type="text/javascript">
window.onload = function(e){
	var table; var target = [8];
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
            "url": "<?php echo site_url('old_journals/old_journal/journal_list')?>",
            "type": "POST",
            "dataType": "json",
            "data": function ( data ) {

                data.account_id = $('#account_id').val();
                data.currency_id = $('#currency_id').val();
                data.start_date = $('#start_date').val();
                data.end_date = $('#end_date').val();
                data.code_number = $('#code_number').val();
                // alert( data.code_number );
                // alet(data.currency);
                // data.pashiCsrf = csrfHash;
                // console.log(csrfHash);
            }
        },
        "columnDefs":[  
        {  
            "targets":target,  
            "className": "hidden-print"
        },  
        ],
        "lengthMenu" : [[10, 25, 50, 100, -1 ], [10, 25, 50, 100, "همه"]], "pageLength" : 10,
        // "footerCallback": function ( row, data, start, end, display ) {
        //     var api = this.api(), data;
 
        //     // converting to interger to find total
        //     var intVal = function ( i ) {
        //         return typeof i === 'string' ? parseFloat(i.replace(/[\$,]/g, '')) :
        //          typeof i === 'number' ? parseFloat(i) : 0;
        //     };

         
	    //     var total_debit = api.ajax.json().total_debit;
        //     // Format total_debit with separators
        //     var total_debit_formatted = parseFloat(total_debit).toLocaleString();

        //     var total_credit = api.ajax.json().total_credit;
        //     var total_credit_formatted = parseFloat(total_credit).toLocaleString();

        //     var balance = parseFloat(total_debit) - parseFloat(total_credit);
        //     var blanced_formatted = parseFloat(balance).toLocaleString();

	    //     $( api.column( 0 ).footer() ).html('');
        //     $( api.column( 1 ).footer() ).html('');
        //     $( api.column( 2 ).footer() ).html('');
        //     $( api.column( 3 ).footer() ).html('مجموع');
            
        //     $( api.column( 4 ).footer() ).html('<span style="color:blue;">'+total_debit_formatted+'</span>');
        //     $( api.column( 5 ).footer() ).html('<span style="color:blue;">'+total_credit_formatted+'</span>');
        //     $( api.column( 6 ).footer() ).html('<span style="color:blue;">'+blanced_formatted+'</span>');

        //     $( api.column( 7 ).footer() ).html('');
        //     $( api.column( 8 ).footer() ).html('');

        // },

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
</script>
<script>
$(document).on('click','#journalDetails',function(e){
    var id = $(this).data('id');
    var url = 'reports/ledger/'+ id;
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