<div  class="filterForm animated fadeIn" id="searchWrapper1">  
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-md-10 col-sm-6 col-xs-6 m-b-10">
            <select  class="form-control select2 col-md-10 col-sm-6 col-xs-6" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_id" id="account_id" required> 
                <option value=""> انتخاب حساب</option>
                <?php foreach($accounts as $key => $value){ ?>
                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        
        <div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
            <button class="btn  mybtn search_btn form-control" onclick="submitAccountIdToURL()" >
                <i class="fa fa-search"></i>
            </button>
        </div>

    </div>
    </div>
</div>  <!-- /id="filter_form" -->	