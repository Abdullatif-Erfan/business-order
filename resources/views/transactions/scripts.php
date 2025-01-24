<script type="text/javascript">
var csrfToken = "<?php echo $this->security->get_csrf_hash(); ?>";
window.onload = function(e){
var table; var target = [8,9];
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
            "url": "<?php echo site_url('transactions/transaction/transaction_list')?>",
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
    function show_add_modal()
    {
        jQuery("#addJournal").modal('show');
        // window.setTimeout(function()
        // {
        //     $('#showLoader').html('<center><img src="<?php echo base_url(); ?>assets/img/loader.gif" style="width:20%;margin-top:30px;margin-bottom:20px" alt="Loading"/></center>');
        // }, 1000);
        
    }
// $(document).on('click','#memberDetails',function(e){
// 	e.preventDefault();
// 	var id = $(this).data('id');
// 	jQuery("#detailsModal").modal('show');
// 	getMemberDetailsData(id);		
// })
// function getMemberDetailsData(id)
// {
//     $('#memberDetailsData').html('<center><img src="<?php echo base_url(); ?>assets/img/job_loader.gif" style="width:100%;margin-top:30px;margin-bottom:20px" alt="Loading"/></center>');
// 	$.ajax({
// 		type:'POST',
// 		data:{id:id},
// 		url:"<?php echo base_url() . 'member/member/getMemberDetails'; ?>",
// 		success: function(result)
// 		{
// 			$("#memberDetailsData").html(result);
// 		},
// 		error: function (xhr, status) {
// 			$('#memberDetailsData').html('Error, به نسبت ضعیف بودن انترنت جزییات نشان داده نشد ');
// 		}
// 	});
// }
// function showNextMember(action, user_id)
// {
//     $('#memberDetailsData').html('<center><img src="<?php echo base_url(); ?>assets/img/job_loader.gif" style="width:100%;margin-top:30px;margin-bottom:20px" alt="Loading"/></center>');
// 	$.ajax({
// 		type:'POST',
// 		data:{id:user_id,action:action},
// 		url:"<?php echo base_url() . 'member/member/getNextMemberDeta'; ?>",
// 		success: function(result)
// 		{
// 			$("#memberDetailsData").html(result);
// 		},
// 		error: function (xhr, status) {
// 			$('#memberDetailsData').html('Error, به نسبت ضعیف بودن انترنت جزییات نشان داده نشد ');
// 		}
// 	});
// }
// $(document).on('click','#addToCommittee',function(e){
// 	e.preventDefault();
// 	var id = $(this).data('id');
// 	var name = $(this).data('id2');
// 	$('#mem_id').val(id);
// 	$('#memberFullName').html("علاوه کردن " + name + " در یکی از کمیته های ذیل ");
// 	jQuery("#committeeModal").modal('show');
// 	getCommitteeData(id);	
// })

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



// function getCommitteeData()
// {
// 	$('#getCommitteeData').html('<center><img src="<?php echo base_url(); ?>assets/img/loader.gif" style="width:20%;margin-top:30px;margin-bottom:20px" alt="Loading"/></center>');
// 	$.ajax({
// 		type:'POST',
// 		data:{},
// 		url:"<?php echo base_url() . 'member/member/getCommitteeData'; ?>",
// 		success: function(result)
// 		{
// 			$("#getCommitteeData").html(result);
// 		},
// 		error: function (xhr, status) {
// 			$('#requestEdit').html('Error, به نسبت ضعیف بودن انترنت جزییات نشان داده نشد ');
// 		}
// 	});
// }
		
</script>