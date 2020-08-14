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
                <td><?= $row['parceiro'] ?></td>
                <td><?= $row['fatura_parceiro_lote_id'] ?></td>
                <td><?= $row['data_corte'] ?></td>
                <td><?= $row['gera_oficial'] ?></td>
                <td><?= $row['data_processamento'] ?></td>
                <td><?= $row['data_processamento'] ?></td>
            </tr>
            <?php
        }
    }
}
?>
</table>