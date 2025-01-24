<!-- insertion -->
<div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box-tools"> 
            <a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
            <button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
                <span class="fas fa-plus-square"></span>  &nbsp; ثبت  جدید </button>
            </a> 
        </div>
        
        <div id="add_form" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
        <div class="box-body">
        <?php   echo form_open_multipart('salary/salary/addSalary'); ?>
        <input type="hidden" name="customer_id" value="<?=$customer_id?>" >
        <input class="form-control"  value="<?=get_new_journal_code()?>" type="hidden"  name="code" required>

            <div class="form-body">
                <div class="row">
                    
                    <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                    
                       <div class="col-md-4 col-sm-6 col-xs-12">
                           <label> حساب پرداخت کننده</label>
                            <div class="form-group form-floating-label">
                            <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_account_id" required> 
                            <option value=""> حساب پرداخت کننده را انتخاب نمایید</option>
                            <?php foreach($account as $key => $value)
                            { ?>
                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                            <?php } ?>
                            </select>   
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <label for=""> پرداخت معاش به </label>
                            <div class="form-group form-floating-label">
                            <input class="form-control input-solid" id="name"  readonly type="text" name="emp_name" value="<?=$this_employee[0]['full_name']?>">
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <label>مبلغ پرداخت</label>
                            <div class="form-group form-floating-label">
                            <input class="form-control input-solid" id="amount" name="amount" type="number" step="0.01" required><label for="amount" class="placeholder"> مقدار </label> 
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <label>انتخاب واحد پولی</label>
                            <div class="form-group form-floating-label">
                            <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency" required> 
                            <option value=""> واحد پولی</option>
                            <?php foreach($currency as $key => $value)
                            { ?>
                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                            <?php } ?>
                            </select>   
                            </div>
                        </div>

                        <div class="col-md-8 col-sm-6 col-xs-12">
                            <label> تاریخ </label>
                                <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                <div class="input-group-append">
                                <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                    data-targetselector="#exampleInput00" data-englishnumber="true">
                                    <span class="fa fa-calendar"></span> 
                                </span>
                                </div>
                                    <input class="form-control"  name="dates" id="exampleInput00"  
                                    data-targetselector="#exampleInput00" value="<?=todays_date()?>" 
                                    data-mddatetimepicker="true"  placeholder="تاریخ "  data-placement="right" data-englishnumber="true"  >
                                </div>
                        </div>
                    
                    </div>
                    </div>

                    <!-- <div class="col-md-12 col-sm-6 col-xs-12">
                    <textarea name="details" id="details" cols="30" rows="2" name="details" class="form-control" placeholder="جزییات..."></textarea>
                    </div> -->

                    <div class="col-md-12 col-sm-12 col-xs-12 m-t-20">
                        <div class="col-md-2 col-sm-2 col-xs-12 pull-left">
                            <button type="submit" name="submit" class="btn btn-primary btn-sm pull-left" >
                            <span class="btn-label"> <i class="fa fa-save"></i> </span>
                                ثبت
                            </button>
                        </div>
                        <div class="col-md-10 col-sm-10 hidden-xs"></div>
                    </div>

                </div>
                </div>  
                <!-- /form-body -->
            </form>
         </div> <!-- box-body -->
        </div>  <!-- /id="add_form" -->		
    </div>
<!-- /insertion -->