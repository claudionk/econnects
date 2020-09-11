<?php if ($layout != "front" && !empty($exibe_url_acesso_externo)) { ?>
    <!-- Modal -->
    <div class="modal fade" id="modal_acesso_externo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <!-- <div id="modal_acesso_externo" class="modal fade" role="dialog"> -->
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Envio para <?php echo ($exibe_url_acesso_externo_tipo == 'cancelamento') ? 'cancelamento' : 'pagamento' ?> externo</h4>
                </div>
                <div class="modal-body">

                    <?php if(!isset($url_acesso_externo) || empty($url_acesso_externo)) {?>
                        <h4>O acesso externo não está configurado.</h4>
                        <p>Para configurar o acesso externo, é necessário criar um usuário de tipo slug "acesso_token". Deste modo o sistema reconhecerá o usuário de acesso externo.</p>
                    <?php } else { ?>

                        <p>URL para acesso externo: <input id="url_acesso_externo" class="form-control" type="text" readonly="readonly" value="<?php echo isset($url_acesso_externo) && !empty($url_acesso_externo) ? $url_acesso_externo : "O acesso externo não está configurado." ?>"></p>
                        <p>Ou envie a URL via e-mail ou SMS.</p>

                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="form-label">Nome do Contato:</label>
                                        <input type="text" class="form-control" id="nome_contato" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="form-label">E-mail:</label>
                                        <input type="text" class="form-control" id="email" value="<?php echo issetor($cotacao['email']); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="form-label">Telefone:</label>
                                        <input type="text" class="form-control inputmask-celular" id="sms" value="<?php echo issetor($cotacao['telefone']); ?>">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <input type="hidden" id="produto_parceiro_id" value="<?php echo $produto_parceiro_configuracao['produto_parceiro_id'] ?>">

                    <?php } ?>

                </div>

                <div class="modal-footer">
                    <button type="button" id="bt_enviar_email" class="btn btn-primary" data-acao="<?php echo ($exibe_url_acesso_externo_tipo == 'cancelamento') ? 'cancelamento' : 'pagamento' ?>" ><i class="fa fa-mail-forward"></i> Enviar URL por e-mail</button>
                    <button type="button" id="bt_enviar_sms" class="btn btn-primary" data-acao="<?php echo ($exibe_url_acesso_externo_tipo == 'cancelamento') ? 'cancelamento' : 'pagamento' ?>" ><i class="fa fa-mail-forward"></i> Enviar URL por SMS</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Fechar</button>
                </div>

            </div>

        </div>
    </div>

    <script src="<?php echo app_assets_url("modulos/venda/partials/js/acesso_token_externo.js", "admin") ?>"></script>
<?php } ?>