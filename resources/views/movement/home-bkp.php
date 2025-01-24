<?php $bul=base_url(); ?>
<script>
function selectedBuyingForm(category_id)
{
    var bul = $('#bul').val();
    window.location.href= bul + 'movements/'+category_id;
}
function updateURLwithCompanyAndMedPreIds(med_pre_and_comp_id)
{
    var both = med_pre_and_comp_id.split('/');
    var med_pre_id = both[0];
    var company_id = both[1];
    var bul = $('#bul').val();
    var category_id = $('#category_id').val();
    window.location.href= bul + 'movements/' + category_id + '/'+ med_pre_id +'/'+ company_id;
}
function limitIncreaseMoreThanAmount(value, limit)
{
    if(parseInt(value) > 0)
    {
        if(parseInt(value) > parseInt(limit)) {
            alert('بیشتر از مقدار موجود ثبت نمیتوانید');
            $('#amount').val('');
        }
    } 
    else 
    {
        alert('لطفا نمبر درست و بالاتر از صفر انتخاب نمایید');
        $('#amount').val('');
    }

}
</script>
<style>
.dt-button{ display:none !important;}
</style>
<!--  main content -->
<div class="main-panel">
	<div class="content">
		<div class="page-inner">
		<div class="row ">

		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="card" style="min-height:400px;">
			<div class="card-header" style="padding: 10px; text-align:center;">
            <input type="hidden" id="bul" value="<?=$bul?>" >
            <input type="hidden" id="category_id" value="<?=$this->uri->segment('2')?>" >


                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group form-floating-label">
                            <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="category_id" id="category"
                                onchange="selectedBuyingForm(this.value)" required> 
                                <?php if(!empty($category_id)) 
                                        { ?>
                                            <option value="<?=$category_id?>"> 
                                            <?= intval($category_id) === 1 ? "دوا" : "عمومی";  ?>
                                    <?php } else { ?>
                                            <option value=""> کدام اجناس را انتقال میدهید ؟  </option>
                                    <?php } ?>
                                    <option value=""> کدام اجناس را انتقال میدهید ؟  </option>
                                    <option value="1"> دوا </option>
                                    <option value="2"> عمومی </option>

                            </select> 
                            <span style='color:red'><?=form_error('category')?></span>
                        </div> 
                    </div>

			</div>
            
			<div class="card-body"><!-- card-body -->
						
                 <div class="col-12 m-t-20">
                    <div class="row">
                        <div class="col-sm-9 col-x-12">
                            <!-- row 1 -->
                            
                            <?php
                            $cur_amount = 0; 
                            if(count($item_details) > 0) {
                            // $cur_amount = intval($item_details[0]['amount']) - intval($item_details[0]['cur_amount']);
                               $cur_amount = $item_details[0]['amount'];
                                ?>

                                <?php echo form_open('moveItems'); ?>
                                <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                <button class="btn btn-info form-control">  <?=$item_details[0]['item_name']?> </button>
                                </div>
                                
                                <input type="hidden" name="category_id" value="<?=$this->uri->segment(2)?>" />
                                <input type="hidden" name="label" value="<?=$label?>" />
                                <input type="hidden" name="next" value="<?=intval($next_id) + 1?>" />
                                <input type="hidden" name="prev" value="<?=intval($prev_id) - 1?>" />

                                <input type="hidden" name="name" value="<?=$item_details[0]['item_name']?>" />
                                <input type="hidden" name="buy_pre_id" value="<?=$item_details[0]['buy_pre_id']?>" />
                                <input type="hidden" name="unit_id" value="<?=$item_details[0]['unit_id']?>" />
                                <input type="hidden" name="expire_date" value="<?=$item_details[0]['expire_date'] ?? 0 ?>" />

                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for="">مقدار موجود به <?=$item_details[0]['unit_name']?> </label>
                                        <input class="form-control"   type="number" name="income_amount"
                                         value="<?=$cur_amount?>" required readonly >
                                    </div> 
                                </div>

                                
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for="">مقدار انتقال به <?=$item_details[0]['unit_name']?> </label>
                                        <input class="form-control"  name="amount" id="amount" type="number"
                                         oninput="limitIncreaseMoreThanAmount(this.value,<?=$cur_amount?>)"
                                        value="<?=$cur_amount?>" required>
                                    </div> 
                                </div>

                                <!-- row 2 -->

                                
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for="">  فیات خرید </label>
                                        <input class="form-control"  name="bought_up" type="number"
                                        value="<?=$item_details[0]['new_unit_price']?>" required>
                                    </div> 
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for=""> فیات فروش </label>
                                        <input class="form-control"  name="sell_up" type="number"
                                        value="<?= intval($item_details[0]['sell_up']) > 0 
                                        ? $item_details[0]['sell_up'] : '' ?>" required>
                                    </div> 
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for=""> مقدار برای نوتفکشن </label>
                                        <input class="form-control"  name="notification_amount" type="number">
                                    </div> 
                                </div>

                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for=""> انتقال به ؟</label>
                                        <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="warehouse_id" id="warehouse_id" required> 
                                            <option value=""> انتخاب گزینه </option>
                                            <?php foreach($warehouse as $key => $value){ ?>
                                                <option value="<?=$value['id']?>">  <?=$value['name']?> </option>
                                            <?php } ?>
                                        </select> 
                                        <span style='color:red'><?=form_error('warehouse_id')?></span>
                                    </div> 
                                </div>
                            
                                <?php if(!empty($item_details[0]['weigth']))
                                { ?>
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="form-group form-floating-label">
                                            <label for="">  وزن به کیلو </label>
                                            <input class="form-control"  name="weigth" type="number"
                                            value="<?=$item_details[0]['weigth']?>" required>
                                        </div> 
                                    </div>
                                <?php } ?>

                                
    
                                <!-- buttons -->
                                <div class="col-md-8 col-sm-8 col-xs-12 m-t-40" style="display: flex;justify-content: center;align-items: center;gap: 20px;">

                                    <!-- <a href="<?=base_url().'movements/'.$category_id.'?prev='.$item_details[0]['buy_pre_id']?>">
                                       <button type="button" class="btn  btn-sm"> <i class="fa fa-arrow-right"></i> &nbsp; قبلی </button>
                                    </a> -->

                                  
                                    <button type="submit" class="form-control btn btn-info btn-sm">  <i class="fa fa-save"></i> 
                                    &nbsp;  تایید و ثبت </button>

                                    <!-- بعدی -->
                                    <a href="<?=base_url().'movements/'.$category_id.'?next='.$item_details[0]['buy_pre_id']?>">
                                    <button type="button" class="btn btn-sm"> بعدی  &nbsp;  <i class="fa fa-arrow-left"></i>  </button>
                                    </a>
                                </div>

                                </div>
                            </form>
                            <?php } else if(!empty(intval($this->uri->segment(2))))
                               { ?>
                                <div class="col-12" style="background: #f7f6f6;height: 190px;display: flex;align-items: center;justify-content: center;margin-right: 3px;flex-direction:column">
                                        
                                        <h4>لیست ختم گردیده است</h4>
                                        <br/>
                                      <?php if(!empty($prev_id)) { ?>
                                          <a href="<?=base_url().'movements/'.$this->uri->segment(2).'?prev='.$prev_id ?>"> 
                                          <button class="btn  btn-sm">  &nbsp; بعدی <i class="fa fa-arrow-left"></i> </button>
                                          </a>
                                      <?php } ?>
                                       
                                       <?php if(!empty($next_id)) { ?>
                                          <a href="<?=base_url().'movements/'.$this->uri->segment(2).'?prev='.$next_id ?>"> 
                                          <button class="btn btn-sm">   <i class="fa fa-recycle"></i> &nbsp; نمایش مجدد لیست  </button>
                                          </a> 
                                      <?php } ?>
  
                                      </div>
                           <?php } else { ?>
                                    <div class="col-12" style="background: #f7f6f6;height: 215px;display: flex;align-items: center;justify-content: center;margin-right: 3px;">
                                        <h3>کتگوری اجناس را در بالا انتخاب نمایید تا لیست نشان داده شود</h3>
                                    </div>
                            <?php } ?>


                        </div>
                        <div class="col-sm-3 hidden-xs">
                            <img src="<?=$bul.'assets/img/no_image.png'?>" alt="" style="width:100%">
                        </div>
                    </div>
                 </div>
                        <br />
                        <br />

					
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
	
