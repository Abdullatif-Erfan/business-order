<script type="text/javascript">
var csrfToken = "<?php echo $this->security->get_csrf_hash(); ?>";
window.onload = function(e){
var table; var target = [7,8];
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
            "url": "<?php echo site_url('exchanges/exchange/exchange_list')?>",
            "type": "POST",
            "dataType": "json",
            "data": function ( data ) {

                data.account_id = $('#account_id').val();
                data.currency_id = $('#currency_id').val();
                data.start_date = $('#start_date').val();
                data.end_date = $('#end_date').val();
                // data.pashiCsrf = csrfToken;
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
        "lengthMenu" : [[10, 25, 50, 100, -1 ], [10, 25, 50, 100, "همه"]], "pageLength" : 10
    });
    $('#btn-filter').click(function(){ 
        table.ajax.reload();  
    });
    $('#btn-reset').click(function(){ 
        data.account_id = '';
        data.currency_id = '';
        data.start_date = '';
        data.end_date = '';
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
</script>