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
                    <li class="active"><?php echo $page_subtitle;?></li>
                </ol>

            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("{$current_controller_uri}/view/{$qualificacao_id}")?>" class="btn  btn-app btn-primary">
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
                                    <input type="hidden" name="qualificacao_id" value="<?php echo $qualificacao_id; ?>"/>
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

                                                    <?php $field_name = 'pergunta';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Pergunta</label>
                                                        <div class="col-md-10"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'objetivo';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Objetivo</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'qualificacao_categoria_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Categoria</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option  value="">Selecione</option>
                                                                <?php

                                                                foreach($categorias as $linha) { ?>
                                                                    <option  value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'qualificacao_criterio_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Critério</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option  value="">Selecione</option>
                                                                <?php

                                                                foreach($criterios as  $linha) { ?>
                                                                    <option  value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'peso';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Peso</label>
                                                        <div class="col-md-2"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'regua_logica';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Regua</label>
                                                        <div class="col-md-3">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option  value="">Selecione</option>
                                                                <?php

                                                                foreach($reguas as  $linha_value => $linha_name) { ?>
                                                                    <option  value="<?php echo $linha_value ?>"
                                                                        <?php if(isset($row[$field_name])){if($row[$field_name] == $linha_value) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha_name; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>


                                                </div>
                                                <!-- // Column END -->


                                            </div>
                                            <!-- // Row END -->
                                            
                                        </div>
                                    </div>
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/view/{$qualificacao_id}")?>" class="btn  btn-app btn-primary">
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