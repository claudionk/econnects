<?php
if($_POST)
    $row = $_POST;
?>
<form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off"
      xmlns="http://www.w3.org/1999/html" enctype="multipart/form-data">

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

    <div class="card">

        <!-- Widget heading -->
        <div class="card-body">

            <!-- Form -->
            <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
            <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
            <!-- Widget -->


            <!-- Widget heading -->
            <h4 class="text-primary"><?php echo $page_subtitle;?></h4>

            <!-- // Widget heading END -->

            <div class="row">
                <div class="col-md-6">
                    <?php $this->load->view('admin/partials/validation_errors');?>
                    <?php $this->load->view('admin/partials/messages'); ?>
                </div>
            </div>

            <!-- Row -->
            <div class="row">

                <!-- Column -->
                <div class="col-md-12">

                    <?php $field_name = 'comunicacao_tipo_id'; ?>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Tipo de comunicação *</label>
                        <div class="col-md-4">
                            <?php echo app_get_select_html($field_name, 'nome', $comunicacao_tipos, issetor($row[$field_name]), 'chave', 'Selecione', 'style="width: 100%;" class="form-control"'); ?>
                        </div>
                    </div>

                    <?php $field_name = 'comunicacao_engine_configuracao_id';?>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Configuração de Engine</label>
                        <div class="col-md-4">
                            <?php echo app_get_select_html($field_name, 'nome', $comunicacoes_engines_configuracoes, issetor($row[$field_name]), 'chave', 'Selecione', 'style="width: 100%;" class="form-control"'); ?>
                        </div>
                    </div>

                    <?php $field_name = 'descricao';?>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Descrição *</label>
                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                    </div>

                    <?php $field_name = 'slug';?>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Slug *</label>
                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                    </div>

                    <?php $field_name = 'mensagem_titulo';?>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Título *</label>
                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                    </div>

                    <?php $field_name = 'mensagem_de';?>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Remetente *</label>
                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                    </div>

                    <?php $field_name = 'mensagem';?>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Template da mensagem *</label>
                        <div class="col-md-8">
                            <textarea class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text"  /><?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?></textarea>
                            <?php echo display_ckeditor($ckeditor); ?>
                        </div>
                    </div>

                    <?php $field_name = 'mensagem_anexo';?>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Anexos *</label>
                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="file" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
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
