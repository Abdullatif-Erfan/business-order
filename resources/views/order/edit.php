<?php   echo form_open_multipart('order/order/edit'); ?>
    <input type="hidden" name="id" value="<?php echo $record[0]['id']; ?>">
    <div class="form-body">
        <div class="row">
                        
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group form-floating-label">
                <input class="form-control input-solid" id="name" name="name" value="<?=$record[0]['name']?>" type="text" required><label for="name" class="placeholder">نام جنس </label> 
                </div>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group form-floating-label">
                   <input class="form-control input-solid" id="amount" name="amount" type="number" step="0.01" value="<?=$record[0]['amount']?>" required ><label for="phone" class="placeholder"> وزن </label>
                </div> 
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group form-floating-label">
                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="unit_id" required> 
                    <option value="<?=$record[0]['unit_id']?>"><?=$record[0]['uname']?></option>
                    <option value=""> واحد را انتخاب نمایید</option>
                    <?php foreach($unit as $key => $value)
                    { ?>
                    <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                    <?php } ?>
                    </select>  
                </div>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group form-floating-label">
                <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id" required> 
                    <option value="<?=$record[0]['branch_id']?>"><?=$record[0]['branch_name']?></option>
                    <option value=""> شعبه را انتخاب نمایید</option>
                    <?php $branches = show_all('branch');
                    foreach($branches as $key => $value)
                    {    ?>
                    <option value="<?=$value['id']?>"><?=$value['name']?></option>
                    <?php  } ?>
                </select>  
                </div>
            </div>


            <div class="col-md-4 col-sm-6 col-xs-12 m-t-10">
                <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                <div class="input-group-append">
                <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                    data-targetselector="#action_date" data-englishnumber="true">
                    <span class="fa fa-calendar"></span> 
                </span>
                </div>
                    <input class="form-control"   id="action_date" name="action_date"  
                    data-targetselector="#action_date" value="<?=$record[0]['action_date']?>" 
                    data-mddatetimepicker="true"  placeholder="تاریخ اجرا"  data-placement="right" data-englishnumber="true"  >
                </div>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group form-floating-label">
                <input class="form-control input-solid" id="payed" name="payed" value="<?=$record[0]['payed']?>" type="number" step="0.01" required><label for="payed" class="placeholder"> مقدار پرداخت</label>
                </div> 
            </div>
            <div class="col-md-12 col-sm-6 col-xs-12">
                <div class="form-group form-floating-label">
                <input class="form-control input-solid" id="details" name="details" value="<?=$record[0]['details']?>" type="text"><label for="details" class="placeholder"> جزییات</label>
                </div> 
            </div>
             

        </div>
        </div>  <!-- /form-body -->

        <div class="row" style="background-color:#ebf6fc !important;background-color: #ddd;padding: 8px;position: relative;padding-right:30px;margin-bottom: -14px;">
                <button type="submit" name="submit" class="btn btn-info btn-sm m-l-10">
                     <span class="btn-label"> <i class="fa fa-save"></i> </span>
                        ثبت
                </button>
                <button type="button" class="btn btn-warning btn-sm m-l-10" style="float:left !important" data-dismiss="modal">لغو </button>
          </div>   

    </form>