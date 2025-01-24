     <!-- insertion -->
     <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse"
	  href="#add_package" aria-expanded="false">
				<button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
					<span class="fas fa-plus-square"></span>  &nbsp; ثبت جدید </button>
				</a> 
			</div>
			<div id="add_package" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
				<div class="box-body">
				<?php  echo form_open('settings/setting/add'); ?>
				<input type="hidden" name="table" value="package">
				<input type="hidden" name="activated_by" value="<?php echo $this->session->userdata('name'); ?>">
				<input type="hidden" name="activated_date" value="<?php echo Date('Y-m-d'); ?>">

				<div class="form-body">
					<div class="row">
										
						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="name" name="name" type="text" required>
								<label for="name" class="placeholder">    نام پکیج را بنویسید</label>
							    <span style='color:red'><?=form_error('name')?></span>
						    </div> 
						</div>	
						
						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						      <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="type" required> 
							           <option value=""> انتخاب پکیج </option>
									   <option value="1"> لایت </option>
                                       <option value="2"> بزنیس </option>
                                       <option value="3"> بزنیس پلس </option>
                                    </select> 
                                 <span style='color:red'><?=form_error('type')?></span> 
						    </div> 
						</div>	
											
						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						      <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="status" required> 
                                       <option value="0"> غیر فعال </option>
                                       <option value="1"> فعال </option>
                                    </select> 
                                 <span style='color:red'><?=form_error('status')?></span> 
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