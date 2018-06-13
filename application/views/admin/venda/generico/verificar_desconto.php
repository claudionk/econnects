<?php
if($_POST)
    $row = $_POST;
?>

<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome();?></li>
    </ol>
</div>

<!-- // Widget END -->
<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
        <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Próximo
        </a>
    </div>
</div>



<div class="card">
    <div class="card-body">

        <!-- Form -->
        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" action="<?php echo base_url("admin/venda/seguro_viagem_verificar_desconto/{$produto_parceiro_id}/{$cotacao_id}") ?>">

            <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
            <input type="hidden" name="finalizar_desconto_aprovado" value="1">
            <!-- Widget -->
            <div class="row">

                <div class="card-body">

                    <?php $this->load->view('admin/venda/step', array('step' => 3, 'produto_parceiro_id' =>  issetor($produto_parceiro_id))); ?>

                    <br>
                    <h4>Dados iniciais para Cotação</h4>
                    <hr>
                    <br>

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

                            <?php foreach ($seguro_equipamento as $equipamento) : ?>
                                <br>
                                <h4>PLANO: <?php echo $equipamento['plano']; ?></h4>
                                <hr>
                                <br>

                                <?php $field_name = 'motivo';?>
                                <?php if(isset($equipamento[$field_name]) && !empty($equipamento[$field_name])) :?>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Motivo:</label>
                                        <div class="col-md-8"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? $equipamento[$field_name] : set_value($field_name); ?>" /></div>
                                    </div>
                                <?php endif; ?>

                                <?php $field_name = 'email';?>
                                <?php if(isset($equipamento[$field_name]) && !empty($equipamento[$field_name])) :?>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Email Segurado:</label>
                                        <div class="col-md-8"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? $equipamento[$field_name] : set_value($field_name); ?>" /></div>
                                    </div>
                                <?php endif; ?>

                                <?php $field_name = 'origem';?>
                                <?php if(isset($equipamento[$field_name]) && !empty($equipamento[$field_name])) :?>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Origem:</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? $equipamento[$field_name] : set_value($field_name); ?>" /></div>
                                        <?php $field_name = 'destino';?>
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Destino:</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? $equipamento[$field_name] : set_value($field_name); ?>" /></div>
                                    </div>
                                <?php endif; ?>
                                <?php $field_name = 'data_saida';?>
                                <?php if(isset($equipamento[$field_name]) && !empty($equipamento[$field_name])) :?>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Data Saída:</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? app_date_mysql_to_mask($equipamento[$field_name], 'd/m/Y') : set_value($field_name); ?>" /></div>
                                        <?php $field_name = 'data_retorno';?>
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Data Retorno:</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? app_date_mysql_to_mask($equipamento[$field_name], 'd/m/Y')  : set_value($field_name); ?>" /></div>
                                    </div>
                                <?php endif; ?>


                                <?php $field_name = 'qnt_dias';?>
                                <?php if(isset($equipamento[$field_name]) && !empty($equipamento[$field_name])) :?>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Dias:</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? $equipamento[$field_name] : set_value($field_name); ?>" /></div>
                                        <?php $field_name = 'num_passageiro';?>
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Passageiros:</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? $equipamento[$field_name] : set_value($field_name); ?>" /></div>
                                    </div>
                                <?php endif; ?>

                                <?php $field_name = 'repasse_comissao';?>
                                <?php if(isset($equipamento[$field_name]) && !empty($equipamento[$field_name])) :?>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Desconto (%):</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? app_format_currency($equipamento[$field_name]) : set_value($field_name); ?>" /></div>
                                        <?php $field_name = 'comissao_corretor';?>
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão (%):</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? app_format_currency($equipamento[$field_name])  : set_value($field_name); ?>" /></div>
                                    </div>
                                <?php endif; ?>
                                <?php $field_name = 'desconto_condicional';?>
                                <?php if(isset($equipamento[$field_name]) && !empty($equipamento[$field_name])) :?>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Desc. Cond. (%):</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? app_format_currency($equipamento[$field_name]) : set_value($field_name); ?>" /></div>
                                        <?php $field_name = 'premio_liquido';?>
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Prêmio Líquido:</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? app_format_currency($equipamento[$field_name])  : set_value($field_name); ?>" /></div>
                                    </div>
                                <?php endif; ?>

                                <?php $field_name = 'iof';?>
                                <?php if(isset($equipamento[$field_name]) && !empty($equipamento[$field_name])) :?>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">IOF (%):</label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? app_format_currency($equipamento[$field_name]) : set_value($field_name); ?>" /></div>
                                        <?php $field_name = 'premio_liquido_total';?>
                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Prêmio Total: </label>
                                        <div class="col-md-3"><input class="form-control" readonly id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($equipamento[$field_name]) ? app_format_currency($equipamento[$field_name])  : set_value($field_name); ?>" /></div>
                                    </div>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        </div>
                        <!-- // Column END -->


                    </div>
                    <!-- // Row END -->
                    <div class="separator"></div>
                    <!-- // Form actions END -->
                </div>
            </div>
            <!-- // Widget END -->

        </form>
        <!-- // Form END -->

    </div>
</div>

<!-- // Widget END -->
<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
        <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Próximo
        </a>
    </div>
</div>