<?php
if ($_POST) {
    $row = $_POST;
}
$cpfcnpj = app_cpf_to_mask(issetor($cotacao_dados['cnpj_cpf'], ''));
$nome = issetor($cotacao_dados['nome'], '');
$valor = app_format_currency(issetor($valor_total, 0), true);
$produto = issetor($cotacao_dados['equipamento_nome'], '');
$nome_seguro = issetor($produtos_nome, '');

if ( !empty($carrinho_vazio) ): ?>
    <div class="row">
        <div class="col-md-6">
            <h2 class="text-light text-center">Aguardando pagamento<br>
                <small class="text-primary">O carrinho de compras está vazio</small>
            </h2>
        </div>
    </div>
<?php 
else: ?>

<form class="form form-pagamento form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
    <input type="hidden" name="produto_parceiro_id" value="<?php if (isset($produto_parceiro_id)) echo $produto_parceiro_id; ?>" />
    <input type="hidden" name="cotacao_id" id="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>" />
    <input type="hidden" name="pedido_id" id="pedido_id" value="<?php if (isset($pedido_id)) echo $pedido_id; ?>" />
    <input type="hidden" id="url_ver_pedido" name="url_aguardando_pagamento" value="<?php echo base_url("admin/gateway/pedido/"); ?>" />
    <input type="hidden" id="url_pagamento_confirmado" name="url_pagamento_confirmado" value="<?php echo  base_url($url_pagamento_confirmado) ?>/" />
    <!-- Widget -->

    <div class="row">
        <div class="col-md-6">
            <?php $this->load->view('admin/partials/validation_errors'); ?>
            <?php $this->load->view('admin/partials/messages'); ?>
        </div>
    </div>

    <!-- Collapsible Widgets -->
    <div class="row">
        <div class="col-md-12">

            <?php
            $this->load->view('admin/venda/equipamento/front/step', array('step' => 4, 'produto_parceiro_id' => $produto_parceiro_id, 'title' => 'PAGAMENTO'));
            $this->load->view('admin/venda/partials/enviar_token_acesso'); ?>

            <div class="col-md-12 col-sm-12 col-xs-12 icon-login">
                <i class="fa fa-lock text-primary-dark border-primary" aria-hidden="true"></i>
            </div>

            <div class="col-md-12">
                <div id="term-block">
                    <div id="term-block-content" style="border:2px solid #ddd; overflow-y: scroll; background:white;position: relative;width: 100%;scroll-behavior: auto;height: 204px;">
                        <div style="margin:5%">
                            <p><strong>TERMO DE AUTORIZAÇÃO DE COBRANÇA DE PRÊMIO DE SEGURO</strong></p>
                            <div style="">
                                <span>
                                    Eu, <?php echo $nome; ?>, inscrito no CPF/MF sob o n <?php echo $cpfcnpj; ?>, proponente do(s) seguro(s) <?php echo $nome_seguro; ?>, autorizo que o pagamento do prêmio de seguro no valor de <?php echo $valor; ?> seja realizado em conjunto com o pagamento do(s) <?php echo $produto; ?> ora adquirido(s).
                                    <hr>
                                    <small>1) O segurado poderá desistir do seguro contratado no prazo de 7 (sete) dias corridos a contar da assinatura da proposta, no caso de contratação por apólice individual, ou da emissão do bilhete, no caso de contratação por bilhete, ou do efetivo pagamento do prêmio, o que ocorrer por último.</small>
                                    <hr>
                                    <small>2) No caso de pagamento de prêmio fracionado, considera-se o pagamento da primeira parcela como o efetivo pagamento.</small>
                                </span>
                            </div>
                            <!-- <div style=""><span>(LOCAL), (DATA)</span></div>
                            <div style=""><span>_______________________________</span></div>
                            <div style=""><span>(ASSINATURA DO SEGURADO)</span></div> -->
                        </div>
                    </div>

                    <div id="div-ask-read-term" class="col-xs-11">
                        <h5 class=" text-justify text-sm-left">
                            Por favor, leia o texto até o final para habilitar o campo de aceite do termo.
                        </h5>
                    </div>
                    <div class="col-xs-1">
                        <a href="javascript:void(0)" data-toggle="tooltip" class="tooltip-icon terms" data-placement="left" title="Leia o Termo até o final para habilitar o botão de Aceite do termo">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                        </a>
                    </div>

                    <div class="col-xs-12">
                        <hr>
                    </div>

                    <?php if($isConfirmaEmail == true): ?>

                        <div class="col-xs-12">
                            <h5 class="text-sm-left">Você receberá no e-mail abaixo o Bilhete do Seguro, as Condições Gerais do Seguro e o Termo de Autorização de Cobrança.</h5>
                            <input type="email" name="email" class="form-control" value="<?= $cotacao_dados["email"]; ?>">
                        </div>

                        <div class="col-xs-12">
                            <hr>
                        </div>

                    <?php endif; ?>

                    <div id="aceite-term-check" class="col-xs-11" style="display:none">
                        <label>
                            <input type="checkbox" id="check_termo" /> Estou de acordo com os termos de uso.
                        </label>
                    </div>
                </div>
            </div>

            <div id="pagamento_content">

                <?php if ( !empty($pedidos) ) { ?>
                <hr>
                <?php $valor_total = 0; ?>
                <?php foreach ($pedidos as $pedido) : ?>
                    <?php $valor_total += $pedido['valor_total']; ?>
                    <div class="col-12" style="margin: 4px; box-shadow: 0px 7px 14px rgba(140, 140, 140, 0.45);">
                        <div class="col-12" style="background-color: #f0f0f0; padding: 0 4px; border-bottom: 1px solid #ddd;">
                            <b>Pedido:</b> <?= $pedido["codigo"]; ?>
                        </div>
                        <div class="col-xs-10" style="background-color: #f4f4f4; padding: 0 4px; border-bottom: 1px solid #ddd;">
                            <b>Vigência</b><br>
                            de <?= date("d/m/Y", strtotime($pedido["inicio_vigencia"])); ?> até <?= date("d/m/Y", strtotime($pedido["fim_vigencia"])); ?>
                        </div>
                        <div class="col-xs-2" style="background-color: #f4f4f4; padding: 0 4px; border-bottom: 1px solid #ddd;">
                            <b>Valor</b><br>
                            <?= app_format_currency($pedido['valor_total'], false, 2 ); ?>
                        </div>
                        <div class="col-12" style="background-color: #fff; padding: 0 4px;">
                            <?= $pedido["nome"]; ?>
                        </div>
                        <div class="col-12" style="background-color: #f4f4f4; padding: 0 4px; border-top: 1px solid #ddd;">
                        
                            <small><i>Clique <a class="btn-link" target="_blank" href="<?= base_url("{$current_controller_uri}/termo/{$pedido['produto_parceiro_id']}")?>">aqui</a> para as condições gerais do produto</i></small>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>
                <div class="col-12 text-center">
                    TOTAL: <?= app_format_currency($valor_total, false, 2 ); ?>
                </div>
                <hr>
                <?php } ?>
                
                
                <div class="col-12 text-center">
                    </small>As condições contratuais/regulamento deste produto protocolizadas pela sociedade/entidade junto à SUSEP – Superintendência de Seguros Privados – Autarquia Federal responsável pela fiscalização, normatização e controle dos mercados de Seguro, previdência complementar aberta, capitalização, resseguro e corretagem de Seguro poderão ser consultadas no endereço eletrônico www.susep.gov.br, de acordo com o número de processo 15414.900534/2014-57 para garantia estendida e 15414.902260/2013-50 para equipamentos portáteis. O registro deste plano na SUSEP não implica, por parte da Autarquia, incentivo ou recomendação à sua comercialização. O Segurado poderá consultar a situação cadastral do seu corretor de seguros, no site da SUSEP. Central de atendimento ao segurado: SAC: 0800 7740 772 (dúvidas, reclamações, sugestões) - 0800 704 2474 (Atendimento para Deficientes Auditivos), Ouvidoria: 0800 704 7099 (segunda à sexta-feira, das 8h30 às 17h).<small>
                </div>
                <hr>
                <div class="col-xs-12">
                    <label>
                        <input type="checkbox" id="check_condicoes" /> Li e Concordo com as condições gerais
                    </label>
                </div>
                <hr>
                <div id="select-forma-pagamento" class="col-xs-12 select-forma-pagamento" style="display:none;">
                    <div class="form-group">
                        <label for="forma_pagamento" class="control-label"> Forma de pagamento </label>
                        <div class="label">
                            <select class="form-control select" id="formaPagamento" name="forma_pagamento_tipo_id" onchange="selectFormaPagamento()">
                                <option value=""></option>
                                <?php foreach ($forma_pagamento as $index => $forma) { ?>
                                    <option value="<?php echo $forma['tipo']['forma_pagamento_tipo_id']; ?>"> <?php echo $forma['tipo']['nome']; ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <?php
                    foreach ($forma_pagamento as $index => $forma) :
                        $this->load->view("admin/venda/equipamento/front/steps/pagamento/" . $forma['tipo']['slug'], array('forma' => $forma, 'row' => issetor($campos, array())));
                    endforeach;
                    ?>
                </div>

                <div class="col-xs-12 btns" id="btnSubmit" style="display: none;">
                    <a class="btn btn-app btn-primary btn-proximo background-primary border-primary" onclick="$('#validateSubmitForm').submit();" id="btn-proximo">
                        Próximo <i class="fa fa-angle-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="card" style="display:none;">
    <div class="card-body">
        <?php if ($context != "pagamento") { ?>
            <a href="<?php echo base_url("{$current_controller_uri}/{$produto_slug}/{$produto_parceiro_id}/{$step}/{$cotacao_id}") ?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
        <?php } ?>

        <?php if (($produto_parceiro_configuracao['venda_carrinho_compras']) && ($context != "pagamento")) : ?>
            <a class="btn  btn-app btn-primary" href="<?php echo base_url("{$current_controller_uri}/{$produto_slug}/{$produto_parceiro_id}/7/{$cotacao_id}/$pedido_id") ?>">
                <i class="fa fa-cart-plus"></i> Adicionar no carrinho
            </a>
        <?php endif; ?>

        <a class="btn pull-right btn-app btn-primary btn-proximo" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-arrow-right"></i> Próximo
        </a>

        <a style="display:none;" class="btn pull-right btn-app btn-success btn-pagamento-efetuado" href="#">
            <i class="fa fa-arrow-right"></i> Pagamento Efetuado
        </a>
    </div>
</div>

<script>
    function checkTerm() {
        if ($("#check_termo:checked").length) {
            $('#pagamento_content').show();
            $('#term-block').hide();
            $('#step-title').text($('#step-title-original').val());
        }
    }

    function checkCondicoes(){
        if ($("#check_condicoes:checked").length) {
            $('#select-forma-pagamento').show();
        }
    }

    $('document').ready(function() {
        $('#step-title').text('TERMO / ' + $('#step-title').text());

        $('#pagamento_content').hide()

        $('.step').css('width:auto')

        $('#check_termo').on('click', function() {
            checkTerm();
        })

        $('#check_condicoes').on("click", function(){
            checkCondicoes();
        });

        $('#term-block-content').on('scroll', function() {
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                $('#aceite-term-check').show()
            }
        })

    })
</script>
<?php endif; ?>