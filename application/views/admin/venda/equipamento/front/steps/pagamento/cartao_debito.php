<?php
if ($_POST) {
    $row = $_POST;
}

//echo 'Débito!';
?>
<div class="row forma-pagamento" id="pagamento-debito">
    <?php if (isset($forma['pagamento'])) : ?>
        <input type="hidden" name="bandeira_debito" value="<?php echo $forma['pagamento'][0]['produto_parceiro_pagamento_id']; ?>">
    <?php endif; ?>
    <div class="col-md-6">
        <div class="col-xs-12 select-forma-pagamento">
            <div class="row">
                <div class="form-group">
                    <label for="forma_pagamento" class="control-label"> Bandeira </label>
                    <div class="label">
                        <select class="form-control select" id="bandeira_cartao_debito" name="bandeira_cartao_debito">
                            <option value=""></option>
                            <?php foreach ($forma['bandeiras'] as $linha) { ?>
                                <option name="" value="<?php echo $linha['slug'] ?>" <?php if (isset($row['bandeira_cartao_debito'])) {
                                                                                            if ($row['bandeira_cartao_debito'] == $linha['slug']) {
                                                                                                echo " selected ";
                                                                                            };
                                                                                        }; ?>>
                                    <?php echo $linha['nome'] ?>
                                </option>
                            <?php } ?>
                        </select>
                        <?php echo app_get_form_error('bandeira_cartao_debito'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group<?php echo (app_is_form_error('numero_debito')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('numero_debito')) ? ' has-error' : ''; ?>">

            <div class="col-md-6">
                <label for="numero_debito" class="control-label">Número do Cartão</label>
                <input class="form-control numeros_cartao" id="numero_debito" name="numero_debito" type="text" value="<?php echo isset($row['numero_debito']) ? $row['numero_debito'] : set_value('numero_debito'); ?>" />
                <?php echo app_get_form_error('numero_debito'); ?>
            </div>

            <div class="col-md-6">
                <label for="nome_cartao_debito" class="control-label">Nome como no cartão</label>
                <input class="form-control" id="nome_cartao_debito" name="nome_cartao_debito" type="text" value="<?php echo isset($row['nome_cartao_debito']) ? $row['nome_cartao_debito'] : set_value('nome_cartao_debito'); ?>" />
                <?php echo app_get_form_error('nome_cartao_debito'); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-6">
                <label for="validade_debito" class="control-label"> Validade </label>
                <input class="form-control validade_cartao" id="validade_debito" name="validade_debito" type="text" value="<?php echo isset($row['validade_debito']) ? $row['validade_debito'] : set_value('validade_debito'); ?>" />
                <?php echo app_get_form_error('validade_debito'); ?>
            </div>
            <div class="col-xs-5">
                <label for="codigo_debito" class="control-label"> CVV </label>
                <input class="form-control" id="codigo_debito" name="codigo_debito" type="text" maxlength='3' value="<?php echo isset($row['codigo_debito']) ? $row['codigo_debito'] : set_value('codigo_debito'); ?>" />
                <?php echo app_get_form_error('codigo_debito'); ?>
            </div>
            <div class="col-xs-1">
                <a href="javascript:void(0)" class="tooltip-icon" data-toggle="tooltip" data-placement="left" title="C&oacute;digo de seguran&ccedil;a do cart&atilde;o">
                    <i class="fa fa-question-circle" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>



    <div class="col-xs-12">
        <div class="form-group">
            <?php if ($produto_parceiro_configuracao['pagamento_tipo'] == 'RECORRENTE') { ?>
                <div class="col-xs-12">
                    Esse produto é um pagamento recorrente, no dia do vencimento você receberá uma notificação para efetuar o pagamento.
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="col-xs-12" style="margin-bottom: 20px; margin-top: 10px; z-index: 999;display: none;">
        <div class="col-xs-11">
            <label>
                <input type="checkbox" checked="checked" /> Estou de acordo com os termos de uso.
            </label>
        </div>
        <div class="col-xs-1">
            <a href="javascript:void(0)" data-toggle="tooltip" class="tooltip-icon terms" data-placement="left" title="Tooltip on left">
                <i class="fa fa-question-circle" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <div class="col-md-12">
                <div class="card-wrapper-debito"></div>
            </div>
        </div>
    </div>
</div>