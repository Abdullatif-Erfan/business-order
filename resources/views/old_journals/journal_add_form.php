
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

    function handleCheckbox(id)
    {
        var  element = document.getElementById('customer_wrapper'+id);
        if(element.style.display==="none") {
            element.style.display="block";
        } else {
            element.style.display="none";
        }
    }

    function clearSelectedOption()
    {
        // $('#to_transaction_option').val('');
        // $('#to_transaction_option')[0].selectedIndex = -1;
        $('#to_transaction_option').prop('selectedIndex', -1);
        $('#submit_button').fadeOut(1);
    }
    function clearSelectedOption2()
    {
        // $('#to_transaction_option').val('');
        // $('#to_transaction_option')[0].selectedIndex = -1;
        $('#from_transaction_option').prop('selectedIndex', -1);
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
                    // var results = result == '1' ? "کریدت": "دبت";
                    var results = result == '1' ? "کریدت" : result == 2 ? "دبت" : "";
                    $('#to_transaction_label').val(results);
                }
            });
        } 
        else if(parseInt(flag) === 2 && parseInt(option) > 0)
        {
            var from_account_id = $('#from_account_id').val();
            $.ajax({
                type:'POST',
                data:{ option: option,account_id:from_account_id },
                url:"<?php echo base_url() . 'getTransctionType'; ?>",
                success: function(result)
                {
                    $('#submit_button').fadeIn(1);
                    $('#from_transaction_type').val(result);
                    var results = result == '1' ? "کریدت" : result == 2 ? "دبت" : "";
                    $('#from_transaction_label').val(results);
                }
            });
        }
        else 
        {
            $('#submit_button').fadeOut(1);
            $('#from_transaction_label').val('');
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
                        <h4 class="card-title">فورم ثبت ژورنال  ویا روزنامچه
                        <span class="pull-left"><a href="<?php echo base_url(); ?>oldJournalList"><button class="btn mybtn bg-default"> برگشت به لیست  </button></a></span></h4>
                     </div>
                    
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                         <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                         echo form_open_multipart('addOldJournal',$attributes); ?>
                         <input class="form-control"  value="<?=get_new_journal_code()?>" type="hidden"  name="code" required>
                        
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

                            <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <label> حساب  </label>
                                <div class="form-group form-floating-label">
                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_id" id="account_id"    required>             
                                        <option value=""> حساب را انتخاب نمایید   </option>
                                        <?php foreach($accounts as $key => $value){ ?>
                                            <option value="<?=$value['id']?>">  <?=$value['name']?></option>
                                        <?php } ?>
                                    </select> 
                                    <span style='color:red'><?=form_error('account_id')?></span>
                                </div> 
                            </div>
                            
                          <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label for="">مبلغ</label>
                                    <input type="number" class="form-control input-solid" step="0.01"  name="amount"  id="amount" placeholder="مبلغ به عدد" required>  
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group form-floating-label">
                                <label for="">واحد پولی</label>
                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency_id" id="currency_id" required> 
                                        <!-- <option value=""> واحد پولی</option> -->
                                            <?php foreach($currency as $key => $value){ ?>
                                                <option value="<?=$value['id']?>"><?=$value['name']?></option>
                                            <?php } ?>
                                    </select> 
                                    <span style='color:red'><?=form_error('currency_id')?></span> 
                                </div> 
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12">
                              <div class="form-group">
                                <label for=""> انتخاب گزینه </label>
                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="transaction_type" id="transaction_type" required> 
                                        <option value=""> یک گزینه را انتخاب نمایید</option>
                                        <option value="1">حساب قبلی  (بردگی / افزایش به حساب  ) </option>
                                        <option value="2">حساب قبلی ( رسیدگی / کاهش از حساب ) </option>
                                    </select>   
                                </div>
                            </div>

                            <!-- <div class="col-md-4 col-sm-6 col-xs-12">
                              <div class="form-group">
                                <label for=""> انتخاب گزینه </label>
                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="payment_type" id="payment_type" required> 
                                        <option value=""> --- بطور ---</option>
                                        <option value="1"> نقد </option>
                                        <option value="2"> قرض  </option>
                                        <option value="3"> طلب  </option>
                                    </select>   
                                </div>
                            </div> -->
                            <input type="hidden" name="payment_type" value="1" >

                            <div class="col-md-8 col-sm-6 col-xs-12">
                                <div class="form-group">
                                <label for="">یادداشت </label>
                                <input type="text" class="form-control input-solid" name="details" value="نقل از حساب سابقه" 
                                id="details" required>  
                            </div>
                         </div>

                         <div class="col-md-4 col-sm-6 col-xs-12">
                                <label> تاریخ ثبت</label>
                                <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                <div class="input-group-append">
                                <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                    data-targetselector="#exampleInput00" data-englishnumber="true">
                                    <span class="fa fa-calendar"></span> 
                                </span>
                                </div>
                                    <input class="form-control"  name="todays_date" id="exampleInput00"  
                                    data-targetselector="#exampleInput00" value="<?=todays_date()?>" required
                                    data-mddatetimepicker="true"  placeholder="تاریخ ثبت"  data-placement="right" data-englishnumber="true"  >
                                </div>
                            </div>

                            <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    <div class="col-3 col-xs-6">
                                      <input type="submit" id="submit_button" name="submit" value=" ثبت   " class="form-control btn bg-blue pull-left">
                                    </div>
                                    <div class="col-3 col-xs-6">
                                    <a href="<?=$bul?>oldJournalList">
                                      <button type="button"  class="form-control btn bg-danger">لغو</button>
                                    </a>
                                    </div>
                                </div>
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
        <script>
            document.getElementById('from_amount').addEventListener('input', function() {
                let from_amount = this.value.replace(/,/g, ''); // Remove existing commas
                from_amount = from_amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
                from_amount = from_amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
                this.value = from_amount;
            });
       </script>
