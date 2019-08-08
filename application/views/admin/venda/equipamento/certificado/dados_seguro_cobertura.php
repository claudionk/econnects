<table class="tabela_premio" width="100%" border="0">
    <thead>
        <tr style="background-color: #d2d2d2;">
            <td>Descrição</td>
            <td>LMI <br>Indenização</td>
            <td>P.O.S <br>(Part. Obrig. Segurado) </td>
            <td>Carência</td>
            <td>Prêmio</td>
        </tr>
    </thead>

    <tbody>
    <?php $premio_total = 0; foreach ($coberturas_all as $i => $cobertura) {
        $premio = $cobertura['premio_liquido_total'];
        $premio_total += $premio;
        ?>
        <tr>
            <td><?= $cobertura['cobertura_nome']; ?></td>
            <td>R$<?= app_format_currency($cobertura['importancia_segurada']); ?></td>
            <td><?= isempty($cobertura['franquia'], 'Não Há'); ?></td>
            <td><?= isempty($cobertura['carencia'], 'Não Há'); ?></td>
            <td>R$<?= app_format_currency($premio); ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="4" align="right">Prêmio Total (com IOF): </td>
        <td>R$<?php echo app_format_currency($premio_total) ?></td>
    </tr>
    </tbody>

</table>
