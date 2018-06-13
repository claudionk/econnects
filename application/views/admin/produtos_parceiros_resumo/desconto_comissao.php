<?php if($row['desconto_config']) : ?>
    <?php if($row['desconto']['habilitado'] == '1'):?>
        <?php $field_name = 'valor';?>
        <div class="form-group desconto_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Valor Total</label>
            <div class="col-md-2"><input readonly class="form-control inputmask-valor" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['desconto'][$field_name]) ? $row['desconto'][$field_name] : set_value($field_name); ?>" /></div>
        </div>
        <?php $field_name = 'utilizado';?>
        <div class="form-group desconto_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Utlizado</label>
            <div class="col-md-2"><input readonly class="form-control inputmask-valor" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['desconto'][$field_name]) ? $row['desconto'][$field_name] : set_value($field_name); ?>" /></div>
        </div>
        <?php $field_name = 'saldo';?>
        <div class="form-group desconto_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Saldo</label>
            <div class="col-md-2"><input readonly class="form-control inputmask-valor" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo (isset($row['desconto']['valor']) && isset($row['desconto']['utilizado'])) ? ($row['desconto']['valor'] - $row['desconto']['utilizado']) : '000,00'; ?>" /></div>
        </div>
        <?php $field_name = 'data_ini';?>
        <div class="form-group desconto_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Data início</label>
            <div class="col-md-2"><input readonly placeholder="__/__/____" class="form-control inputmask-date" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['desconto'][$field_name]) ? app_date_mysql_to_mask($row['desconto'][$field_name]) : set_value($field_name); ?>" /></div>
        </div>
        <?php $field_name = 'data_fim';?>
        <div class="form-group desconto_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Data Final</label>
            <div class="col-md-2"><input readonly placeholder="__/__/____" class="form-control inputmask-date" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['desconto'][$field_name]) ? app_date_mysql_to_mask($row['desconto'][$field_name]) : set_value($field_name); ?>" /></div>
        </div>
        <?php $field_name = 'descricao';?>
        <div class="form-group desconto_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Descrição</label>
            <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['desconto'][$field_name]) ? $row['desconto'][$field_name] : set_value($field_name); ?>" /></div>
        </div>

    <?php else : ?>
        <strong>Não habilitado</strong>

    <?php endif; ?>
<?php else : ?>

    <strong>Não Configurado</strong>
<?php endif;  ?>