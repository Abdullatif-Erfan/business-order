<script>
function general()
{
	localStorage.setItem('id',"");
    localStorage.setItem('function',"");
}
function showBackupList(){
	alert('ok');
	die();
	$.ajax({
        type:'POST',
        data:{},
        url:"<?php echo base_url() . 'settings/backup/showCreatedBackup'; ?>",
        success: function(result)
        {
			alert(result);
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
// window.onload = function(e)
// {   
//   if(localStorage.getItem('id')>=1)
//   {
//     var functions = localStorage.getItem('function');
//     if(functions=="backup"){ showBackupList(); }
//   } 
//   else
//   {
//     //   general();
// 	localStorage.setItem('id',"");
//     localStorage.setItem('function',"");
//   }
// }
</script>
<script>
//     document.addEventListener("DOMContentLoaded", function() {
//     var backupLink = document.getElementById("backupLink");

//     // Attach the click event listener
//     backupLink.addEventListener("click", function(event) {
//         // Call the getBackupList() function
// 		console.log('getBackupList is called');
//         getBackupList();
// 		// alert('ok');
//     });
// });
function getBackupList()
{
	// $('#backupWrapper').html('<center><img src="<?php echo base_url(); ?>assets/img/job_loader.gif" style="width:20%;margin-top:20px;" alt="Loading"/></center>');
	// alert('called');
	$.ajax({
        type:'POST',
        data:{},
        url:"<?php echo base_url() . 'settings/backup/showCreatedBackup'; ?>",
        success: function(result)
        {
			// alert(result);
			// die();
            $("#backupWrapper").html(result);
        }
		  error: function(xhr, status, error) {
            // Handle error response
            console.log("Ajax request failed: " + error);
        }
    });
}
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
						<!-- <li class="active"><a data-toggle="tab" href="#branch">شعبه</a></li> -->
						<li class="active"><a data-toggle="tab" data-id="0" href="#warehouse">گدام</a></li>
						<!-- <li><a data-toggle="tab"  href="#store">فروشگاه</a></li> -->
						<li><a data-toggle="tab" data-id="1"  href="#unit">واحد اجناس</a></li>
						<li><a data-toggle="tab" data-id="2"  href="#currency">واحد پولی</a></li>
						<li><a data-toggle="tab" data-id="3" href="#account_type">حساب اصلی</a></li>
						<li><a data-toggle="tab" data-id="4" href="#account"> حساب فرعی </a></li>
						<li><a data-toggle="tab" data-id="5" href="#customers"> لیست مشتریان </a></li>
						<li><a data-toggle="tab" data-id="6" href="#organization"> پروفایل شرکت</a></li>
						<?php if($this->session->userdata('isAdmin') == 1) { ?>
							<li><a data-toggle="tab"data-id="7"  href="#package">پکیج</a></li>
						<?php } ?>
						<!-- <li><a data-toggle="tab"  href="#backupWrapper" onclick="getBackupList();">نسخه پشتبان</a></li> -->
						<!-- <li><a data-toggle="tab" href="#backupWrapper" id="backupLink" >نسخه پشتبان</a></li> -->
					</ul>

					 <div class="tab-content">
						<!-- branch -->
								<!-- <div id="branch" class="tab-pane fade in active">  -->
								       <!-- <br>   -->
								       <?php 
								        // $this->load->view('settings/branch/add'); 
								       ?>
								<!-- <br>  <?php 
								// $this->load->view('settings/branch/list');
								?>       -->
								<!-- </div> -->
						<!-- / branch -->

						<!-- warehouse -->
					        	<div id="warehouse" class="tab-pane fade in active"> 
								<br>  <?php
										if(doesHaveAccessTo('settings','create_records'))
										{
											if(activePackageId() >= 2) {
												$this->load->view('settings/warehouse/add');
											}
										}
									   ?>
								<br>  <?php $this->load->view('settings/warehouse/list'); ?>      
								</div>
						<!-- / warehouse -->

						<!-- store -->
					        	<div id="store" class="tab-pane fade"> 
								<br>  <?php 
										// if(doesHaveAccessTo('settings','create_records'))
										// {
										// 	$this->load->view('settings/store/add');
										// }
										?>
								<br>  <?php
								    //  $this->load->view('settings/store/list'); 
									?>      
								</div>
						<!-- / store -->

						<!-- unit -->
						<div id="unit" class="tab-pane fade"> 
								<br>  <?php 
										if(doesHaveAccessTo('settings','create_records'))
										{
										  $this->load->view('settings/unit/add');
										}
										?>
								<br>  <?php $this->load->view('settings/unit/list'); ?>      
								</div>
						<!-- / unit -->

						<!-- currency -->
								<div id="currency" class="tab-pane fade">
								<br> <?php 
								    if(doesHaveAccessTo('settings','create_records'))
									 {
										if(activePackageId() >= 2) {
											$this->load->view('settings/currency/add'); 
										}
									 }
								    ?>
								<br> <?php $this->load->view('settings/currency/list'); ?> 
								</div>
						<!-- /currency -->

						<!-- account_type -->
								<div id="account_type" class="tab-pane fade">
								<br> <?php 
										// if(doesHaveAccessTo('settings','create_records'))
										// {
										// 	$this->load->view('settings/account_type/add'); 
										// }
									   ?>
								<br> <?php $this->load->view('settings/account_type/list'); ?> 
								</div>
						<!-- /account_type -->

						<!-- account -->
								<div id="account" class="tab-pane fade">
								<br> <?php
									if(doesHaveAccessTo('settings','create_records'))
									{
										$this->load->view('settings/account/add'); 
									}
								   ?>
							     <br> <?php $this->load->view('settings/account/list'); ?> 
								</div>
						<!-- /account -->

						<!-- customer -->
						      <div id="customers" class="tab-pane fade">
								<br> <?php $this->load->view('settings/customer/list'); ?> 
							  </div>
						<!-- /customer -->

						<!-- organization -->
								<div id="organization" class="tab-pane fade ">
								<br> <?php
										// if(doesHaveAccessTo('settings','create_records'))
										// {
										// 	$this->load->view('settings/organization/add'); 
										// }
									   ?>
								<br> <?php $this->load->view('settings/organization/list');  ?>      
								</div>
						<!-- / organization -->

						<!-- package -->
						        <div id="package" class="tab-pane fade ">
								<br> <?php
										if(doesHaveAccessTo('settings','create_records'))
										{
											// $this->load->view('settings/package/add'); 
										}
									   ?>
								<br> <?php $this->load->view('settings/package/list');  ?>      
								</div>
						<!-- / package -->


						 </div>

					   </div> <!-- / card-body -->
				     </div>
				   </div> <!-- / row -->
				   
				   
				  </div>
		       </div>
		    </div>

				<!-- footer -->
				<?php $this->load->view('component/footer-text.php'); ?>
				<!-- /footer -->
			</div>
        <!-- /main content -->
        