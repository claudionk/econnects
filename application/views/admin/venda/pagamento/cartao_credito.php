<?php
if($_POST){
    $row = $_POST;
}

?>
<div class="pg-pagamento-credito" id="pagamento-credito">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <i class="fa fa-lock" aria-hidden="true"></i>
    </div>

    <?php if(isset($forma['pagamento'])) : ?>
    <input type="hidden" name="bandeira" value="<?php echo $forma['pagamento'][0]['produto_parceiro_pagamento_id']; ?>">
    <?php endif; ?>

    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="form-group<?php echo (app_is_form_error('numero')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('nome_cartao')) ? ' has-error' : ''; ?>">
            <label class="control-label" for="numero">Número do Cartão</label>
            <input class="form-control" id="numero" name="numero" type="tel" value="<?php echo isset($row['numero']) ? $row['numero'] : set_value('numero'); ?>" />
            <?php echo app_get_form_error('numero'); ?>
        </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="form-group<?php echo (app_is_form_error('numero')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('nome_cartao')) ? ' has-error' : ''; ?>">
            <label class="control-label" for="nome_cartao">Nome (Como no cartão)</label>
            <input class="form-control" id="nome_cartao" name="nome_cartao" type="text" value="<?php echo isset($row['nome_cartao']) ? $row['nome_cartao'] : set_value('nome_cartao'); ?>" />
            <?php echo app_get_form_error('nome_cartao'); ?>
        </div>
    </div>
</div>

<?php /*
<div class="row" id="pagamento-credito">
    <?php if(isset($forma['pagamento'])) : ?>
    <input type="hidden" name="bandeira" value="<?php echo $forma['pagamento'][0]['produto_parceiro_pagamento_id']; ?>">
    <?php endif; ?>
    <div class="col-md-6">

        <div class="form-group<?php echo (app_is_form_error('numero')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('nome_cartao')) ? ' has-error' : ''; ?>">
            <div class="col-md-6">
                <label class="control-label" for="numero">Número do Cartão</label>
                <input class="form-control" placeholder="Número do Cartão" id="numero" name="numero" type="tel" value="<?php echo isset($row['numero']) ? $row['numero'] : set_value('numero'); ?>" />
                <?php echo app_get_form_error('numero'); ?>
            </div>
            <div class="col-md-6">
                <h5>Nome (Como no cartão)</h5>
                <input placeholder="Nome (Como no cartão)" class="form-control" id="nome_cartao" name="nome_cartao" type="text" value="<?php echo isset($row['nome_cartao']) ? $row['nome_cartao'] : set_value('nome_cartao'); ?>" />
                <?php echo app_get_form_error('nome_cartao'); ?>
            </div>
        </div>

        <div class="form-group<?php echo (app_is_form_error('validade')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('codigo')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('bandeira_cartao')) ? ' has-error' : ''; ?>">
            <div class="col-md-4">
                <h5>Bandeira</h5>
                <select class="form-control" name="bandeira_cartao" id="bandeira_cartao">
                    <option name="" value="">Selecione</option>
                    <?php

                    foreach($forma['bandeiras'] as $linha) { ?>
                        <option name="" value="<?php echo $linha['slug'] ?>"
                            <?php if(isset($row['bandeira_cartao'])){if($row['bandeira_cartao'] == $linha['slug']) {echo " selected ";};}; ?> >
                            <?php echo $linha['nome'] ?>
                        </option>
                    <?php }  ?>
                </select>
                <?php echo app_get_form_error('bandeira_cartao'); ?>
            </div>
            <div class="col-md-4">
                <h5>Validade <small>(MM/AAAA)</small></h5>
                <input class="form-control" placeholder="Validade (MM/AAAA)" id="validade" name="validade" type="tel" value="<?php echo isset($row['validade']) ? $row['validade'] : set_value('validade'); ?>" />
                <?php echo app_get_form_error('validade'); ?>
            </div>
            <div class="col-md-4">
                <h5>Código</h5>
                <input placeholder="Código" class="form-control" id="codigo" name="codigo" type="tel" value="<?php echo isset($row['codigo']) ? $row['codigo'] : set_value('codigo'); ?>" />
                <?php echo app_get_form_error('codigo'); ?>
            </div>
        </div>

        <?php
        if(($produto_parceiro_configuracao['pagamento_tipo'] == 'RECORRENTE')  ) {
        ?>
            <div class="form-group<?php echo (app_is_form_error('dia_vencimento')) ? ' has-error' : ''; ?>">
                <div class="col-md-6">
                    <h5>Dia do vencimento do Cart&atilde;o</h5>
                    <select class="form-control" name="dia_vencimento" id="dia_vencimento">
                        <?php
                        $dia_vencimento = isset( $row['dia_vencimento'] ) ? $row['dia_vencimento'] : set_value('dia_vencimento') ;
                        if( (int)$dia_vencimento > 1 && (int)$dia_vencimento < 31 ){ $dia_vencimento = date("d"); }
                        foreach(array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31) as $linha) {
                            printf( "<option value='%s'%s>%s</option>" , $linha , (int)$dia_vencimento == $linha ? "selected" : "" , $linha );
                        }
                        ?>
                    </select>
                    <?php echo app_get_form_error('dia_vencimento'); ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <div class="col-md-12">
                <div class="card-wrapper"></div>
            </div>
        </div>
        <?php if( $produto_parceiro_configuracao['pagamento_tipo'] != 'RECORRENTE' ) { ?>
        <div class="col-md-12">
            <?php $hd = "";  ?>
            <?php foreach ($forma['pagamento'] as $bandeira) : ?>
                <?php $field_name = "parcelamento_{$bandeira['produto_parceiro_pagamento_id']}";?>
                <div <?php echo $hd; ?> class="form-group parcelamento parcelamento_<?php echo $bandeira['produto_parceiro_pagamento_id']; ?>">
                    <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Parcelamento *</label>
                    <div class="col-md-10">
                        <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                            <?php foreach($bandeira['parcelamento'] as $parcela => $linha) : ?>
                                <option name="" value="<?php echo $parcela; ?>"
                                    <?php if(isset($row[$field_name])){if($row[$field_name] == $parcela) {echo " selected ";};}; ?> >
                                    <?php echo $linha["Descricao"]; ?>
                                </option>
                            <?php endforeach;  ?>
                        </select>
                    </div>
                </div>
                <?php $hd = 'style="display: none;"';  ?>
            <?php endforeach; ?>
        </div>
        <?php
        }
        ?>
    </div>

</div>
*/ ?>