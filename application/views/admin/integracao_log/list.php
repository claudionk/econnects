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
                        <a href="<?php echo base_url("admin/integracao/index")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>

                <!-- Widget -->
                <div class="card">


                    <div class="card-body">
                        <!-- Table -->
                        <table class="table table-hover">
                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th class="center" width="10%">Sequencia</th>
                                <th width="10%">Início</th>
                                <th width="10%">Fim</th>
                                <th width='10%'>Status</th>
                                <th width='10%'>Registros</th>
                                <th class="center" width='20%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php // print_r($rows) ;?>
                            <?php  foreach($rows as $row) :?>
                            <tr>

                                <td class="center"><?php echo $row['sequencia'];?></td>
                                <td><?php echo app_date_mysql_to_mask($row['processamento_inicio'])  ?></td>
                                <td><?php echo app_date_mysql_to_mask($row['processamento_fim'])  ?></td>
                                <td><?php echo $row['integracao_log_status_nome'];?></td>
                                <td><?php echo $row['quantidade_registros'];?></td>
                                <td class="center">
                                    <a href="<?php echo base_url("{$current_controller_uri}/view/{$integracao_id}/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Detalhes </a>
                                </td>
                            </tr>
                            <?php endforeach;?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->
                        <?php echo $pagination_links;?>
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>