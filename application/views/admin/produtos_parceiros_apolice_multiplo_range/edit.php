<?php
if($_POST)
    $row = $_POST;
?>

<div class="section-header">
        <ol class="breadcrumb">
            <li class="active"><?php echo app_recurso_nome();?></li>
        </ol>
    </div>

    <div class="card">

        <!-- Widget heading -->
        <div class="card-body">
            <a href="<?php echo base_url("admin/produtos_parceiros_apolice_multiplo_range/index/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-edit"></i> Salvar
            </a>
        </div>

    </div>

    <form method="post" id="validateSubmitForm" class="form-horizontal">

    <!-- Widget -->
    <div class="card">

        <div class="card-body">
            <h4 class="text-primary"><?php echo $page_subtitle;?></h4>

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
                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Descrição *</label>
                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                    </div>
                    <?php $field_name = 'numero_inicio';?>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Número Inicio *</label>
                        <div class="col-md-8"><input <?php if($new_record == '0') : echo 'readonly'; endif; ?> class="form-control loadNumberEnd" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                    </div>
                    <?php $field_name = 'numero_fim';?>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Número Final</label>
                        <div class="col-md-8"><input readonly class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" style="color:#999" /></div>
                    </div>

                    <?php $field_name = 'quantidade';?>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Quantidade *</label>
                        <div class="col-md-8"><input <?php if($new_record == '0') : echo 'readonly'; endif; ?> class="form-control loadNumberEnd" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                    </div>

                    <?php if($new_record == '0') : ?>
                    <?php $field_name = 'sequencia';?>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Número Atual</label>
                        <div class="col-md-8"><input <?php if($new_record == '0') : echo 'readonly'; endif; ?> class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                    </div>
                    <?php endif; ?>

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

                </div>
                <!-- // Column END -->


            </div>
            <!-- // Row END -->
            
        </div>
    </div>
    <!-- // Widget END -->

    <div class="card">
        <div class="card-body">
            <h4 class="text-primary">Produtos</h4>

            <label>* Selecione os Produtos que irão compor o range da Apólice</label>
            
                <input type="hidden" name="parceiro_id" value="<?php echo $parceiro_id;?>" />
                <input type="hidden" name="produto_parceiro_apolice_multiplo_range_id" value="<?php echo $produto_parceiro_apolice_multiplo_range_id;?>" />

                <ul class="list acoes">

                    <?php foreach($produtos as $prod) : ?>

                        <li class="">

                            <div idAcao='1' class="checkbox checkbox-inline checkbox-styled">
                                <label>
                                    <input <?php echo (!empty($prod['ok'])) ? 'checked' : ''; ?> idAcao='1' idRecurso='<?php echo $prod['produto_parceiro_id'] ?>' class='btRecurso' type="checkbox" name='produto_parceiro_id[<?php echo $prod['produto_parceiro_id'] ?>]'>
                                    
                                    <?php echo $prod['nome_prod_parc'];?>
                                </label>
                             </div>

                        </li>

                    <?php endforeach;?>
                </ul>

            
        </div>
    </div>

    </form>

    <div class="card">

        <!-- Widget heading -->
        <div class="card-body">
            <a href="<?php echo base_url("admin/parceiros/index")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-edit"></i> Salvar
            </a>
        </div>

    </div>


<script type="text/javascript">
jQuery(function($){

    $('.loadNumberEnd').bind('keyup', function() {
        debugger;
        var ini = $('#numero_inicio').val();
        var fim = $('#quantidade').val();

        ini = ini > 0 ? parseInt(ini) : 0;
        fim = fim > 0 ? parseInt(fim) : 0;
        $('#numero_fim').val(ini + fim);
    });

});
</script>