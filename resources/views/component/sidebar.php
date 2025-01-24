<?php $bul = base_url();
	$packageId = activePackageId();
?>
        <div class="sidebar sidebar-style-2">			
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left float_dr float_en mr-2 avatar avatar-online">
							<?php 
							if ($this->session->userdata('userId')) {  
								$userId = $this->session->userdata('userId');  
								$userImage = show_this_column('photo', 'tbl_users', array('userId' => $userId)); 
								if(!empty($userImage))
								{ ?>
                                    <img src="<?php echo $bul.$userImage; ?>" alt="..." class="avatar-img rounded-circle">
						  <?php }
							  else { ?>
							  <img src="<?php echo base_url(); ?>assets/img/no_image.png" alt="..." class="avatar-img rounded-circle">
							<?php } 
						  } ?>
							

						</div>
						<div class="info">
							<a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
								<span>
									<?php echo $this->session->userdata ( 'name' ); ?>
									<span class="user-level"> 
										 <?php echo $this->session->userdata ( 'roleText' ); ?>
								    </span>
									<span class="caret"></span>
								</span>
							</a>
							<div class="clearfix"></div>
						</div>
					</div>
					<ul class="nav nav-primary">
						<li class="nav-item">
							<a  href="<?php echo $bul; ?>home">
								<i class="fas fa-home"></i>
								<p>صفحه اصلی</p>
							</a>
						</li>

						<!-- <li class="nav-item">
							<a data-toggle="collapse" href="#lists">
								<i class="fas fa-user-plus"></i>
								<p>لست اشخاص</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="lists">
								<ul class="nav nav-collapse">
								    <li>
										<a href="<?=$bul?>customer/0"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
											<span class="sub-item">کارمندان</span>
										</a>
									</li>
									<li>
									   <a href="<?=$bul?>customer/1"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
											<span class="sub-item"> فروشنده گان </span>
										</a>
									</li>
									<li>
									   <a href="<?=$bul?>customer/2"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
											<span class="sub-item">  تهیه کننده گان </span>
										</a>
									</li>
									<li>
									    <a href="<?=$bul?>customer/3"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
											<span class="sub-item"> مشتریان</span>
										</a>
									</li>
									<li>
									    <a href="<?=$bul?>customer/4"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
											<span class="sub-item"> سهم داران </span>
										</a>
									</li>
									
								</ul>
							</div>
						</li> -->

						

						<?php
							if(doesHaveAccessTo('settings','list')) {
							?>
							<li class="nav-item">
							<a href="<?=$bul?>settings">
								<i class="fas fa-cog"></i>
								<p> تنظیمات اولیه</p>
							</a>
						  </li>
						 <?php } ?>
					
						<!-- <li class="nav-item">
							<a href="<?php echo $bul; ?>rates">
								<i class="fas fa-percentage"></i>
								<p> ثبت نرخ روز</p>
							</a>
						</li>	 -->
						
						<?php
							if(doesHaveAccessTo('journal','list') && $packageId >= 1) {
							?>
						    <li class="nav-item">
							   <a href="<?php echo $bul; ?>journals">
								  <i class="fas fa-file-invoice-dollar"></i>
								   <p> روزنامچه / ژورنال </p>
						    	</a>
						   </li>
						 <?php } ?>
					


						 <?php
								if(doesHaveAccessTo('gen_buy','list')  && $packageId >= 1)  { ?>
								<li class="nav-item">
								   <a data-toggle="collapse" href="#buy-chicken">
									   <i class="fas fa-cart-arrow-down"></i>
									   <p>خرید عمومی  </p>
									   <span class="caret"></span>
								   </a>
								   <div class="collapse" id="buy-chicken">
									   <ul class="nav nav-collapse">
										   <!-- <li>
											   <a href="<?=$bul?>categoryList"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
												   <span class="sub-item"> کتگوری خریدها  </span>
											   </a>
										   </li> -->
										   <li>
											   <a href="<?=$bul?>showBuyingForm"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
												   <span class="sub-item">  خرید جدید </span>
											   </a>
										   </li>
										   <li>
											   <a href="<?=$bul?>buyPreList"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
												   <span class="sub-item"> ثبت اجناس برای خرید </span>
											   </a>
										   </li>
										   <li>
											   <a href="<?=$bul?>boughtList"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
												  <span class="sub-item">  لیست خرید </span>
											   </a>
										   </li>
										   <!-- <li>
											   <a href="<?=$bul?>monthly"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
												   <span class="sub-item">  بل ها </span>
											   </a>
										   </li> -->
									   </ul>
								   </div>
								</li>	

						 <?php } ?>

						<!-- <li class="nav-item">
							<a data-toggle="collapse" href="#production">
								<i class="fas fa-layer-group"></i>
								<p>تولیدی</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="production">
								<ul class="nav nav-collapse">
								   <li>
										<a href="<?=$bul?>monthly"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
											<span class="sub-item">  اقلام </span>
										</a>
									</li>
									<li>
										<a href="<?=$bul?>monthly"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
											<span class="sub-item"> قلم </span>
										</a>
									</li>
								</ul>
							</div>
						</li>	 -->

						<?php
						    if(doesHaveAccessTo('transfer','list')  && $packageId >= 1) {
							?>
								<li class="nav-item">
									<a href="<?php echo $bul; ?>movements">
										<i class="fas fa-exchange-alt"></i>
										<p> انتقالات اجناس</p>
									</a>
								</li>	
						 <?php } ?>
						 

						 <?php
							if(doesHaveAccessTo('gudam','list')  && $packageId >= 1) {
							?>
								<li class="nav-item">
									<a data-toggle="collapse" href="#items">
										<i class="fas fa-luggage-cart"></i>
										<p> گدام </p>
										<span class="caret"></span>
									</a>
									<div class="collapse" id="items">
										<ul class="nav nav-collapse">
										<?php $route = "";
											$warehouse = show_all('warehouse'); 
											foreach($warehouse as $key => $value)
											{ ?>
											<li>
												<a href="<?php echo base_url().'warehouseItems/'.$value['id']; ?>"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
													<span class="sub-item"><?=$value['name']?></span>
												</a>
											</li>
											<?php }  ?>
										</ul>
									</div>
								</li> 
						 <?php } ?>
						 

					    
						 <?php
							if(doesHaveAccessTo('sales','list')  && $packageId >= 1) {
							?>
									<li class="nav-item">
										<a data-toggle="collapse" href="#selling">
											<i class="fas fa-file-upload"></i>
											<p> فروشات </p>
											<span class="caret"></span>
										</a>
										<div class="collapse" id="selling">
											<ul class="nav nav-collapse">
												<li>
													<a href="<?=$bul.'showSalesForm'?>"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
														<span class="sub-item"> فروشات جدید</span>
													</a>
												</li>
												
												<li>
													<a href="<?=$bul.'salesList'?>"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
														<span class="sub-item">   لیست فروشات  </span>
													</a>
												</li>

											<!-- <li>
													<a href="#"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
														<span class="sub-item"> بل های فروشات </span>
													</a>
												</li> -->

											</ul>
										</div>
									</li>
						 <?php } ?>


						<!-- <li class="nav-item">
							<a href="<?php echo $bul; ?>order">
								<i class="fas fa-list-alt"></i>
								<p> سفارشات</p>
							</a>
						</li>	

						
						<li class="nav-item">
						   <a href="<?php echo $bul; ?>returned_items">
					 			<i class="flaticon-back" style="color:green"></i>
								<p>اجناس مستردی </p>
							</a>
					    </li> -->

						<?php
							if(doesHaveAccessTo('reports','list')  && $packageId >= 1) {
							if(activePackageId() == 1) {  ?>
								<li class="nav-item">
									<a data-toggle="collapse" href="#reports">
										<i class="fas fa-list-ol"></i>
										<p>  گزارشات </p>
										<span class="caret"></span>
									</a>
									<div class="collapse" id="reports">
										<ul class="nav nav-collapse">
											<li>
												<a href="<?=$bul?>cashflow"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
													<span class="sub-item"> کهاته مشتریان </span>
												</a>
											</li>
											<li>
												<a href="<?=$bul?>chartOfAccount"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
													<span class="sub-item"> چارت حسابات </span>
												</a>
											</li>
										</ul>
									</div>
								</li>
							<?php }
							else if(activePackageId() >= 2) {
							?>
						   <li class="nav-item">
							<a href="<?php echo $bul; ?>reports">
								<i class="fas fa-list-ol"></i>
								<p> گزارشات</p>
							</a>
						  </li>
						 <?php 
						} }
						?>
						 
						<!-- <li class="nav-item">
							<a href="<?php echo $bul; ?>user">
								<i class="fas fa-users"></i>
								<p>مدیریت کاربران</p>
							</a>
						</li>	 -->
						<?php
						// if($is_admin == 1)
						// if()
						if(doesHaveAccessTo('users','list') || $this->session->userdata('isAdmin') == 1) 
						{
							if(activePackageId() == 1) 
							{ ?>
							<li class="nav-item">
									<a href="<?php echo $bul; ?>management/user">
									<i class="fas fa-users"></i>
								    <p>مدیریت کاربران</p>
									</a>
							</li>
							<?php } 
							else if(activePackageId() >= 2)
							{ ?>
							<li class="nav-item">
							<a data-toggle="collapse" href="#user">
								<i class="fas fa-cart-arrow-down"></i>
								<p> مدیریت کاربران </p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="user">
								<ul class="nav nav-collapse">
									<li>
										<a href="<?=$bul?>management/roles"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
											<span class="sub-item"> رول </span>
										</a>
									</li>
									<li>
										<a href="<?=$bul?>management/user"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
											<span class="sub-item"> کاربران </span>
										</a>
									</li>
								</ul>
							</div>
					    	</li>
						<?php }
					   }
					 ?>

						<?php 
						 if(!empty($packageId))
						 { ?>
							<li class="nav-item">
								<a  href="<?php echo base_url().'settings/backup'; ?>">
									<i class="fas fas fa-database"></i>
									<p>  نسخه پشتبان </p>
								</a>
							</li>
						 <?php }
						?>

					</ul>
				</div>
			</div>
		</div>

		