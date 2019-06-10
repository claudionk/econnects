<?php
if($_POST){
    $row = $_POST;
}
?>

<?php if ($layout != "front") { ?>
    <div class="section-header">
        <ol class="breadcrumb">
            <li class="active"><?php echo app_recurso_nome(); ?></li>
        </ol>
    </div>


    <div class="card">
        <div class="card-body">

            <a href="<?php echo base_url("{$current_controller_uri}/{$produto_slug}/{$produto_parceiro_id}/{$step}/{$cotacao_id}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>

            <?php if($produto_parceiro_configuracao['venda_carrinho_compras']) :?>
                <a class="btn  btn-app btn-primary" href="<?php echo base_url("{$current_controller_uri}/{$produto_slug}/{$produto_parceiro_id}/7/{$cotacao_id}/$pedido_id")?>">
                    <i class="fa fa-cart-plus"></i> Adicionar no carrinho
                </a>

            <?php endif; ?>
            <a class="btn pull-right btn-app btn-primary btn-proximo" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-arrow-right"></i> Próximo
            </a>
        </div>
    </div>
<?php } ?>

<form class="form form-pagamento form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
    <input type="hidden" name="produto_parceiro_id" value="<?php if (isset($produto_parceiro_id)) echo $produto_parceiro_id; ?>"/>
    <input type="hidden" name="cotacao_id" id="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>
    <input type="hidden" name="pedido_id" id="pedido_id" value="<?php if (isset($pedido_id)) echo $pedido_id; ?>"/>
    <input type="hidden" id="url_ver_pedido"  name="url_aguardando_pagamento" value="<?php echo base_url("admin/gateway/pedido/"); ?>"/>
    <input type="hidden" id="url_pagamento_confirmado"  name="url_pagamento_confirmado" value="<?php echo  base_url($url_pagamento_confirmado) ?>/"/>
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
            if((isset($layout)) && ($layout == 'front') && ($context != "pagamento")) {
                $this->load->view('admin/venda/equipamento/front/step', array('step' => 4, 'produto_parceiro_id' => $carrossel['produto_parceiro_id'], 'title' => 'PAGAMENTO' ));
            }else{
                if ($context != "pagamento") {
                 $this->load->view("admin/venda/step", array('step' => 4, 'produto_parceiro_id' => $produto_parceiro_id, 'title' => 'PAGAMENTO' ));
                }
            }
            ?>

            <?php $this->load->view('admin/venda/partials/enviar_token_acesso'); ?>

            <div class="col-xs-12 select-forma-pagamento">
                <div class="form-group">
                    <label for="forma_pagamento" class="control-label"> Forma de pagamento </label>
                    <select class="form-control" id="formaPagamento" onchange="selectFormaPagamento()">
                        <option value=""></option>
                        <?php foreach ($forma_pagamento as $index => $forma){ ?>
                        <option value="<?php echo $forma['tipo']['forma_pagamento_tipo_id']; ?>"> <?php echo $forma['tipo']['nome']; ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <?php
            foreach ($forma_pagamento as $index => $forma):
                $this->load->view("admin/venda/pagamento/". $forma['tipo']['slug'], array('forma' => $forma, 'row' => issetor($campos, array())));
            endforeach;
            ?>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body">
        <?php if ($context != "pagamento") { ?>
            <a href="<?php echo base_url("{$current_controller_uri}/{$produto_slug}/{$produto_parceiro_id}/{$step}/{$cotacao_id}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
        <?php } ?>

        <?php if(($produto_parceiro_configuracao['venda_carrinho_compras']) && ($context != "pagamento") ) :?>
            <a class="btn  btn-app btn-primary" href="<?php echo base_url("{$current_controller_uri}/{$produto_slug}/{$produto_parceiro_id}/7/{$cotacao_id}/$pedido_id")?>">
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