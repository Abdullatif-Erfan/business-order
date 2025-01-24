<div class="form-group form-floating-label">
    <select  class="form-control" style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="type_id" required> 
        <option value=""> نوع جنس را انتخاب نمایید </option>
        <?php foreach($record as $key => $value)
        { ?>
        <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
        <?php } ?>
    </select>  
</div>