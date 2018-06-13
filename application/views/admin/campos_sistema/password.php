<?php
if($_POST)
    $row = $_POST;

    $tamanho = ($tamanho/2);
?>

<div class="col-md-<?php echo $tamanho ?> <?php echo (app_is_form_error($field_name)) ? 'has-error' : ''; ?>">
    <div class="form-group">
        <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
        <input class="form-control <?php echo issetor($class) ?>" placeholder="<?php echo $field_label ?>" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="password" value="<?php echo  isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" />
        <?php echo app_get_form_error($field_name); ?>
    </div>
</div>

<div class="col-md-<?php echo $tamanho ?> <?php echo (app_is_form_error("{$field_name}_confirm")) ? 'has-error' : ''; ?>">
    <div class="form-group">
        <label class="control-label" for="<?php echo "{$field_name}_confirm";?>">Repita a senha</label>
        <input class="form-control <?php echo issetor($class) ?>" placeholder="Repita a senha" id="<?php echo $field_name ?>_confirm" name="<?php echo $field_name ?>_confirm" type="password" value="" />
        <?php echo app_get_form_error("{$field_name}_confirm"); ?>
    </div>
</div>