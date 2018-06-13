<div class="layout-app">
    <!-- row -->
    <div class="row row-app">

        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">

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
                        <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
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

                                    <div class="card tabs-left style-default-light">
                                        <!-- Tab content -->
                                        <?php  $this->load->view('admin/produtos_parceiros_configuracao/sub_tab_produto');?>
                                        <div class="card-body tab-content style-default-bright">
                                            <div class="tab-content">

                                                <input type="hidden" name="url_ordem" id="url_ordem" value="<?php echo  base_url("$current_controller_uri/set_ordem/{$produto_parceiro_id}"); ?>">
                                                <input type="hidden" name="url_tipo" id="url_tipo" value="<?php echo  base_url("$current_controller_uri/index/{$produto_parceiro_id}/"); ?>">

                                                        <!-- Tab content -->
                                                        <div id="tabCampo" class="tab-pane active widget-body-regular">

                                                            <!-- Table -->
                                                            <table class="table table-hover">

                                                                <!-- Table heading -->
                                                                <thead>
                                                                <tr>
                                                                    <th width='2%'><div class="checkbox checkbox-inline checkbox-styled"><label><input type="checkbox" name="selec_todos" id="checkAll"></label></div></th>
                                                                    <th width='10%'>Tipo</th>
                                                                    <th width='45%'>Nome</th>
                                                                </tr>
                                                                </thead>
                                                                <!-- // Table heading END -->

                                                                <!-- Table body -->
                                                                <tbody>

                                                                <!-- Table row -->
                                                                <?php foreach($servicos as $row) :?>
                                                                    <?php
                                                                    $idx_parceiro = array_search($row['servico_id'], array_column($rows, 'servico_id'));
                                                                    $linha_parceiro = ($idx_parceiro !== FALSE) ? $rows[$idx_parceiro] : array();


                                                                    ?>
                                                                    <tr>
                                                                        <td><div class="checkbox checkbox-inline checkbox-styled"><label><input type="checkbox" class="checkbox_row" name="selec_row[]" id="selec_row_<?php echo $row['servico_id']; ?>" value="<?php echo $row['servico_id']; ?>" <?php if($idx_parceiro !== FALSE) {echo ' checked'; } ?>></label></div></td>
                                                                        <td><?php echo $row['servico_tipo_nome'];?></td>
                                                                        <td><?php echo $row['nome'];?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                                <!-- // Table row END -->

                                                                </tbody>
                                                                <!-- // Table body END -->

                                                            </table>
                                                            <!-- // Table END -->
                                                            <div class="row">
                                                                <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                                                                    <i class="fa fa-edit"></i> Salvar
                                                                </a>
                                                            </div>
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

        </form>
    </div>
</div>