<?php
if($_POST)
    $row = $_POST;
?>
<div class="col-md-6 form-group<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
    <label class="col-md-3 control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
    <div class="col-md-9">
        <input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" />
        <?php echo app_get_form_error($field_name); ?>
    </div>
</div>

