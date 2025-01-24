<?php $bul=base_url(); ?>
<script>
function selectedBuyingForm(category_id)
{
    var bul = $('#bul').val();
    window.location.href= bul + 'movements/'+category_id;
}
function selectedBuyingItem(med_pre_id)
{
    var bul = $('#bul').val();
    window.location.href= bul + 'movements/'+med_pre_id;
}


function limitIncreaseMoreThanAmount(value, limit)
{
    if(parseInt(value) > 0)
    {
        if(parseInt(value) > parseInt(limit)) {
            alert('بیشتر از مقدار موجود ثبت نمیتوانید');
            $('#amount').val('');
            $('#total').val('');
        }
        else
        {
            var isHeen = parseInt($('#isHeen').val());
            var amount = parseFloat(value);
            
            if(isHeen > 1) 
            {
                // var weigth =  parseFloat($('#weigth').val());
                // $('#total').val(weigth * amount);
            }
            else
            {
                var bought_up =  parseFloat($('#bought_up').val());
                $('#total').val(bought_up * amount);
            }
        }
    } 
    else 
    {
        alert('لطفا نمبر درست و بالاتر از صفر انتخاب نمایید');
        $('#amount').val('');
        $('#total').val('');
    }

}
function findTotalWithThisWeight(value)
{
    if(parseInt(value) > 0)
    {
        var cur_weigth = parseFloat($('#cur_weigth').val());
        if(parseInt(value) > parseInt(cur_weigth)) {
            alert('بیشتر از وزن موجود ثبت نمیتوانید');
            $('#amount').val('');
            $('#total').val('');
        }
        else
        {
            var weigth = parseFloat(value);
            var bought_up =  parseFloat($('#bought_up').val());
            $('#total').val(weigth * bought_up);
        }
    }
    else 
    {
        alert('لطفا نمبر درست و بالاتر از صفر انتخاب نمایید');
        $('#amount').val('');
        $('#total').val('');
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
            <h3>صفحه انتقال اجناس</h3>
			</div>
            
			<div class="card-body"><!-- card-body -->
						
                 <div class="col-12 m-t-20">
                    <div class="row">
                        <div class="col-sm-9 col-x-12">
                            <!-- row 1 -->
                                    
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group form-floating-label">
                                        <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true"
                                            onchange="selectedBuyingItem(this.value)" required> 
                                            <?php if(!empty($this_item[0]['pre_list_id'])) { ?>
                                                <option value="<?=$this_item[0]['pre_list_id']?>"> <?=$this_item[0]['item_name']?> </option>
                                            <?php } ?>
                                            <option value="">  --- انتخاب لیست اجناس --- </option>
                                             <?php foreach($item_details as $k => $v)
                                             { ?>
                                                  <option value="<?=$v['id']?>"> <?=$v['item_name']. ' ( '.$v['cur_amount'].' '.$v['unit_name'].' ) ' ?> </option>

                                             <?php }  ?>
                                        </select> 
                                        <span style='color:red'><?=form_error('category')?></span>
                                    </div> 
                                </div>
                            </div>

                            <?php
                            $cur_amount = 0; 
                            if(count($this_item) > 0) {
                            // $cur_amount = intval($item_details[0]['amount']) - intval($item_details[0]['cur_amount']);
                               $cur_amount = $this_item[0]['cur_amount'];
                                ?>
                                
                                <?php echo form_open('moveItems'); ?>
                                <div class="row">
                            

                                <input type="hidden" name="bought_item_details_id" value="<?=$this_item[0]['id']?>" />
                                <input type="hidden" name="name" value="<?=$this_item[0]['item_name']?>" />
                                <input type="hidden" name="buy_pre_id" value="<?=$this_item[0]['buy_pre_id']?>" />
                                <input type="hidden" name="unit_id" value="<?=$this_item[0]['unit_id']?>" />
                                <input type="hidden" name="expire_date" value="<?=$this_item[0]['expire_date'] ?? 0 ?>" />
                                <input type="hidden" name="moved_amount" value="<?=$this_item[0]['moved_amount'] ?? 0 ?>" />
                                

                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for="">مقدار موجود به <?=$this_item[0]['unit_name']?> </label>
                                        <input class="form-control"   type="number" name="income_amount"
                                         value="<?=$cur_amount?>" step="0.01" required readonly >
                                    </div> 
                                </div>

                                
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for="">مقدار انتقال به <?=$this_item[0]['unit_name']?> </label>
                                        <input class="form-control"  name="amount" id="amount" type="number"
                                         oninput="limitIncreaseMoreThanAmount(this.value,<?=$cur_amount?>)"
                                        value="<?=$cur_amount?>" required step="0.01">
                                    </div> 
                                </div>

                                <!-- row 2 -->

                                
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for="">  فیات خرید </label>
                                        <input class="form-control"  name="bought_up" id="bought_up"  type="number" step="0.01"
                                        value="<?=$this_item[0]['bought_up']?>" readonly   required>
                                    </div> 
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="form-group form-floating-label">
                                        <label for=""> فیات فروش </label>
                                        <input class="form-control"  name="sell_up" type="number" step="0.01"
                                        value="<?= intval($this_item[0]['sell_up']) > 0 
                                        ? $this_item[0]['sell_up'] : '' ?>" required>
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
                            
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="form-group form-floating-label">
                                            <label for="">    قیمت مجموعی خرید </label>
                                            <input type="hidden" id="isHeen" value="0" />
                                            <input type="number" class="form-control" name="total" id="total" 
                                            value="<?= floatval($this_item[0]['amount'] * $this_item[0]['bought_up']) ?>"
                                             step="0.01"  readonly required />
                                        </div> 
                                    </div>

                             
                                <!-- buttons -->
                                    <div class="col-md-8 col-sm-4 col-xs-12 m-t-40" style="display: flex;justify-content: center;align-items: center;gap: 20px;">
                                        <button type="submit" class="form-control btn btn-info btn-sm">  <i class="fa fa-save"></i> 
                                        &nbsp;  تایید و ثبت </button>
                                    </div>

                                </div>
                            </form>
                            <?php 
                               } 
                               else if(count($this_item) <=0 || empty($this_item))
                               { ?>
                               <div class="col-12" style="background: #f7f6f6;height: 215px;display: flex;align-items: center;justify-content: center;margin-right: 3px;">
                                        <h3>لیست اجناس را در بالا انتخاب نمایید  </h3>
                                    </div>
                                <?php } else { ?>
                                        <div class="col-12">
                                           <div class="col-12" style="background: #f7f6f6;height: 190px;display: flex;align-items: center;justify-content: center;margin-right: 3px;flex-direction:column">
                                              <h4>لیست ختم گردیده است</h4>
                                              <br/>
                                          </div>
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
	
