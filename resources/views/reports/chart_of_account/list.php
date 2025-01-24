<script>
    function getJournalData(code)
    {
        
    }
</script>

<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
			
					
			  <div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
                  <div class="card-header" style="padding:10px">
						<h4 class="card-title"> چارت حسابات 
						
					
						<button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button>
						</h4>
					</div>
					<div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->
						
                    <!-- panel -->
                    <div class="col-md-12"  id="print_area">
                        <div class="panel-group" id="accordion">
                        <?php
                        foreach ($account_type as $value) { ?>
                            <div class="panel panel-default">
                                <div class="panel-heading" style="background-color:#f0eded">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$value['code']?>" class="">
                                            <?=$value['name']?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse<?=$value['code']?>" class="panel-collapse collapse in" style="height: auto;">
                                    <div class="panel-body" id="body<?=$value['code']?>">
                                        <?php if($value['code'] == 1000) {
                                            $this->load->view('reports/chart_of_account/banks_table');
                                        } else if($value['code'] == 2000) {
                                            // $this->load->view('reports/chart_of_account/incomes_table');
                                        }  else if($value['code'] == 3000) {
                                            // $this->load->view('reports/chart_of_account/spends_table');
                                        }  else if($value['code'] == 4000) {
                                            $this->load->view('reports/chart_of_account/customers_table');
                                        }  else if($value['code'] == 5000) {
                                            // $this->load->view('reports/chart_of_account/employees_table');
                                        }  else if($value['code'] == 6000) {
                                            // $this->load->view('reports/chart_of_account/seller_table');
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                      </div>

                    <!-- /panel -->


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
        