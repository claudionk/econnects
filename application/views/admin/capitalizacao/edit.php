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
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
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
                                                    <br>
                                                    <h4>Dados da Capitalização</h4>
                                                    <hr>
                                                    <br>
                                                     <?php $field_name = 'capitalizacao_tipo_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php

                                                                foreach($tipo as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                     <?php $field_name = 'capitalizacao_sorteio_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Sorteio *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php

                                                                foreach($sorteio as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'tipo_qnt_sorteio';?>
                                                    <div class="form-group radio radio-styled">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo de Qtde. de Sorteios *</label>
                                                        <div class="col-md-8">
                                                            <label class="radio-inline">
                                                                <input type="radio" id="<?php echo $field_name; ?>2" name="<?php echo $field_name; ?>" class="required styled tipo_qnt_sorteio" value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                Fixa
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="<?php echo $field_name; ?>1" name="<?php echo $field_name; ?>" class="required styled tipo_qnt_sorteio" value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                Meses de Vigência
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'qnt_sorteio';?>
                                                    <div class="form-group qnt_sorteio <?php if (isset($row['tipo_qnt_sorteio']) && $row['tipo_qnt_sorteio'] == '1') echo 'hide'; ?>">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Quantidade de Sorteios</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'nome';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Nome *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'descricao';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Descrição *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
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

                                                    <?php $field_name = 'data_primeiro_sorteio';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Primeiro Sorteio</label>
                                                        <div class="col-md-8"><input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? app_dateonly_mysql_to_mask($row[$field_name]) : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'dia_corte';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Dia de Corte</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'qtde_titulos_por_compra';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Quantidade de Títulos por compra *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'num_remessa';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Número Seqüencial Remessa *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'tipo_custo';?>
                                                    <div class="form-group radio radio-styled">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo de Custo *</label>
                                                        <div class="col-md-8">
                                                            <label class="radio-inline">
                                                                <input type="radio" id="<?php echo $field_name; ?>2" name="<?php echo $field_name; ?>" class="required styled tipo_custo" value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                Variável
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="<?php echo $field_name; ?>1" name="<?php echo $field_name; ?>" class="required styled tipo_custo" value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                Fixo (não precisa ser informado)
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'valor_custo_titulo';?>
                                                    <div class="form-group valor_custo_titulo <?php if (isset($row['tipo_custo']) && $row['tipo_custo'] == '1') echo 'hide'; ?>">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Custo do título *</label>
                                                        <div class="col-md-8"><input class="form-control" ui-number-mask="10" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div> <!-- //Alterado para Quero Quero -->                                                               
                                                    </div>

                                                    <?php $field_name = 'valor_minimo_participacao';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Valor Mínimo *</label>
                                                        <div class="col-md-8"><input class="form-control inputmask-valor" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'valor_sorteio';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Valor do sorteio *</label>
                                                        <div class="col-md-8"><input class="form-control inputmask-valor" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'titulo_randomico';?>
                                                    <div class="form-group radio radio-styled">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Forma de distribuição *</label>
                                                        <div class="col-md-8" > 
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                Randômico
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                Sequencial
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <?php $field_name = 'serie';?>
                                                    <div class="form-group radio radio-styled">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Série *</label>
                                                        <div class="col-md-8" > 
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                Fechada
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                Aberta
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <?php $field_name = 'responsavel_num_sorte';?>
                                                    <div class="form-group radio radio-styled">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Responsável por <br>Gerar o número da Sorte *</label>
                                                        <div class="col-md-8" > 
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                Integração
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                Parceiro
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="2" <?php if (isset($row[$field_name]) && $row[$field_name] == '2') echo 'checked="checked"'; ?> />
                                                                Manual
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <?php $field_name = 'ativo';?>
                                                    <div class="form-group radio radio-styled">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Ativo *</label>
                                                        <div class="col-md-8" > 
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

<script type="text/javascript">
jQuery(function($){
    $('.tipo_custo').change(function(){
        if ( $(this).val() == 1 )
        {
            $('#valor_custo_titulo').val('0,0000000000'); //Alterado para Quero Quero
            $('.valor_custo_titulo').addClass('hide');
        } else {
            $('.valor_custo_titulo').removeClass('hide');
        }
    });

    $('.tipo_qnt_sorteio').change(function(){
        if ( $(this).val() == 0 )
        {
            $('.qnt_sorteio').removeClass('hide');
        } else {
            $('.qnt_sorteio').addClass('hide');
        }
    });
});
</script>
