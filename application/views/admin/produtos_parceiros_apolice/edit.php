<?php
if($_POST)
    $row = $_POST;
?>
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

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                    <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                        <i class="fa fa-edit"></i> Salvar
                    </a>
                </div>

            </div>
            <!-- col-separator.box -->
            <div class="col-separator col-unscrollable bg-none box col-separator-first">

                <!-- col-table -->
                <div class="col-table">

                    <!-- col-table-row -->
                    <div class="col-table-row">

                        <!-- col-app -->
                        <div class="col-app col-unscrollable">

                            <!-- col-app -->
                            <div class="col-app">

                                <!-- Form -->
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">
                                    <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                    <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                                    <input type="hidden" name="produto_parceiro_id" value="<?php echo $produto_parceiro_id; ?>"/>
                                    <!-- Widget -->

                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php $this->load->view('admin/partials/validation_errors');?>
                                            <?php $this->load->view('admin/partials/messages'); ?>
                                        </div>

                                    </div>
                                    <div class="card">

                                        <!-- // Widget heading END -->
                                        <?php $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>
                                        <div class="col-md-12">
                                            <div class="card-body tab-content">

                                            <!-- Row -->
                                            <div class="row innerLR">

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
                                                                <!-- Tab content -->
                                                                <div id="tabGeral" class="tab-pane active widget-body-regular">

                                                                            <?php $field_name = 'nome';?>
                                                                            <div class="form-group">
                                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Nome *</label>
                                                                                <div class="col-md-10"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                            </div>

                                                                            <?php $field_name = 'slug';?>
                                                                            <div class="form-group">
                                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Slug *</label>
                                                                                <div class="col-md-10"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                            </div>

                                                                            <?php $field_name = 'template';?>
                                                                            <div class="form-group">
                                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Conteúdo</label>
                                                                                <div class="col-md-10"><textarea class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" rows='5' /><?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?></textarea></div>
                                                                                <?php echo display_ckeditor($ckeditor_template); ?>
                                                                            </div>
                                                                </div>

                                                            </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <!-- // Row END -->
                                            
                                        </div>
                                        </div>
                                    </div>
                                    <!-- // Widget END -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-arrow-left"></i> Voltar
                                            </a>
                                            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                                                <i class="fa fa-edit"></i> Salvar
                                            </a>
                                        </div>

                                    </div>
                                </form>
                                <!-- // Form END -->

                            </div>
                            <!-- // END col-app -->

                        </div>
                        <!-- // END col-app.col-unscrollable -->

                    </div>
                    <!-- // END col-table-row -->

                </div>
                <!-- // END col-table -->

            </div>
            <!-- // END col-separator.box -->
        </div>
    </div>
</div>