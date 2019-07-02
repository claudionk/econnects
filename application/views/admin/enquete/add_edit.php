<?php
if($_POST)
    $row = $_POST;
?>
<div class="layout-app" ng-controller="Enquete">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <!-- col-separator.box -->
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                    <li class="active"><?php echo $page_subtitle;?></li>
                </ol>

            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $this->load->view('admin/partials/validation_errors');?>
                                        <?php $this->load->view('admin/partials/messages'); ?>
                                    </div>

                                </div>

                                <!-- Form -->
<!--                                <form class="form" id="validateSubmitForm" validate="true" model_validate="--><?php //echo $model_name ?><!--" method="post" autocomplete="off" enctype="multipart/form-data">-->
                                    <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">

                                    <!-- Título -->
                                    <div class="card-head style-gray-dark">
                                        <header><?php if($new_record) echo $titulo_adicionar; else echo $titulo_editar; ?> <?php echo $titulo_singular ?></header>
                                    </div>

                                    <!-- Conteúdo -->
                                    <div class="card-body">

                                        <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                        <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>

                                        <div class="row">

                                            <div id="validation_errors"></div>


                                            <div class="row">

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <?php
                                                        $field_name = "nome";
                                                        template_control("text", $field_name, "Nome da enquete", $row[$field_name], array());
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <?php
                                                        $field_name = "titulo";
                                                        template_control("text", $field_name, "Título da enquete", $row[$field_name], array());
                                                        ?>
                                                    </div>
                                                </div>
<!--                                                <div class="col-md-12">-->
<!--                                                    <div class="form-group">-->
<!--                                                        --><?php
//                                                        $field_name = "data_corte";
//                                                        template_control("date", $field_name, "Data de Corte", $row[$field_name], array());
//                                                        ?>
<!--                                                    </div>-->
<!--                                                </div>-->

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <?php
                                                        $field_name = "texto_inicial";
                                                        template_control("textarea", $field_name, "Texto inicial", $row[$field_name], array());
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <?php
                                                        $field_name = "texto_final";
                                                        template_control("textarea", $field_name, "Texto final", $row[$field_name], array());
                                                        ?>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">

                                                <div class="col-md-12">

                                                    <button type="button" class="btn btn-primary" ng-click="adicionar_pergunta()"><i class="fa fa-plus"></i> Adicionar Pergunta</button>

                                                    <?php $perguntas = isset($perguntas) ? json_encode($perguntas) : '[]'; ?>

                                                    <table class="table table-responsive" ng-init='perguntas = <?php echo $perguntas ?>'>

                                                        <tr>
                                                            <td>Pergunta</td>
                                                            <td>Tipo de campo</td>
                                                            <td>Opções</td>
                                                        </tr>

                                                        <tr ng-repeat="pergunta in perguntas" >
                                                            <td>
                                                                <input  type="text" class="form-control" name="enquete_pergunta[{{ $index }}][pergunta]" ng-model="pergunta.pergunta">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="enquete_pergunta[{{ $index }}][tipo]" ng-model="pergunta.tipo">
                                                                    <option value="zero_a_dez">0 à 10</option>
                                                                    <option value="sim_nao">Sim ou não</option>
                                                                    <option value="texto">Texto</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <span ng-hide="pergunta.tipo == 'select' || pergunta.tipo == 'multiselect'  || pergunta.tipo == 'zero_a_dez'">Este campo não possui opções</span>

                                                                <div ng-show="pergunta.tipo == 'zero_a_dez'">
                                                                    <input type="text" class="form-control" ng-model="pergunta.opcoes_1" ng-init="pergunta.opcoes_1 = get_opcoes_valor($index,1)" placeholder="Texto do 0 (Ex: Muito insatisfeito)">
                                                                    <input type="text" class="form-control" ng-model="pergunta.opcoes_2" ng-init="pergunta.opcoes_2 = get_opcoes_valor($index,2)" placeholder="Texto do 5 (Ex: Indiferente)">
                                                                    <input type="text" class="form-control" ng-model="pergunta.opcoes_3" ng-init="pergunta.opcoes_3 = get_opcoes_valor($index,3)" placeholder="Texto do 10 (Ex: Muito satisfeito)">

                                                                    <input type="hidden" class="form-control" name="enquete_pergunta[{{ $index }}][opcoes]" ng-value="get_opcoes($index)">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-remove btn-sm" ng-click="remover_pergunta($index)"><i class="fa fa-minus"></i></button>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="card-actionbar">
                                        <div class="card-actionbar-row">
                                            <button onclick="$('#validateSubmitForm').submit();" type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Salvar</button>
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


