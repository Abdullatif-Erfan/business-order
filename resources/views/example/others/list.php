<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
			lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "همه"]],
            pageLength: 10,
			columnDefs: [
                { width: '150px', targets: 6 } // Adjust the index (6) to the correct column index
            ]
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
		<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="card">
			<div class="card-header">
				<h4 class="card-title"> لیست  </h4>
			</div>
			<div class="card-body"><!-- card-body -->
								
			<div class="table-responsive table_responsive" style="padding:5px;">
             <table id="example" class="table table-bordered table-striped table-hover">
                <thead>
                     <tr>
                        <th> &nbsp; شماره </th>
                        <th> نام   </th>	
                        <th> ویرایش    </th>	
                        <th> حذف   </th>	
                      </tr>
                </thead>
                <tbody>
                   <?php $id=1; 
                     foreach($records as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo $value['name']; ?></td>
                            <td>
                            <i class="fas fa-pen" style="font-size:20px;color:green;" alt="حذف"></i>
                            </td>

                             <td>
                             <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                             </td>
                        </tr>
                   <?php $id++; } ?>    
                    
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
	
