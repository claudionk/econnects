<?php
    if($_POST){
        $row = $_POST;
    }
?>
<div class="row" id="pagamento-debito">
    <?php if(isset($forma['pagamento'])) : ?>
        <input type="hidden" name="bandeira_debito" value="<?php echo $forma['pagamento'][0]['produto_parceiro_pagamento_id']; ?>">
    <?php endif; ?>
    <div class="col-md-6">

        <div class="form-group<?php echo (app_is_form_error('numero_debito')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('numero_debito')) ? ' has-error' : ''; ?>">

            <div class="col-md-6">
                <h5>Número do Cartão</h5>
                <input class="form-control" placeholder="Número do Cartão" id="numero_debito" name="numero_debito" type="text" value="<?php echo isset($row['numero_debito']) ? $row['numero_debito'] : set_value('numero_debito'); ?>" />
                <?php echo app_get_form_error('numero_debito'); ?>
            </div>

            <div class="col-md-6">
                <h5>Nome (Como no cartão)</h5>
                <input placeholder="Nome (Como no cartão)" class="form-control" id="nome_cartao_debito" name="nome_cartao_debito" type="text" value="<?php echo isset($row['nome_cartao_debito']) ? $row['nome_cartao_debito'] : set_value('nome_cartao_debito'); ?>" />
                <?php echo app_get_form_error('nome_cartao_debito'); ?>
            </div>
        </div>

        <div class="form-group<?php echo (app_is_form_error('validade_debito')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('codigo_debito')) ? ' has-error' : ''; ?>">
            <div class="col-md-4">
                <h5>Bandeira</h5>
                <select class="form-control" name="bandeira_cartao_debito" id="bandeira_cartao_debito">
                    <option name="" value="">Selecione</option>
                    <?php
                    foreach($forma['bandeiras'] as $linha) { ?>
                        <option name="" value="<?php echo $linha['slug'] ?>"
                            <?php if(isset($row['bandeira_cartao_debito'])){if($row['bandeira_cartao_debito'] == $linha['slug']) {echo " selected ";};}; ?> >
                            <?php echo $linha['nome'] ?>
                        </option>
                    <?php }  ?>
                </select>
                <?php echo app_get_form_error('bandeira_cartao_debito'); ?>
            </div>

            <div class="col-md-4">
                <h5>Validade (MM/AAAA)</h5>
                <input class="form-control" placeholder="Validade (MM/AAAA)" id="validade_debito" name="validade_debito" type="text" value="<?php echo isset($row['validade_debito']) ? $row['validade_debito'] : set_value('validade_debito'); ?>" />
                <?php echo app_get_form_error('validade_debito'); ?>
            </div>
            <div class="col-md-4">
                <h5>Código</h5>
                <input placeholder="Código" class="form-control" id="codigo_debito" name="codigo_debito" type="text" value="<?php echo isset($row['codigo_debito']) ? $row['codigo_debito'] : set_value('codigo_debito'); ?>" />
                <?php echo app_get_form_error('codigo_debito'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php if($produto_parceiro_configuracao['pagamento_tipo'] == 'RECORRENTE') { ?>
                <div class="col-md-6">
                    Esse produto é um pagamento recorrente, no dia do vencimento você receberá uma notificação para efetuar o pagamento.
                </div>
            <?php } ?>
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