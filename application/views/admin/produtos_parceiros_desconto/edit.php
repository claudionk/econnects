<?php
if($_POST)
    $row = $_POST;
?>
<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <!-- col-separator.box -->
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
                                        <?php  $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>
                                        <!-- // Widget heading END -->

                                        <div class="card-body">

                                            <!-- Row -->
                                            <div class="row innerLR">

                                                <div class="relativeWrap">
                                                    <div class="widget widget-tabs widget-tabs-double-2 widget-tabs-responsive">

                                                        <!-- Tabs Heading -->

                                                         <!-- // Tabs Heading END -->

                                                        <div class="col-md-12">
                                                            <div class="widget-body">
                                                            <div class="card tabs-left style-default-light">
                                                                <!-- Tab content -->
                                                                <?php  $this->load->view('admin/produtos_parceiros_configuracao/sub_tab_regra_negocio');?>
                                                                <div class="card-body tab-content style-default-bright">
                                                                     <div class="tab-content">

                                                                <!-- Tab content -->
                                                                <div id="tbDesconto" class="tab-pane active widget-body-regular">
                                                                    <?php $field_name = 'habilitado';?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Habilitado *</label>
                                                                        <label class="radio-inline radio-styled radio-primary">
                                                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                                   value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                            Sim
                                                                        </label>
                                                                        <label class="radio-inline radio-styled radio-primary">
                                                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                                   value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                            Não
                                                                        </label>
                                                                    </div>
                                                                    <?php $field_name = 'valor';?>
                                                                    <div class="form-group desconto_habilitado">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Valor Total</label>
                                                                        <div class="col-md-2"><input class="form-control inputmask-valor" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                    <?php $field_name = 'utilizado';?>
                                                                    <div class="form-group desconto_habilitado">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Utlizado</label>
                                                                        <div class="col-md-2"><input readonly class="form-control inputmask-valor" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                    <?php $field_name = 'saldo';?>
                                                                    <div class="form-group desconto_habilitado">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Saldo</label>
                                                                        <div class="col-md-2"><input readonly class="form-control inputmask-valor" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo (isset($row['valor']) && isset($row['utilizado'])) ? ($row['valor'] - $row['utilizado']) : '000,00'; ?>" /></div>
                                                                    </div>
                                                                    <?php $field_name = 'data_ini';?>
                                                                    <div class="form-group desconto_habilitado">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Data início</label>
                                                                        <div class="col-md-2"><input placeholder="__/__/____" class="form-control inputmask-date" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? app_date_mysql_to_mask($row[$field_name]) : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                    <?php $field_name = 'data_fim';?>
                                                                    <div class="form-group desconto_habilitado">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Data Final</label>
                                                                        <div class="col-md-2"><input placeholder="__/__/____" class="form-control inputmask-date" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? app_date_mysql_to_mask($row[$field_name]) : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                    <?php $field_name = 'descricao';?>
                                                                    <div class="form-group desconto_habilitado">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Descrição</label>
                                                                        <div class="col-md-2"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
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
                                            <!-- // Row END -->
                                            
                                        </div>
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