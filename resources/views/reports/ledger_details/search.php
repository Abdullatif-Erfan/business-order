<div  class="filterForm " id="searchWrapper1">  
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">
            
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <input class="form-control" id="details"  type="text" placeholder="جستجو به اساس حساب / تفصیلات " >
                </div>

                <div class="col-md-3 col-sm-6 col-xs-6">
                   <select  class="form-control select2" 
                        style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id" required> 
                        <option value="">  واحد پولی  </option>
                        <?php foreach($currency as $key => $val)
                        { ?>
                            <option  value="<?= $val['id'] ?>"><?= $val['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>


                <div class="col-md-3 col-sm-6 col-xs-6">
                    <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                    <div class="input-group-append">
                    <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                        data-targetselector="#end_date" data-englishnumber="true">
                        <span class="fa fa-calendar"></span> 
                    </span>
                    </div>
                        <input class="form-control"  id="end_date"  
                        data-targetselector="#end_date" value="" 
                        data-mddatetimepicker="true"  placeholder="تاریخ ختم / الی  امروز"  data-placement="right" data-englishnumber="true"  >
                    </div>
                </div>

               

            <div class="col-md-3 col-sm-6 col-xs-12">
                <button class="btn  mybtn search_btn form-control"   id="btn-search">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </div> 
    </div>  <!-- /id="search_form" -->