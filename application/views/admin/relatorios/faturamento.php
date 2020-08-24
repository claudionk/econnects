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
                <td> <button type="submit" name="btnAnalitico" value="A" class="btn btn-primary btnExportExcel"><i class="fa fa-cloud-download"> </i>  Analítico</button>
                     <button type="submit" name="btnResumo" value="R" class="btn btn-primary btnExportExcel"><i class="fa fa-cloud-download"> </i>  Resumo</button>
                     <button type="submit" name="btnSaldo" value="S" class="btn btn-primary btnExportExcel"><i class="fa fa-cloud-download"> </i>  Saldo</button>
                </td>
            </tr>
            <?php
        }
    }
}
?>
</table>