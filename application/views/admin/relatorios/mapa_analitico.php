<style type="text/css">
    .tm_col_min_100 { min-width: 100px; }
    .tm_col_min_150 { min-width: 150px; }
    .tm_col_min_200 { min-width: 200px; }
</style>
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
                <td class="tm_col_min_200"><?= $row['plano_nome'] ?></td>
                <td class="tm_col_min_200"><?= $row['representante'] ?></td>
                <td class="tm_col_min_150"><?= $row['cobertura'] ?></td>
                <td class="tm_col_min_150"><?= $row['venda_cancelamento'] ?></td>
                <td><?= $row['data_emissao'] ?></td>
                <td><?= $row['ini_vigencia'] ?></td>
                <td><?= $row['fim_vigencia'] ?></td>
                <td><?= $row['num_apolice'] ?></td>
                <td class="tm_col_min_150"><?= $row['segurado_nome'] ?></td>
                <td><?= $row['documento'] ?></td>
                <td><?= $row['equipamento'] ?></td>
                <td class="tm_col_min_150"><?= $row['marca'] ?></td>
                <td class="tm_col_min_200"><?= $row['modelo'] ?></td>
                <td class="tm_col_min_150"><?= $row['imei'] ?></td>
                <td class="tm_col_min_150"><?= $row['nome_produto_parceiro'] ?></td>
                <td><?= $row['importancia_segurada'] ?></td>
                <td class="tm_col_min_150"><?= $row['forma_pagto'] ?></td>
                <td><?= $row['num_endosso'] ?></td>
                <td><?= $row['vigencia_parcela'] ?></td>
                <td><?= $row['parcela'] ?></td>
                <td><?= $row['status_parcela'] ?></td>
                <td class="tm_col_min_150"><?= $row['data_processamento_cli_sis'] ?></td>
                <td class="tm_col_min_150"><?= $row['data_cancelamento'] ?></td>
                <td class="tm_col_min_150"><?= app_format_currency($row['valor_parcela'], true) ?></td>
                <td class="tm_col_min_150"><?= app_format_currency($row['PB_RF'], true) ?></td>
                <td class="tm_col_min_150"><?= app_format_currency($row['PL_RF'], true) ?></td>
                <td class="tm_col_min_150"><?= app_format_currency($row['PB_QA'], true) ?></td>
                <td class="tm_col_min_150"><?= app_format_currency($row['PL_QA'], true) ?></td>
                <td class="tm_col_min_150"><?= app_format_currency($row['pro_labore'], true) ?></td>
                <td class="tm_col_min_150"><?= app_format_currency($row['valor_comissao'], true) ?></td>
            </tr>
            <?php
        }
    }
}
?>
</table>