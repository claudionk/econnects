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
                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>
                <?php $this->load->view('admin/implantacoes/search_form')?>
                <!-- Widget -->
                <div class="card">
                    <div class="card-body" style="display: none;">
                        <?php echo implode("\n", app_montar_relacionamento_produto($rows)); ?>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th width='30%'>Parceiro</th>
                                <th width='40%'>Produto</th>
                                <th width='20%'>Status</th>
                                <th width='20%'>Data Status</th>
                                <th class="center" width='25%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php
                            // print_pre($rows);die();
                             foreach ($rows as $row) : ?>
                                <tr>
                                    <td><?php echo emptyor($row['representante'], '-');?></td>
                                    <td><?php echo $row['nome_prod_parc'];?></td>
                                    <td><?php echo $row['implantacao_status_nome'];?></td>
                                    <td><?php echo app_date_mysql_to_mask($row['data_implantacao_status'], 'd/m/Y');?></td>
                                    <td class="center">
                                        <a href="<?php echo base_url("{$current_controller_uri}/view/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-search"></i>  Visualizar </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <?php echo $pagination_links?>
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>
