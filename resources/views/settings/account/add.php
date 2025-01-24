     <!-- insertion -->
     <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse"
	  href="#add_account" aria-expanded="false">
				<button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
					<span class="fas fa-plus-square"></span>  &nbsp; ثبت جدید </button>
				</a> 
			</div>
			<div id="add_account" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
				<div class="box-body">
				<?php  echo form_open('settings/setting/addAccount'); ?>
				<input type="hidden" name="table" value="account">
				<div class="form-body">
					<div class="row">
										
						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="name" name="name" type="text" required>
								<label for="name" class="placeholder">    نام حساب را بنویسید</label>
							    <span style='color:red'><?=form_error('name')?></span>
						    </div> 
						</div>	
						
						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						      <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_type_id" required> 
                                       <option value=""> انتخاب حساب اصلی</option>
                                        <?php foreach($account_type as $key => $value){ ?>
                                            <option value="<?=$value['id'].'/'.$value['code']?>"><?=$value['name']?></option>
                                        <?php } ?>
                                    </select> 
                                 <span style='color:red'><?=form_error('account_type_id')?></span> 
						    </div> 
						</div>	
											
						<!-- <div class="col-md-2 col-sm-6 col-xs-12">
							<div class="form-group form-floating-label">
								<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id" > 
									<option value=""> انتخاب شعبه</option>
									<?php foreach($branch as $key => $value){ ?>
										<option value="<?=$value['id']?>"><?=$value['name']?></option>
									<?php } ?>
								</select> 
								<span style='color:red'><?=form_error('branch_id')?></span> 
							</div> 
						</div>	 -->


						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="description" name="description" type="text" >
								<label for="description" class="placeholder"> تفصیلات</label>
							    <span style='color:red'><?=form_error('description')?></span>
						    </div> 
						</div>	


						<div class="col-md-1 col-sm-2 col-xs-2 center m-t-10">
							<button type="submit" name="submit" class="btn btn-primary btn-sm m-l-10" >
								ثبت
							</button>
						</div>

					</div>
					</div>  <!-- /form-body -->
				</form>
			</div> <!-- box-body -->
		</div>  <!-- /id="add_form" -->	
<!-- /insertion -->