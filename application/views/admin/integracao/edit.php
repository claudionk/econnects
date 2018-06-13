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

                                                    <?php $field_name = 'parceiro_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Parceiro *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php

                                                                foreach($parceiro as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
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


                                                    <?php $field_name = 'slug';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Slug *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'descricao';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Descrição *</label>
                                                        <div class="col-md-8">
                                                            <textarea name="<?php echo $field_name;?>" id="<?php echo $field_name;?>" class="form-control" rows="3" placeholder=""><?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?></textarea>
                                                        </div>
                                                    </div>
                                                    <h4>Configurações</h4>
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

                                                        <?php $field_name = 'ambiente';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Ambiente *</label>
                                                            <div class="col-md-8">
                                                                <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                    <option name="" value="">Selecione</option>
                                                                    <?php

                                                                    foreach($ambiente as $key => $value) { ?>
                                                                        <option name="" value="<?php echo $key; ?>"
                                                                            <?php if(isset($row)){if($row[$field_name] == $key) {echo " selected ";};}; ?> >
                                                                            <?php echo $value; ?>
                                                                        </option>
                                                                    <?php }  ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <?php $field_name = 'integracao_comunicacao_id';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Comunicação *</label>
                                                            <div class="col-md-8">
                                                                <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                    <option name="" value="">Selecione</option>
                                                                    <?php

                                                                    foreach($comunicacao as $linha) { ?>
                                                                        <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                            <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                            <?php echo $linha['nome']; ?>
                                                                        </option>
                                                                    <?php }  ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <?php $field_name = 'periodicidade_unidade';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Periodicidade *</label>
                                                            <div class="col-md-3">
                                                                <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                    <option name="" value="">Selecione</option>
                                                                    <?php

                                                                    foreach($periodicidade_unidade as $key => $value) { ?>
                                                                        <option name="" value="<?php echo $key; ?>"
                                                                            <?php if(isset($row)){if($row[$field_name] == $key) {echo " selected ";};}; ?> >
                                                                            <?php echo $value; ?>
                                                                        </option>
                                                                    <?php }  ?>
                                                                </select>
                                                            </div>
                                                            <?php $field_name = 'periodicidade';?>
                                                            <div class="col-md-2"><input class="form-control" placeholder="Quantidade" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            <?php $field_name = 'periodicidade_hora';?>
                                                            <div class="col-md-3"><input class="form-control time-mask" placeholder="__:__" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>
                                                        <?php $field_name = 'campo_chave';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Campo Chave *</label>
                                                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>
                                                        <?php $field_name = 'script_sql';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Script SQL *</label>
                                                            <div class="col-md-8">
                                                                <textarea name="<?php echo $field_name;?>" id="<?php echo $field_name;?>" class="form-control" rows="10" placeholder=""><?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?></textarea>
                                                                <div>Campos: {data_ini_mes_anterior}, {data_fim_mes_anterior}, {data_ini_mes}, {data_fim_mes}</div>
                                                            </div>
                                                        </div>
                                                        <?php $field_name = 'before_execute';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Antes de Executar </label>
                                                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>

                                                        <?php $field_name = 'after_execute';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Depois de Executar </label>
                                                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>

                                                        <?php $field_name = 'before_detail';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Antes de Executar Detalhe</label>
                                                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>

                                                        <?php $field_name = 'after_detail';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Depois de Executar Detalhe</label>
                                                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>

                                                        <?php $field_name = 'habilitado';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Habilitado *</label>
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

                                                        <h4>Dados de Conexão</h4>
                                                        <hr>

                                                        <?php $field_name = 'host';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Host *</label>
                                                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>
                                                        <?php $field_name = 'porta';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Porta *</label>
                                                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>
                                                        <?php $field_name = 'usuario';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Usuário *</label>
                                                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>
                                                        <?php $field_name = 'senha';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Senha *</label>
                                                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>
                                                        <?php $field_name = 'diretorio';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Diretório *</label>
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