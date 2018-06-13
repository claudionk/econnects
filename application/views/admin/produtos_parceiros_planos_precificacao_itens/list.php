<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">

            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?> do plano:  <span class="text-danger"><?php echo $parceiro_plano['nome'];?></li>
                </ol>

            </div>

            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("admin/produtos_parceiros_planos/view_by_produto_parceiro/{$parceiro_plano['produto_parceiro_id']}")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>

                        <a href="<?php echo base_url("$current_controller_uri/add/{$produto_parceiro_plano_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Adicionar
                        </a>

                        <a href="<?php echo base_url("$current_controller_uri/exportar_excel/{$produto_parceiro_plano_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-download"></i> Exportar precificação
                        </a>

                        <a data-toggle="modal" data-target="#modal" href="<?php echo base_url("$current_controller_uri/importar_preficicacao/{$produto_parceiro_plano_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-upload"></i> Importar precificação
                        </a>

                        <a href="<?php echo app_assets_url("arquivos/exemplo-precificacao.xlsx", "common")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-download"> </i> Baixar modelo
                        </a>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/validation_errors');?>
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
                                <th width='20%'>Tipo</th>
                                <th width='20%'>Intervalo</th>
                                <th width='25%'>Valor</th>
                                <th class="center" width='35%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                            <tr>
                                <td><?php echo $row['tipo'];?></td>
                                <td><?php echo $row['inicial'];?> - <?php echo $row['final'];?> (<?php echo $row['unidade_tempo'];?>)</td>
                                <td><?php echo app_format_currency($row['valor'], FALSE, 3);?></td>
                                <td class="center">
                                    <a href="<?php echo base_url("{$current_controller_uri}/edit/{$produto_parceiro_plano_id}/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                    <a href="<?php echo base_url("$current_controller_uri/delete/{$produto_parceiro_plano_id}/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
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