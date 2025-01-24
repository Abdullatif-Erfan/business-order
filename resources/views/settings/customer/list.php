						
     <div class="table-responsive table_responsive" style="padding:5px;">
        <table id="example8" class="table table-bordered table-striped table-hover">
                <thead>
                     <tr>
                        <th> &nbsp; شماره </th>
                        <th>  کتگوری  </th>		
                        <th> نام مکمل  </th>					
                        <th>شماره تماس</th>									
                        <th>آدرس</th>	
                        <th>ویرایش</th>		
                        <th>حذف </th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; $id2=200; 
                     foreach($customer as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo $value['name']; ?></td>
                            <td><?php echo $value['full_name']; ?></td>

                            <td><?php echo $value['phone']; ?></td>
                            <td><?php echo $value['address']; ?></td>

                              <td> 
                                 <?php if(doesHaveAccessTo('settings','edit_records')) { ?>
                                    <a href="<?php echo base_url(); ?>settings/customer/editForm/<?php echo  $this->my_encryption->do_encode ($value['account_id']); ?>">
                                       <i class="fas fa-pen-square"  style="font-size:20px;color:green"></i>
                                    </a>
                                 <?php } ?>
                             </td>
                             <td>
                             <?php if(doesHaveAccessTo('settings','delete_records')) { 
                                  if($value['id'] > 3) { ?>
                                <a href="<?php echo base_url(); ?>settings/customer/deleteCustomer/<?php echo $this->my_encryption->do_encode($value['account_id']); ?>">
                                    <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                                </a>
                             <?php } } ?>
                             </td>
                        </tr>
                   <?php $id++; $id2++; } ?>
                    
                </tbody>
            </table>
        </div> <!-- /table responsive -->  