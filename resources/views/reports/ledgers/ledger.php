<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
			lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "همه"]],
            pageLength: 10,
			// columnDefs: [
            //     { width: '100px', targets: 6 } // Adjust the index (6) to the correct column index
            // ]
        });
    });
</script>
<!--  main content -->
		<div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
              <input type="hidden" id="base_url" value="<?php echo base_url(); ?>" >


				<div class="row">
			  
		    	<div class="col-md-12 col-sm-12 col-xs-12 m-t--10">
				  <div class="card">
				
                  <div class="card-header" style="padding: 4px 20px !important;">
                   <h3>  لیجر حسابات مشتریان</h3>
                 </div>

                <!-- search and filter area -->
                <?php echo form_open('reports/ledgers'); ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-6 m-b-10 m-t-10">
                            <select class="form-control select2 col-md-5 col-sm-6 col-xs-12" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_id" id="account_id"> 
                                <?php if(!empty($cur_account[0]['name'])) { ?>
                                    <option value="<?=$cur_account[0]['id']?>"><?=$cur_account[0]['name']?></option>
                                <?php } ?>
                                <option value=""> انتخاب مشتری</option>
                                <?php foreach($account as $key => $value){ ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6 col-xs-6 m-b-10 m-t-10">
                            <select class="form-control select2 col-md-5 col-sm-6 col-xs-12" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency_id" id="currency_id"> 
                                <?php if(!empty($cur_currency[0]['name'])) { ?>
                                    <option value="<?=$cur_currency[0]['id']?>"><?=$cur_currency[0]['name']?></option>
                                <?php } ?>
                                <option value=""> انتخاب واحد پولی</option>
                                <?php foreach($currency as $key => $value){ ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-3  col-sm-6 col-xs-6 m-t-10">
                            <select  class="form-control select2" 
                                style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="searched_year"> 
                                <option value="<?=$searched_year?>"><?=$searched_year?></option>
                                <option value=""> انتخاب سال دیگر </option>
                                <?php for($i=1400; $i<=1440; $i++)
                                { ?>
                                    <option  value="<?php echo $i; ?>">
                                    <?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 col-sm-6 col-xs-6 m-b-4 m-t-10">
                            <button class="btn mybtn search_btn form-control" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>

                        <div class="col-md-1 col-sm-6 col-xs-6 m-b-4 m-t-10">
                            <button class="printBtn " onclick="print_page()" style="padding: 6px 12px;" >
                            <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
                <!-- / search and filter area -->

           <div class="card-body" id="print_area" style="width: 100%;overflow-x: scroll;">
           <!-- card-body -->	
					<!-- print header -->
					<div class="col-md-12 col-sm-12 col-xs-12 hide">
					    <img src="<?php echo base_url().show_where('header','org_bio',['is_active' => 1]); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
					    <center><h4 class="card-title">لیجر حسابات  </h4></center>
					</div>	
					<!-- / end of print header -->
                    <div class="table-responsive table_responsive" style="padding:5px;">
                    <table id="myTable" class="table table-bordered table-striped table-hover"  style="width:100%">
                            <thead>
                                <tr>
                                    <th> شماره</th>
                                    <th> نام مشتری</th>
                                    <th> کتگوری معاملات</th>
                                    <th> رسیدگی (طلب)</th>
                                    <th> بردگی (باقی)</th>
                                    <th> بیلانس</th>
                                    <th> وضعیت</th>
                                </tr>
                            </thead>
                            <tbody>
                             <?php  $id =1; $balance=0;
                                if(intval($cur_account[0]['id']) > 0) {
                                    $account_name = show_where('name','account',array('id' => $cur_account[0]['id']));
                                } else {
                                    $account_name = "همه مشتریان";
                                }
                                foreach($ledger as $key => $value)
                                { 
                                    if(intval($id) === 1) 
                                    {
                                        $balance = floatval($value['total_other_payment']) - floatval($value['total_own_payment']);
                                    } 
                                    else
                                    {   
                                        $cur_total = floatval($value['total_other_payment']) - floatval($value['total_own_payment']);
                                        $balance += $cur_total;
                                    }                        

                                    ?>
                                    <tr>
                                        <td><?=$id?></td>
                                        <td><?=$account_name?></td>
                                        <td><?=$value['type_name']?></td>
                                        <td><?=$value['total_other_payment']?></td>
                                        <td><?=$value['total_own_payment']?></td>
                                        <td><?=$balance?></td>
                                        <td><?php 
                                            if(intval($balance) === 0) {
                                                echo "<label class='label label-info'>تصفیه</label>";
                                            } else if(intval($balance) < 0) {
                                                echo "<label class='label label-warning'>مشتری باقی است</label>";                                                
                                            } else {
                                                echo "<label class='label label-info'>مشتری طلب است</label>";
                                            }
                                        ?></td>
                                    </tr>
                                <?php $id++; }
                             ?>
                            </tbody>
                            
                        </table>
                    </div>

					
					   </div> <!-- / card-body -->

        
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>

				<!-- footer -->
				<?php
				//  $this->load->view('component/footer-text.php');
				 ?>
				<!-- /footer -->
			</div>
		<!-- /main content -->
		

		
		 <!-- detailsModal -->
		     <div class="modal fade in" id="detailsModal" role="dialog">
                  <div class="modal-dialog2">
                    <div class="modal-content">
                      <div class="modal-header">
                         <h4 class="modal-title pull-left" style="color: #3b6c08 !important;margin-top:-5px;">جزییات</h4>
						 <button type="button" class="pull-left close" style="color:red" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                      <div class="row w100" id="memberDetailsData">
                      </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-danger" data-dismiss="modal">بسته کردن </button>
                      </div>
                    </div>
                  </div>
                </div>
        <!-- /detailsModal -->


		