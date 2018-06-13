<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">

            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>

            </div>

            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <div class="col-md-3">

                            <a href="<?php echo base_url("admin/qualificacoes/index")?>" class="btn  btn-app btn-primary">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>

                            <a href="<?php echo base_url("$current_controller_uri/add/{$qualificacao_id}")?>" class="btn  btn-app btn-primary">
                                <i class="fa  fa-plus"></i> Adicionar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>

                 <?php $this->load->view('admin/qualificacoes_questoes/search_form');?>
                <!-- Widget -->
                <div class="card">

                    <div class="card-body">

                        <!-- Table -->
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th width='50%'>Pergunta</th>
                                <th width='5%'>Peso</th>
                                <th width='10%'>Regua</th>
                                <th class="center" width='35%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                            <tr>

                                <td><?php echo $row['pergunta'];?></td>
                                <td><?php echo $row['peso'];?></td>
                                <td><?php echo $row['regua_logica'];?> de <?php echo $row['regua_logica'];?></td>
                                <td class="center">
                                    <a href="<?php echo base_url("{$current_controller_uri}/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                    <a href="<?php echo base_url("admin/qualificacoes_questoes_opcoes/view/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Opções </a>
                                    <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                </td>
                            </tr>
                            <?php endforeach;?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->
                        <?php  echo $pagination_links; ?>
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>