<style>
.dt-button{ display:none !important;}
</style>
<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				
					
				<div class="row">
		
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-header">
						<h4 class="card-title"> راپور شعبه ( <?=$branch_name?> )
						  <span class="pull-left"><i class="fa fa-print" onclick="print_page();"></i></span>
						</h4>
				
                    <!-- filter area -->
                    <div class="col-md-12 col-sm-12 col-xs-12 filter_cover m-t-10 m-b-5" id="filterArea">
                    <?php  echo form_open('cache'); ?>
                        <div class="row">
 
                            <div class="col-md-11 col-sm-11 col-xs-11">
                                   <select  class="form-control select2" 
                                        style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id"> 
                                       <option value="<?=$branch_id?>"><?=$branch_name?></option>
                                        <option value=""> --- انتخاب شعبه --- </option>
                                        <?php foreach($branch as $key => $v)
                                        { ?>
                                            <option  value="<?php echo $v['id']; ?>">
                                            <?php echo $v['name']; ?></option>
                                        <?php } ?>
                                    </select>
                               </div>

                            <div class="col-md-1 col-sm-6 col-xs-6">
                                <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-6">
                                    <button type="submit" id="btn-filter" class="btn btn-info2 form-control btn-sm" style="border-left: 4px solid #fca505;">
                                        <i class="fa fa-search" style='font-size:12px;color:#ee70c9 !important;'> </i> </span> 
                                    </button>
                                </div>
                                </div>
                            </div>

                            </div>
                            </form>
                        </div>
                        <!-- / filter area -->

                     </div>

					<div class="card-body" id="print_area" style="width: 100%;overflow-x: scroll;"><!-- card-body -->	
					<!-- print header -->
					<div class="col-md-12 col-sm-12 col-xs-12 hide">
					    <img src="<?php echo base_url().show_where('header','org_bio',['is_active' => 1]); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
					    <center><h4 class="card-title">راپور شعبه </h4></center>
					</div>	
					<!-- / end of print header -->
					
                    
                    <div class="col-12">
                       <div class="row">
                           <div class="col-md-6 col-sm-6 col-xs-12">
                             <div class="table_responsive" style="padding:5px;">
                                <table class="table">
                                   <thead>
                                   <tr>
                                     <td  colspan="4"><center><h2> <?=$store[0]['account_name'];?> </h2></center></td>
                                   </tr>
                                   <tr>
                                     <th>شماره</th>
                                     <th>واحد پولی</th>
                                     <th>مبلغ</th>
                                     <th>به حروف</th>
                                  </tr>
                                  </thead>
                                   <tbody>
                                   <?php $id =1;
                                   
                                    foreach($store_amounts as $key => $value)
                                    { ?>
                                    <tr>
                                        <td><?=$id?></td>
                                        <td><?=$value['currency_name']?></td>
                                        <td><?=number_format($value['total_debit'] - $value['total_credit'])?></td>                                        
                                        <td>یک هزار</td>
                                    </tr>
                                    <?php $id++; }
                                   ?>
                                   </tbody>
                                </table>
                             </div>
                           </div>
                           <div class="col-md-6 col-sm-6 col-xs-12">
                           <div class="table_responsive" style="padding:5px;">
                                <table class="table">
                                   <thead>
                                   <tr>
                                     <td  colspan="4"><center><h2>معاملات به افغانی</h2></center></td>
                                   </tr>
                                   <tr>
                                     <th>شماره</th>
                                     <th> حساب</th>
                                     <th>مبلغ</th>
                                  </tr>
                                  </thead>
                                   <tbody>
                                  <tr>
                                   <td>1</td>
                                   <td>AFN</td>
                                   <td>123456</td>
                                   </tr>
                                   <tr>
                                   <td>2</td>
                                   <td></td>
                                   <td></td>
                                   </tr>
                                   <tr>
                                   <td>3</td>
                                   <td></td>
                                   <td></td>
                                   </tr>
                                   </tbody>
                                </table>
                             </div>
                           </div>
                       </div>
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
        

 	<!-- Edit modal -->
	       <div id="edit_modal" class="modal fade in"  role="dialog" aria-labelledby="edit_modal" aria-hidden="true">
            <div class="modal-dialog2">
               <div class="modal-content">
                <div class="modal-header bg-blue3">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h5 class="modal-title"> ویرایش </h5>
                </div>
                <div class="modal-body" id="EditData"></div>   
               </div>
            </div>
        </div>
	<!-- /Edit modal -->  