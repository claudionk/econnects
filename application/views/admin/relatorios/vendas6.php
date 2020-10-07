
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
        <header>Relatório 06 de Vendas</header>
    </div>

    <div class="card-body">

        <p>Selecione uma data inicial e final para resgatar os registros.</p>

        <div class="row">
            <form action="" method="POST">
            <div class="col-md-12">
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
                    <button disabled='disabled' type="submit" id="btnFiltro" class="btn btn-primary"><i class="fa fa-search"> </i>  Filtrar dados</button>
                    <button type="submit" name="btnExcel" value="S" class="btn btn-primary btnExportExcel"><i class="fa fa-cloud-download"> </i>  Exportar Excel</button>
                </div>

            </div>
            </form>
        </div>

        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped">
                    <tr>
                        <?php 
                if (isset($columns)) {
                        foreach ($columns as $col) { ?>
                            <th><?= $col ?></th>
                        <?php } ?>
                    </tr>
                <?php 
                }

                if (isset($result)) {
                    if (empty($result)) {
                        ?><tr>
                            <td colspan="8"> Nenhum resultado encontrado.</td>
                        </tr><?php
                    } else {

                        foreach ($result as $row) { ?>
                            <tr>
                                <td><?= app_date_mysql_to_mask($row['status_data'], 'd/m/Y') ?></td>
                                <td><?= $row['nome_fantasia'] ?></td>
								<td><?= $row['plano_nome'] ?></td>
								<td><?= $row['num_apolice'] ?></td>
								<td><?= $row['num_apolice_cliente'] ?></td>
								<td><?= $row['cod_loja'] ?></td>
								<td><?= $row['data_pedido'] ?></td>
								<td><?= $row['ini_vigencia'] ?></td>
								<td><?= $row['fim_vigencia'] ?></td>
                                <td></td>
								<td><?= $row['documento'] ?></td>
								<td><?= $row['data_nascimento'] ?></td>
								<td><?= $row['segurado'] ?></td>
								<td><?= $row['endereco_cidade'] ?></td>
								<td><?= $row['endereco_estado'] ?></td>
								<td><?= $row['endereco_cep'] ?></td>
								<td><?= $row['endereco_logradouro']?></td>
								<td><?= $row['tipo_equipamento'] ?></td>
								<td><?= $row['marca'] ?></td>
								<td><?= $row['modelo'] ?></td>
								<td><?= app_format_currency($row['nota_fiscal_valor'], true) ?></td>
								<td><?= app_format_currency($row['premio_liquido'], true) ?></td>
								<td><?= app_format_currency($row['premio_liquido_total'], true) ?></td>
								<td></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
                </table>
            </div>
        </div>
    </div>
</div>
