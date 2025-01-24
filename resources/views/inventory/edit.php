<?php   echo form_open_multipart('inventory/inventory/edit'); ?>
    <input type="hidden" name="id" value="<?php echo $record[0]['id']; ?>">
    <div class="form-body">
        <div class="row">
                        
            <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group form-floating-label">
                <input class="form-control input-solid" id="name" name="name" type="text" value="<?=$record[0]['name']?>" required><label for="name" class="placeholder">نام جنس </label> 
                </div>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group form-floating-label">
                <input class="form-control input-solid" id="amount" name="amount" type="number" step="0.01"  value="<?=$record[0]['amount']?>"  required><label for="amount" class="placeholder">مقدار ورود </label>
                </div> 
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group form-floating-label">
                <input class="form-control input-solid" id="available" name="available" type="number" step="0.01"  value="<?=$record[0]['available']?>"  required><label for="available" class="placeholder">مقدار موجود </label>
                </div> 
            </div>


            <div class="col-md-4 col-sm-6 col-xs-12">
               <div class="form-group form-floating-label">
               <select  class="form-control" style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="unit_type" required> 
               <option  value="<?php echo $record[0]['unit_type']; ?>"><?php echo $record[0]['uname']; ?></option>
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
                <input class="form-control input-solid" id="unit_price" name="unit_price" type="number" step="0.01"  value="<?=$record[0]['unit_price']?>"  required><label for="unit_price" class="placeholder">قیمت فی واحد</label>
                </div> 
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group form-floating-label">
                <input class="form-control input-solid" id="dates" name="dates" type="datetime-local"  value="<?=$record[0]['dates']?>" ><label for="dates" class="placeholder"> تاریخ ورود به گدام</label>
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