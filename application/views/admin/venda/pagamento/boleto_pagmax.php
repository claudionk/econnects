<div class="row forma-pagamento" id="pagamento-boleto">

    <div class="col-md-12">
        <div class="col-md-4">
            <h5>CPF</h5>
            <input class="form-control inputmask-cpf" placeholder="CPF" id="sacado_documento" name="sacado_documento" value="<?php echo isset($row['sacado_documento']) ? $row['sacado_documento'] : set_value('sacado_documento'); ?>" />
            <?php echo app_get_form_error('sacado_documento'); ?>
        </div>
        <div class="col-md-4">
            <h5>Nome</h5>
            <input class="form-control" placeholder="Nome Completo" id="sacado_nome" name="sacado_nome" value="<?php echo isset($row['sacado_nome']) ? $row['sacado_nome'] : set_value('sacado_nome'); ?>" />
            <?php echo app_get_form_error('sacado_nome'); ?>
        </div>
        <div class="col-md-4">
            <h5>CEP</h5>
            <input class="form-control inputmask-cep" placeholder="CEP" id="sacado_endereco_cep" name="sacado_endereco_cep" value="<?php echo isset($row['sacado_endereco_cep']) ? $row['sacado_endereco_cep'] : set_value('sacado_endereco_cep'); ?>" />
            <?php echo app_get_form_error('sacado_endereco_cep'); ?>
        </div>
        <div class="col-md-6">
            <h5>Endereço</h5>
            <input class="form-control" placeholder="Endereço" id="sacado_endereco" name="sacado_endereco" value="<?php echo isset($row['sacado_endereco']) ? $row['sacado_endereco'] : set_value('sacado_endereco'); ?>" />
            <?php echo app_get_form_error('sacado_endereco'); ?>
        </div>
        <div class="col-md-2">
            <h5>Número</h5>
            <input class="form-control" placeholder="Número" id="sacado_endereco_num" name="sacado_endereco_num" value="<?php echo isset($row['sacado_endereco_num']) ? $row['sacado_endereco_num'] : set_value('sacado_endereco_num'); ?>" />
            <?php echo app_get_form_error('sacado_endereco_num'); ?>
        </div>
        <div class="col-md-4">
            <h5>Complemento</h5>
            <input class="form-control" placeholder="Complemento" id="sacado_endereco_comp" name="sacado_endereco_comp" value="<?php echo isset($row['sacado_endereco_comp']) ? $row['sacado_endereco_comp'] : set_value('sacado_endereco_comp'); ?>" />
            <?php echo app_get_form_error('sacado_endereco_comp'); ?>
        </div>
        <div class="col-md-5">
            <h5>Bairro</h5>
            <input class="form-control" placeholder="Bairro" id="sacado_endereco_bairro" name="sacado_endereco_bairro" value="<?php echo isset($row['sacado_endereco_bairro']) ? $row['sacado_endereco_bairro'] : set_value('sacado_endereco_bairro'); ?>" />
            <?php echo app_get_form_error('sacado_endereco_bairro'); ?>
        </div>
        <div class="col-md-5">
            <h5>Cidade</h5>
            <input class="form-control" placeholder="Cidade" id="sacado_endereco_cidade" name="sacado_endereco_cidade" value="<?php echo isset($row['sacado_endereco_cidade']) ? $row['sacado_endereco_cidade'] : set_value('sacado_endereco_cidade'); ?>" />
            <?php echo app_get_form_error('sacado_endereco_cidade'); ?>
        </div>
        <div class="col-md-2">
            <h5>Estado</h5>
            <input class="form-control" placeholder="Estado" id="sacado_endereco_uf" name="sacado_endereco_uf" value="<?php echo isset($row['sacado_endereco_uf']) ? $row['sacado_endereco_uf'] : set_value('sacado_endereco_uf'); ?>" />
            <?php echo app_get_form_error('sacado_endereco_uf'); ?>
        </div>
        <div class="col-md-12">
            <br/>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body no-padding">
                    <div class="alert alert-callout alert-danger no-margin">
                        <strong class="text-xl">R$ <?php echo $carrossel['valor_total'] ?></strong><br>
                        <span class="text-danger">Vencimento <?php  echo app_add_dias_uteis(date('Y-m-d'), issetor($forma['pagamento'][0]['boleto_vencimento'], 3))?></span>
                        <div class="stick-bottom-left-right">
                        </div>
                    </div>
                </div><!--end .card-body -->
            </div><!--end .card -->
        </div>
    </div>

</div>