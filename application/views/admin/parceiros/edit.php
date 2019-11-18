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
                    <div class="col-md-row">

                        <!-- col-app -->
                        <div class="col-app col-unscrollable">

                            <!-- col-app -->
                            <div class="col-app">

                                <!-- Form -->
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
                                    <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                    <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                                  	<input type="hidden" name="parceiro_pai_id" value="<?php if (isset($row['parceiro_pai_id'])) echo $row['parceiro_pai_id']; ?>"/>
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



                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h3 class="panel-title">Dados de Cadastro</h3>
                                                        </div>
                                                        <div class="panel-body">
                                                            <?php $field_name = 'nome';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Razão Social</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'nome_fantasia';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Nome Fantasia</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'apelido';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Apelido</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>


                                                            <?php $field_name = 'slug';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">URL de acesso:</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'cnpj';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">CNPJ</label>
                                                                <div class="col-md-9"><input class="form-control input-sm inputmask-cnpj" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                            <?php $field_name = 'codigo_susep';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Código SUSEP</label>
                                                                <div class="col-md-9"><input class="form-control input-sm" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                            <?php $field_name = 'codigo_sucursal';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Código Sucursal</label>
                                                                <div class="col-md-9"><input class="form-control input-sm" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                            <?php $field_name = 'codigo_corretor';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Código Corretor</label>
                                                                <div class="col-md-9"><input class="form-control input-sm" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'matriz_id';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Matriz</label>
                                                                <div class="col-md-9">
                                                                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                        <option name="" value="">-- Nenhuma --</option>

                                                                        <?php foreach($matriz as $linha) {  ?>
                                                                            <option name="" value="<?php echo $linha['parceiro_id'] ?>"
                                                                                <?php if(isset($row[$field_name])){if($row[$field_name] == $linha['parceiro_id']) {echo " selected ";};}; ?> >
                                                                                <?php echo $linha['nome']; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <?php $field_name = 'parceiro_tipo_id';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Tipo</label>
                                                                <div class="col-md-9">
                                                                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                        <option name="" value="">-- Selecione --</option>
                                                                        <?php

                                                                        foreach($tipos_parceiros as $linha) {  ?>
                                                                            <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                                <?php if(isset($row[$field_name])){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                                <?php echo $linha['nome']; ?>
                                                                            </option>
                                                                        <?php }  ?>
                                                                    </select>
                                                                </div>
                                                            </div>


                                                            <?php $field_name = 'parceiro_status_id';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Status</label>
                                                                <div class="col-md-9">
                                                                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                        <option name="" value="">-- Selecione --</option>
                                                                        <?php

                                                                        foreach($status as $linha) { ?>
                                                                            <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                                <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                                <?php echo $linha['nome']; ?>
                                                                            </option>
                                                                        <?php }  ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>





                                                </div>
                                                <!-- // Column END -->


                                                <div class="col-md-6">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h3 class="panel-title">Endereço</h3>
                                                        </div>
                                                        <div class="panel-body">
                                                            <?php $field_name = 'cep';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">CEP</label>
                                                                <div class="col-md-6"><input class="form-control inputmask-cep" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                <div class="col-md-3"><a class="btn btn-default buscarCEP" href="#" role="button">Buscar</a></div>
                                                            </div>

                                                            <?php $field_name = 'endereco';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Endereço</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <div class="form-group">
                                                                <?php $field_name = 'numero';?>
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Número</label>
                                                                <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>

                                                                <?php $field_name = 'complemento';?>
                                                                <label class="col-md-1 control-label" for="<?php echo $field_name;?>">Compl.</label>
                                                                <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'bairro';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Bairro</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>

                                                            </div>

                                                            <?php $field_name = 'localidade_estado_id';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Estado</label>
                                                                <div class="col-md-9">
                                                                    <select class="form-control comboEstado" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                        <option name="" value="">-- Selecione --</option>
                                                                        <?php
                                                                        foreach($estados as $linha) {  ?>
                                                                            <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                                <?php if(isset($row[$field_name])){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                                <?php echo $linha['nome']; ?>
                                                                            </option>
                                                                        <?php }  ?>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <?php $field_name = 'localidade_cidade_id';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Cidade</label>
                                                                <div class="col-md-9">
                                                                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                        <option name="" value="">-- Selecione --</option>
                                                                        <?php

                                                                        foreach($cidades as $linha) {  ?>
                                                                            <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                                <?php if(isset($row[$field_name])){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                                <?php echo $linha['nome']; ?>
                                                                            </option>
                                                                        <?php }  ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <br/>
                                                                <br/>
                                                                <br/>
                                                                <br/>
                                                                <br/>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row"></div>
                                                <div class="col-md-6">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h3 class="panel-title">Dados da Extranet</h3>
                                                        </div>
                                                        <div class="panel-body">
                                                            <?php $field_name = 'extranet_url';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">URL</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'extranet_codigo_acesso';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Código de Acesso</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'extranet_senha';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Senha</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h3 class="panel-title">Acesso a API</h3>
                                                        </div>
                                                        <div class="panel-body">
                                                            <?php $field_name = 'api_host';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">IP:</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'api_key';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Chave de acesso:</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'api_senha';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Senha de acesso:</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                 <div class="col-md-6">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h3 class="panel-title">WhatsApp</h3>
                                                        </div>
                                                        <div class="panel-body">
                                                            <?php $field_name = 'whatsapp_num';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Número:</label>
                                                                <div class="col-md-9"><input class="form-control inputmask-celular" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>

                                                            <?php $field_name = 'whatsapp_msg';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Mensagem Padrão:</label>
                                                                <div class="col-md-9"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row"></div>
                                                <div class="col-md-12">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h3 class="panel-title">Aparência</h3>
                                                        </div>
                                                        <div class="panel-body">
                                                            <?php $field_name = 'logo';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Logo</label>
                                                                <div class="col-md-9">
                                                                    <?php if(isset($row[$field_name]) && $row[$field_name] != '') :?>
                                                                        <p>
                                                                            <img src="<?php echo $base_image_url . $row[$field_name];?>"  width="200"/>
                                                                        </p>
                                                                    <?php endif;?>
                                                                    <input id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="file" value="<?php echo $field_name; ?>" />
                                                                    <input id="<?php echo $field_name;?>-antiga" name="<?php echo $field_name;?>-antiga" type="hidden" value="<?php if(isset($row[$field_name])) {echo $row[$field_name];}; ?>" />

                                                                </div>
                                                            </div>
                                                            <?php $field_name = 'theme';?>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label" for="<?php echo $field_name;?>">Tema</label>
                                                                <div class="col-md-9">
                                                                    <div class="row">
                                                                        <div class="col-sm-4">
                                                                            <div class="card card-tiles <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-default') echo 'card-outlined style-default-dark'; ?>">
                                                                                <div class="row">
                                                                                    <div class="col-xs-8">
                                                                                        <div class="card-body height-1" style="background-color:#0aa89e"></div>
                                                                                    </div>
                                                                                    <div class="col-xs-4">
                                                                                        <div class="card-body height-1" style="background-color:#9C27B0"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body small-padding text-center">
                                                                                    <div class="radio radio-styled">
                                                                                        <label class="radio-inline">
                                                                                            <input type="radio" name="<?php echo $field_name; ?>" class="required styled"
                                                                                                   value="theme-default" <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-default') echo 'checked="checked"'; ?> />
                                                                                            Tema Padrão
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!--end .card -->
                                                                        </div><!--end .col -->
                                                                        <div class="col-sm-4">
                                                                            <div class="card card-tiles <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-1') echo 'card-outlined style-default-dark'; ?>">
                                                                                <div class="row">
                                                                                    <div class="col-xs-8">
                                                                                        <div class="card-body height-1" style="background-color:#2196F3"></div>
                                                                                    </div>
                                                                                    <div class="col-xs-4">
                                                                                        <div class="card-body height-1" style="background-color:#673AB7"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body small-padding text-center">
                                                                                    <div class="radio radio-styled">
                                                                                        <label class="radio-inline">
                                                                                            <input type="radio" name="<?php echo $field_name; ?>" class="required styled"
                                                                                                   value="theme-1" <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-1') echo 'checked="checked"'; ?> />
                                                                                            Tema 1
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!--end .card -->
                                                                        </div><!--end .col -->
                                                                        <div class="col-sm-4">
                                                                            <div class="card card-tiles <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-2') echo 'card-outlined style-default-dark'; ?>">
                                                                                <div class="row">
                                                                                    <div class="col-xs-8">
                                                                                        <div class="card-body height-1" style="background-color:#ff5722"></div>
                                                                                    </div>
                                                                                    <div class="col-xs-4">
                                                                                        <div class="card-body height-1" style="background-color:#2196F3"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body small-padding text-center">
                                                                                    <div class="radio radio-styled">
                                                                                        <label class="radio-inline">
                                                                                            <input type="radio" name="<?php echo $field_name; ?>" class="required styled"
                                                                                                   value="theme-2" <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-2') echo 'checked="checked"'; ?> />
                                                                                            Tema 2
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!--end .card -->
                                                                        </div><!--end .col -->
                                                                    </div><!--end .row -->
                                                                    <div class="row">
                                                                        <div class="col-sm-4">
                                                                            <div class="card card-tiles <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-3') echo 'card-outlined style-default-dark'; ?>">
                                                                                <div class="row">
                                                                                    <div class="col-xs-8">
                                                                                        <div class="card-body height-1" style="background-color:#3f51b5"></div>
                                                                                    </div>
                                                                                    <div class="col-xs-4">
                                                                                        <div class="card-body height-1" style="background-color:#FFC107"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body small-padding text-center">
                                                                                    <div class="radio radio-styled">
                                                                                        <label class="radio-inline">
                                                                                            <input type="radio" name="<?php echo $field_name; ?>" class="required styled"
                                                                                                   value="theme-3" <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-3') echo 'checked="checked"'; ?> />
                                                                                            Tema 3
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!--end .card -->
                                                                        </div><!--end .col -->
                                                                        <div class="col-sm-4">
                                                                            <div class="card card-tiles <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-4') echo 'card-outlined style-default-dark'; ?>">
                                                                                <div class="row">
                                                                                    <div class="col-xs-8">
                                                                                        <div class="card-body height-1" style="background-color:#8BC34A"></div>
                                                                                    </div>
                                                                                    <div class="col-xs-4">
                                                                                        <div class="card-body height-1" style="background-color:#673AB7"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body small-padding text-center">
                                                                                    <div class="radio radio-styled">
                                                                                        <label class="radio-inline">
                                                                                            <input type="radio" name="<?php echo $field_name; ?>" class="required styled"
                                                                                                   value="theme-4" <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-4') echo 'checked="checked"'; ?> />
                                                                                            Tema 4
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!--end .card -->
                                                                        </div><!--end .col -->
                                                                        <div class="col-sm-4">
                                                                            <div class="card card-tiles <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-5') echo 'card-outlined style-default-dark'; ?>">
                                                                                <div class="row">
                                                                                    <div class="col-xs-8">
                                                                                        <div class="card-body height-1" style="background-color:#EB0038"></div>
                                                                                    </div>
                                                                                    <div class="col-xs-4">
                                                                                        <div class="card-body height-1" style="background-color:#607D8B"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body small-padding text-center">
                                                                                    <div class="radio radio-styled">
                                                                                        <label class="radio-inline">
                                                                                            <input type="radio" name="<?php echo $field_name; ?>" class="required styled"
                                                                                                   value="theme-5" <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-5') echo 'checked="checked"'; ?> />
                                                                                            Tema 5
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!--end .card  -->
                                                                        </div><!--end .col -->
                                                                    </div><!--end .row -->
                                                                    <div class="row">
                                                                        <div class="col-sm-4">
                                                                            <div class="card card-tiles <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-6') echo 'card-outlined style-default-dark'; ?>">
                                                                                <div class="row">
                                                                                    <div class="col-xs-8">
                                                                                        <div class="card-body height-1" style="background-color:#ff9800"></div>
                                                                                    </div>
                                                                                    <div class="col-xs-4">
                                                                                        <div class="card-body height-1" style="background-color:#666666"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body small-padding text-center">
                                                                                    <div class="radio radio-styled">
                                                                                        <label class="radio-inline">
                                                                                            <input type="radio" name="<?php echo $field_name; ?>" class="required styled"
                                                                                                   value="theme-6" <?php if (isset($row[$field_name]) && $row[$field_name] == 'theme-6') echo 'checked="checked"'; ?> />
                                                                                            Tema 6
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!--end .card -->
                                                                        </div><!--end .col -->
                                                                    </div><!--end .row -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>


                                                <div class="row"></div>
                                                <div class="col-md-12">
                                                <?php $field_name = 'termo_aceite_usuario';?>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="<?php echo $field_name;?>">termo de aceite usuário *</label>
                                                        <div class="col-md-9">
                                                            <textarea class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text"  /><?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?></textarea>
                                                            <?php echo display_ckeditor($ckeditor); ?>
                                                        </div>
                                                    </div>
                                                </div>
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

