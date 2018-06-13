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

                                                    <?php $field_name = 'pai_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">menu Principal (Pai) *</label>
                                                        <div class="col-md-9">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <?php foreach ($list_pai as $index => $item) : ?>
                                                                    <option  value="<?php echo $index; ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $index) {echo " selected ";};}; ?> >
                                                                        <?php echo $item; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>



                                                    <?php $field_name = 'nome';?>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Nome *</label>
                                                        <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'slug';?>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Slug *</label>
                                                        <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'controller';?>

                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Controller *</label>
                                                        <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'acao';?>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Ação *</label>
                                                        <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'parametros';?>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Parametros *</label>
                                                        <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>


                                                    <?php $field_name = 'externo';?>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Acesso Externo</label>
                                                        <div class="col-sm-9">
                                                            <label class="radio-inline radio-styled">
                                                                <input type="radio" name="<?php echo $field_name;?>" value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?>><span>Sim</span>
                                                            </label>
                                                            <label class="radio-inline radio-styled">
                                                                <input type="radio" name="<?php echo $field_name;?>" value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?>><span>Não</span>
                                                            </label>
                                                        </div><!--end .col -->
                                                    </div>

                                                    <?php $field_name = 'url';?>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">URL *</label>
                                                        <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'target';?>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Destino (Target) *</label>
                                                        <div class="col-md-9">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option  value="_self"
                                                                    <?php if(isset($row)){if($row[$field_name] == '_self') {echo " selected ";};}; ?> >
                                                                    Mesma Página
                                                                </option>
                                                                <option  value="_blank"
                                                                    <?php if(isset($row)){if($row[$field_name] == '_blank') {echo " selected ";};}; ?> >
                                                                    Nova página
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php $field_name = 'exibir_menu';?>
                                                    <div class="radio radio-styled">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Exibir Menu *</label>
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

                                                </div>
                                                <!-- // Column END -->


                                            </div>
                                            <!-- // Row END -->
                                        </div>
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