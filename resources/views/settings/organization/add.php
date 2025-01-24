     <!-- insertion -->
			<div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
				<button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
					<span class="fas fa-plus-square"></span>  &nbsp; ثبت جدید </button>
				</a> 
			</div>
			<div id="add_form" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
					<div class="box-body">
				<?php  echo form_open_multipart('settings/setting/addCompanyInfo'); ?>
				<div class="form-body">
					<div class="row">
													
						<div class="col-md-4 col-sm-6 col-xs-6">
						<div class="form-group form-floating-label">
							<input class="form-control input-solid" id="name" name="name" type="text" required>
								<label for="name" class="placeholder">نام شرکت </label>
							<span style='color:red'><?=form_error('name')?></span>
						</div> 
						</div>

						<div class="col-md-4 col-sm-6 col-xs-6">
						<div class="form-group form-floating-label">
							<input class="form-control input-solid" id="phone" name="phone" type="text" 
							required>
								<label for="name" class="placeholder">شماره فعال شرکت</label>
							<span style='color:red'><?=form_error('phone')?></span>
						</div> 
						</div>

						<div class="col-md-4 col-sm-6 col-xs-6">
						<div class="form-group form-floating-label">
							<input class="form-control input-solid" id="address" name="address" type="text"
							 required>
								<label for="address" class="placeholder">آدرس شرکت</label>
							<span style='color:red'><?=form_error('address')?></span>
						</div> 
						</div>

						<div class="col-md-4 col-sm-6 col-xs-6">
						  <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="header" name="header" type="file">
								<label for="header" class="placeholder">هیدر</label>
							<span style='color:red'><?=form_error('header')?></span>
						 </div> 
						</div>

						<div class="col-md-4 col-sm-6 col-xs-6">
						<div class="form-group form-floating-label">
							<input class="form-control input-solid" id="logos" name="logos" type="file">
								<label for="logos" class="placeholder">لوگو</label>
							<span style='color:red'><?=form_error('logos')?></span>
						</div> 
						</div>

						<div class="col-md-4 col-sm-6 col-xs-6">
						   <div class="form-group form-floating-label">
						       <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id" required> 
                                       <option value=""> انتخاب شعبه</option>
                                        <?php foreach($branch as $key => $value){ ?>
                                            <option value="<?=$value['id']?>"><?=$value['name']?></option>
                                        <?php } ?>
                                    </select> 
                                 <span style='color:red'><?=form_error('branch_id')?></span> 
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
				</form>
			</div> <!-- box-body -->
		</div>  <!-- /id="add_form" -->	
<!-- /insertion -->