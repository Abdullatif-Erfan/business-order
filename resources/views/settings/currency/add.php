     <!-- insertion -->

     <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_currency" aria-expanded="false">
				<button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
					<span class="fas fa-plus-square"></span>  &nbsp; ثبت جدید </button>
				</a> 
			</div>
			<div id="add_currency" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
				<div class="box-body">
				<?php  echo form_open('settings/setting/add'); ?>
				<input type="hidden" name="table" value="currency">
				<div class="form-body">
					<div class="row">
													
						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="name" name="name" type="text" required>
								<label for="name" class="placeholder">  واحد پولی را بنویسید</label>
							    <span style='color:red'><?=form_error('name')?></span>
						   </div> 
						</div>	
						
						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="symbol" name="symbol" type="text" required>
								<label for="symbol" class="placeholder">   علامت مختصر را بنویسید</label>
							    <span style='color:red'><?=form_error('symbol')?></span>
						   </div> 
						</div>	
						

						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="color" name="color"    type="color" style="height: 40px !important;"  >
								<label for="color" class="placeholder">    نمایش رنگ واحد پولی </label>
							    <span style='color:red'><?=form_error('color')?></span>
						   </div> 
						</div>	

						
						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<select class="form-control"  name="is_base">
								<option value="0">انتخاب پول پایه</option>
								<option value="1">بلی</option>
								<option value="0">نخیر</option>
							</select>
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