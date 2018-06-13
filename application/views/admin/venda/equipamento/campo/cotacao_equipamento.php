<?php
if($_POST)
    $row = $_POST;

?>
<div class="col-md-12 <?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
    <div class="form-group">
        <select name="<?php echo $field_name;?>" data-selected="<?php echo isset($row[$field_name]) ?  $row[$field_name] : ''; ?>" id="js-<?php echo $field_name;?>-ajax" class="js-<?php echo $field_name;?>-ajax" style="width: 100%;">
            <option value="" selected="selected">Digite para buscar um equipamento</option>
        </select>
        <?php echo app_get_form_error($field_name); ?>
        <label class=" control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
    </div>
</div>
