<?php foreach($records as $key => $value) { ?>  
    <a href="javascript:void(0);">  
        <div class="notif-content">  
            <span class="block p-10">  
                <b class="col-blue"><?php echo $value['name']; ?></b> در   (<?php echo $value['wname']; ?>)  
                <?php if (intval($value['remained_days']) >= 0) { ?>  
                    <span class="badge badge-success"> <?php echo $value['remained_days']; ?> روز تاریخ انقضای شان مانده است </span>  
                <?php } else { ?>  
                    <span class="badge badge-danger">  <?php echo $value['remained_days']; ?> روز تاریخ شان گذشته است </span>  
                <?php } ?>  
            </span>  
        </div>  
    </a>  
<?php } ?>  