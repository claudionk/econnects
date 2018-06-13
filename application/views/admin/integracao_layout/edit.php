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
                    <a href="<?php echo base_url("{$current_controller_uri}/index/{$integracao_id}")?>" class="btn  btn-app btn-primary">
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
                                    <input type="hidden" name="integracao_id" value="<?php echo $integracao_id; ?>"/>
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

                                                    <h4>Dados cadastrais</h4>
                                                    <hr>
                                                    <?php $field_name = 'tipo';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php

                                                                foreach($tipo as $key => $value) { ?>
                                                                    <option name="" value="<?php echo $key; ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $key) {echo " selected ";};}; ?> >
                                                                        <?php echo $value; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'campo_tipo';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo Campo *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php

                                                                foreach($campo_tipo as $key => $value) { ?>
                                                                    <option name="" value="<?php echo $key; ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $key) {echo " selected ";};}; ?> >
                                                                        <?php echo $value; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <?php $field_name = 'nome';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Nome *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'descricao';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Descrição *</label>
                                                        <div class="col-md-8">
                                                            <textarea name="<?php echo $field_name;?>" id="<?php echo $field_name;?>" class="form-control" rows="3" placeholder=""><?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?></textarea>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'formato';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Formato *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>
                                                    <?php $field_name = 'tamanho';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tamanho *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'obrigatorio';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Obrigatório *</label>
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

                                                    <?php $field_name = 'inicio';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Início *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'fim';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Fim *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'nome_banco';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Nome no Banco *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'function';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Function *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'valor_padrao';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Valor Padrão *</label>
                                                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        <?php $field_name = 'qnt_valor_padrao';?>
                                                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>



                                                    <!-- // Column END -->


                                                </div>
                                                <!-- // Row END -->

                                            </div>
                                        </div>
                                    </div>

                                    <!-- // Widget END -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/index/{$integracao_id}")?>" class="btn  btn-app btn-primary">
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