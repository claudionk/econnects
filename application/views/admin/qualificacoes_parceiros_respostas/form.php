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
                    <li class="active"><?php echo $page_subtitle;?></li>
                </ol>

            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("admin/qualificacoes_parceiros/view/{$qualificacao_id}")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-edit"></i> Voltar
                    </a>

                    <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                        <i class="fa fa-edit"></i> Salvar
                    </a>
                </div>

            </div>
            <!-- col-separator.box -->
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
                                    <input type="hidden" name="qualificacao_parceiro_id" value="<?php echo $qualificacao_parceiro_id; ?>"/>

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
                                                <div class="col-md-8">

                                                    <!-- Table -->
                                                    <table class="table table-hover">

                                                        <!-- Table heading -->
                                                        <thead>
                                                        <tr>
                                                            <th width='5%'>Peso</th>
                                                            <th width='40%'>Pergunta</th>
                                                            <th class="center" width='25%'>Opção / Resposta</th>
                                                            <th class="center" width='25%'>Valor Exato</th>
                                                        </tr>
                                                        </thead>
                                                        <!-- // Table heading END -->

                                                        <!-- Table body -->
                                                        <tbody>

                                                        <!-- Table row -->
                                                        <?php foreach($questoes as $questao) :?>

                                                            <?php
                                                                if(!$questao['opcoes']){

                                                                    continue;

                                                                }

                                                            ?>
                                                            <tr>
                                                                <td class="center">

                                                                    <?php echo $questao['peso']; ?>
                                                                </td>
                                                                <td>

                                                                    <?php echo $questao['pergunta']; ?>
                                                                </td>

                                                                <td>
                                                                    <select name="respostas[<?php echo $questao['qualificacao_questao_id'] ?>][qualificacao_questao_opcao_id]" class="form-control ">
                                                                        <option value="">Selecione...</option>

                                                                        <?php foreach($questao['opcoes'] as $opcao) :?>
                                                                            <option  value="<?php echo $opcao['qualificacao_questao_opcao_id'];?>"
                                                                                <?php if(isset($current_respostas[ $questao['qualificacao_questao_id']])
                                                                                     && ($current_respostas[ $questao['qualificacao_questao_id']]['qualificacao_questao_opcao_id'] == $opcao['qualificacao_questao_opcao_id'] )  ) echo 'selected="true"'; ?>>
                                                                                <?php echo $opcao['nome'];?> - (<?php echo   $opcao['regua_valor'];?>)
                                                                            </option>
                                                                        <?php endforeach;?>
                                                                    </select>

                                                                </td>
                                                                <td>
                                                                    <input name="respostas[<?php echo $questao['qualificacao_questao_id'] ?>][valor_exato]" class="form-control"
                                                                           value="<?php if(isset($current_respostas[ $questao['qualificacao_questao_id']])) echo $current_respostas[ $questao['qualificacao_questao_id']]['valor_exato']; ?>" >
                                                                </td>

                                                            </tr>
                                                        <?php endforeach;?>
                                                        <!-- // Table row END -->

                                                        </tbody>
                                                        <!-- // Table body END -->

                                                    </table>
                                                    <!-- // Table END -->

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
                                            <a href="<?php echo base_url("admin/qualificacoes_parceiros/view/{$qualificacao_id}")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-edit"></i> Voltar
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