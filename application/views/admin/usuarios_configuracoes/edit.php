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
                    <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                        <i class="fa fa-edit"></i> Salvar
                    </a>
                </div>

            </div>
            <!-- col-separator.box -->
            <div class="col-separator col-unscrollable bg-none box col-separator-first">

                <!-- col-table -->
                <div class="col-table">


                    <div class="col-separator-h"></div>

                    <!-- col-table-row -->
                    <div class="col-table-row">

                        <!-- col-app -->
                        <div class="col-app col-unscrollable">

                            <!-- col-app -->
                            <div class="col-app">

                                <!-- Form -->
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
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
                                                    <br>
                                                    <h4>Dados de Cadastro</h4>
                                                    <hr>
                                                    <br>
                                                    <?php $field_name = 'nome';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Nome *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>
                                                    
                                                    <?php $field_name = 'cpf';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">CPF *</label>
                                                        <div class="col-md-8"><input class="form-control inputmask-cpf" placeholder="___.___.___-__" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'colaborador_cargo_id';?>
                                                    <input class="form-control inputmask-cpf" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="hidden" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" />

                                                    <br>
                                                    <h4>Dados de acesso</h4>
                                                    <hr>
                                                    <br>
                                                    <?php $field_name = 'email';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">E-mail *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>
                                                    <?php $field_name = 'senha';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Senha *</label>
                                                        <div class="col-md-8">
                                                            <input class="form-control <?php if($new_record) echo 'required';?>" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="password"  autocomplete="off" />
                                                            <?php if(!$new_record) :?>
                                                            <p class="help-block">Deixar campo em branco caso não deseja alterar a senha.</p>
                                                            <?php endif;?>
                                                        </div>
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