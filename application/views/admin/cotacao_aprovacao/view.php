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
                                                <div class="col-md-10">

                                                    <?php $field_name = 'codigo';?>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Código:</label>
                                                        <div class="col-md-8"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'motivo';?>
                                                    <?php if(isset($row[$field_name]) && !empty($row[$field_name])) :?>

                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Motivo:</label>
                                                            <div class="col-md-8"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php $field_name = 'motivo_obs';?>
                                                    <?php if(isset($row[$field_name]) && !empty($row[$field_name])) :?>

                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Motivo OBS:</label>
                                                            <div class="col-md-8"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php $field_name = 'motivo_ativo';?>
                                                    <?php if(isset($row[$field_name]) && !empty($row[$field_name])) :?>

                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Fazer Ativo:</label>
                                                            <div class="col-md-8"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php foreach ($seguro_viagem as $viagem) : ?>
                                                        <br>
                                                        <h4>PLANO: <?php echo $viagem['plano']; ?></h4>
                                                        <hr>
                                                        <br>

                                                        <?php $field_name = 'motivo';?>
                                                        <?php if(isset($viagem[$field_name]) && !empty($viagem[$field_name])) :?>

                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Motivo:</label>
                                                                <div class="col-md-8"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? $viagem[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php $field_name = 'email';?>
                                                        <?php if(isset($viagem[$field_name]) && !empty($viagem[$field_name])) :?>

                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Email Segurado:</label>
                                                                <div class="col-md-8"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? $viagem[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php $field_name = 'origem';?>
                                                        <?php if(isset($viagem[$field_name]) && !empty($viagem[$field_name])) :?>

                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Origem:</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? $viagem[$field_name] : set_value($field_name); ?>" /></div>
                                                                <?php $field_name = 'destino';?>
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Destino:</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? $viagem[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php $field_name = 'data_saida';?>
                                                        <?php if(isset($viagem[$field_name]) && !empty($viagem[$field_name])) :?>

                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Data Saída:</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? app_date_mysql_to_mask($viagem[$field_name], 'd/m/Y') : set_value($field_name); ?>" /></div>
                                                                <?php $field_name = 'data_retorno';?>
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Data Retorno:</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? app_date_mysql_to_mask($viagem[$field_name], 'd/m/Y')  : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        <?php endif; ?>


                                                        <?php $field_name = 'qnt_dias';?>
                                                        <?php if(isset($viagem[$field_name]) && !empty($viagem[$field_name])) :?>

                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Dias:</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? $viagem[$field_name] : set_value($field_name); ?>" /></div>
                                                                <?php $field_name = 'num_passageiro';?>
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Passageiros:</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? $viagem[$field_name] : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php $field_name = 'repasse_comissao';?>
                                                        <?php if(isset($viagem[$field_name]) && !empty($viagem[$field_name])) :?>

                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Repasse Comissão (%):</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? app_format_currency($viagem[$field_name]) : set_value($field_name); ?>" /></div>
                                                                <?php $field_name = 'comissao_corretor';?>
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão (%):</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? app_format_currency($viagem[$field_name])  : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php $field_name = 'desconto_condicional';?>
                                                        <?php if(isset($viagem[$field_name]) && !empty($viagem[$field_name])) :?>

                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Desc. Cond. (%):</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? app_format_currency($viagem[$field_name]) : set_value($field_name); ?>" /></div>
                                                                <?php $field_name = 'premio_liquido';?>
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Prêmio Líquido:</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? app_format_currency($viagem[$field_name])  : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php $field_name = 'iof';?>
                                                        <?php if(isset($viagem[$field_name]) && !empty($viagem[$field_name])) :?>

                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">IOF (%):</label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? app_format_currency($viagem[$field_name]) : set_value($field_name); ?>" /></div>
                                                                <?php $field_name = 'premio_liquido_total';?>
                                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Prêmio Total: </label>
                                                                <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($viagem[$field_name]) ? app_format_currency($viagem[$field_name])  : set_value($field_name); ?>" /></div>
                                                            </div>
                                                        <?php endif; ?>

                                                    <?php endforeach; ?>


                                                    <?php if(isset($desconto) && !empty($desconto['utilizado'])) :?>

                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Saldo R$ :</label>
                                                            <div class="col-md-3"><input class="form-control" readonly id="" name="saldo" type="text" value="<?php echo app_format_currency($desconto['valor'] - $desconto['utilizado'], false, 2); ?>" /></div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <!-- // Column END -->


                                            </div>
                                            <!-- // Row END -->
                                            <div class="separator"></div>
                                            <!-- // Form actions END -->
                                        </div>
                                    </div>
                                    <!-- // Widget END -->
                                    <div class="col-separator-h"></div>

                                    <div class="col-separator-h"></div>

                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-arrow-left"></i> Voltar
                                            </a>

                                            <a href="<?php echo base_url("{$current_controller_uri}/aprovar/{$row[$primary_key]}")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-check-square-o"></i> Aprovar Desconto
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