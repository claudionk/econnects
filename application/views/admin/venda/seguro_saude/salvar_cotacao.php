<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome();?></li>
    </ol>
</div>

<!-- // Widget END -->
<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
        <a class="btn pull-right btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-arrow-right"></i> Próximo
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Form -->
        <form class="form" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
            <input type="hidden" name="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>
            <input type="hidden" name="nome_segurado" id="nome_segurado" value="<?php if (isset($nome_segurado)) echo $nome_segurado; ?>"/>
            <input type="hidden" id="url_busca_cliente"  name="url_busca_cliente" value="<?php echo base_url("{$current_controller_uri}/get_cliente"); ?>"/>

            <div class="row">
                <div class="col-md-6">
                    <?php // $this->load->view('admin/partials/validation_errors');?>
                    <?php $this->load->view('admin/partials/messages'); ?>
                </div>
            </div>

            <!-- Column -->
            <div class="col-md-12">

                <?php $this->load->view('admin/venda/step', array('step' => 2, 'produto_parceiro_id' => $produto_parceiro_id )); ?>

                <br>
                <h4><?php echo app_produto_traducao('Dados Para Salvar a Cotação', $produto_parceiro_id); ?></h4>
                <hr>
                <br>


                <?php // echo print_r($campos_salvar_cotacao); ?>
                <?php foreach ($campos_salvar_cotacao as $campo): ?>

                    <?php

                    $data_campo = array();
                    $data_campo['row'] = $row;
                    $data_campo['field_name'] = $campo['campo_nome_banco'];
                    $data_campo['field_label'] = $campo['label'];
                    $data_campo['list'] = isset($list) ? $list : array();
                    $data_campo['contato_tipo'] = $data_salvar_cotacao['contato_tipo'];
                    $data_campo['tamanho'] = $campo['tamanho'] == 0 ? 6 : $campo['tamanho'];
                    $data_campo['class'] = $campo['campo_classes'];
                    $data_campo['cotacao'] = (isset($cotacao)) ? $cotacao : array();
                    $data_campo['carrossel'] = (isset($carrossel)) ? $carrossel : array();


                    if($campo['classe_slug'] == 'fixo'){
                        $this->load->view('admin/campos_sistema/'. $campo['campo_slug'], $data_campo);
                    }else{

                        $data_campo['list'] = (empty($campo['opcoes'])) ? explode(',', $campo['campo_opcoes']) : explode(',', $campo['opcoes']);
                        $this->load->view('admin/campos_sistema/'. $campo['classe_slug'], $data_campo);
                    }

                    ?>

                <?php endforeach; ?>

            </div>

        </form>
    </div>
</div>

<!-- // Widget END -->
<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
        <a class="btn pull-right btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-arrow-right"></i> Próximo
        </a>
    </div>
</div>

