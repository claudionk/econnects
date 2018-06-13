
<!-- Trigger the modal with a button -->
<button type="button" class="btn btn-info btn-sm pull-right" data-toggle="modal" data-target="#modal_acesso_externo"><i class="fa fa-plus"></i> ADICIONAR CARTÃO DE CRÉDITO</button>

<!-- Modal -->
<div id="modal_acesso_externo" class="modal fade" role="dialog">
    <div class="modal-dialog">


        <form method="POST" action="<?php echo admin_url("pedido/inserir_cartao/{$pedido['pedido_id']}") ?>">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Trocar cartão de Crédito</h4>
            </div>
            <div class="modal-body">


                <div class="row">
                    <div class="col-md-12">


                        <input name="bandeira" type="hidden" value="<?php echo isset($pedido_cartao['bandeira']) ? $pedido_cartao['bandeira'] : set_value('bandeira'); ?>" />
                        <input name="pedido_cartao_id" type="hidden" value="<?php echo isset($pedido_cartao['pedido_cartao_id']) ? $pedido_cartao['pedido_cartao_id'] : set_value('pedido_cartao_id'); ?>" />


                        <div class="form-group">

                            <div class="col-md-12">
                                <h5>Bandeira</h5>
                                <select required="required" class="form-control" name="bandeira_cartao" id="bandeira_cartao">
                                    <option name="" value="">Selecione</option>
                                    <?php foreach($bandeiras as $linha) { ?>
                                        <option name="" value="<?php echo $linha['slug'] ?>"
                                            <?php if(isset($row['bandeira_cartao'])){if($row['bandeira_cartao'] == $linha['slug']) {echo " selected ";};}; ?> >
                                            <?php echo $linha['nome'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <?php echo app_get_form_error('bandeira_cartao'); ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo (app_is_form_error('numero')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('nome')) ? ' has-error' : ''; ?>">

                            <div class="col-md-6">
                                <h5>Número do Cartão</h5>
                                <input class="form-control" maxlength="16" minlength="16" required="required" placeholder="Número do Cartão" id="numero" name="numero" type="text" value="<?php echo isset($pedido_cartao['numero']) ? $pedido_cartao['numero'] : set_value('numero'); ?>" />
                                <?php echo app_get_form_error('numero'); ?>
                            </div>

                            <div class="col-md-6">
                                <h5>Nome (Como no cartão)</h5>
                                <input placeholder="Nome (Como no cartão)" required="required" class="form-control" id="nome" name="nome" type="text" value="<?php echo isset($pedido_cartao['nome']) ? $pedido_cartao['nome'] : set_value('nome'); ?>" />
                                <?php echo app_get_form_error('nome_cartao'); ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo (app_is_form_error('validade')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('codigo')) ? ' has-error' : ''; ?>">
                            <div class="col-md-6">
                                <h5>Validade (MM/AAAA)</h5>
                                <input class="form-control" required="required" placeholder="Validade (MM/AAAA)" id="validade" name="validade" type="text" value="<?php echo isset($pedido_cartao['validade']) ? $pedido_cartao['validade'] : set_value('validade'); ?>" />
                                <?php echo app_get_form_error('validade'); ?>
                            </div>
                            <div class="col-md-6">
                                <h5>Código</h5>
                                <input placeholder="Código" required="required" class="form-control" id="codigo" name="codigo" type="text" value="" />
                                <?php echo app_get_form_error('codigo'); ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo (app_is_form_error('dia_vencimento')) ? ' has-error' : ''; ?>">
                            <?php if($produto_parceiro_configuracao['pagamento_tipo'] == 'RECORRENTE') { ?>
                                <div class="col-md-6">
                                    <h5>Dia do vencimento</h5>
                                    <input required="required" placeholder="Dia do vencimento" class="form-control" id="dia_vencimento" name="dia_vencimento" type="number" value="<?php echo isset($pedido_cartao['dia_vencimento']) ? $pedido_cartao['dia_vencimento'] : set_value('dia_vencimento'); ?>" />
                                    <?php echo app_get_form_error('dia_vencimento'); ?>
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Efetuar troca do cartão</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        </form>

    </div>
</div>

<script src="<?php echo app_assets_url("modulos/venda/partials/js/acesso_token_externo.js", "admin") ?>"></script>

