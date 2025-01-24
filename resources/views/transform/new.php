<?php if(is_admin() || is_manager() && has_priviledge('7','can_add')){ ?>
    <div class="card-header" style="padding:10px;">
        <select name="" id="" class="form-control" onchange="show_item_list_modal(this.value);" required>
            <option value="">انتخاب گدام </option>
            <?php foreach($branch as $key => $value)
            { ?>
            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
            <?php } ?>
        </select>
    </div>
    <?php } ?>

    <div class="col-md-12 col-sm-12 col-xs-12 m-t-5">
    <span class="pull-left" style="margin-bottom:-20px;font-size:20px; color:blue"><i class="fa fa-print" onclick="print_page();"></i></span>
    </div>

    <!-- dynamic fields for print -->
    <?php echo form_open('transform/transform/addBill', array('id'=>'myForm'));?>
    <input type="hidden" id="counter" name="counter" value="0">
    <div class="col-md-12 col-sm-12 col-xs-12" style="display:none" id="selectedFields"> </div>
    <?php echo form_close(); ?>
    <!-- /dynamic fields for print -->
    <div class="card-body" id="print_area"><!-- card-body -->	
    <!-- print header -->
    <div class="col-md-12 col-sm-12 col-xs-12 hide">
        <img src="<?php echo base_url().show('header','org_bio'); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
        <center><h4 class="card-title"> لیست  انتقالات  </h4></center>
    </div>	
    <!-- / end of print header -->
    <div class="table-responsive table_responsive" style="padding:5px;"><!-- table -->
    <table id="transferTable" class="table table-striped table-bordered my_table">
        <thead>
            <tr>
                <th>شماره</th>
                <th>نام جنس</th>				
                <th><center>تعداد  / وزن </center> </th>	
                <th><center>واحد</center></th>
                <th><center>قیمت مجموعی</center></th>
                <th><center>مفاد </center></th>		
                <th><center> انتقال از</center></th>
                <th><center>  به </center></th>	
                <th><center>تاریخ</center></th>		
                <th><center>توسط</center></th>
                <th><center>انتخاب</center></th>
            </tr>
        </thead>
        <tfoot>
            <tr style="background:#eefcff">
                <td></td><td></td>
                <td></td><td></td>
                <td></td><td></td>
                <td></td><td></td>
                <td></td><td></td>
                <td><center><button onclick="submitForm()" class="btn btn-warning btn-sm hidden-print" style="padding: 1px 14px !important;">ایجاد بل</button></center></td>
            </tr>
        </tfoot>
        </table>
        </div> <!-- /table responsive -->  
    
        </div> <!-- / card-body -->