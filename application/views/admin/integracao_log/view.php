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
                        <a href="<?php echo base_url("admin/integracao_log/index/$integracao_id")?>" class="btn  btn-app btn-primary">
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
                                <th class="center" width="10%">ID</th>
                                <th width="10%">Linha</th>
                                <th width="10%">Chave</th>
                                <th width='10%'>Status</th>
                                <th width='10%'>Código Retorno</th>

                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php // print_r($rows) ;?>
                            <?php  foreach($rows as $row) :?>
                            <tr>

                                <td class="center"><?php echo $row['integracao_log_detalhe_id'];?></td>
                                <td><?php echo $row['num_linha'] ?></td>
                                <td><?php echo $row['chave'] ?></td>
                                <td><?php echo $row['integracao_log_status_nome'];?></td>
                                <td><?php echo $row['retorno_codigo'];?></td>
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