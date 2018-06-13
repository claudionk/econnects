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
                        <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
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

                    <?php $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>
                    <div class="card-body">

                        <div class="relativeWrap">
                            <div class="widget widget-tabs widget-tabs-double-2 widget-tabs-responsive">

                                <!-- Tabs Heading -->

                                <!-- // Tabs Heading END -->

                                <div class="widget-body">
                                    <div class="tab-content">
                                            <div class="card tabs-left style-default-light">
                                                <!-- Tab content -->
                                                <?php  $this->load->view('admin/produtos_parceiros_configuracao/sub_tab_regra_negocio');?>
                                                <div class="card-body tab-content style-default-bright">
                                                      <div id="tabCampo" class="tab-pane active widget-body-regular">
                                            <div class="row innerTB">
                                                <div class="col-md-6">

                                                    <a href="<?php echo base_url("$current_controller_uri/add/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
                                                        <i class="fa  fa-plus"></i> Adicionar
                                                    </a>
                                                </div>
                                            </div>


                                            <!-- Table -->
                                            <table class="table table-hover">

                                                <!-- Table heading -->
                                                <thead>
                                                <tr>
                                                    <th width='65%'>Nome</th>
                                                    <th width='10%'>Parâmetros</th>
                                                    <th class="center" width='25%'>Ações</th>
                                                </tr>
                                                </thead>
                                                <!-- // Table heading END -->

                                                <!-- Table body -->
                                                <tbody>

                                                <!-- Table row -->
                                                <?php foreach($rows as $row) :?>
                                                    <tr>

                                                        <td><?php echo $row['regra_preco_nome'];?></td>
                                                        <td><?php echo $row['parametros'];?></td>
                                                        <td class="center">
                                                            <a href="<?php echo base_url("{$current_controller_uri}/edit/{$produto_parceiro_id}/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                                            <a href="<?php echo base_url("$current_controller_uri/delete/{$produto_parceiro_id}/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; echo $pagination_links?>
                                                <!-- // Table row END -->

                                                </tbody>
                                                <!-- // Table body END -->

                                            </table>
                                            <!-- // Table END -->
                                        </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>