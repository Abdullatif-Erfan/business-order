<?php foreach($records as $key => $value)
{ ?>
<a href="javascript:void(0);">
    <div class="notif-content">
        <span class="block p-10">
            <b class="col-blue"><?php echo  $value['name']; ?></b> در   (<?php echo $value['wname']; ?>)
            به تعداد (<?php echo $value['cur_amount']; ?>) <?=$value['unit_name']?> مانده است
        </span>
    </div>
</a>
<?php } ?>

