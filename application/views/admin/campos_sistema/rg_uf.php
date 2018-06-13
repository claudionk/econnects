<?php
if($_POST)
    $row = $_POST;

?>
<div class="form-group col-md-<?php echo $tamanho ?> <?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
        <label class=" control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
        <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
            <option name="" value="">Selecione</option>
            <?php

            if(isset($list['rg_uf'])){
                foreach($list['rg_uf'] as $linha) { ?>
                    <option value="<?php echo $linha['sigla'] ?>"
                        <?php if(isset($row[$field_name])){if($row[$field_name] == $linha['sigla']) {echo " selected ";};}; ?> >
                        <?php echo $linha['sigla'] ?>
                    </option>
                <?php }  ?>
            <?php }  ?>
        </select>
        <?php echo app_get_form_error($field_name); ?>
</div>