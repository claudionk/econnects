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
                        <a href="<?php echo base_url("admin/clientes/index")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                        <a href="<?php echo base_url("$current_controller_uri/export_pdf/{$cliente_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-print"></i> Exportar PDF
                        </a>
                        <a href="<?php echo base_url("$current_controller_uri/export_excel/{$cliente_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-print"></i> Exportar Excel
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
                                <th width='30%'>Data</th>
                                <th width='30%'>Status</th>
                                <th width='30%'>Responsável</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php
                            foreach($rows as $row) :?>
                            <tr>
                                <td><?php echo app_dateonly_mysql_to_mask($row['data']);?></td>
                                <td><?php echo $row['cliente_evolucao_status_descricao'];?></td>
                                <td><?php echo $row['colaborador_nome'];?></td>
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