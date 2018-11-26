<style type="text/css">
    .rotate {
        /*writing-mode: vertical-rl !important;text-orientation: upright;*/
        -ms-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -webkit-transform: rotate(-90deg);
        transform: rotate(-90deg);
        font-size: 28px;
        margin: 0 -40px;
    }
    .baseLine td {
        border-bottom: 1px solid #c9c9c9;
    }
    .baseLineTD {
        border-bottom: 1px solid #c9c9c9;
        font-weight: bold;
    }
</style>
<table class="table table-striped">
    <tr>
        <td></td>
        <td></td>
        <td colspan="3" align="center" class="baseLineTD">VENDAS</td>
    </tr>

    <tr>
        <th></th>
        <th></th>
        <th>Roubo ou Furto</th>
        <th>Quebra Acidental</th>
        <th>TOTAL</th>
    </tr>

<?php
if (isset($result)) {
    if (empty($result)) {
        ?><tr>
            <td colspan="8"> Nenhum resultado encontrado.</td>
        </tr><?php
    } else {

        // echo "<pre>";
        // print_r($result);
        // die();

        foreach ($result as $row) { 
            ?>
            <tr>
                <td rowspan="6" style="vertical-align: middle;" class="baseLineTD">
                    <div class="rotate"><?= $row['desc'] ?></div>
                </td>
                <td>Quantidade de Registros</td>
                <td><?= $row['quantidade_RF'] ?></td>
                <td><?= $row['quantidade_QA'] ?></td>
                <td><?= $row['quantidade_RF'] + $row['quantidade_QA'] ?></td>
            </tr>
            <tr>
                <td>Prêmio Bruto</td>
                <td><?= app_format_currency($row['PB_RF'], true) ?></td>
                <td><?= app_format_currency($row['PB_QA'], true) ?></td>
                <td><?= app_format_currency($row['PB_RF'] + $row['PB_QA'], true) ?></td>
            </tr>
            <tr>
                <td>IOF</td>
                <td><?= app_format_currency($row['IOF_RF'], true) ?></td>
                <td><?= app_format_currency($row['IOF_QA'], true) ?></td>
                <td><?= app_format_currency($row['IOF_RF'] + $row['IOF_QA'], true) ?></td>
            </tr>
            <tr>
                <td>Prêmio Líquido</td>
                <td><?= app_format_currency($row['PL_RF'], true) ?></td>
                <td><?= app_format_currency($row['PL_QA'], true) ?></td>
                <td><?= app_format_currency($row['PL_RF'] + $row['PL_QA'], true) ?></td>
            </tr>
            <tr>
                <td>Pró-labore LASA</td>
                <td><?= app_format_currency($row['pro_labore_RF'], true) ?></td>
                <td><?= app_format_currency($row['pro_labore_QA'], true) ?></td>
                <td><?= app_format_currency($row['pro_labore_RF'] + $row['pro_labore_QA'], true) ?></td>
            </tr>
            <tr class="baseLine">
                <td>Comissão de Corretagem</td>
                <td><?= app_format_currency($row['valor_comissao_RF'], true) ?></td>
                <td><?= app_format_currency($row['valor_comissao_QA'], true) ?></td>
                <td><?= app_format_currency($row['valor_comissao_RF'] + $row['valor_comissao_QA'], true) ?></td>
            </tr>
            <?php


        }
    }
}
?>
</table>