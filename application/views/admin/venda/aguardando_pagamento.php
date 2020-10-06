<?php if ($layout != "front") { ?>
<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome(); ?></li>
    </ol>
</div>
<?php } ?>

<!-- col-app -->
<div class="card">

    <!-- col-app -->
    <div class="card-body">

        <!-- Form -->
        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
            <input type="hidden" name="pedido_id" id="pedido_id" value="<?php if (isset($pedido_id)) echo $pedido_id; ?>"/>
            <input type="hidden" id="url_aguardando_pagamento"  name="url_aguardando_pagamento" value="<?php echo base_url("admin/gateway/consulta/"); ?>"/>

            <div class="row">
                <div class="col-md-6">
                    <?php $this->load->view('admin/partials/validation_errors');?>
                    <?php $this->load->view('admin/partials/messages'); ?>
                </div>
            </div>
            <!-- Row -->
            <div class="row innerLR">

                <!-- Column -->
                <div class="col-md-12">

                    <h2 class="text-light text-center"><?php echo app_produto_traducao('Aguardando pagamento', $produto_parceiro_id); ?><br>
                        <small class="text-primary"><?php echo app_produto_traducao('Aguarde a confirmação de seu pagamento', $produto_parceiro_id); ?></small>
                    </h2>

                    <?php
                    if((isset($layout)) && ($layout == 'front') && ($context != "pagamento")) {
                        $this->load->view('admin/venda/equipamento/front/step', array('step' => 5, 'produto_parceiro_id' => $produto_parceiro_id ));
                    }else{
                        if ($context != "pagamento") {
                            $this->load->view("admin/venda/step", array('step' => 5, 'produto_parceiro_id' => $produto_parceiro_id ));
                        }
                    }
                    ?>

                    <div class="col-md-6">

                        <!-- Stats Widget -->
                        <span href="" class="widget-stats widget-stats-2">
                            <span class="count"><?php echo $pedido['codigo'] ?></span>
                            <span class="txt"><?php echo app_produto_traducao('CÓDIGO DO PEDIDO', $produto_parceiro_id); ?></span>
                        </span>
                        <!-- // Stats Widget END -->

                        <!-- // Stats Widget END -->

                    </div>
                </div>
                <!-- // Column END -->
            </div>                                            <!-- Row -->
            <div class="row">

                <!-- Column -->
                <div class="col-md-12">
                    <div class="col-md-12">
                        <!-- Widget With Progress Bar -->
                        <div class="relativeWrap">
                            <div class="card">
                                <div class="progress progress-primary" id="widget-progress-bar">
                                    <div class="progress-bar"><strong class="text-progress-bar">Aguardando pagamento</strong> - <strong class="steps-percent">100%</strong></div>
                                </div>
                            </div>
                        </div>
                        <!-- // Widget With Progress Bar END -->
                    </div>
                    <div class="col-md-12">

                        <h4 class="text-primary">Status do pagamento</h4>


                        <div class="widget-body left">

                            <?php
                            $class = "btn-info";
                            if($pedido['pedido_status_id'] == 3 ){
                                $class = 'btn-success';
                            }elseif($pedido['pedido_status_id'] == 4 ){
                                $class = 'btn-danger';
                            }
                            ?>

                            <span id="btn-status"; class="btn <?php echo $class;?>"><?php echo $pedido['pedido_status_nome']; ?></span>
                            <em class="text-caption status-detalhe"></em>

                        </div>
                    </div>
                </div>
                <!-- // Column END -->
            </div>


            <!-- // Widget END -->
        </form>
        <!-- // Form END -->
    </div>


    <!-- // END col-app -->
</div>
<div class="card">

    <!-- col-app -->
    <div class="card-body">
        <?php if ($context != "pagamento") { ?>
            <div class="box-sucesso" style="display: none">
                <a class="btn  btn-app btn-primary" href="<?php echo base_url("{$current_controller_uri}/{$produto_slug}/{$produto_parceiro_id}/6/{$pedido_id}"); ?>" >
                    <i class="fa fa-edit"></i> <?php echo app_produto_traducao('Emissão Certificado', $produto_parceiro_id); ?>
                </a>
            </div>
        <?php } ?>

        <div class="innerAll  bg-white box-erro" style="display: none">
            <a href="<?php echo base_url("{$current_controller_uri}/{$produto_slug}/{$produto_parceiro_id}/4/{$pedido['cotacao_id']}/{$pedido_id}"); ?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Novo Pagamento
            </a>
        </div>

        <div class="innerAll  bg-white box-debito" style="display: none">
            <a href="#" target="_blank" class="btn btn-app btn-primary">
                <i class="fa fa-external-link"></i> Abrir janela débito
            </a>
        </div>

    </div>
</div>
<!-- Modal -->
<div id="modal-debito" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Pagamento Débito</h4>
            </div>
            <div class="modal-body">
                <p>Para que o pagamento em débito seja realizado você será redirecionada para a página de pagamento do banco, caso isso não acontece será necessário que você clique no botão abaixo para acessar.</p>
            </div>
            <div class="modal-footer">
                <a href="#" target="_blank" class="btn btn-app btn-primary btn-debito">
                    <i class="fa fa-external-link"></i> Abrir janela débito
                </a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>

    </div>
</div>
<!-- // END col-app.col-unscrollable -->


<!-- Modal -->
<div id="modal-falha" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Aguardando Pagamento</h4>
            </div>
            <div class="modal-body">
                <p>Houve falha na comunicação - Tentando novamente.</p>
            </div>
            <div class="modal-footer">
                <!-- <a href="#" target="_blank" class="btn btn-app btn-primary btn-debito">
                    <i class="fa fa-external-link"></i> Abrir janela débito
                </a> -->
                <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
            </div>
        </div>

    </div>
</div>


<div id="modal-sms" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <h3 class="text-ultra-bold text-primary">BAIXE NOSSO APP PARA EMITIR SEU SEGURO</h3>
                <p class="text-lg text-primary">ou acesse o link enviado para seu celular</p>
                <br/>
                <a href="<?php echo $this->config->item("URL_APLICATIVO"); ?>">
                    <img width="70%" src="<?php echo app_assets_url('core/images/google-play.png' , 'admin'); ?>">
                </a>
                <br/>
            </div>
            <div class="modal-footer">
                <a href="<?php echo $this->config->item("URL_APLICATIVO"); ?>" class="btn btn-primary">OK</a>
            </div>
        </div>

    </div>
</div>
