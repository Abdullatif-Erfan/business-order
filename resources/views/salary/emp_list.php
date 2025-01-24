<div class="text" style="margin-top: 10px; border-bottom: 2px dotted #d9d9d9;padding-bottom: 5px;margin-bottom: 5px">  لیست کارمندان </div>

    <?php $id = 1; foreach($employee as $key => $value){ ?>
        <a href="<?=base_url()."salary/".$value['id']?>">
            <div id="reports" data-id="1"><?=$id." : ".$value['full_name']?></div>
        </a>
    <?php $id++; } ?>