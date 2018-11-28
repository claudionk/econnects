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
                <td><?= $row['operacao'] ?></td>
                <td><?= $row['grupo'] ?></td>
                <td><?= $row['cobertura'] ?></td>
                <td><?= $row['data_emissao'] ?></td>
                <td><?= $row['ini_vigencia'] ?></td>
                <td><?= $row['fim_vigencia'] ?></td>
                <td><?= $row['num_apolice'] ?></td>
                <td><?= $row['segurado_nome'] ?></td>
                <td><?= $row['documento'] ?></td>
                <td><?= $row['equipamento'] ?></td>
                <td><?= $row['marca'] ?></td>
                <td><?= $row['modelo'] ?></td>
                <td><?= $row['imei'] ?></td>
                <td><?= $row['nome_produto_parceiro'] ?></td>
                <td><?= $row['importancia_segurada'] ?></td>
                <td><?= $row['num_endosso'] ?></td>
                <td><?= $row['vigencia_parcela'] ?></td>
                <td><?= $row['parcela'] ?></td>
                <td><?= $row['status_parcela'] ?></td>
                <td><?= $row['data_cancelamento'] ?></td>
                <td><?= app_format_currency($row['valor_parcela'], true) ?></td>
                <td><?= app_format_currency($row['PB_RF'], true) ?></td>
                <td><?= app_format_currency($row['PL_RF'], true) ?></td>
                <td><?= app_format_currency($row['PB_QA'], true) ?></td>
                <td><?= app_format_currency($row['PL_QA'], true) ?></td>
                <td><?= app_format_currency($row['pro_labore'], true) ?></td>
                <td><?= app_format_currency($row['valor_comissao'], true) ?></td>
            </tr>
            <?php
        }
    }
}
?>
</table>