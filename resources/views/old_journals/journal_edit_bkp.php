
<?php $bul=base_url(); ?>
<script>
    function showHideDates()
    {
        var show = document.getElementById("dateWrapper");
        if (show.style.display == "none") {
            show.style.display = "block";
            $('#exampleInput02').attr('required', 'required');
            $('#exampleInput03').attr('required', 'required');
            $('#exampleInput04').attr('required', 'required');
        } else {
            show.style.display = "none";
            $('#exampleInput02').removeAttr('required');
            $('#exampleInput03').removeAttr('required');
            $('#exampleInput04').removeAttr('required');
        }

        // $('#dateWrapper').fadeIn(500);
        // $('#'+dateWrapper).fadeOut(500);
        // var pay_date = $('#exampleInput02').val();
        // $('#exampleInput02').attr('required', 'required');
        // $('#exampleInput02').removeAttr('required');
        // $('#exampleInput02').val(''); 
    }

    function getPayerBalance(currency_id)
    {
        var account_id = $('#to_account_id').val();
        if(parseInt(account_id) > 0 )
        {
            $('#show_current_balance').html('<center><img src="<?php echo base_url(); ?>assets/img/small_loader.gif" style="width:7%" alt="Loading"/></center>');
            $.ajax({
                type:'POST',
                data:{account_id: account_id,currency_id:currency_id },
                url:"<?php echo base_url() . 'getCurrentBalance'; ?>",
                success: function(result)
                {
                    // if($result=1)
                    // {
                    //     tempAlert(" موفقانه حذف گردید ",1000);
                    //     window.setTimeout(function()
                    //     {
                    //         document.location.reload();
                    //     }, 1000);
                    // } else { tempAlert("  حذف نگردید ",1000);}
                    $('#show_current_balance').html(" مبلغ موجود  تابحال :  " + result);
                    $('#cur_balance').val(result);
                    $('#from_currency_id').val(currency_id);
                }
            });

        } else {
            $('#show_current_balance').html("");
        }
    }
    function fillThisValueToFrom_amount()
    {
        var cur_amount = $('#to_amount').val();
        $('#from_amount').val(cur_amount);
    }
    function calculateRecievedAmount(currency_id) 
    {
        var cur_balance = $('#cur_balance').val();
        var from_currency_id = $('#from_currency_id').val();
        var to_currency_id = currency_id;
        var cur_amount = $('#to_amount').val();
        // alert(cur_balance);
        // اگر پول پرداختی و دریافتی یکسان بود مقدار اولیه در مقدار دریافت تایپ گردد
        if(parseInt(from_currency_id) === parseInt(to_currency_id)) {
            $('#from_amount').val(cur_amount);
        }
        else 
        {
            // if(parseInt(cur_balance) > 1)  {  // convert to new new currency
            // }   else  { }
            // $('#show_current_balance').html('<center><img src="<?php echo base_url(); ?>assets/img/small_loader.gif" style="width:7%" alt="Loading"/></center>');
            $.ajax({
                type:'POST',
                data:{from_currency_id: from_currency_id,to_currency_id:to_currency_id,cur_amount:cur_amount },
                url:"<?php echo base_url() . 'getConvertedAmount'; ?>",
                success: function(result)
                {
                    $('#from_amount').val(result);
                }
            });
        }
    }

    function clearSelectedOption()
    {
        $('#to_transaction_option').prop('selectedIndex', -1);
        $('#submit_button').fadeOut(1);
    }

    function setTransactionType(flag, option)
    {
        var debit = 1;
        var credit = 2;
        let finalTransction_type = 0;
        // check for to_transaction_type
        if(parseInt(flag) === 1 && parseInt(option) > 0)
        {
            var to_account_id = $('#to_account_id').val();
            $.ajax({
                type:'POST',
                data:{ option: option,account_id:to_account_id },
                url:"<?php echo base_url() . 'getTransctionType'; ?>",
                success: function(result)
                {
                    $('#submit_button').fadeIn(1);
                    $('#to_transaction_type').val(result);
                    var results = result == '1' ? "کریدت": "دبت";
                    $('#to_transaction_label').val(results);
                }
            });
        } 
       
    }
   
</script>



    <!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
		       
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card" style="min-height: 400px"> 
                    <div class="card-header" style="padding: 10px;">
                        <h4 class="card-title">فورم ویرایش ژورنال  ویا روزنامچه
                        <span class="pull-left"><a href="<?php echo base_url(); ?>oldJournalList"><button class="btn mybtn bg-default">
                            برگشت به لیست  </button></a></span></h4>
                    </div>
                    
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                        <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                         echo form_open_multipart('oldUpdateTo',$attributes); ?>
                         <input  value="<?=$journal[0]['id']?>" type="hidden"  name="id" >
                         <input type="hidden" id="cur_balance" value="" >
                         <input type="hidden" id="from_currency_id" value="<?=$journal[0]['currency']?>" >
                         <div class="form-body" style="padding: 0px 0px 15px !important;">
                          <div class="row" style="padding: 10px 20px;">

                          
                          <?php
                           //   Show Form Validation Error
                            if(!empty($this->session->flashdata('validationError'))) 
                            {
                                $err = $this->session->flashdata('validationError');
                                echo ' <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="alert alert-danger m-r-10 m-l-10 error">'.$err.'</div>
                                 </div>';
                            }
                            ?>


                          <div class="col-md-12 col-sm-12 col-xs-12">
                            <fieldset class="scheduler-border" style="border:1px solid #dbdad3 !important;background:#f9f9f9 !important;margin:10px 0px 10px 0px !important;">
                                <legend class="scheduler-border journal_legend"> &nbsp;  1 &nbsp; </legend>
                                <div class="row">
                                      

                                <input type="hidden" name="to_transaction_type" id="to_transaction_type" value="<?=$journal[0]['transaction_type']?>" />

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                  <label> حساب  </label>
                                    <div class="form-group form-floating-label">
                                        <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_account_id" id="to_account_id" onchange="clearSelectedOption()"   required> 
                                          <option value="<?=$journal[0]['account_id']?>"> <?=$journal[0]['account_name']?>              
                                            <option value=""> حساب را انتخاب نمایید   </option>
                                            <?php foreach($accounts as $key => $value){ ?>
                                                <option value="<?=$value['id']?>">  <?=$value['name']?></option>
                                            <?php } ?>
                                        </select> 
                                        <span style='color:red'><?=form_error('to_account_id')?></span>
                                    </div> 
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                  <div class="row">
                                    <div class="col-8">
                                        <div class="form-group form-floating-label">
                                                <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="to_transaction_option" onchange="setTransactionType(1,this.value)"   > 
                                                    <option value="<?=$journal[0]['transaction_type']?>"> <?=intval($journal[0]['transaction_type'])===1 ? "کاهش": "افزایش" ?> </option>
                                                    <option value=""> نوعیت حساب را انتخاب نمایید </option>
                                                    <option value="1"> کاهش </option>
                                                    <option value="2"> افزایش</option>
                                                </select> 
                                                <span style='color:red'><?=form_error('to_transaction_option')?></span>
                                            </div> 
                                    </div>
                                    <div class="col-4 mt-10">
                                       <div class="form-group form-floating-label">
                                          <input type="text" id="to_transaction_label" class="form-control" value="<?=intval($journal[0]['transaction_type'])===1 ? "کریدت": "دبت" ?>" disabled>
                                       </div>
                                    </div>
                                  </div>
                                </div>

                                    
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                <label> واحد پولی  </label>
                                    <div class="form-group form-floating-label">
                                        <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_currency"  onchange="getPayerBalance(this.value)"  required> 
                                        <option value="<?=$journal[0]['currency']?>"> <?=$journal[0]['currency_name']?> </option> 
                                            <option value="">  واحد پولی  را انتخاب نمایید </option>
                                                <?php foreach($currency as $key => $value){ ?>
                                                    <option value="<?=$value['id']?>"> <?=$value['name']?></option>
                                                <?php } ?>
                                            </select> 
                                        <span style='color:red'><?=form_error('to_currency')?></span> 
                                    </div> 
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                <!-- <label> مبلغ پرداخت</label> -->
                                    <div class="form-group form-floating-label">
                                        <input class="form-control input-solid" id="to_amount" name="to_amount" type="text" step="0.01"  required  value="<?=$journal[0]['amount']?>">
                                            <label for="to_amount" class="placeholder">   مبلغ  </label>
                                            <span style='color:red'><?=form_error('to_amount')?></span>
                                    </div> 
                                    <div class="col-12" id="show_current_balance"></div>
                                 </div>

                                 <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group form-floating-label m-t-20">
                                    <input class="form-control input-solid" id="to_comment" name="to_comment" value="<?=$journal[0]['details']?>" type="text" >
                                        <label for="to_comment" class="placeholder"> کمنت    </label>
                                        <span style='color:red'><?=form_error('to_comment')?></span>
                                    </div> 
                               </div>


                                </div>
                            </fieldset>
                          </div>

               
                          <div class="col-md-12 col-sm-12 col-xs-12">
                          <fieldset class="scheduler-border" style="border:1px solid #dbdad3 !important;background:#f9f9f9 !important;margin:10px 0px 10px 0px !important;">
                            <legend class="scheduler-border journal_legend"> &nbsp;  3 &nbsp; </legend>
                            <div class="row">
                                   
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label> تاریخ ثبت</label>
                                    <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#exampleInput00" data-englishnumber="true">
                                        <span class="fa fa-calendar"></span> 
                                        </span>
                                    </div>
                                        <input class="form-control"  name="todays_date" id="exampleInput00"  
                                        data-targetselector="#exampleInput00" value="<?=$journal[0]['inserted_short_date']?>" 
                                        data-mddatetimepicker="true"  placeholder="تاریخ ثبت"  data-placement="right" data-englishnumber="true"  >
                                    </div>
                                </div>


                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group form-floating-label m-t--15"><label> اسناد</label>
                                    <input type="file" class="form-control input-solid" name="doc" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx" >
                                    <span style='color:red'><?=form_error('doc')?></span>
                                    </div>
                                </div>

                            </div>
                          </fieldset>
                          </div>

                          
					

                        <!-- hidden dates -->
                        <div class="col-md-12 col-sm-12 col-xs-12" id="dateWrapper" style="display:<?php if(!empty($journal[0]['notification_date'])) { echo "";} else { echo "none";} ?>">
                            <div class="row">
                                <!-- pay_date -->
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                <label> تاریخ پرداخت</label>
                                    <div class="form-group">
                                        <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#exampleInput02" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="pay_date" id="exampleInput02"  
                                            data-targetselector="#exampleInput02" value="<?=$journal[0]['pay_date']?>" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ پرداخت"  data-placement="right" data-englishnumber="true"  >
                                        </div>
                                     </div>	
                                </div>

                                <!-- recieve_date -->
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                <label> تاریخ دریافت</label>
                                    <div class="form-group">
                                        <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#exampleInput03" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="recieve_date" id="exampleInput03"  
                                            data-targetselector="#exampleInput03" value="<?=$journal[0]['recieve_date']?>" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ دریافت"  data-placement="right" data-englishnumber="true"  >
                                        </div>
                                     </div>	
                                </div>

                                <!-- notification_date -->
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                <label> تاریخ آگهی</label>
                                    <div class="form-group">
                                        <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#exampleInput04" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="notification_date" id="exampleInput04"  
                                            data-targetselector="#exampleInput04" value="<?=$journal[0]['notification_date']?>" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ آگهی یا نوتفکیشن"  data-placement="right" data-englishnumber="true"  >
                                        </div>
                                     </div>	
                                </div>


                            </div>
                        </div>
                        <!-- / hidden dates -->


                            <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    <!-- <div class="col-6 col-xs-12">
                                      <button type="button" onclick="showHideDates()"  class="form-control btn " style="border: 1px solid #2196f3">
                                        <i class="fa fa-calendar"></i> &nbsp;
                                        ثبت تاریخ پرداخت / دریافت / آگهی
                                      </button>
                                    </div> -->
                                    <div class="col-3 col-xs-6">
                                      <input type="submit" id="submit_button" name="submit" value=" ثبت " class="form-control btn bg-blue pull-left">
                                    </div>
                                    <div class="col-3 col-xs-6">
                                    <a href="<?=$bul?>oldJournalDelete/<?=$journal[0]['id']?>">
                                      <button type="button" onClick="return doConfirm();" class="form-control btn bg-danger">حذف</button>
                                    </a>
                                    </div>
                                    <div class="col-3 col-xs-6">
                                    <a href="<?=$bul?>oldJournalList">
                                      <button type="button"  class="form-control btn bg-warning">لغو</button>
                                    </a>
                                    </div>
                                </div>
                            </div>


                        </div>
                        </div>  <!-- /form-body -->
                    <?php echo form_close(); ?>

						</div> <!-- box-body -->
						
                       
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>
		</div>
        <!-- /main content -->
           
        <script>
            document.getElementById('to_amount').addEventListener('input', function() {
                let to_amount = this.value.replace(/,/g, ''); // Remove existing commas
                to_amount = to_amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
                to_amount = to_amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
                this.value = to_amount;
            });
       </script>