<script>
function general()
{
	localStorage.setItem('id',"");
    localStorage.setItem('function',"");
}
function showBackupList(){
	$.ajax({
        type:'POST',
        data:{},
        url:"<?php echo base_url() . 'settings/backup/showCreatedBackup'; ?>",
        success: function(result)
        {
            $("#backup").html(result);
			localStorage.setItem('id','2');
        	localStorage.setItem('function','backup');
        }
		  error: function(xhr, status, error) {
            // Handle error response
            console.log("Ajax request failed: " + error);
        }
    });
}
window.onload = function(e)
{   
  if(localStorage.getItem('id')>=1)
  {
    var functions = localStorage.getItem('function');
    if(functions=="backup"){ showBackupList(); }
  } 
  else
  {
    //   general();
	localStorage.setItem('id',"");
    localStorage.setItem('function',"");
  }
}
// function calculate_reverse_amount() {
//     var to_currency_amount = parseInt($('#to_currency_amount').val()); 
// 	alert(to_currency_amount);
// }
</script>

<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
			
					
				<div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->
											

					<ul class="nav my_nave nav-tabs" id="myTab2">
						<li class="active"><a data-toggle="tab" href="#rate">نرخ اسعار</a></li>
						<li><a data-toggle="tab" href="#online_rate"> نرخ اسعار آنلاین</a></li>
					</ul>

					 <div class="tab-content">


					<!-- branch -->
						<div id="rate" class="tab-pane fade in active"> 
						  <br>  <?php $this->load->view('rates/add');  ?>
						  <br>  <?php $this->load->view('rates/list'); ?>      
						</div>
					<!-- / branch -->

					<!-- online_rate -->
						<div id="online_rate" class="tab-pane fade">
						  <br> <?php  $this->load->view('rates/online_rate'); ?> 
						</div> 
					<!-- /online_rate -->


						</div>

					   </div> <!-- / card-body -->
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>

				<!-- footer -->
				<?php $this->load->view('component/footer-text.php'); ?>
				<!-- /footer -->
			</div>
        <!-- /main content -->
        