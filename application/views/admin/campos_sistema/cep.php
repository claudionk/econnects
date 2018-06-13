<?php
if($_POST)
    $row = $_POST;
?>

<div class="col-md-<?php echo $tamanho ?> <?php echo (app_is_form_error($field_name)) ? 'has-error' : ''; ?>">
    <div class="form-group">
        <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
        <input data-plano="<?php echo $plano_id?>" class="busca-cep inputmask-cep form-control " placeholder="_____-___" id="<?php echo $field_name ?>_<?php echo $passageiro ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" data-passageiro="<?php echo $passageiro ?>" />
        <?php echo app_get_form_error($field_name); ?>
    </div>
</div>