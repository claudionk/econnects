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
                    <li class="active"><?php echo $page_subtitle;?></li>
                </ol>

            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("{$current_controller_uri}/index/{$capitalizacao_id}")?>" class="btn  btn-app btn-primary">
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
                                    <input type="hidden" name="capitalizacao_id" value="<?php echo $capitalizacao_id; ?>"/>
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

                                                    <?php $field_name = 'numero_inicio';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Número Inicio *</label>
                                                        <div class="col-md-8"><input <?php if($new_record == '0') : echo 'readonly'; endif; ?> class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>
                                                    <?php $field_name = 'numero_fim';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Número Final</label>
                                                        <div class="col-md-8"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'quantidade';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Quantidade *</label>
                                                        <div class="col-md-8"><input <?php if($new_record == '0') : echo 'readonly'; endif; ?> class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'data_inicio';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Início Distribuição</label>
                                                        <div class="col-md-8"><input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? app_dateonly_mysql_to_mask($row[$field_name]) : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'data_fim';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Fim Distribuição</label>
                                                        <div class="col-md-8"><input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? app_dateonly_mysql_to_mask($row[$field_name]) : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'num_serie';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Número da Série <?php echo (!empty($responsavel_num_sorte)) ? '*' : ''; ?></label>
                                                        <div class="col-md-8"><input <?php echo (!empty($required_num_serieresponsavel_num_sorte)) ? 'required' : ''; ?> class="form-control <?php echo (!empty($responsavel_num_sorte)) ? 'required' : ''; ?>" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'serie_aberta';?>
                                                    <div class="form-group radio radio-styled">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo de Série *</label>
                                                        <div class="col-md-8">
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                Aberta
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                Fechada
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'ativo';?>
                                                    <div class="form-group radio radio-styled">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Ativo *</label>
                                                        <div class="col-md-8">
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                Sim
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                Não
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'solicita_range';$solicita_range = issetor($row[$field_name], set_value($field_name)); ?>
                                                    <div class="form-group <?php echo ( $solicita_range != 2 ) ? 'radio radio-styled' : ''; ?>">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Solicitar Range de Número da Sorte</label>
                                                    <?php
                                                    /*
                                                    0 - Nao solicita
                                                    1 - Solicitar
                                                    2 - Solicitado - Ag Retorno
                                                    */
                                                    if ( $solicita_range == 2 )
                                                    {

                                                        ?>
                                                        <div class="col-md-8 alert-warning">
                                                            Série Solicitada. Aguardando Retorno
                                                            <input id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="hidden" value="<?php echo $solicita_range; ?>" />
                                                        </div>
                                                        <?php 

                                                    } else { 

                                                        ?><div class="col-md-8">
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="1" <?php if ($solicita_range == '1') echo 'checked="checked"'; ?> />
                                                                Sim
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="0" <?php if ($solicita_range == '0') echo 'checked="checked"'; ?> />
                                                                Não
                                                            </label>
                                                        </div>

                                                    <?php } ?>

                                                    </div>

                                                    <?php $field_name = 'responsavel_num_sorte';?>
                                                    <input id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="hidden" value="<?php echo (!empty($responsavel_num_sorte)) ? '1' : '0'; ?>" />

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
                                            <a href="<?php echo base_url("{$current_controller_uri}/index/{$capitalizacao_id}")?>" class="btn  btn-app btn-primary">
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