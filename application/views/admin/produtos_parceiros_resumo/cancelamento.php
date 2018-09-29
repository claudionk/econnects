<?php if($row['cancelamento_config']) : ?>
        <h4>Antes do início da vigência</h4>
        <hr>
        <br>

        <?php if($row['cancelamento']['seg_antes_hab'] == 1) :?>
        <?php $field_name = 'seg_antes_dias';?>
        <div class="form-group antes_habilitado">
            <label class="col-md-2 control-label" >Quantidade máxima de dias</label>
            <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento'][$field_name]) ? $row['cancelamento'][$field_name] : '0'; ?>" /></div>
        </div>
        <?php $field_name = 'seg_antes_calculo';?>
        <div class="form-group antes_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Forma de cálculo da Penalidade *</label>
            <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento'][$field_name]) ? $row['cancelamento'][$field_name] : '0'; ?>" /></div>
        </div>
        <?php $field_name = 'seg_antes_valor';?>
        <div class="form-group antes_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Valor</label>
            <div class="col-md-2"><input ng-disabled ng-model="seg_antes_valor" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/></div>
        </div>
        <?php else : ?>

        <strong>Não Habilitado</strong>

        <?php endif; ?>

        <br>
        <br>
        <h4>Depois do início da vigência</h4>
        <hr>
        <br>
        <?php if($row['cancelamento']['seg_depois_hab'] == 1) :?>

        <?php $field_name = 'calculo_tipo';
            $tipo = array('T' => 'TABELA PRAZO CURTO', 'P' => 'PRO-RATA', 'E' => 'ESPECIAL');
            $tipo_calculo = (isset($row['cancelamento']['calculo_tipo'])) ? $tipo[$row['cancelamento']['calculo_tipo']] : 'Não configurado';
        ?>
        <div class="form-group">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Cálculo *</label>
            <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo $tipo_calculo; ?>" /></div>
        </div>

        <div class="row cancelamento_especial">
            <?php $field_name = 'seg_depois_dias';?>
            <div class="form-group">
                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Quantidade máxima de dias</label>
                <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento'][$field_name]) ? $row['cancelamento'][$field_name] : '0'; ?>" /></div>
            </div>
            <?php $field_name = 'seg_depois_dias_carencia';?>
            <div class="form-group">
                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Carência de dias p/ utilização do cálculo</label>
                <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento'][$field_name]) ? $row['cancelamento'][$field_name] : '0'; ?>" /></div>
            </div>
            <?php $field_name = 'seg_depois_calculo';?>
            <div class="form-group">
                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Forma de cálculo da Penalidade *</label>
                <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento'][$field_name]) ? $row['cancelamento'][$field_name] : '0'; ?>" /></div>
            </div>
            <?php $field_name = 'seg_depois_valor';?>
            <div class="form-group">
                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Valor</label>
                <div class="col-md-2">
                  <input ng-model="seg_depois_valor" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
              </div>
            </div>
        </div>
        <?php else : ?>

            <strong>Não Habilitado</strong>

        <?php endif; ?>
        <br>
        <br>
        <h4>Cancelamento por Inadimplência</h4>
        <hr>
        <br>
        <?php if($row['cancelamento']['inad_hab'] == 1) :?>
        <?php $field_name = 'inad_max_dias';?>
        <div class="form-group inadimplencia_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Quantidade máxima de dias</label>
            <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento'][$field_name]) ? $row['cancelamento'][$field_name] : '0'; ?>" /></div>
        </div>
        <?php $field_name = 'inad_max_parcela';?>
        <div class="form-group inadimplencia_habilitado">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Quantidade máxima de parcelas</label>
            <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento'][$field_name]) ? $row['cancelamento'][$field_name] : '0'; ?>" /></div>
        </div>
        <?php else : ?>

            <strong>Não Habilitado</strong>

        <?php endif; ?>
        <br>
        <br>
        <h4>Reativação por inadimplência</h4>
        <hr>
        <br>

        <?php if($row['cancelamento']['inad_reativacao_hab'] == 1) :?>
        <?php $field_name = 'inad_reativacao_max_dias';?>
        <div class="form-group inadimplencia_reativacao">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Máximo de dias cancelado</label>
            <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento'][$field_name]) ? $row['cancelamento'][$field_name] : '0'; ?>" /></div>
        </div>

        <?php $field_name = 'inad_reativacao_max_parcela';?>
        <div class="form-group inadimplencia_reativacao">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Máximo de parcelas</label>
            <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento'][$field_name]) ? $row['cancelamento'][$field_name] : '0'; ?>" /></div>
        </div>



        <?php $field_name = 'inad_reativacao_calculo';?>
        <div class="form-group inadimplencia_reativacao">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Forma de cálculo da Penalidade *</label>
            <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row['cancelamento']['cancelamento'][$field_name]) ? $row['cancelamento']['cancelamento'][$field_name] : '0'; ?>" /></div>
        </div>
        <?php $field_name = 'inad_reativacao_valor';?>
        <div class="form-group inadimplencia_reativacao">
            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Valor</label>
            <div class="col-md-2"><input ng-model="inad_reativacao_valor" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/></div>
        </div>
        <?php else : ?>

            <strong>Não Habilitado</strong>

        <?php endif; ?>

        <br>
        <br>
        <h4>Indenização</h4>
        <hr>
        <br>


        <?php $field_name = 'indenizacao_hab';?>
        <?php if($row['cancelamento']['indenizacao_hab'] == 1) :?>
            <strong>Habilitado</strong>
        <?php else : ?>

            <strong>Não Habilitado</strong>

        <?php endif; ?>

<?php else : ?>

    <strong>Não Configurado</strong>
<?php endif;  ?>
