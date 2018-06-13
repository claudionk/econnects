<?php
$tamanhos = array(
    2 => 'Muito pequeno - 2',
    4 => 'Pequeno - 4',
    6 => 'Médio - 6',
    8 => 'Grande - 8',
    12 => 'Muito Grande - 12',
);

?>
<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">

            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>

            </div>

            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>



                <!-- Widget -->
                <div class="card">

                    <!-- // Widget heading END -->
                    <?php $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>
                    <div class="card-body">

                        <div class="relativeWrap">
                            <div class="widget widget-tabs widget-tabs-double-2 widget-tabs-responsive">

                                <!-- Tabs Heading -->

                                <!-- // Tabs Heading END -->

                                <div class="widget-body">

                                    <div class="card tabs-left style-default-light">
                                        <!-- Tab content -->
                                        <?php  $this->load->view('admin/produtos_parceiros_configuracao/sub_tab_produto');?>
                                        <div class="card-body tab-content style-default-bright">
                                            <div class="tab-content">

                                        <input type="hidden" name="url_ordem" id="url_ordem" value="<?php echo  base_url("$current_controller_uri/set_ordem/{$produto_parceiro_id}"); ?>">
                                        <input type="hidden" name="url_tipo" id="url_tipo" value="<?php echo  base_url("$current_controller_uri/index/{$produto_parceiro_id}/"); ?>">
                                        <!-- Tab content -->
                                        <div id="tabCampo" class="tab-pane active widget-body-regular">
                                            <div class="row innerTB">


                                                <?php $field_name = 'campo_tipo_id';?>
                                                <label class="col-md-2 control-label" style="text-align: right;" for="<?php echo $field_name;?>">Tipo de campo:</label>
                                                <div class="col-md-5">
                                                    <select class="form-control campo_tipo" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                        <?php

                                                        foreach($campo_tipo as $linha) { ?>
                                                            <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                <?php if(isset($campo_tipo_id)){if($campo_tipo_id == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                <?php echo $linha['nome']; ?>
                                                            </option>
                                                        <?php }  ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-5">

                                                    <a href="<?php echo base_url("$current_controller_uri/add/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary pull-right">
                                                        <i class="fa  fa-plus"></i> Adicionar
                                                    </a>

                                                    <a onclick='salvarOrdem()' class="btn  btn-app btn-primary pull-right">
                                                        <i class="fa  fa-sort-amount-asc"></i> Salvar ordem
                                                    </a>
                                                </div>



                                            </div>


                                            <!-- Table -->
                                            <table id="tabela-ordem" class="table table-hover">

                                                <!-- Table heading -->
                                                <thead>
                                                <tr>
                                                    <th class="center">Ordem</th>
                                                    <th width='45%'>Nome</th>
                                                    <th width='25%'>Tamanho</th>
                                                    <th class="center" width='25%'>Ações</th>
                                                </tr>
                                                </thead>
                                                <!-- // Table heading END -->

                                                <!-- Table body -->
                                                <tbody>

                                                <!-- Table row -->
                                                <?php $i = 0; ?>
                                                <?php foreach($rows as $row) :?>
                                                <tr data-id="<?php echo $row[$primary_key];?>" data-ordem="<?php echo $i; ?>" >

                                                    <td class="center"><?php echo $row['ordem'];?></td>
                                                    <td><?php echo $row['campo_nome'];?></td>
                                                    <td><?php echo isset($tamanhos[$row['tamanho']]) ? $tamanhos[$row['tamanho']] : '--'; ?></td>
                                                    <td class="center">
                                                        <a href="<?php echo base_url("{$current_controller_uri}/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                                        <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                                    </td>
                                                </tr>

                                                <?php $i++; ?>
                                                <?php endforeach; ?>
                                                <!-- // Table row END -->

                                                </tbody>
                                                <!-- // Table body END -->

                                            </table>
                                             <!-- // Table END -->
                                        </div>
                                    </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>