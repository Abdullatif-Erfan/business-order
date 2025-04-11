<style>
.dt-button{ display:block !important;}
#table_filter{display:block !important;}
table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
}
</style>

<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
			
					
				<div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->
											
                   <h4>لیست ژورنال سابقه</h4>
                <!-- Insertion -->
                    <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_currency" aria-expanded="false">
                        <button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
                            <span class="fas fa-plus-square"></span>  &nbsp; ثبت جدید </button>
                        </a> 
                    </div>
                    <div id="add_currency" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
                    <div class="box-body">
                    <?php  echo form_open('addOldJournals'); ?>
                    <div class="form-body">
                        <div class="row">
	 				
                        <div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
								<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_id"   required> 
									<option value="">حساب  را انتخاب نمایید</option>
									<?php foreach($accounts as $key => $value){ ?>
										<option value="<?=$value['id']?>">  <?=$value['name']?></option>
									<?php } ?>
								</select> 
								<span style='color:red'><?=form_error('account_id')?></span> 
						    </div> 
						</div>	


						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="amount" name="amount" type="text" step="0.01"  required>
								<label for="amount" class="placeholder">   مبلغ </label>
							    <span style='color:red'><?=form_error('amount')?></span>
						   </div> 
						</div>	
						

                

						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						           <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency"   required> 
                                       <option value=""> انتخاب واحد پولی </option>
                                        <?php foreach($currency as $key => $value){ ?>
                                            <option value="<?=$value['id']?>"> <?=$value['name']?></option>
                                        <?php } ?>
                                    </select> 
                                    <span style='color:red'><?=form_error('currency')?></span> 
						   </div> 
						</div>



						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						           <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="status"   required> 
                                       <option value=""> انتخاب  وضعیت </option>
                                       <option value="1"> طلب / موجود / عواید / ذخیره </option>  
                                       <option value="2"> باقی / قرضدار   </option>
                                    </select> 
                                    <span style='color:red'><?=form_error('currency')?></span> 
						   </div> 
						</div>

                        
						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						      <input class="form-control input-solid" id="details" name="details" type="text" value="انتقال حسابات سابقه" required>
								<label for="details" class="placeholder">   تفصیلات </label>
							    <span style='color:red'><?=form_error('details')?></span>
						    </div> 
						</div>


                        <div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid"  id="code"
                                value="<?=get_new_journal_code()?>" type="text" disabled required>
                                <input  name="code" value="<?=get_new_journal_code()?>" type="hidden">
							    <span style='color:red'><?=form_error('code')?></span>
						    </div> 
						</div>	
					 


						<div class="col-md-2 col-sm-2 col-xs-2 center m-t-10">
								<button type="submit" name="submit" class="btn btn-primary btn-sm m-l-10" >
								<span class="btn-label"> <i class="fa fa-save"></i> </span>
									ثبت
								</button>
						</div>

                                </div>
                                </div>  <!-- /form-body -->
                        <?php echo form_close(); ?>
                        </div> <!-- box-body -->
                    </div>  <!-- /id="add_form" -->	
                  <!-- /insertion -->
                                                           
                    
                    <br />

            <div class="table-responsive table_responsive" style="padding:5px;"><!-- table -->
            <table id="example" class=" table table-bordered table-striped table-hover">
            <!-- <table id="example" class="display nowrap" style="width:100%"> -->
                <thead>
                    <tr>
                        <th> شماره &nbsp; </th>
                        <th> کد </th>
                        <th> حساب </th>
                        <th>طلب / عواید / موجود</th>
                        <th>باقی / قرضدار</th>
                        <th>واحد پولی</th>
                        <th>ثبت کننده</th>
                        <th class="hidden-print">حذف</th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; $id2=20; 
                    foreach($journals as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?> </td>
                            <td> <?=$value['code'];?> </td>
                            <td>
                               <a target="_blank" href="reports/ledger/<?=$value['account_id']?>"class="hidden-print">
                                  <?=$value['account_name'];?>
                               </a>
                            </td>
                            <td> <?php if(intval($value['transaction_type']) === 2) { echo number_format($value['amount'],2); }?> </td>
                            <td> <?php if(intval($value['transaction_type']) === 1) { echo number_format($value['amount'],2); }?> </td>
                            <td> <?=$value['currency_symbol']?> </td>
                            <td> <?=$value['full_name'];?> </td>
                             <td>
                                <?php if(is_admin()) { ?>
                                    <a href="<?php echo base_url(); ?>deleteOldJournals/<?php echo $this->my_encryption->do_encode($value['id']); ?>">
                                    <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                                </a>
                             <?php } ?>
                             </td>
                        </tr>
                        <?php $id++; $id2++; } ?>
                    
                    </tbody>
               </table>
             </div> <!-- /table responsive -->  


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
        

        <script>
            document.getElementById('amount').addEventListener('input', function() {
                let amount = this.value.replace(/,/g, ''); // Remove existing commas
                amount = amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
                amount = amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
                this.value = amount;
            });
       </script>