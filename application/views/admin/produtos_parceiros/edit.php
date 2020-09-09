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
                    <a href="<?php echo base_url("{$current_controller_uri}/view_by_parceiro/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
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
                                    <input type="hidden" name="parceiro_id" value="<?php echo $parceiro_id; ?>"/>
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

                                                    <?php $field_name = 'nome';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>"> Nome fantasia</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>
                                                    <?php $field_name = 'slug_produto';?> 
                                                    <div class="form-group"> 
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Slug *</label> 
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div> 
                                                    </div>
                                                    <?php $field_name = 'codigo_susep';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Código SUSEP</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>
                                                    <?php $field_name = 'cod_tpa';?> 
                                                    <div class="form-group"> 
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Código TPA</label> 
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div> 
                                                    </div>
                                                    <?php $field_name = 'cod_sucursal';?> 
                                                    <div class="form-group"> 
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Código Sucursal</label> 
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div> 
                                                    </div>
                                                    <?php $field_name = 'cod_ramo';?> 
                                                    <div class="form-group"> 
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Código Ramo</label> 
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div> 
                                                    </div>
                                                    <?php $field_name = 'produto_ramo_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Ramo / Produto *</label>
                                                        <div class="col-md-4">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php

                                                                foreach($produtos_ramos as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row['produto_ramo_id'])){if($row['produto_ramo_id'] == $linha[$field_name]) {echo " selected ";};}; ?>
                                                                        <?php if(isset($row['produto_produto_ramo_id'])){if($row['produto_produto_ramo_id'] == $linha[$field_name]) {echo " selected ";};}; ?>
                                                                        >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                        <?php $field_name = 'produto_id';?>
                                                        <div class="col-md-4">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php

                                                                foreach($produtos as $linha) { ?>
                                                                    <option name="" class="<?php echo $linha['produto_ramo_id'] ?>" value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <?php $field_name = 'seguradora_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Parceiro</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option  value="">Selecione</option>
                                                                <?php

                                                                foreach($seguradoras as $linha) { ?>
                                                                    <option  value="<?php echo $linha['parceiro_id'] ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha['parceiro_id']) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php $field_name = 'lista_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Lista de Produtos</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option  value="">Selecione</option>
                                                                <?php

                                                                foreach($listas as $linha) { ?>
                                                                    <option  value="<?php echo $linha['lista_id'] ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha['lista_id']) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php $field_name = 'venda_agrupada';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Venda Multi Parceiros *</label>
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
                                    <!-- // Widget END -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/view_by_parceiro/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
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
