<?php
if($_POST)
    $row = $_POST;
?>
<div class="col-md-6 form-group<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
    <label class="col-md-3 control-label" for="<?php echo $field_name;?>">CEP *</label>
    <div class="col-md-9">
        <input data-plano="<?php echo $plano_id?>" class="busca-cep form-control inputmask-cep" placeholder="_____-___" id="<?php echo $field_name ?>_<?php echo $passageiro ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" data-passageiro="<?php echo $passageiro ?>" />
        <?php echo app_get_form_error($field_name); ?>
    </div>
</div>