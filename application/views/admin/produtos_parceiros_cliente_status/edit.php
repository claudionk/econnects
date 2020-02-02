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
            <div class="col-md-12">
                <!-- col-separator.box -->
                <!-- col-separator.box -->
                <div class="section-header">
                    <ol class="breadcrumb">
                        <li class="active"><?php echo app_recurso_nome();?></li>
                        <li class="active"><?php echo $page_subtitle;?></li>
                    </ol>

                </div>

                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                        <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                            <i class="fa fa-edit"></i> Salvar
                        </a>
                    </div>

                </div>
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
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <h4 class="text-primary"><?php echo $page_subtitle;?></h4>
                                        </div>
                                        <!-- // Widget heading END -->

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <?php $this->load->view('admin/partials/validation_errors');?>
                                                    <?php $this->load->view('admin/partials/messages'); ?>
                                                </div>

                                            </div>
                                            <!-- Row -->
                                            <div class="row innerLR">

                                                <!-- Column -->
                                                <div class="col-md-6">

                                                    <?php $field_name = 'cliente_evolucao_status_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Status *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php foreach($evolucao_status as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row[$field_name])){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['descricao']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- // Column END -->
                                                
                                                <!-- Column -->
                                                <div class="col-md-6">
                                                    <a  href="" class="btn  btn-app btn-primary" data-toggle="modal" data-target="#add_cliente_status">
                                                        <i class="fa  fa-plus"></i> Novo
                                                    </a>
                                                </div>
                                                <!-- // Column END -->
                                            </div>
                                            <!-- // Row END -->
                                            
                                        </div>
                                    </div>
                                    <!-- // Widget END -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-arrow-left"></i> Voltar
                                            </a>
                                            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                                                <i class="fa fa-edit"></i> Salvar
                                            </a>
                                        </div>

                                    </div>

                                    <!-- Modal -->
                                    <div id="add_cliente_status" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Adcionar Status</h4>
                                                </div>
                                                <div class="modal-body cliente_status_modal">

                                                 <!-- Modal Form -->
                                                            
                                                    <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                                    <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                                                    <!-- Widget -->
                                                    <div class="card">

                                                        <!-- Widget heading -->
                                                        <div class="card-body">
                                                            <h4 class="text-primary"><?php echo $page_subtitle;?></h4>
                                                        </div>
                                                        <!-- // Widget heading END -->

                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <?php $this->load->view('admin/partials/validation_errors');?>
                                                                    <?php $this->load->view('admin/partials/messages'); ?>
                                                                </div>
                                                            </div>
                                                            <!-- Row -->
                                                            <div class="row innerLR">

                                                                <!-- Column -->
                                                                <div class="col-md-6">
                                                                    <?php $field_name = 'descricao';?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Descrição *</label>
                                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                 
                                                                </div>
                                                                <!-- // Column END -->
                                                            </div>
                                                            <!-- // Row END -->
                                                            
                                                        </div>
                                                    </div>
                                                    <!-- // Widget END -->
                                                    <div class="card">

                                                        <!-- Widget heading -->
                                                        <div class="card-body">
                                                            <button type="button" class="btn  btn-app btn-primary" data-dismiss="modal">
                                                                <i class="fa fa-arrow-left"></i> Voltar
                                                            </button>
                                                            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                                                                <i class="fa fa-edit"></i> Salvar
                                                            </a>
                                                        </div>

                                                    </div>
                                                                    
                                                <!-- // Modal Form END -->
                                                </div>
                                                
                                            </div>   
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
