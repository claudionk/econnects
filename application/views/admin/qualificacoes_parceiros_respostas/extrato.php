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
                    <a href="<?php echo base_url("admin/qualificacoes_parceiros/view/{$qualificacao_id}")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                </div>

            </div>
            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">



                <!-- Widget -->
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <h4 class="text-primary"><?php echo app_recurso_nome();?></h4>
                    </div>
                    <!-- // Widget heading END -->

                    <div class="card-body">


                        <!-- Table -->
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>

                                <th width="5%">Peso</th>
                                <th width="6%">Lógica</th>
                                <th width="20%">Pergunta</th>
                                <th width="20%">Resposta</th>
                                <th width="3%">Régua</th>
                                <th width="8%">Valor Exato</th>
                                <th class="center" width='5%'>Pontuação</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php $total = 0; foreach($rows as $row) :?>
                                <tr>


                                    <td><?php echo $row['peso'];?></td>
                                    <td><?php echo $row['regua_logica'];?> de <?php echo $row['regua_logica'];?></td>
                                    <td><?php echo $row['pergunta'];?></td>
                                    <td><?php echo $row['opcao_nome'];?></td>
                                    <td><?php echo $row['regua_valor'];?></td>
                                    <td><?php echo $row['valor_exato'];?></td>
                                    <td><?php echo $row['pontuacao'];?></td>

                                </tr>
                            <?php $total += $row['pontuacao'] ; endforeach;?>
                            <tr>

                                <td colspan="6"><strong>Pontuação Total: </strong></td>
                                <td> <strong><?php echo $total;?></strong></td>

                            </tr>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->

                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>