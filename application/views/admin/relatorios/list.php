
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
        <header>Relatório de Vendas</header>
    </div>

    <div class="card-body">

        <p>Selecione uma data inicial e final para resgatar os registros. Arreste os campos para as linhas, colunas ou dados.</p>

        <div class="row">
            <div class="col-md-12">
                <?php $field_name = "data_inicio"; $field_label = "Data inicial: " ?>
                <div class="col-md-3 col-sm-4 form-group">
                    <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                    <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo date("d/m/Y",strtotime("-1 month")) ?> ?>" />
                </div>

                <?php $field_name = "data_fim"; $field_label = "Data final: " ?>
                <div class="col-md-3 col-sm-4 form-group">
                    <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                    <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo date("d/m/Y") ?> ?>" />
                </div>

                <div class="col-md-2 col-sm-4">
                    <button id="btnFiltro" class="btn btn-primary btnFiltrarResultadoRelatorios"><i class="fa fa-search"> </i>  Filtrar dados</button>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div id="relatorio_container"></div>
            </div>
        </div>




    </div>
</div>
