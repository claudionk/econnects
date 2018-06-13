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
                    <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
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
                                    <!-- Widget -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <h4 class="text-primary"><?php echo $page_subtitle;?></h4>
                                        </div>
                                        <!-- // Widget heading END -->

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <?php $this->load->view('admin/partials/validation_errors');?>
                                                    <?php $this->load->view('admin/partials/messages'); ?>
                                                </div>

                                            </div>
                                            <!-- Row -->
                                            <div class="row innerLR">

                                                <!-- Column -->
                                                <div class="col-md-6">

                                                    <?php $field_name = 'email';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">E-mail</label>
                                                        <div class="col-md-8"><input class="form-control required" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" autocomplete="off" /></div>
                                                    </div>
                                                    
                                                    <?php $field_name = 'senha';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Senha</label>
                                                        <div class="col-md-8">
                                                            <input class="form-control <?php if($new_record) echo 'required';?>" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="password"  autocomplete="off" />
                                                            <?php if(!$new_record) :?>
                                                            <p class="help-block">Deixa o campo senha em branco se não deseja alterar a senha atual.</p>
                                                            <?php endif;?>
                                                            </div>
                                                    </div>

                                                    <?php $field_name = 'ativo';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Ativo</label>
                                                        <div class="col-md-2">

                                                                <select  class="form-control required" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                    <?php foreach(array( '1' => 'Sim', '0' => 'Não') as $item_value => $item_name) :?>
                                                                        <option value="<?php echo $item_value;?>" <?php  if(isset($row[$field_name]) && $row[$field_name] == $item_value) echo 'selected="selected"'; ?>><?php echo $item_name;?></option>
                                                                    <?php endforeach;?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- // Column END -->
                                            </div>
                                            <!-- // Row END -->
                                            
                                        </div>
                                    </div>
                                    <!-- // Widget END -->
                                    <!-- Widget heading -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
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