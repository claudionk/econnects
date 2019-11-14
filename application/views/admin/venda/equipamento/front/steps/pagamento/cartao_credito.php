<?php
if($_POST){
    $row = $_POST;
}
?>

<?php //* ?>
<div class="row forma-pagamento" id="pagamento-credito">
    <?php if(isset($forma['pagamento'])) : ?>
    <input type="hidden" name="bandeira" value="<?php echo $forma['pagamento'][0]['produto_parceiro_pagamento_id']; ?>">
    <?php endif; ?>
    <div class="col-md-6">
        <div class="form-group select-forma-pagamento <?php echo (app_is_form_error('bandeira_cartao')) ? ' has-error' : ''; ?>" >
            <div class="row">
                <label for="forma_pagamento" class="control-label"> Bandeira </label>
                <div class="label">
                    <select class="form-control select" id="bandeira_cartao" name="bandeira_cartao">
                        <option value=""></option>
                        <?php
                        foreach($forma['bandeiras'] as $linha) { ?>
                            <option name="" value="<?php echo $linha['slug'] ?>"
                                <?php if(isset($row['bandeira_cartao'])){if($row['bandeira_cartao'] == $linha['slug']) {echo " selected ";};}; ?> >
                                <?php echo $linha['nome'] ?>
                            </option>
                        <?php }  ?>
                    </select>
                </div>
                <?php echo app_get_form_error('bandeira_cartao'); ?>
            </div>
        </div>

        <div class="form-group<?php echo (app_is_form_error('numero')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('nome_cartao')) ? ' has-error' : ''; ?>">
            <div class="col-md-6">
                <label class="control-label" for="numero">Número do cartão</label>
                <input class="form-control numeros_cartao" id="numero" name="numero" type="tel" value="<?php echo isset($row['numero']) ? $row['numero'] : set_value('numero'); ?>" />
                <?php echo app_get_form_error('numero'); ?>
            </div>
            <div class="col-md-6">
            <label class="control-label" for="nome_cartao"> Nome como no cartão </label>
                <input class="form-control" id="nome_cartao" name="nome_cartao" type="text" value="<?php echo isset($row['nome_cartao']) ? $row['nome_cartao'] : set_value('nome_cartao'); ?>" />
                <?php echo app_get_form_error('nome_cartao'); ?>
            </div>
        </div>

        <div class="row form-group<?php echo (app_is_form_error('validade')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('codigo')) ? ' has-error' : ''; ?>">
            <div class="col-xs-6">
                <label class="control-label" for="validade"> Validade </label>
                <input class="form-control validade_cartao" id="validade" name="validade" type="tel" value="<?php echo isset($row['validade']) ? $row['validade'] : set_value('validade'); ?>" />
                <?php echo app_get_form_error('validade'); ?>
            </div>
            <div class="col-xs-5">
                <label class="control-label" for="codigo"> CVV </label>
                <input class="form-control" maxlength='3' id="codigo" name="codigo" type="tel" value="<?php echo isset($row['codigo']) ? $row['codigo'] : set_value('codigo'); ?>" />
                <?php echo app_get_form_error('codigo'); ?>
            </div>
            <div class="col-xs-1">
                <a href="javascript:void(0)" class="tooltip-icon" data-toggle="tooltip" data-placement="left" 
                    title="C&oacute;digo de seguran&ccedil;a do cart&atilde;o">
                    <i class="fa fa-question-circle" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>

    

    <?php
    if(($produto_parceiro_configuracao['pagamento_tipo'] == 'RECORRENTE')  ) {
    ?>
    <div class="col-xs-12 select-forma-pagamento">
        <div class="form-group<?php echo (app_is_form_error('dia_vencimento')) ? ' has-error' : ''; ?>">
            <label for="forma_pagamento" class="control-label"> Dia do vencimento do Cart&atilde;o </label>
            <div class="label">
                <select class="form-control select" id="dia_vencimento" name="dia_vencimento">
                    <option value=""></option>
                    <?php
                    $dia_vencimento = isset( $row['dia_vencimento'] ) ? $row['dia_vencimento'] : set_value('dia_vencimento') ;
                    if( (int)$dia_vencimento > 1 && (int)$dia_vencimento < 31 ){ $dia_vencimento = date("d"); }
                    foreach(array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31) as $linha) {
                        printf( "<option value='%s'%s>%s</option>" , $linha , (int)$dia_vencimento == $linha ? "selected" : "" , $linha );
                    }
                    ?>
                </select>
            </div>
            <?php echo app_get_form_error('dia_vencimento'); ?>
        </div>
    </div>
    <?php
    }
    ?>

    <?php if( $produto_parceiro_configuracao['pagamento_tipo'] != 'RECORRENTE' ) { ?>
    <div class="col-xs-12 select-forma-pagamento">
        <div class="form-group">
            <!--
            <label for="forma_pagamento" class="control-label"> Parcelamento * </label>
            -->
            <?php $hd = "";  ?>
            <?php foreach ($forma['pagamento'] as $bandeira) : ?>
                <?php $field_name = "parcelamento_{$bandeira['produto_parceiro_pagamento_id']}";?>
                <div <?php echo $hd; ?> class="parcelamento parcelamento_<?php echo $bandeira['produto_parceiro_pagamento_id']; ?>">
                    <label class="control-label" for="<?php echo $field_name;?>">Parcelamento *</label>
                    <div class="label">
                        <select class="form-control select" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                            <?php foreach($bandeira['parcelamento'] as $parcela => $linha) : ?>
                                <option name="" value="<?php echo $parcela; ?>"
                                    <?php if(isset($row[$field_name])){ if($row[$field_name] == $parcela) { echo " selected "; } } ?> >
                                    <?php echo $linha["Descricao"]; ?>
                                </option>
                            <?php endforeach;  ?>
                        </select>
                    </div>
                </div>
                <?php $hd = 'style="display: none;"';  ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php } ?>

    <div class="col-xs-12">
        <div class="col-xs-11">
            <label>
                <input type="checkbox" checked="checked" /> Estou de acordo com os termos de uso.
            </label>
        </div>
        <div class="col-xs-1">
            <a href="javascript:void(0)" data-toggle="tooltip" class="tooltip-icon terms" data-placement="left" 
            title="Tooltip on left">
                <i class="fa fa-question-circle" aria-hidden="true"></i>
            </a>
        </div>
    </div>

</div>
<?php //*/ ?>