<script type="text/javascript">
function validaRepresentante() {
    if (document.frmOpcao.representante.value == ''){
        alert('Informe o representante');
        return false;
    }
    else
    {
        var _slug = document.frmOpcao.representante.value;
        $('#getslug').val($("select[name='representante'] option:selected").attr("slug"));
        $('#getSelecionado').val($("select[name='representante'] option:selected").text());
        return true;
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
        <form name="frmOpcao" action="" method="POST" onSubmit="return validaRepresentante()">
        <div class="row">
            <p>Selecione um representante.</p>
            <div class="col-md-12 form-group">
                <select class="form-control" name="representante">
                    <option value="">Informe o representante</option>
                    <?php 
                    foreach ($combo as $k => $v) {
                        echo "<option slug=".$v['slug']." value=".$v['parceiro_id'].">".$v['nome']."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row">
            <p>Selecione uma data inicial e final para resgatar os registros.</p>
        </div>

        
        <div class="row">
            <div class="col-md-12 form-group">
                <?php $field_name = "layout"; $field_label = "Visualização: " ?>
                <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                <div class="form-check checkbox-inline">
                  <input type="radio" class="form-check-input" id="<?php echo $field_name ?>0" name="<?= $field_name ?>" value="mapa_analitico" <? if ($layout == "mapa_analitico") {echo "checked='checked'";} ?> >
                  <label class="form-check-label" for="<?php echo $field_name ?>0">Analítico</label>
                </div>
                <div class="form-check checkbox-inline">
                  <input type="radio" class="form-check-input" id="<?php echo $field_name ?>1" name="<?php echo $field_name ?>" value="mapa_sintetico" <? if ($layout == "mapa_sintetico") {echo "checked='checked'";} ?> >
                  <label class="form-check-label" for="<?php echo $field_name ?>1">Sintético</label>
                </div>
            </div>
        </div>
        <div class="row">
            
            <?php $field_name = "data_inicio"; $field_label = "Data inicial: " ?>
            <div class="col-md-3 col-sm-4 form-group">
                <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?= $data_inicio ?>" />
            </div>

            <?php $field_name = "data_fim"; $field_label = "Data final: " ?>
            <div class="col-md-3 col-sm-4 form-group">
                <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?= $data_fim ?>" />
            </div>

            <div class="col-md-6 col-sm-4">
                <input id="getslug" name="slug" type="hidden" value='' />
                <input id="getSelecionado" name="nomerepresentante" type="hidden" value='' />
                <button type="submit" id="btnFiltro" class="btn btn-primary"><i class="fa fa-search"> </i>  Filtrar dados</button>
                <button type="submit" name="btnExcel" value="S" class="btn btn-primary btnExportExcel"><i class="fa fa-cloud-download"> </i>  Exportar Excel</button>
            </div>

        </div>
        </form>
        <div class="row">
            <div class="col-md-12 table-responsive">
                <?php
                    // Exibir dados 
                    if($flag)
                        $this->load->view($src."/".$layout) 
                ?>
            </div>
        </div>

    </div>
</div>
