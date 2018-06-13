<?php
if($_POST){
    $row = $_POST;
}

?>
<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome(); ?></li>
    </ol>
</div>


<div class="card">
    <div class="card-body">

        <a href="<?php echo base_url("{$current_controller_uri}/equipamento/{$carrossel['produto_parceiro_id']}/3/{$cotacao_id}")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>

        <?php if($produto_parceiro_configuracao['venda_carrinho_compras']) :?>
            <a class="btn  btn-app btn-primary" href="<?php echo base_url("{$current_controller_uri}/equipamento/{$carrossel['produto_parceiro_id']}/7/{$cotacao_id}/$pedido_id")?>">
                <i class="fa fa-cart-plus"></i> Adicionar no carrinho
            </a>

        <?php endif; ?>
        <a class="btn  btn-app btn-primary btn-proximo" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Próximo
        </a>
    </div>
</div>

<div class="card">

    <!-- col-app -->
    <div class="card-body">

            <!-- Form -->
        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="produto_parceiro_id" value="<?php if (isset($carrossel['produto_parceiro_id'])) echo $carrossel['produto_parceiro_id']; ?>"/>
            <input type="hidden" name="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>
            <input type="hidden" name="pedido_id" value="<?php if (isset($pedido_id)) echo $pedido_id; ?>"/>
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
                    <?php $this->load->view('admin/venda/seguro_viagem/step', array('step' => 4 )); ?>

                    <h4>Formas de Pagamento</h4>


                    <div class="panel-group" id="accordion1">
                    <?php $in = " in"; ?>
                    <?php $expanded = " expanded"; ?>
                    <?php $select = " checked"; ?>
                        <?php foreach ($forma_pagamento as $index => $forma) : ?>
                            <!-- Accordion Item -->
                            <div class="card panel<?php if(isset($row['forma_pagamento_tipo_id'])){if($row['forma_pagamento_tipo_id'] == $forma['tipo']['forma_pagamento_tipo_id']) {echo ' expanded';}} else {if(!isset($row['forma_pagamento_tipo_id'])){ echo $expanded;}} ?>">
                                <div class="card-head" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-<?php echo $forma['tipo']['forma_pagamento_tipo_id']; ?>">
                                    <header>
                                        <div class="radio radio-styled">
                                            <label>
                                                <input type="radio" name="forma_pagamento_tipo_id" value="<?php echo $forma['tipo']['forma_pagamento_tipo_id']; ?>" <?php if(isset($row['forma_pagamento_tipo_id'])){if($row['forma_pagamento_tipo_id'] == $forma['tipo']['forma_pagamento_tipo_id']) {echo 'checked="checked"';}} else {if(!isset($row['forma_pagamento_tipo_id'])){ echo $select;}} ?> >
                                                <span><?php  echo $forma['tipo']['nome'];  ?></span>
                                            </label>
                                        </div>
                                    </header>
                                    <div class="tools">
                                        <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                    </div>
                                </div>
                                <div id="accordion1-<?php echo $forma['tipo']['forma_pagamento_tipo_id']; ?>" class="collapse <?php if(isset($row['forma_pagamento_tipo_id'])){if($row['forma_pagamento_tipo_id'] == $forma['tipo']['forma_pagamento_tipo_id']) {echo ' in';}} else {if(!isset($row['forma_pagamento_tipo_id'])){ echo $in;}} ?>">
                                    <div class="panel-body">
                                        <?php $this->load->view('admin/venda/equipamento/pagamento/'. $forma['tipo']['slug'], array('forma' => $forma));?>
                                    </div>
                                </div>
                            </div>

                            <?php $in = ""; $expanded = ""; $select = "";?>
                        <?php endforeach;  ?>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/equipamento/{$carrossel['produto_parceiro_id']}/3/{$cotacao_id}")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
        <a class="btn  btn-app btn-primary btn-proximo" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Próximo
        </a>
    </div>
</div>