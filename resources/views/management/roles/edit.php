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
<?php
$roleId = $roleInfo->roleId;
$role = $roleInfo->role;
$status = $roleInfo->status;
?>

	<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				
			  <!-- breadcrum -->
				<div class="page-header m-t--10">
					<ul class="breadcrumbs">
						<li class="nav-home">
							<a href="<?php echo base_url(); ?>home">
								<i class="fas fa-home"></i>
							</a>
						</li>
						<li class="separator">
							<i class="flaticon-right-arrow"></i>
						</li>
						<li class="nav-item">
							<a href="<?php echo base_url(); ?>management/roles/roleListing">رول </a>
						</li>
					</ul>
				</div>
				<!-- /breadcrum -->
					
				<div class="row">
		      
			
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-body"><!-- card-body -->
										
				
                    <?php echo form_open('management/roles/editRole'); ?>
                           <input type="hidden" value="<?php echo $roleId; ?>" name="roleId" id="roleId"  />
							<div class="form-body">
								<div class="row">
										
									<div class="col-md-5 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="role" name="role" type="text" value="<?=$role?>" required><label for="role" class="placeholder">نام رول را بنویسید</label>
										<span style='color:red'><?=form_error('role')?></span>
									  </div> 
									</div>

								   <div class="col-md-4 col-sm-6 col-xs-12">
									 <div class="form-group form-floating-label">
									  <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="status" required> 
										<option value="<?=$status?>"><?=intval($status)==1?'فعال':'غیرفعال'?></option>
										<option value="">وضعیت فعلی</option>
										<option value="1">فعال</option>
										<option value="2">غیرفعال</option>
									  </select>  
									 </div>
									</div>

									<div class="col-md-3 col-sm-6 col-xs-12 m-t-10">
                                        <button type="submit" name="submit" class="btn btn-primary btn-sm " >
                                            <span class="btn-label"> <i class="fa fa-save"></i> </span>
                                            ثبت
                                        </button>
                                        <a href="<?=base_url().'management/roles'?>" style="margin-right:20px">
                                            <button type="button" class="btn btn-warning btn-sm"> لغو </button>
                                        </a>
                                            
									</div>

								</div>  <!-- /form-body -->
							</form>
						</div> <!-- box-body -->


					   </div> <!-- / card-body -->

                        <hr />
                        <!-- Metrix card body -->
                        <div class="card-body">
                          
                            <div class="col-12">
                                <div class="row">
                                    <div class="box border col-12">
                                        <div class="box-header">
                                            <h3 class="box-title">انتخاب صلاحیت برای این رول</h3>
                                            <div class="box-tools">
                                            </div>
                                        </div><!-- /.box-header -->
                                        <?php echo form_open('management/roles/storeAccessMatrix'); ?>
                                        <input type="hidden" value="<?php echo $roleId; ?>" name="roleIdForMatrix" id="roleIdForMatrix" />
                                        <div class="box-body table-responsive no-padding">
                                            <table class="table table-hover">
                                                <tr>
                                                    <th>بخش</th>
                                                    <th>تمام صلاحیت</th>
                                                    <th> نمایش</th>
                                                    <th>ایجاد</th>
                                                    <th>ویرایش</th>
                                                    <th>حذف</th>
                                                </tr>
                                                <?php
                                                if(!empty($moduleList))
                                                {
                                                    foreach($moduleList as $record)
                                                    {
                                                        $key = array_search($record['module'], array_column($roleAccessMatrix, 'module'));
                                                        $matrix = (array) $roleAccessMatrix[$key];
                                                ?>
                                                <tr>
                                                    <td>
                                                       <b><?php echo $record['label'] ?></b> 
                                                       <input type="hidden" name="access[<?= $record['module'] ?>][module]" value="<?php echo $record['module'] ?>"  /> 
                                                    </td>
                                                    
                                                    <td>
                                                        <input type='checkbox' name='access[<?= $record['module'] ?>][total_access]' 
                                                          <?= ($matrix['total_access'] == 1) ? 'checked':''; ?> />
                                                    </td>
                                                    
                                                    <td>
                                                        <input type='checkbox' name='access[<?= $record['module'] ?>][list]' <?= ($matrix['list'] == 1) ? 'checked':''; ?> />
                                                    </td>
                                                    
                                                    <td>
                                                        <input type='checkbox' name='access[<?= $record['module'] ?>][create_records]' <?= ($matrix['create_records'] == 1) ? 'checked':''; ?> />
                                                    </td>
                                                    
                                                    <td>
                                                        <input type='checkbox' name='access[<?= $record['module'] ?>][edit_records]' <?= ($matrix['edit_records'] == 1) ? 'checked':''; ?> />
                                                    </td>
                                                    
                                                    <td>
                                                        <input type='checkbox' name='access[<?= $record['module'] ?>][delete_records]' <?= ($matrix['delete_records'] == 1) ? 'checked':''; ?> />
                                                    </td>

                                                </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </table>
                                        
                                        </div><!-- /.box-body -->
                                        <div class="box-footer clearfix">
                                            <input type="submit" class="btn btn-primary" value="ثبت صلاحیت" />

                                            <a href="<?=base_url().'management/roles'?>" style="margin-right:20px">
                                                <button type="button" class="btn btn-warning"> لغو </button>
                                            </a>
                                            <br />
                                            <br />

                                        </div>
                                        </form>
                                        </div><!-- /.box -->
                                </div>
                            </div>
                        </div>
                        <!-- /Metrix Card Body -->

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