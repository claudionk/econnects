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
                        <a href="<?php echo base_url("$current_controller_uri/add")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Adicionar
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
                                <th  width='10%' class="center">ID</th>
                                <th width='10%'>Pedido</th>
                                <th width='20%'>Processado</th>
                                <th width='10%'>Parcelas</th>
                                <th width='15%'>Valor Total</th>
                                <th width='20%'>Status</th>
                                <th class="center" width='25%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                            <tr>

                                <td class="center"><?php echo $row[$primary_key];?></td>
                                <td><?php echo $row['pedido_codigo'];?></td>
                                <td><?php echo app_date_mysql_to_mask($row['data_processamento']) ;?></td>
                                <td><?php echo $row['num_parcela'];?></td>
                                <td><?php echo app_format_currency($row['valor_total']);?></td>
                                <td><?php echo $row['fatura_status_nome'];?></td>
                                <td class="center">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary btn-parcelas" data-fatura="<?php echo $row[$primary_key];?>">  <i class="fa fa-arrow-circle-down"></i>  Parcelas </a>
                                    <a href="<?php echo base_url("{$current_controller_uri}/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                </td>
                            </tr>
                            <tr style="display: none;" class="grid-parcela grid-grouped-<?php echo $row[$primary_key]; ?>">
                                <td colspan="7">
                                    <table class="table">

                                        <!-- Table heading -->
                                        <thead>
                                        <tr>
                                            <th width='10%'>Parcela</th>
                                            <th width='20%'>Processado</th>
                                            <th width='10%'>Vencimento</th>
                                            <th width='15%'>Pagamento</th>
                                            <th width='15%'>Valor</th>
                                            <th width='20%'>Status</th>
                                        </tr>
                                        </thead>
                                        <!-- // Table heading END -->

                                        <!-- Table body -->
                                        <tbody>

                                        <!-- Table row -->
                                        <?php foreach($row['parcelas'] as $parcela) :?>
                                            <tr>

                                                <td><?php echo $parcela['num_parcela'];?></td>
                                                <td><?php echo app_date_mysql_to_mask($parcela['data_processamento']) ;?></td>
                                                <td><?php echo app_date_mysql_to_mask($parcela['data_vencimento'], 'd/m/Y') ;?></td>
                                                <td><?php echo app_date_mysql_to_mask($parcela['data_pagamento']) ;?></td>
                                                <td><?php echo app_format_currency($parcela['valor']);?></td>
                                                <td><?php echo $parcela['fatura_status_nome'];?></td>
                                            </tr>
                                        <?php endforeach;?>
                                        <!-- // Table row END -->

                                        </tbody>
                                        <!-- // Table body END -->

                                    </table>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->
                        <?php echo $pagination_links; ?>
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>