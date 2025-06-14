<script>
function calculate_reverse_amount() {
    var to_currency_amount = parseFloat($('#to_currency_amount').val()); 
	var result =  (1 / to_currency_amount).toFixed(10);
    $('#reverse_amount').val(result);
}
function change_points(value) {
	var to_currency_amount = parseFloat($('#to_currency_amount').val()); 
	var result =  (1 / to_currency_amount).toFixed(parseFloat(value));
    $('#reverse_amount').val(result);

}
</script>
<!-- insertion -->

         <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_currency" aria-expanded="false">
				<button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
					<span class="fas fa-plus-square"></span>  &nbsp; <th>{{__('common.add')}}</th> </button>
				</a> 
			</div>
			<div id="add_currency" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
				<div class="box-body">
				<?php  echo form_open('addRates'); ?>
				<div class="form-body">
					<div class="row">
	 				
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h3>نوت: همیشه پول بزرگتر را به کوچکتر تبدیل نمایید</h3>
					</div>
						
					 <div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						      <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_currency_id"   required> 
                                       <option value=""> انتخاب واحد پولی بزرگتر</option>
                                        <?php foreach($currency as $key => $value){ ?>
                                            <option value="<?=$value['id']?>"> یک  <?=$value['name']?></option>
                                        <?php } ?>
                                    </select> 
                                    <span style='color:red'><?=form_error('from_currency_id')?></span> 
						    </div> 
						</div>	
						
						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						             <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_currency_id"   required> 
                                       <option value=""> انتخاب واحد پولی کوچکتر</option>
                                        <?php foreach($currency as $key => $value){ ?>
                                            <option value="<?=$value['id']?>"> معادل به  <?=$value['name']?></option>
                                        <?php } ?>
                                    </select> 
                                    <span style='color:red'><?=form_error('to_currency_id')?></span> 
						    </div> 
						</div>


						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							    <input class="form-control input-solid" id="to_currency_amount" name="to_currency_amount" type="number" onkeyup="calculate_reverse_amount()" step="0.01"  required>
								<label for="to_currency_amount" class="placeholder">   مبلغ </label>
							    <span style='color:red'><?=form_error('to_currency_amount')?></span>
						   </div> 
						</div>	
						

						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="reverse_amount" placeholder="مبلغ عکس تبادله" name="reverse_amount" type="text" required>
								<!-- <label for="reverse_amount" class="placeholder">  مبلغ عکس تبادله </label> -->
							    <span style='color:red'><?=form_error('reverse_amount')?></span>
						   </div> 
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						      <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true"  onchange="change_points(this.value)"> 
                                       <option value=""> تعداد خانه اعشاریه</option>
                                        <?php for($i=10;$i>=1; $i--){ ?>
                                            <option value="<?=$i?>"> <?=$i?> خانه</option>
                                        <?php } ?>
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