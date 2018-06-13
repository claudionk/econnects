<?php
if($_POST)
    $row = $_POST;
?>
<div class="col-md-6 form-group<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
    <label class="col-md-3 control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
    <div class="col-md-9">
        <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
            <option name="" value="">Selecione</option>

            <option name="" value="M"
                <?php if(isset($row[$field_name])){if($row[$field_name] == 'M') {echo " selected ";};}; ?> >
                Masculino
            </option>
            <option name="" value="F"
                <?php if(isset($row[$field_name])){if($row[$field_name] == 'F') {echo " selected ";};}; ?> >
                Feminino
            </option>
        </select>

        <?php echo app_get_form_error($field_name); ?>
    </div>
</div>