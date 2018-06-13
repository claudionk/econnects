<?php
    if($_POST != null)
        $row = $_POST;
    if(!isset($row['localidade_cidade_id']))
        $row['localidade_cidade_id'] = 0;
?>
<script>
    var base_url = '<?php echo base_url() ?>';

    $(document).ready(function()
    {
        <?php if(isset($estadoCidade)) : ?>
        buscaCidades(<?php echo $estadoCidade ?>, base_url, <?php if(isset($row['localidade_cidade_id'])) { echo $row['localidade_cidade_id']; } else { echo 0 ;} ?>, null);
        <?php endif; ?>
        wscep();
        busca();
    })
</script>


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
                    <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
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
                                                <br>
                                                <h4>Dados cadastrais</h4>
                                                <hr>
                                                <br>
                                                <!-- Column -->
                                                <div class="col-md-6">
                                                    <?php $field_name = 'tipo_cliente';?>
                                                    <input type="hidden" id="<?php echo $field_name ?>" name="<?php echo $field_name; ?>" value='CO' class="required styled"/>

                                                    <?php $field_name = 'codigo';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Código</label>
                                                        <div class="col-md-8"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                                    </div>


                                                    <?php $field_name = 'razao_nome';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Razão social *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'cnpj_cpf';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">CNPJ *</label>
                                                        <div class="col-md-8"><input class="form-control cnpj inputmask-cnpj" placeholder="__.___.___/____-__" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? app_cnpj_to_mask($row[$field_name]): set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'ie_rg';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Inscrição Estadual</label>
                                                        <div class="col-md-8"><input class="form-control ie_rg" placeholder="__.___.___-_" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>
                                                    <?php $field_name = 'nome_fantasia';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Nome fantasia</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'data_nascimento';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Data de fundação</label>
                                                        <div class="col-md-8"><input class="form-control inputmask-date" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? app_date_mysql_to_mask($row[$field_name]) : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'site';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Site</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'pabx';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">PABX</label>
                                                        <div class="col-md-8"><input class="form-control inputmask-telefone" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'cliente_grupo_empresarial_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Grupos Empresarial</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php foreach($gruposEmpresariais as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha[$field_name] ?>" 
                                                                        <?php if(isset($row[$field_name])){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select> 
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'cliente_evolucao_status_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Status *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php foreach($evolucao_status as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row[$field_name])){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['descricao']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'colaborador_comercial_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Responsável Comercial *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php 
                                                                
                                                                foreach($colaboradores as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha['colaborador_id'] ?>" 
                                                                        <?php if(isset($row['colaborador_comercial_id'])){if($row['colaborador_comercial_id'] == $linha['colaborador_id']) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select> 
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'colaborador_id';?>
                                                    <input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="hidden" value="
                                                        <?php echo isset($row[$field_name]) ? $row[$field_name] : ($this->session->userdata('colaborador_id')) ? $this->session->userdata('colaborador_id') : $this->session->userdata('usuario_id'); ?>" />


                                                    <br>
                                                    <h4>Endereço</h4>
                                                    <hr>
                                                    <br>
                                                    <?php $field_name = 'cep';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">CEP *</label>
                                                        <div class="col-md-8"><input class="form-control inputmask-cep" placeholder="_____-___" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>
                                                    <?php $field_name = 'estados';?>
                                                    <div class='form-group'>
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Estado *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name ='<?php echo $field_name;?>' id='<?php echo $field_name;?>' onchange='buscaCidades($(this).val(), base_url, 0, null)'/>
                                                            <option value='0'>Selecione</option>
                                                            <?php foreach ($estados as $estado): ?>
                                                                <option
                                                                    <?php if(isset($estadoCidade) && ($estado['localidade_estado_id'] == $estadoCidade)) { echo 'selected'; } ?>
                                                                    value="<?php echo $estado['localidade_estado_id']?>"><?php echo $estado['sigla'] ?></option>

                                                            <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php $field_name = 'localidade_cidade_id';?>
                                                    <div class='form-group'>
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Cidade *</label>
                                                        <div class="col-md-8">
                                                            <select id="<?php echo $field_name;?>" class="form-control" name="<?php echo $field_name;?>[]">
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'bairro';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Bairro *</label>
                                                        <div class="col-md-8"><input class="form-control"  id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'endereco';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Logradouro *</label>
                                                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        <?php $field_name = 'numero';?>
                                                        <div class="col-md-2"><input placeholder="Nº" class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        <?php $field_name = 'complemento';?>
                                                        <div class="col-md-2"><input placeholder="Comple." class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
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