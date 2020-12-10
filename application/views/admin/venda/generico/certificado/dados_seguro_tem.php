<table class="tabela_premio" width="100%" border="0">
    <thead>
        <tr>
            <td>Descrição:</td>
            <td>Diárias</td>
            <td>Capital Segurado Máximo¹:</td>
            <td>Prêmio²:</td>
            <td>Franquia</td>
            <td>Carência</td>
        </tr>
    </thead>

    <?php $premio_total = 0; foreach ($coberturas_all as $i => $cobertura) {
        $premio = $cobertura['premio_liquido_total'];
        $premio_total += $premio;
        ?>
        <tr>
            <td><?php echo ($i+1) . " - " . $cobertura['cobertura_nome']; ?></td>
            <td><?php echo (empty($cobertura['diarias'])) ? 'NÃO HÁ' : 'ATÉ '.$cobertura['diarias'].' DIÁRIAS'; ?></td>
            <td>R$ <?php echo app_format_currency($cobertura['importancia_segurada']); ?></td>
            <td>R$ <?php echo app_format_currency($premio); ?></td>
            <td><?= isempty($cobertura['franquia'], 'Não Há'); ?></td>
            <td><?= isempty($cobertura['carencia'], 'Não Há'); ?></td>
        </tr>
    <?php } ?>

    <tr>
        <td colspan="3">Prêmio Total (com IOF): </td>
        <td>R$ <?php echo app_format_currency($premio_total) ?></td>
        <td colspan="2"></td>
    </tr>

</table>
