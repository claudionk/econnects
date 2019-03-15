<?php
if($_POST)
    $row = $_POST;
?>
<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <!-- col-separator.box -->
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>

            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                    <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                        <i class="fa fa-edit"></i> Salvar
                    </a>
                </div>

            </div>
            <div class="col-separator col-unscrollable bg-none box col-separator-first">

                <!-- col-table -->
                <div class="col-table">

                    <!-- col-table-row -->
                    <div class="col-table-row">

                        <!-- col-app -->
                        <div class="col-app col-unscrollable">

                            <!-- col-app -->
                            <div class="col-app">

                                <!-- Form -->
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">
                                    <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                    <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                                    <input type="hidden" name="produto_parceiro_id" value="<?php echo $produto_parceiro_id; ?>"/>


                                    <?php
                                    $field_name = "ordem";
                                    echo template_control("hidden", $field_name, "Ordem", $row[$field_name], array()); ?>

                                    <!-- Widget -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <h4 class="text-primary"><?php echo $page_subtitle;?></h4>
                                        </div>
                                        <!-- // Widget heading END -->

                                        <div class="card-body">
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

                                                    <div class="col-md-6">
                                                        <?php $field_name = 'campo_tipo_id';?>
                                                        <?php echo template_control("select", $field_name, "Formulário do campo", $row[$field_name], array(
                                                            'options' => $campo_tipo_list,
                                                            'descricao' => 'nome',
                                                            'valor' => $field_name,
                                                        )); ?>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <?php $field_name = 'campo_id';?>
                                                        <?php echo template_control("select", $field_name, "Tipo do campo", $row[$field_name], array(
                                                            'options' => $campo_list,
                                                            'descricao' => 'nome',
                                                            'valor' => $field_name,
                                                            'class' => 'select2-list',
                                                        )); ?>
                                                    </div>

                                                </div>
                                                <!-- // Column END -->

                                                <!-- Column -->
                                                <div class="col-md-12">

                                                    <div class="col-md-6">
                                                        <?php $field_name = 'label';?>
                                                        <?php echo template_control("text", $field_name, "Label do campo", $row[$field_name], array(
                                                        )); ?>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <?php $field_name = 'tamanho';?>
                                                        <label class="form-label" style="width: 100%;">Tamanho
                                                            <a  href="" class="pull-right" data-toggle="modal" data-target="#modal_explicacao_tamanhos">
                                                                O que é este campo <i class="fa fa-question"></i>
                                                            </a>
                                                        </label>
                                                        <?php echo template_control("select", $field_name, "", $row[$field_name], array(
                                                            'options' => array(
                                                                ['valor' => '2', 'descricao' => 'Muito pequeno - 2'],
                                                                ['valor' => '3', 'descricao' => 'Pouco Pequeno - 3'],
                                                                ['valor' => '4', 'descricao' => 'Pequeno - 4'],
                                                                ['valor' => '6', 'descricao' => 'Médio - 6'],
                                                                ['valor' => '8', 'descricao' => 'Grande - 8'],
                                                                ['valor' => '12', 'descricao' => 'Muito Grande - 12'],
                                                            )
                                                        )); ?>
                                                    </div>

                                                </div>
                                                <!-- // Column END -->

                                                <!-- Column -->
                                                <div class="col-md-12">

                                                    <div class="col-md-6">
                                                        <?php $field_name = 'opcoes';?>
                                                        <?php echo template_control("text", $field_name, "Opções", $row[$field_name], array(
                                                        )); ?>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <?php

                                                            $field_name = 'validacoes';
                                                            $valores = array(
                                                                'options' => $row[$field_name],
                                                                'valor' => 'slug',
                                                            );

                                                            echo template_control("select", $field_name . '[]', "Validações", $valores, array(
                                                            'options' => $campo_validacao_list,
                                                            'valor' => 'slug',
                                                            'descricao' => 'nome',
                                                            'multiple' => true,
                                                            'class' => 'select2-list',
                                                        )); ?>
                                                    </div>
                                                </div>
                                                <!-- // Column END -->


                                            </div>
                                            <!-- // Row END -->
                                            
                                        </div>
                                    </div>
                                    <!-- // Widget END -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-arrow-left"></i> Voltar
                                            </a>
                                            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                                                <i class="fa fa-edit"></i> Salvar
                                            </a>
                                        </div>

                                    </div>
                                </form>
                                <!-- // Form END -->

                            </div>
                            <!-- // END col-app -->

                        </div>
                        <!-- // END col-app.col-unscrollable -->

                    </div>
                    <!-- // END col-table-row -->

                </div>
                <!-- // END col-table -->

            </div>
            <!-- // END col-separator.box -->
        </div>
    </div>
</div>



<!-- Modal -->
<div id="modal_explicacao_tamanhos" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Tamanho dos campos</h4>
            </div>
            <div class="modal-body explicacao_modal">

                <div class="row">
                    <div class="col-md-12">
                        <p>Veja abaixo os tamanhos de campos e qual a proporção correspondente na tela de venda.</p>
                    </div>
                </div>

                <div class="row ex">
                    <div class="col-md-12">
                        <b>Tamanho Muito Grande - 12</b>
                    </div>
                </div>

                <div class="row ex">
                    <div class="col-md-8">
                        <b>Tamanho Grande - 8</b>
                    </div>
                </div>

                <div class="row ex">
                    <div class="col-md-6">
                        <b>Tamanho Médio - 6</b>
                    </div>
                </div>

                <div class="row ex">
                    <div class="col-md-4">
                        <b>Pequeno - 4</b>
                    </div>
                </div>

                <div class="row ex">
                    <div class="col-md-4">
                        <b>Pouco Pequeno - 3</b>
                    </div>
                </div>

                <div class="row ex">
                    <div class="col-md-2">
                        <b>Mt. Pqn. - 2</b>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <p><b>Combinações:</b> Os campos irão se combinar automaticamente de modo a ocupar 100% da tela, conforme o exemplo abaixo.</p>
                    </div>
                </div>


                <div class="row ex">
                    <div class="col-md-6">
                        <b>Tamanho Médio - 6</b>
                    </div>

                    <div class="col-md-2">
                        <b>Mt. Pqn. - 2</b>
                    </div>

                    <div class="col-md-4">
                        <b>Pequeno - 4</b>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Fechar</button>
            </div>
        </div>

    </div>
</div>