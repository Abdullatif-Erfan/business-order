<div class="col-12">
        <form id="myForm"  action="home" method="post">
        <div class="row">

        <div class="col-md-3 col-sm-6 col-xs-6">
            <select  class="form-control mt-1 mb-1" 
                style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="year"> 
                <option value="{{ $data['year'] }}">{{ $data['year'] }}</option>
                <option value="">-- انتخاب سال -- </option>
                <?php for($i=1400; $i<=1440; $i++)
                { ?>
                    <option  value="<?php echo $i; ?>">
                    <?php echo $i; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-3 col-sm-6  col-xs-6">
            <select  class="form-control mt-1 mb-1" 
                style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="month"> 
                <option value="{{ $data['month'] }}">{{ $data['month'] }}</option>
                <option value=""> -- انتخاب  ماه -- </option>
                <option value="100"> همه </option>
                <?php for($i=1; $i<=12; $i++)
                { ?>
                    <option  value="<?=$i?>"><?=$i?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-3 col-sm-6  col-xs-6">
            <select  class="form-control mt-1 mb-1" 
                style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="day"> 
                <option value="{{ $data['day'] }}"><?=intval($data['day'] ) === 100 ? "همه" : $data['day'] ?></option>
                <option value=""> -- انتخاب  روز -- </option>
                <option value="100"> همه </option>
                <?php for($i=1; $i<=31; $i++)
                { ?>
                    <option  value="<?=$i?>"><?=$i?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-6">
            <button class="btn  mybtn search_btn form-control" style="margin-top:5px">
			   <i class="fa fa-search"></i>
			</button>
        </div>

    </div>
   </form>
 </div>