<script>
    function createBackup() {
    var conf = confirm("آیا نسبت به گرفتن نسخه پشتیبان موافق هستید؟");
    
    if (conf) {
        // Replace the URL with the actual URL where your backup creation logic exists
        var bul = $('#bul').val();
        window.location.href = bul + "settings/backup/createBackup";
        
        // If the URL is relative to the current page, you can use something like:
        // window.location.href = "createBackup";
    }
}

    document.addEventListener("click", function(event) {
    // Check if the clicked element is the backupButton
    if (event.target.id === "backupButton") {
        createBackup(); // Call the createBackup function
    }
});

function doConfirmRestore()
{
	var conf = confirm('آیا میخواهید ریستور نمایید ؟');
	if(conf)
	{
		return true;
	} else {
		return false;
	}
}
</script>
<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
			
			  <input type="hidden" id="bul" value="<?=base_url()?>" >
			  <div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->
											
			<!-- content -->
            <div class="row">
          <div class="col-md-8 col-sm-12 col-xs-12">
              <div class="col-md-12 col-sm-12 col-xs-12">
                   <div class="box-header with-border">
                        <div class="box-tools pull-right">
                        <button id="backupButton" class="btn btn-primary btn-sm btn-info font-bold" type="button">
                            <i class="fa fa-plus-square-o"></i> ایجاد فایل پشتبانی جدید
                        </button>

                        </div>
                    </div>
                    <br><hr class='hr'>
              </div>
                 <div class="table-responsive m-b-20">
                    <table id="example4" class="table table-hover rtl">
                        <thead>
                            <tr>
                               <th>شماره</th>
                                <th>فایل</th> 
                                <!-- <th>دانلود</th> -->
                                <th>ریستور</th>
                                <th>حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            foreach ($dbfileList as $data) {
                                ?>
                                <tr>
                                  <th><?php echo $count; ?></th>
                            
                                  <td width="80%" class="mailbox-name">
                                        <a href="#"><?php echo $data; ?></a>
                                    </td>                                    

                                    <!-- <td class="mailbox-name">
                                        <a href="<?php echo base_url().'settings/backup/downloadbackup/' . $data; ?>" class=" btn btn-icon btn-round btn-info btn-sm" >
                                        <i class='fa fa-download m-t-5'></i>
                                        </a>
                                    </td> -->

                                    <td class="mailbox-name">
										<a href="<?php echo base_url().'settings/backup/fileRestore/' . $data; ?>"
                                         onClick="return doConfirmRestore()">
                                            <button type='button' class='btn btn-icon btn-round btn-warning btn-sm'  type="submit" name="backup" value="restore">
											  <i class='fas fa-redo m-t-5'></i>
                                            </button>
                                       </a>

                                     </td>

                                    <td class="mailbox-name">
                                        <a href="<?php echo base_url().'settings/backup/dropbackup/' . $data; ?>"
                                         onClick="return doConfirm()">
                                            <button type='submit' class='btn btn-icon btn-round btn-warning btn-sm'  type="submit" name="backup" value="restore">
                                                      <i class='fa fa-trash m-t-5'></i>
                                            </button>
                                       </a>
                                    </td>
                                    
                                </tr>
                                <?php
                                $count++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
          </div>
          <div class="col-md-4 col-sm-12 col-xs-12">
             <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">اپلود نسخه پشتیبان</h3>
                    </div>
					<?php  echo form_open_multipart('settings/backup/fileUpload'); ?>
                        <div class="box-body">
                                <input class="filestyle form-control" data-height="30"  type="file" name="file" id="exampleInputFile" required>
                            <span class="text-danger"><?php echo form_error('file'); ?></span>
                        </div> 
                        <div class="box-footer"><br>
                            <button class="btn btn-primary btn-sm pull-right" type="submit" name="backup" value="upload"><i class="fas fa-upload"></i> اپلود</button>
                        </div>
                    </form>
                </div>
          </div>
          </div>
    <!-- /content -->      
					

					   </div> <!-- / card-body -->
				     </div>
				   </div> <!-- / row -->
				   
				   
				  </div>
		       </div>
		    </div>

				<!-- footer -->
				<?php $this->load->view('component/footer-text.php'); ?>
				<!-- /footer -->
			</div>
        <!-- /main content -->
        <?php $bul = base_url(); ?>
	<!--   Core JS Files   -->
    <script src="<?php echo $bul; ?>assets/plugin/datatable/js/jquery.dataTables.js"></script> 
    <script src="<?php echo $bul; ?>assets/plugin/datatable/js/dataTables.bootstrap.js"></script>
<script type="text/javascript">
    var table = $('#example4').DataTable();

    // $('#form1').submit(function () {
    // });
    // $('.formdelete').submit(function () {
    //     var c = confirm("آیا با عملیه حذف موافق هستید ؟");
    //     return c;
    // });
    // $('.formrestore').submit(function () {
    //     var c = confirm("آیا با فعالسازی  این نسخه پشتیبان موافق هستید ؟");
    //     return c;
    // });
</script>
