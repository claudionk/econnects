
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
        <header>Relatório de Venda Direta</header>
    </div>

    <div class="card-body">
        <form action="" method="POST">

            <div class="row">
                    <?php $field_name = "produto_parceiro_id"; $field_label = "Produto: " ?>
                    <div class="col-md-4 form-group">
                        <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                        <select class="form-control" name="<?php echo $field_name;?>">
                            <option value="">Informe o produto</option>
                            <?php 
                            foreach ($combo as $k => $v) {
                                $selected = (!empty($_POST['produto_parceiro_id']) && $_POST['produto_parceiro_id'] == $v['produto_parceiro_id']) ? 'selected="selected"' : "";

                                echo "<option slug=".$v['slug_produto']." value=".$v['produto_parceiro_id']." {$selected}>".$v['nome_prod_parc']."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <?php $field_name = "data_inicio"; $field_label = "Data inicial: " ?>
                    <div class="col-md-2 col-sm-4 form-group">
                        <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                        <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?= $data_inicio ?>" />
                    </div>

                    <?php $field_name = "data_fim"; $field_label = "Data final: " ?>
                    <div class="col-md-2 col-sm-4 form-group">
                        <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                        <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?= $data_fim ?>" />
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <button type="submit" id="btnFiltro" class="btn btn-primary"><i class="fa fa-search"> </i>  Filtrar dados</button>
                        <button type="submit" name="btnExcel" value="S" class="btn btn-primary btnExportExcel"><i class="fa fa-cloud-download"> </i>  Exportar Excel</button>
                    </div>
                </div>
            </div>
        </form>

        <?php if ( !empty($result) ) { ?>
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table">
                    <tr class="style-primary">
                        <td class="text-center">CONSOLIDADO</td>
                        <td class="text-center">%</td>
                        <td class="text-center">TOTAL</td>

                        <?php foreach ($result['dias'] as $data) { ?>
                        <td class="text-center"><?php echo $data['dia']; ?></td>
                        <?php } ?>
                    </tr>
                    <tr class="tb-linhaBranco">
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr class="style-primary">
                        <td>VENDAS</td>
                        <td class="text-center">X%</td>
                        <td class="text-center">X</td>

                        <?php foreach ($result['dias'] as $data) { ?>
                        <td class="text-center tb-titulo-cinza">a</td>
                        <?php } ?>
                    </tr>

                    <!-- PLANOS -->
                    <?php foreach ($result['planos'] as $plano) { ?>
                    <tr class="tb-titulo-cinza">
                        <td><?php echo $plano['nome']; ?></td>
                        <td class="text-center">X%</td>
                        <td class="text-center"><?php echo $plano['qtde']; ?></td>
                        <?php foreach ($result['dias'] as $data) { ?>
                        <td class="text-center tb-conteudo">
                            <?php echo emptyor($result['data']['vendas'][$plano['produto_parceiro_plano_id']][$data['dia_format']], 0 ); ?>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                    <tr class="tb-linhaBranco">
                        <td colspan="4">&nbsp;</td>
                    </tr>

                    <!-- GRUPOS / STATUS -->
                    <?php
                    $descricao_grupo = '';
                    foreach ($result['grupos'] as $grupo)
                    {
                        // valida se deve fazer a quebra do grupo
                        if ( $descricao_grupo != $grupo['descricao_grupo'] ) {
                            if ( !empty($descricao_grupo) ) {
                            ?>
                            <tr class="tb-linhaBranco">
                                <td colspan="4">&nbsp;</td>
                            </tr>
                            <?php } ?>

                        <tr class="style-primary">
                            <td><?php echo $grupo['descricao_grupo']; ?></td>
                            <td class="text-center"><?php echo emptyor($result['grupos_totais'][$grupo['cliente_evolucao_status_grupo_id']]['percentual'], 0 ); ?>%</td>
                            <td class="text-center"><?php echo emptyor($result['grupos_totais'][$grupo['cliente_evolucao_status_grupo_id']]['valor'], 0 ); ?></td>
                            <?php foreach ($result['dias'] as $data) { ?>
                            <td class="text-center tb-titulo-cinza">b</td>
                            <?php } ?>
                        </tr>
                        <?php 
                            $descricao_grupo = $grupo['descricao_grupo'];
                        } ?>

                    <tr class="tb-titulo-cinza">
                        <td><?php echo $grupo['descricao']; ?></td>
                        <td class="text-center">1%</td>
                        <td class="text-center"><?php echo $grupo['qtde']; ?></td>
                        <?php foreach ($result['dias'] as $data) { ?>
                        <td class="text-center tb-conteudo"><?php echo emptyor($result['data']['mailing'][$grupo['produto_parceiro_cliente_status_id']][$data['dia_format']], 0 ); ?></td>
                        <?php } ?>
                    </tr>
                    <?php } ?>

                </table>


                <table class="table table-striped">
                    <tr>
                        <?php /*
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
                                <td><?= $row['segurado'] ?></td>
                                <td><?= $row['documento'] ?></td>
                                <td><?= $row['plano_nome'] ?></td>
                                <td><?= $row['nome_produto_parceiro'] ?></td>
                                <td><?= app_format_currency($row['nota_fiscal_valor'], true) ?></td>
                                <td><?= app_format_currency($row['premio_liquido_total'], true) ?></td>
                                <td><?= $row['num_apolice'] ?></td>
                                <td><?= $row['nome_fantasia'] ?></td>
                                <td><?= $row['cnpj'] ?></td>
                                <td><?= $row['UF'] ?></td>
                                <td><?= $row['vendedor'] ?></td>
                            </tr>
                            <?php
                        }
                    }
                } */
                ?>
                </table>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<style type="text/css">
.tb-titulo-cinza {
    background-color: #ddd !important;
    border: 1px solid #ccc;
    color: initial;
}
.tb-conteudo {
    background-color: #fff !important;
    color: initial;
}
.tb-linhaBranco td {
    line-height: 0.3px !important;
}
</style>