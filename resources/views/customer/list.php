<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
			lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "همه"]],
            pageLength: 10,
			// columnDefs: [
            //     { width: '150px', targets: 6 } // Adjust the index (6) to the correct column index
            // ]
        });
    });
</script>
<style>
.dt-button{ display:none !important;}
</style>
<!--  main content -->
<div class="main-panel">
	<div class="content">
		<div class="page-inner">
		<div class="row ">

		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="card">
			<div class="card-header" style="padding: 10px; text-align:center;">
                
                <a href="<?php echo base_url(); ?>addCustomerForm/<?=$this->uri->segment(2)?>" class="pull-right">
                    <button type="button" class="btn btn-sm mybtn">
                        <i class="fas fa-plus"></i>  ثبت جدید 
                    </button>
                </a>
                <span class="card-title"> لیست  <?=show_customer_type_plural($customer_type)?> </span>
			</div>
            
			<div class="card-body"><!-- card-body -->
						
            <?php 
            $percentage_label = "";
            if(intval($customer_type)===4) 
            { 
                $percentage_label = "فیصدی سهم";
            } else {
                $percentage_label = " نام گزارش دهی ";
            }
            ?>

			<div class="table-responsive table_responsive" style="padding:5px;">
             <table id="myTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th> &nbsp; شماره </th>
                        <th> نام مکمل  </th>					
                        <th>شماره تماس</th>									
                        <th>آدرس</th>	
                        <?php if(intval($customer_type) === 0) { ?>
                        <th>معاشات / پرداخت ها</th>	
                        <?php } ?>
                        <th>ویرایش</th>		
                        <th>حذف </th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; $id2=200; 
                     foreach($customers as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo $value['full_name']; ?></td>
                            <td><?php echo $value['phone']; ?></td>
                            <td><?php echo $value['address']; ?></td>
                          
                            <?php if(intval($customer_type) === 0) { ?>
                                <td>
                                    <center><a href="<?=base_url().'salary/'.$value['id']?>">
                                    <button class="btn btn-info btn-sm"><i class="fas fa-dollar-sign"></i></button>
                                    </a></center>
                                </td>
                           <?php } ?>

                            <td>
                             <?php if(is_admin()) { ?>
                                <a href="<?php echo base_url(); ?>customer/customer/editForm/<?php echo $value['id']; ?>/<?=$customer_type?>">
                                    <i class="fas fa-pen-square"  style="font-size:20px;" alt=""></i>
                                </a>
                             <?php } ?>
                             </td>

                             <td>
                             <?php if(is_admin()) { ?>
                                <a href="<?php echo base_url(); ?>customer/customer/delete/<?php echo $value['id']; ?>/<?=$customer_type?>">
                                    <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                                </a>
                             <?php } ?>
                             </td>
                        </tr>
                   <?php $id++; $id2++; } ?>
                    
                </tbody>
            </table>
        </div> <!-- /table responsive -->  
					
					   </div> <!-- / card-body -->
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>

			<!-- footer -->
			<?php $this->load->view('component/footer-text.php'); ?>
			<!-- /footer -->
		</div>
	<!-- /main content -->
	
