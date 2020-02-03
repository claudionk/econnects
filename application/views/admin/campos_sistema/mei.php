<?php
if($_POST)
    $row = $_POST;
?>

<div class="col-md-<?php echo $tamanho ?> <?php echo (app_is_form_error($field_name)) ? 'has-error' : ''; ?>">
    <div class="form-group">
        <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>

        <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
            <option name="" value="">Selecione</option>

            <option name="" value="1"
                <?php if(isset($row[$field_name])){if($row[$field_name] == '1') {echo " selected ";};}; ?> >
                Sim
            </option>
            <option name="" value="0"
                <?php if(isset($row[$field_name])){if($row[$field_name] == '0') {echo " selected ";};}; ?> >
                Não
            </option>
        </select>
        <?php echo app_get_form_error($field_name); ?>
    </div>
</div>