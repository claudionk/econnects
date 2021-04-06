<script type="text/javascript">
    function validaFiltroData() {
        if (document.frmOpcao.data_inicio.value == '' || 
            document.frmOpcao.data_fim.value == ''){
            alert('Informe a Data');
            return false;
        }
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

        <form name="frmOpcao" action="" method="POST" onSubmit="return validaFiltroData()">
         
         <div class="row">
            <p>Selecione um representante.</p>
            <div class="col-md-12 form-group">
                <select class="form-control" name="representante">
                    <option value="">Informe o representante</option>
                    <?php 
                    foreach ($combo as $k => $v) {
                        $selected = (!empty($_POST['representante']) && $_POST['representante'] == $v['parceiro_id']) ? 'selected="selected"' : "";

                        echo "<option slug=".$v['slug']." value=".$v['parceiro_id']." {$selected}>".$v['nome']."</option>";
                    }
                    ?>
                </select>
            </div>
         </div>

         <div class="row">
            <p>Selecione uma data inicial e final para resgatar os registros.</p>
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
                <button <?php if($layout == 'processamento_venda') {echo "disabled='disabled'";}?> type="submit" id="btnFiltro" class="btn btn-primary"><i class="fa fa-search"> </i>  Filtrar dados</button>
                <button type="submit" name="btnExcel" value="S" class="btn btn-primary btnExportExcel"><i class="fa fa-cloud-download"> </i>  Exportar Excel</button>
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
