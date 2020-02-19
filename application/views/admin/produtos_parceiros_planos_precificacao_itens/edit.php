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
                    <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_plano_id}")?>" class="btn  btn-app btn-primary">
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
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
                                    <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                    <input type="hidden" name="produto_parceiro_plano_id" value="<?php echo $produto_parceiro_plano_id; ?>"/>
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

                                                    <?php $field_name = 'cobranca';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Cobrança *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <option name="" value="VALOR"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'VALOR') {echo " selected ";};}; ?> >Valor
                                                                </option>
                                                                <option name="" value="PORCENTAGEM"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'PORCENTAGEM') {echo " selected ";};}; ?> >Porcentagem
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'tipo';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <option name="" value="RANGE"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'RANGE') {echo " selected ";};}; ?> >Range
                                                                </option>
                                                                <option name="" value="ADICIONAL"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'ADICIONAL') {echo " selected ";};}; ?> >Adicional
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'unidade_tempo';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Unidade *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <option name="" value="DIA"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'DIA') {echo " selected ";};}; ?> >Dia
                                                                </option>
                                                                <option name="" value="MES"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'MES') {echo " selected ";};}; ?> >Mês
                                                                </option>
                                                                <option name="" value="ANO"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'ANO') {echo " selected ";};}; ?> >Ano
                                                                </option>
                                                                <option name="" value="VALOR"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'VALOR') {echo " selected ";};}; ?> >Valor
                                                                </option>
                                                                <option name="" value="IDADE"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'IDADE') {echo " selected ";};}; ?> >Idade
                                                                </option>
                                                                <option name="" value="COMISSAO"
                                                                    <?php if(isset($row)){if($row[$field_name] == 'COMISSAO') {echo " selected ";};}; ?> >Comissão
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <?php $field_name = 'inicial';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Início *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? number_format( $row[$field_name] , 2 , "," , "") : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'final';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Fim *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? number_format( $row[$field_name] , 2 , "," , "")  : set_value($field_name); ?>" /></div>
                                                    </div>


                                                    <?php $field_name = 'valor';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Valor *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? number_format( $row[$field_name] , 15 , "," , "")  : set_value($field_name); ?>" /></div>
                                                    </div>

                                                  <?php 
                                                  if (!empty($descTipo)) {

                                                  $field_name = 'equipamento'; ?>
                                                  <div class="form-group">
                                                    <label class="col-md-4 control-label" for="<?php echo $field_name;?>"><?= $descTipo ?></label>

                                                    <div class="col-md-8"><select name="<?php echo $field_name;?>[]" data-selected="<?php echo isset($row[$field_name]) ?  $row[$field_name] : ''; ?>" id="js-<?= $descSelect ?>-ajax" class="form-control js-<?= $descSelect ?>-ajax" style="width: 100%;" multiple="multiple">
                                                      </select>

                                                    </div>
                                                    <?php echo app_get_form_error($field_name); ?>
                                                  </div>

                                                    <?php $field_name = 'equipamento_de_para'; ?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Equipamento DE x PARA</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>
                                                <?php } ?>

                                                <input type="hidden" name="precificacao_tipo_id" id="precificacao_tipo_id" value="<?= $precificacao_tipo_id ?>">

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
                                            <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_plano_id}")?>" class="btn  btn-app btn-primary">
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