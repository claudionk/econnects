<script type="text/javascript">
    function validaFiltroData() {
        if (document.frmOpcao.data_fim.value == '' || 
            document.frmOpcao.data_inicio.value == ''){
            alert('Informe a Data');
            return false;
        }
    }
</script>

<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome();?></li>
    </ol>
</div>

<div class="row">
    <div class="col-md-6">
        <?php $this->load->view('admin/partials/messages'); ?>
    </div>
</div>

<!-- Widget -->
<div class="card">

    <div class="card-head style-primary">
        <header><?= $title ?></header>
    </div>

    <div class="card-body">

        <div class="row">
            <p>Selecione uma data inicial e final para resgatar os registros.</p>
        </div>

        <form name="frmOpcao" action="" method="POST" onSubmit="return validaFiltroData()">
         <div class="row">
         <?php if($layout != 'faturamento'){?>
            <?php $field_name = "data_inicio"; $field_label = "Data inicial: " ?>
            <div class="col-md-3 col-sm-4 form-group">
                <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?= $data_inicio ?>" />
            </div>
         <?php }?>
            <?php $field_name = "data_fim"; $field_label = "Data final: " ?>
            <div class="col-md-3 col-sm-3 form-group">
                <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?= $data_fim ?>" />
            </div>
            <?php if($layout == 'faturamento') {?>
            <div class="col-md-3 form-group">
                <?php $field_name = "gera_oficial"; $field_label = "Oficial? " ?>
                <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                <div class="form-check checkbox-inline">
                    <input type="radio" class="form-check-input" id="<?php echo $field_name ?>0" name="<?= $field_name ?>" value="N" checked='checked'>
                    <label class="form-check-label" for="<?php echo $field_name ?>0">Não</label>
                </div>
                <div class="form-check checkbox-inline">
                    <input type="radio" class="form-check-input" id="<?php echo $field_name ?>1" name="<?php echo $field_name ?>" value="S" >
                    <label class="form-check-label" for="<?php echo $field_name ?>1">Sim</label>
                </div>
            </div>
            <?php } ?>
            <div class="col-md-4 col-sm-4">
                <button <?php if($layout == 'processamento_venda') {echo "disabled='disabled'";}?> type="submit" id="btnFiltro" class="btn btn-primary"><i class="fa fa-search"> </i>  <?php if($layout == 'faturamento') {echo "Gerar Faturamento"; } else {echo "Filtrar dados";}?></button>
                <?php if($layout != 'faturamento'){?>
                    <button type="submit" name="btnExcel" value="S" class="btn btn-primary btnExportExcel"><i class="fa fa-cloud-download"> </i>  Exportar Excel</button>
                <?php }?>
            </div>

        </div>
        </form>

        <div class="row">
            <div class="col-md-12 table-responsive">
                <?php $this->load->view($src."/".$layout) ?>
            </div>
        </div>

    </div>
</div>
