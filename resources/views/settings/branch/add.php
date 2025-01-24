     <!-- insertion -->
     <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse"
	  href="#add_branch" aria-expanded="false">
				<button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
					<span class="fas fa-plus-square"></span>  &nbsp; ثبت جدید </button>
				</a> 
			</div>
			<div id="add_branch" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
				<div class="box-body">
				<?php  echo form_open('settings/setting/add'); ?>
				<div class="form-body">
					<div class="row">
													
						<div class="col-md-10 col-sm-10 col-xs-10">
						<div class="form-group form-floating-label">
							<input type="hidden" name="table" value="branch">
							<input class="form-control input-solid" id="name" name="name" type="text" required>
								<label for="name" class="placeholder">نام شعبه را بنویسید</label>
							    <span style='color:red'><?=form_error('name')?></span>
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