s<script>
 function submitAccountIdToURL()
 {
    var account_id = parseInt($('#account_id').val());
    var base_url = $('#base_url').val();
    if(account_id > 0) {
        window.location.href= base_url + "reports/ledger/" + account_id;
    } else {
        alert("حساب را انتخاب نمایید");
    }
 }
</script>
<style>
.dt-button{ display:none !important;}
#table_filter{display:none !important;}
table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
}
</style>
<?php $this->load->view('reports/numToChar.php'); ?>
<!--  main content -->
		<div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12 m-t--10">
				  <div class="card">

                  <div class="col-12" style="padding: 20px; margin-bottom: 10px;" >
                        <div class="row">
                        <a href="<?=base_url().'journals'?>">
                            <button class="printBtn">
                               <i class="fas fa-arrow-left"></i>
                            </button>
                         </a>
                        </div>
                    </div>


					<div class="card-body" style="border-top: 1px solid #ddd; margin-top: 10px"><!-- card-body -->				
                     <div class="row" >
                     <!-- right -->
                           <div class="col-md-6 col-xs-8 col-xs-12" id="print_area">
                                    <table class="table table-bordered" style="width:50%">
                                        <tr>
                                            <td colspan="2" style="background-color: #3f7cc7; color: #fff; text-align:center; font-size: 20px; padding: 4px">
                                            جزییات روزنامچه</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 120px;">کد نمبر</td>
                                            <td><?=$records[0]['code']?></td>
                                        </tr>
                                        <tr>
                                            <td>تاریخ ثبت</td>
                                            <td><?=$records[0]['inserted_full_date']?></td>
                                        </tr>
                                        <tr>
                                            <td> حساب <?=intval($records[0]['transaction_type']) === 1 ? " بردگی / دریافت کننده" : " رسیدگی / پرداخت کننده"?> </td>
                                            <td><?=$records[0]['name']?> </td>
                                        </tr>
                                       
                                        <tr>
                                            <td> مبلغ  </td>
                                            <td><?=number_format($records[0]['amount'],2)?> <?=$records[0]['currency_name']?></td>
                                        </tr>
                                        <tr>
                                            <td>مبلغ   به حروف</td>
                                            <td> <?=convertNumber(floatval($records[0]['amount']))?></td>
                                        </tr>
                                        <tr>
                                            <td>تفصیلات</td>
                                            <td> <?=$records[0]['details']?>  </td>
                                        </tr>
                                            <tr style="background-color:#f2fbfb">
                                            <td> حساب <?=intval($records[1]['transaction_type']) === 1 ? " بردگی / دریافت کننده" : " رسیدگی / پرداخت کننده"?> </td>
                                                <td><?=$records[1]['name']?> </td>
                                            </tr>
                                            <tr style="background-color:#f2fbfb">
                                                <td>مبلغ  </td>
                                                <td><?=number_format($records[1]['amount'],2)?> <?=$records[1]['currency_name']?></td>
                                            </tr>
                                            <tr style="background-color:#f2fbfb">
                                               <td>مبلغ   به حروف</td>
                                               <td> <?=convertNumber(floatval($records[1]['amount']))?></td>
                                            </tr>
                                            <tr style="background-color:#f2fbfb">
                                               <td>تفصیلات</td>
                                               <td> <?=$records[1]['details']?>  </td>
                                            </tr>
                                           <tr>
                                             <td>ثبت کننده</td>
                                             <td><?=$records[1]['full_name']?></td>
                                           </tr>
                                        <tr>
                                            <td colspan="2" style="padding: 10px;">
                                                
                                            <!-- return button -->
                                            <!-- <a href="<?=base_url().'journals'?>" class="hidden-print">
                                               <button class="btn btn-primary btn-sm btn-border m-r-10">
                                               <i class="fas fa-arrow-right"></i>  برگشت 
                                               </button>
                                            </a> -->
             

                                            <!-- print button -->
                                            <a target="_blank" href="<?=base_url().'printVoucher/'.$records[0]['times']?>"    class="hidden-print">
                                                <button  class="btn btn-primary btn-sm btn-border m-r-10  hidden-print" >
                                                <i class="fas fa-print"></i>   چاپ رسید 
                                                </button>
                                            </a>

                                               <!-- print button -->
                                               <!-- <button onclick="print_page()" class="btn btn-success btn-sm btn-border m-r-10 hidden-print" >
                                               <i class="fas fa-print"></i>  چاپ این صفحه 
                                               </button> -->
                                            
                                            <!-- edit button -->
                                            <?php 
                                            $editRoute = "journalEditForm/".$records[0]['times']; 
											if($records[0]['status'] == 2) {
                                               if(doesHaveAccessTo('journal','edit_records')) { ?>
                                                <a href="<?=base_url().$editRoute?>"  class="hidden-print">
                                                    <button class="btn btn-primary btn-sm m-r-10">
                                                    <i class="fas fa-pen"></i>  ویرایش  
                                                    </button>
                                                 </a>
                                            <?php } 
											}
                                           ?>
                                            

                                            
                                            <!-- delete button -->
                                            <?php 
											if($records[0]['status'] == 2) {
                                            if(doesHaveAccessTo('journal','delete_records')) { ?>
                                                 <a href="<?=base_url().'deleteJournals/'.$records[0]['times']?>" onClick="return doConfirm();" class="hidden-print">
                                               <button class="btn btn-danger btn-sm m-r-10">
                                               <i class="fas fa-trash error "></i>  حذف 
                                               </button>
                                              </a>
                                            <?php } }
                                           ?>
                                           

                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <!-- / right -->
                                
                                <!-- left -->
                                <div class="col-md-6 col-xs-4 col-xs-12">
                                    <?php if(empty($records[0]['doc'])) {
                                         if(doesHaveAccessTo('journal','edit_records')) { 
                                        ?>
                                        <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                                            echo form_open_multipart('updateJournalFile',$attributes); ?>
                                            <input class="form-control"  value="<?=$records[0]['code']?>" type="hidden"  name="code" required>

                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group form-floating-label m-t--15"><label> اسناد</label>
                                                <input type="file" class="form-control input-solid" name="doc" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx" required >
                                                <span style='color:red'><?=form_error('doc')?></span>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <input type="submit"  name="submit" value="ذخیره سند" class="form-control btn btn-sm btn-primary">
                                            </div>

                                       <?php } } else {
                                    
                                        $extension = pathinfo($records[0]['doc'], PATHINFO_EXTENSION);
                                        $address = strtolower($extension);
                                        $path = base_url().$records[0]['doc'];
      
                                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                                            if (exif_imagetype($path) !== false) { ?>
                                                <img class="img-fluid" src="<?=$path?>" alt="">
                                           <?php } 
                                          }
                                         else { ?>
                                            <a href="<?=$path?>">
                                              <button class="btn btn-sm"> دانلود  <i class="fas fa-download"></i> </button>
                                            </a>
                                        <?php }

                                      } ?>
                                </div>
                                <!-- /left -->
                            </div>


					   </div> <!-- / card-body -->
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>

				<!-- footer -->
				<?php
				//  $this->load->view('component/footer-text.php');
				 ?>
				<!-- /footer -->
			</div>
		<!-- /main content -->
		

		
		