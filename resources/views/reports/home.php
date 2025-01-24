<style>
    .activity-feed li{
        margin-bottom: 10px;
        font-size: 15px;
    }
</style>
<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
			
					
			  <div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
                  <div class="card-header" style="padding:10px">
						<h4 class="card-title"> گزارشات سیستم </h4>
					</div>
					<div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->
											
					     <div class="row">
                            <!-- گزارشات عمومی -->
                            <div class="col-sm-4 col-xs-12">
                             <ol class="activity-feed">
                                <li class="feed-item feed-item-success">
                                    <span class="text"> گذارشات  خرید و فروش </span>
                                    <ol>
                                        <li><a target="_blank" href="<?=base_url()?>daily"> روزانه </a></li>
                                        <li><a target="_blank" href="<?=base_url()?>monthly"> ماهانه </a></li>
                                        <li><a target="_blank" href="<?=base_url()?>yearly"> سالانه </a></li>
                                    </ol>
                                </li>
                                <li class="feed-item feed-item-success">
                                    <!-- <span class="text"> گزارشات فاکتور </span>
                                    <ol>
                                        <li><a target="_blank" href="<?=base_url()?>daily"> فاکتور خرید </a></li>
                                        <li><a target="_blank" href="<?=base_url()?>monthly"> فاکتور فروش </a></li>
                                    </ol> -->
                                </li>
                                <!-- <li class="feed-item feed-item-success">
                                    <span class="text">گذارشات فروشات</span>
                                    <ol>
                                        <li><a target="_blank" href="<?=base_url()?>daily"> طلبات فروشات </a></li>
                                        <li><a target="_blank" href="<?=base_url()?>monthly"> طلبات خرید </a></li>
                                    </ol>
                                </li> -->
                             </ol>
                            </div>

                            <!-- گزارشات دوا -->
                            <div class="col-sm-4 col-xs-12">
                                <ol class="activity-feed">
                                    <li class="feed-item">
                                        <span class="text"> گذارشات  مالی </span>
                                        <ol>
                                            <li><a target="_blank" href="<?=base_url()?>cashflow"> کهاته حسابات </a></li>
                                            <li><a target="_blank" href="<?=base_url()?>reports/balancesheet"> بیلانس شیت </a></li>
                                            <li><a target="_blank" href="<?=base_url()?>chartOfAccount">  چارت حسابات </a></li>
                                            <li><a target="_blank" href="<?=base_url()?>reports/clearance"> تصفیه حساب </a></li>
                                        </ol>
                                    </li>
                                    <li class="feed-item feed-item-primary">
                                    </li>
                             </ol>
                            </div>


                             <!-- گزارشات مرغ و جوجه مرغ -->
                             <div class="col-sm-4 col-xs-12">
                                
                            </div>

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
        