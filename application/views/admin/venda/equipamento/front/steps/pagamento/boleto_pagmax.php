<div class="row forma-pagamento" id="pagamento-boleto">

    <div class="col-md-12">

        <div class="form-group">
            <div class="col-xs-12">
                <label for="sacado_documento" class="control-label">CPF</label>
                <input class="form-control inputmask-cpf" id="sacado_documento" name="sacado_documento" value="<?php echo isset($row['sacado_documento']) ? $row['sacado_documento'] : set_value('sacado_documento'); ?>" autofocus />
                <?php echo app_get_form_error('sacado_documento'); ?>
            </div>
            
            <div class="col-xs-12" style="display:none" id="div_nome">
                <label for="sacado_nome" class="control-label">Nome</label>
                <input class="form-control" id="sacado_nome" name="sacado_nome" value="<?php echo isset($row['sacado_nome']) ? $row['sacado_nome'] : set_value('sacado_nome'); ?>" />
                <?php echo app_get_form_error('sacado_nome'); ?>
            </div>
                
            <div class="col-xs-12" style="display:none" id="div_cep">              
                <label for="sacado_endereco_cep" class="control-label">CEP</label>
                <input class="form-control inputmask-cep" id="sacado_endereco_cep" name="sacado_endereco_cep" value="<?php echo isset($row['sacado_endereco_cep']) ? $row['sacado_endereco_cep'] : set_value('sacado_endereco_cep'); ?>" />
                <?php echo app_get_form_error('sacado_endereco_cep'); ?>
            </div>
            
            <div class="col-xs-12" style="display:none" id="div_endereco">
                <label for="sacado_endereco" class="control-label">Endereço</label>
                <input class="form-control" id="sacado_endereco" name="sacado_endereco" value="<?php echo isset($row['sacado_endereco']) ? $row['sacado_endereco'] : set_value('sacado_endereco'); ?>" />
                <?php echo app_get_form_error('sacado_endereco'); ?>
            </div>

            <div class="col-xs-12" style="display:none" id="div_num">
                <label for="sacado_endereco_num" class="control-label">Número</label>
                <input class="form-control" id="sacado_endereco_num" name="sacado_endereco_num" value="<?php echo isset($row['sacado_endereco_num']) ? $row['sacado_endereco_num'] : set_value('sacado_endereco_num'); ?>" />
                <?php echo app_get_form_error('sacado_endereco_num'); ?>
            </div>

            <div class="col-xs-12" style="display:none" id="div_compl">
                <label for="sacado_endereco_comp" class="control-label">Complemento</label>
                <input class="form-control" id="sacado_endereco_comp" name="sacado_endereco_comp" value="<?php echo isset($row['sacado_endereco_comp']) ? $row['sacado_endereco_comp'] : set_value('sacado_endereco_comp'); ?>" />
                <?php echo app_get_form_error('sacado_endereco_comp'); ?>
            </div>

            <div class="col-xs-12" style="display:none" id="div_bairro">
                <label for="sacado_endereco_bairro" class="control-label">Bairro</label>
                <input class="form-control" id="sacado_endereco_bairro" name="sacado_endereco_bairro" value="<?php echo isset($row['sacado_endereco_bairro']) ? $row['sacado_endereco_bairro'] : set_value('sacado_endereco_bairro'); ?>" />
                <?php echo app_get_form_error('sacado_endereco_bairro'); ?>
            </div>

            <div class="col-xs-12" style="display:none" id="div_cidade">
                <label for="sacado_endereco_cidade" class="control-label">Cidade</label>
                <input class="form-control" id="sacado_endereco_cidade" name="sacado_endereco_cidade" value="<?php echo isset($row['sacado_endereco_cidade']) ? $row['sacado_endereco_cidade'] : set_value('sacado_endereco_cidade'); ?>" />
                <?php echo app_get_form_error('sacado_endereco_cidade'); ?>
            </div>

            <div class="col-xs-12" style="display:none">
                <label for="sacado_endereco_uf" class="control-label" id="div_uf">Estado</label>
                <input class="form-control" id="sacado_endereco_uf" name="sacado_endereco_uf" value="<?php echo isset($row['sacado_endereco_uf']) ? $row['sacado_endereco_uf'] : set_value('sacado_endereco_uf'); ?>" />
                <?php echo app_get_form_error('sacado_endereco_uf'); ?>
            </div>

            <div class="col-xs-12">
                <br/>
            </div>

            <div class="col-xs-12 icon-login" id="div_float">
                <div class="col-xs-12 divBtnFloat">
                    <a  class="btn btn-primary btnCircular" id="float_btn" onclick="mostraInput('div_nome');">
                        <i class="fa fa-arrow-down"></i>
                    </a>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="card">
                    <div class="card-body no-padding">
                        <div class="alert alert-callout alert-danger no-margin">
                            <strong class="text-xl">R$ <?php echo $carrossel['valor_total'] ?></strong><br>
                            <span class="text-danger">Vencimento <?php  echo app_add_dias_uteis(date('Y-m-d'), issetor($forma['pagamento'][0]['boleto_vencimento'], 3))?></span>
                            <div class="stick-bottom-left-right">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


