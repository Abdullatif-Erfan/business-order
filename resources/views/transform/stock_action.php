<?php   echo form_open_multipart('transform/transform/addStockTransfer'); ?>
   <input type="hidden" name="stock_id" value="<?php echo $record[0]['id']; ?>">
   <input type="hidden" name="branch_id" value="<?php echo $stock_branch_id[0]['id']; ?>">
    <div class="form-body">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group form-floating-label">
            <input class="form-control input-solid" id="name" name="name" type="text" value="<?=$record[0]['name']?>" readonly>
            <!-- <label for="name" class="placeholder">نام جنس  </label>  -->
            </div>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group form-floating-label">
            <input class="form-control input-solid" id="available" name="available" type="number" step="0.01" readonly value="<?=$record[0]['available']?>">
            <!-- <label for="available" class="placeholder"> مقدار موجود</label>  -->
            </div>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group form-floating-label">
            <select  class="form-control" style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="unit_type"> 
                <option value="<?=$record[0]['unit_type']?>"><?=$record[0]['unit_name']?></option>
            </select>  
            </div>
            </div>


            <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group form-floating-label">
            <input class="form-control input-solid" id="transfer_amount" name="transfer_amount" type="number" step="0.01"  required>
            <label for="transfer_amount" class="placeholder">مقدار انتقال</label> 
            </div>
            </div>


            <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group form-floating-label">
            <input class="form-control input-solid" id="transfer_total" name="transfer_total" type="number" step="0.01" required>
            <label for="transfer_total" class="placeholder"> قیمت مجموعی</label> 
            </div>
            </div>

            
            <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group form-floating-label">
            <input class="form-control input-solid" id="transfer_prfit" name="transfer_prfit" type="number" step="0.01" required>
            <label for="transfer_prfit" class="placeholder">مفاد مجموعی</label> 
            </div>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group form-floating-label">
            <select  class="form-control" onchange="show_item_type(this.value);" style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="to_branch" required> 
               <option value="">انتقال به </option>
                <?php foreach($branch as $key => $value)
                { ?>
                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                <?php } ?>
            </select>  
            </div>
            </div>

        </div>

        </div>  <!-- /form-body -->

        <div class="row" style="background-color:#ebf6fc !important;background-color: #ddd;padding: 8px;position: relative;padding-right:30px;margin-bottom: -14px;">
                <button type="submit" name="submit" class="btn btn-info btn-sm m-l-10" >
                     <span class="btn-label"> <i class="fa fa-save"></i> </span>
                        ثبت
                </button>
                <button type="button" class="btn btn-warning btn-sm m-l-10 pull-left"  onclick="refresh()" data-dismiss="modal">لغو </button>
          </div>
<?php echo form_close(); ?>