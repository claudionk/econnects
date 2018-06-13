<?php if($row['comissao_config']) : ?>
    <?php $field_name = 'markup';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Markup(%) *</label>
        <div class="col-md-2"><input readonly class="form-control inputmask-moeda" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
    </div>
    <?php $field_name = 'comissao';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão(%) *</label>
        <div class="col-md-2"><input readonly class="form-control inputmask-moeda" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
    </div>
    <?php $field_name = 'comissao_indicacao';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão Indicação(%) *</label>
        <div class="col-md-2"><input readonly class="form-control inputmask-moeda" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
    </div>

    <?php $field_name = 'repasse_comissao';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Repasse comissão *</label>
        <div class="col-md-2">

            <?php if (isset($row[$field_name]) && $row[$field_name] == '1'){
                $value_repasse = 'Sim ';
                $value_repasse .= isset($row['repasse_maximo']) ? app_format_currency($row['repasse_maximo'], false, 3) : '00,000';
            }else{
                $value_repasse = 'Não';
            }
            ?>
            <input readonly class="form-control" type="text" value="<?php echo $value_repasse; ?>%">
        </div>
    </div>

    <br>


    <?php $field_name = 'padrao_comissao';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão (Padrão) (%) *</label>
        <div class="col-md-2"><input readonly class="form-control inputmask-moeda" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
    </div>

    <?php $field_name = 'padrao_comissao_indicacao';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão Indicação (Padrão) (%) *</label>
        <div class="col-md-2"><input readonly class="form-control inputmask-moeda" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
    </div>

    <?php $field_name = 'padrao_repasse_comissao';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Repasse comissão (Padrão) </label>
        <div class="col-md-2">

            <?php if (isset($row[$field_name]) && $row[$field_name] == '1'){
                $value_repasse = 'Sim ';
                $value_repasse .= isset($row['padrao_repasse_maximo']) ? app_format_currency($row['padrao_repasse_maximo'], false, 3) : '00,000';
            }else{
                $value_repasse = 'Não';
            }
            ?>
            <input readonly class="form-control" type="text" value="<?php echo $value_repasse; ?>%">
        </div>
    </div>
<?php else : ?>

    <strong>Não Configurado</strong>
<?php endif;  ?>