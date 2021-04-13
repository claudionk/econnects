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
            <input type="hidden" name="pedido_id" id="pedido_id" value="<?php if (isset($pedidos[0]['pedido_id'])) echo $pedidos[0]['pedido_id']; ?>"/>
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

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th width="40%">PEDIDO</th>
                                <th width="40%">PRODUTO</th>
                                <th width="20%">VALOR</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $valor_total = 0; ?>
                            <?php foreach ($pedidos as $pedido) : ?>
                                <tr>
                                    <td><?php echo $pedido['codigo']; ?></td>
                                    <td><?php echo $pedido['nome']; ?></td>
                                    <td><?php  echo app_format_currency($pedido['valor_total'], false, 2 ); ?></td>
                                </tr>
                                <?php $valor_total += $pedido['valor_total']; ?>

                            <?php endforeach; ?>
                            <tr>
                                <td class="text-right" colspan="2"><strong>TOTAL: </strong></td>
                                <td><?php  echo app_format_currency($valor_total, false, 2 ); ?></td>
                            </tr>

                            </tbody>
                        </table>
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
                            if($pedidos[0]['pedido_status_id'] == 3 ){
                                $class = 'btn-success';
                            }elseif($pedidos[0]['pedido_status_id'] == 4 ){
                                $class = 'btn-danger';
                            }
                            ?>

                            <span id="btn-status"; class="btn <?php echo $class;?>"><?php echo $pedidos[0]['pedido_status_nome']; ?></span>
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
        <div class="box-sucesso" style="display: none">
            <a class="btn  btn-app btn-primary" href="<?php echo base_url("{$current_controller_uri}/index")?>">
                <i class="fa fa-edit"></i> Nova Venda
            </a>
        </div>

        <div class="innerAll  bg-white box-erro" style="display: none">
            <a href="<?php echo base_url("{$current_controller_uri}/pagamento_carrinho/1")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Novo Pagamento
            </a>
        </div>
    </div>
</div>
<!-- // END col-app.col-unscrollable -->
