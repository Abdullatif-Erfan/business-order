
<?php $bul=base_url(); ?>
<script>
function showNextRow(id)
{
    // var row = $('#row'+id);
    // row.style.display="block";
    var elem = document.getElementById("row" + id);
        elem.style.display = "block";
        elem.style.width="188%";
	
}

function removeCurrentRow(id)
{
    // var row = $('#row'+id);
    // row.style.display="block";
    var elem = document.getElementById("row" + id);
		elem.style.display = "none";
	
}
function setItemValue(id)
{  
	$("#amount").focus();	   
}
// function updateTotal(buy_unit_price,id)
// {
//     var amount = parseFloat($('#amount'+id).val());
//     var result = amount * parseFloat(buy_unit_price);
//     $('#total'+id).val(result);
    
//     var total_price = $('#total_price').val();
//     // var final_sum = total_price + result;
//     // var final_sum =  parseFloat(total_price) + parseFloat(result);
//     $('#total_price').val(result);
// }
function updateTotal(buy_unit_price)
{
    var amount = parseFloat($('#amount').val());
    var result = amount * parseFloat(buy_unit_price);
    $('#total').val(result);
    
    var total_price = $('#total_price').val();
    // var final_sum = total_price + result;
    // var final_sum =  parseFloat(total_price) + parseFloat(result);
    $('#total_price').val(result);
}

function updateWhileEnteringDiscount(discount) 
{
    var total_price = $('#total_price').val();
    $('#payable').val(total_price - discount);
}
function updateWhileEnteringCurPay(curpay)
{
    var payable = $('#payable').val();
    if(parseInt(curpay) > parseInt(payable) )
    {
        alert('مقدار پرداخت نادرست میباشد');
    } 
    else 
    {
        $('#remained').val(payable - curpay);
    }
}
</script>
<style>

table.new thead tr th{background-color:#fff !important; color:#000 !important;text-align:center;}
table.my_table thead tr th{background-color:#3f7cc7  !important; color:#fff !important;text-align:center;}
.new tbody tr td{padding: 10px 5px;}
select.select2{text-align:right !important;direction:rtl !important;}

</style>


    <!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
		       
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card" style="min-height: 400px"> 
                    <div class="card-header" style="padding: 10px;">
                        <h4 class="card-title">جزییات فورم خریداری
                        <span class="pull-left">
                        <a href="<?php echo base_url(); ?>boughtList"><button class="btn mybtn bg-default"> برگشت به لیست  </button></a></span></h4>
                     </div>
            
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                         <div class="form-body" style="padding: 0px 0px 15px !important;" id="print_area">
                         <div class="row" style="padding: 10px 40px;">

                      <!-- First Print Section -->
                        
                        <!------------------------ first row --------------------------->
                        <p class="d-none">تاریخ چاپ‌ :  <?=show_full_date2()?></p>
                        <table  style="width:100%">
                           <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
									<td colspan="3">
									<img  src="<?php echo base_url().'assets/img/header.jpg';?>" class="img-responsive visible-print" style="width:100%" alt="header Image">
								     </td>
								</tr>
                                <tr>
                                    <td> نام فروشنده: <?= $med_bought[0]['customer_account_name'] ?></td>
                                    <td>  تاریخ ثبت : <?= $med_bought[0]['idate'] ?></td>
                                    <td>  نمبر بل : <?= 'BUY_'.$med_bought[0]['billno'] ?> </td>
                                </tr>
                        </table>

                        <!-------------------------  second row (item selection) ------------------------------>
                        <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />

                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="row">
                            <div class="table-responsive">
                             <table class="table table-bordered new" style="width:100%">
                                <thead>
                                <tr>
                                    <th style="width:5%"> شماره </th>                                    
                                    <th style="width:10%">  نوع فورم خریداری  </th>
                                    <th style="width:10%">تعداد خرید </th>
                                    <th style="width:10%">واحد</th>
                                    <th style="width:10%">قیمت فی واحد</th>
                                    <th style="width:10%"> قیمت مجموعی</th>
                                    <th style="width:10%">  تاریخ انقضا</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php 
                                 $id = 1;
                                 foreach($details as $key => $value)
                                 { ?>
                                    <tr>
                                        <td><?=$id?></td>
                                        <td><?=$value['pre_list_name']?> </td>
                                        <td><?=$value['amount']?></td>
                                        <td><?=$value['unit_name']?> </td>
                                        <td><?=$value['bought_up']?></td>
                                        <td><?=$value['total']?></td>
                                        <td><?=$value['expire_date']?></td>
                                    </tr>
                                 <?php $id++; }  ?>
                                </tbody>
                            </table>
                            </div>
                            
                             <table class="table table-bordered new" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                <tr>
                                    <td>مجموع پول</td>
                                    <td><?= $med_bought[0]['total_price'] ?></td>
                                    <td> تخفیف </td>
                                    <td><?= $med_bought[0]['discount'] ?></td>
                                    <td> قابل پرداخت</td>
                                    <td><?= $med_bought[0]['payable'] ?></td>
                                </tr>
                                <tr>
                                    <td> پرداخت فعلی</td>
                                    <td><?= $med_bought[0]['cur_pay'] ?></td>
                                    <td> باقی </td>
                                    <td><?= $med_bought[0]['remained'] ?></td>
                                    <td> حساب پرداخت کننده</td>
                                    <td>
                                       <?= $med_bought[0]['account_name'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td> واحد پولی</td>
                                    <td>
                                    <?= $med_bought[0]['currency_name'] ?>
                                    </td>
                                    <td> مصارف ترانسپورت </td>
                                    <td><?= $med_bought[0]['trans_spend'] ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td> تفصیلات</td>
                                    <td  colspan="5"><?= $med_bought[0]['note'] ?></td>
                                </tr>
                             </table>

                            </div> <!-- end of row -->    
                        </div>
                        <!-------------------------  / second row (item selection) ------------------------------>

                        <div class=" visible-print" style="width:100%;margin: 35px 0px; overflow:hidden; height: 24px;color:#000"> ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ </div>
                        
                <!-- Second Print Section -->
                         
                        <!------------------------ first row --------------------------->
                        <table style="width:100%">
                           <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
									<td colspan="3">
									<img  src="<?php echo base_url().'assets/img/header.jpg';?>" class="img-responsive visible-print" style="width:100%" alt="header Image">
								     </td>
								</tr>
                                <tr class="d-none">
                                    <td> نام فروشنده: <?= $med_bought[0]['customer_account_name'] ?></td>
                                    <td>  تاریخ ثبت : <?= $med_bought[0]['idate'] ?></td>
                                    <td>  نمبر بل : <?= 'BUY_'.$med_bought[0]['billno'] ?> </td>
                                </tr>
                        </table>

                        <!-------------------------  second row (item selection) ------------------------------>
                        <hr class="hidden-print" />

                        <div class="col-md-12 col-sm-12 col-xs-12 visible-print">
                            <div class="row">
                            <div class="table-responsive">
                             <table class="table table-bordered new" style="width:100%">
                                <thead>
                                <tr>
                                    <th style="width:5%"> شماره </th>                                    
                                    <th style="width:10%">  نوع فورم خریداری  </th>
                                    <th style="width:10%">تعداد خرید </th>
                                    <th style="width:10%">واحد</th>
                                    <th style="width:10%">قیمت فی واحد</th>
                                    <th style="width:10%"> قیمت مجموعی</th>
                                    <th style="width:10%">  تاریخ انقضا</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php 
                                 $id = 1;
                                 foreach($details as $key => $value)
                                 { ?>
                                    <tr>
                                        <td><?=$id?></td>
                                        <td><?=$value['pre_list_name']?> </td>
                                        <td><?=$value['amount']?></td>
                                        <td><?=$value['unit_name']?> </td>
                                        <td><?=$value['bought_up']?> </td>
                                        <td><?=$value['total']?></td>
                                        <td><?=$value['expire_date']?></td>
                                    </tr>
                                 <?php $id++; }  ?>
                                </tbody>
                            </table>
                            </div>
                            
                             <table class="table table-bordered new" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                <tr>
                                    <td>مجموع پول</td>
                                    <td><?= $med_bought[0]['total_price'] ?></td>
                                    <td> تخفیف </td>
                                    <td><?= $med_bought[0]['discount'] ?></td>
                                    <td> قابل پرداخت</td>
                                    <td><?= $med_bought[0]['payable'] ?></td>
                                </tr>
                                <tr>
                                    <td> پرداخت فعلی</td>
                                    <td><?= $med_bought[0]['cur_pay'] ?></td>
                                    <td> باقی </td>
                                    <td><?= $med_bought[0]['remained'] ?></td>
                                    <td> حساب پرداخت کننده</td>
                                    <td>
                                       <?= $med_bought[0]['account_name'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td> واحد پولی</td>
                                    <td>
                                    <?= $med_bought[0]['currency_name'] ?>
                                    </td>
                                    <td> مصارف ترانسپورت </td>
                                    <td><?= $med_bought[0]['trans_spend'] ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td> تفصیلات</td>
                                    <td  colspan="5"><?= $med_bought[0]['note'] ?></td>
                                </tr>
                             </table>

                            </div> <!-- end of row -->    
                        </div>
                        <!-------------------------  / second row (item selection) ------------------------------>


                            <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    
                                 <!-- print button -->
                                 <!-- <a target="_blank" href="<?=base_url().'printVoucher/'.$med_bought[0]['billno']?>"    class="hidden-print">
                                        <button  class="btn btn-primary btn-sm btn-border m-r-10  hidden-print" >
                                        <i class="fas fa-print"></i>   چاپ بل 
                                        </button>
                                    </a> -->

                                    <!-- print button -->
                                    <button onclick="print_page()" class="btn btn-success btn-sm btn-border m-r-10 hidden-print" >
                                    <i class="fas fa-print"></i>  چاپ  بل 
                                    </button>
                                            
                                    <!-- edit button -->
                                    <a href="<?=base_url().'generalBuyingEditForm/'.$med_bought[0]['times']?>"  class="hidden-print">
                                        <button class="btn btn-primary btn-sm m-r-10">
                                        <i class="fas fa-pen"></i>  ویرایش 
                                        </button>
                                    </a>

                                    
                                    <!-- delete button -->
                                        <a href="<?=base_url().'deleteBoughtItems/'.$med_bought[0]['times']?>" onClick="return doConfirm();" class="hidden-print">
                                            <button class="btn btn-danger btn-sm m-r-10">
                                            <i class="fas fa-trash error "></i>  حذف 
                                            </button>
                                        </a>

                                    </div>
                                </div>
                            </div>


                        </div>
                        </div>  <!-- /form-body -->

						</div> <!-- box-body -->
                        <?php echo form_close(); ?>
						
                       
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>
		</div>
        <!-- /main content -->
        