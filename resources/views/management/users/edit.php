<?php
$userId = $userInfo->userId;
$name = $userInfo->name;
$username = $userInfo->username;
$email = $userInfo->email;
$mobile = $userInfo->mobile;
$roleId = $userInfo->roleId;
$isAdmin = $userInfo->isAdmin;
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
							<a href="<?php echo base_url().'management/user'; ?>">لیست کاربران</a>
						</li>
					</ul>
				</div>
				<!-- /breadcrum -->
					
				<div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-header" style="padding:10px;">
                       <h3> ویرایش کاربر</h3>
					</div>
					<div class="card-body"><!-- card-body -->
										
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
					 <!-- form start -->
                     <?php  $attribute = array('name'=>'add_form');  
                        echo form_open_multipart('management/user/editUser', array('id' => 'img')); ?>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4">    
                                                            
                                    <div class="form-group">
                                        <label for="fname">نام مکمل (ضروری)</label>
                                        <input type="text" class="form-control required" name="fname" minlength="5" maxlength="128" 
                                        value="<?php echo $name; ?>"  required>
                                        <input type="hidden" value="<?php echo $userId; ?>" name="userId" id="userId" />    
                                    </div>
                                    
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">ایمیل آدرس </label>
                                        <input type="email" class="form-control" name="email" minlength="15" maxlength="128" 
                                        value="<?= $email ?>" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for=""> نام کاربری (ضروری)</label>
                                        <input type="text" class="form-control" name="username" minlength="5" maxlength="128" required
                                        value="<?= $username ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password">رمز عبور (ضروری)</label>
                                        <input type="password" class="form-control"  name="password" id="password" minlength="6" maxlength="20" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cpassword">تکرار رمز عبور (ضروری) </label>
                                        <input type="password" id="repassword" class="form-control"  name="cpassword" maxlength="20"
                                        oninput="confirmPass()" >
                                        <span style='color:red' id="conf_pass"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mobile">نمبر مبایل</label>
                                        <input type="text" class="form-control"  name="mobile" maxlength="10" value="<?=$mobile?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="role">رول</label>

                                        <select class="form-control" id="role" name="role" required>
                                          <option value="">انتخاب رول</option>
                                            <?php
                                            if(!empty($roles))
                                            {
                                                foreach ($roles as $rl)
                                                {
                                                    $roleText = $rl->role;
                                                    $roleClass = false;
                                                    if ($rl->roleStatus == 2) {
                                                        $roleText = $rl->role . ' (غیرفعال)';
                                                        $roleClass = true;
                                                    }
                                                    ?>
                                                    <option value="<?php echo $rl->roleId; ?>" <?php if ($roleClass) { echo "class=text-warning"; } ?>  <?php if($rl->roleId == $roleId) { echo "selected=selected";} ?>><?= $roleText ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>

                                    </div>
                                </div>    
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="isAdmin">نوعیت کاربر (ضروری)</label>
                                        <select class="form-control required" id="isAdmin" name="isAdmin" required>
                                           <option value="<?=$isAdmin?>"><?=$isAdmin == 0 ? 'کاربر عادی': 'ادمین'?></option>
                                            <option value="0">کاربر عادی</option>
                                            <option value="1">ادمین</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- <input type="hidden" name="isAdmin" value="<?=$isAdmin?>" /> -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cpassword"> آپلود عکس </label>
                                        <input type="file" class="form-control" id="photo" name="photo" accept=".jpg, .jpeg, .png, .PNG" >
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.box-body -->
    
                        <div class="box-footer m-t-10 m-r-10">
                            <input type="submit" class="btn btn-primary" value="ویرایش کاربر" />
                            <a href="<?=base_url().'management/user'?>" style="margin-right:20px">
                               <button type="button" class="btn btn-warning"> لغو </button>
                            </a>
                            <br />
                            <br />
                        </div>
                    </form>

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
        