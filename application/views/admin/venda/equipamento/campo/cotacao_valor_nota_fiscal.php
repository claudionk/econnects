<?php
if($_POST)
    $row = $_POST;
?>
<div class="col-md-6 form-group<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">

    <input class="form-control inputmask-valor" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" />
    <?php echo app_get_form_error($field_name); ?>
    <label class="control-label" for="<?php echo $field_name;?>">VALOR NOTA FISCAL *</label>

</div>