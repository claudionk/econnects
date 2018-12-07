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
                <td><?= app_date_mysql_to_mask($row['DATA_PROCESSAMENTO'], 'd/m/Y') ?></td>
                <td><?= $row['ARQUIVO'] ?></td>
                <td><?= $row['STATUS'] ?></td>
                <td><?= $row['STATUS_PROCESSAMENTO'] ?></td>
                <td><?= $row['RESULTADO_PROCESSAMENTO'] ?></td>
                <td><?= $row['DETALHE_PROCESSAMENTO'] ?></td>
                <td><?= $row['CODIGO_TRANSACAO'] ?></td>
                <td><?= $row['DESCRIÇÃO_TRANSACAO'] ?></td>
                <td><?= $row['APOLICE'] ?></td>
                <td><?= $row['VIGENCIA'] ?></td>
                <td><?= $row['CPF'] ?></td>
                <td><?= $row['SEXO'] ?></td>
                <td><?= $row['ENDERECO'] ?></td>
                <td><?= $row['TELEFONE'] ?></td>
                <td><?= $row['COD_LOJA'] ?></td>
                <td><?= $row['COD_VENDEDOR'] ?></td>
                <td><?= $row['COD_PRODUTO_SAP'] ?></td>
                <td><?= $row['EAN'] ?></td>
                <td><?= $row['MARCA'] ?></td>
                <td><?= $row['EQUIPAMENTO'] ?></td>
                <td><?= app_format_currency($row['VALOR_NF'], true) ?></td>
                <td><?= app_date_mysql_to_mask($row['DATA_NF'], 'd/m/Y') ?></td>
                <td><?= $row['NRO_NF'] ?></td>
                <td><?= app_format_currency($row['PREMIO_BRUTO'], true) ?></td>
                <td><?= app_format_currency($row['PREMIO_LIQUIDO'], true) ?></td>
                <td><?= $row['FORMA_PAGAMENTO'] ?></td>
                <td><?= $row['NRO_PARCELA'] ?></td>
            </tr>
            <?php
        }
    }
}
?>
</table>