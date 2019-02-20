<div class="premio">
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

        <?php $premio_total = 0; foreach ($coberturas as $i => $cobertura) {

            $premio = $pagamento['valor_total']/(1-(0.38/100)) * $cobertura['porcentagem'];
            $premio_total += $premio;
            ?>
            <tr>
                <td><?php echo ($i+1) . " - " . $cobertura['cobertura_nome']; ?></td>
                <td><?php echo (empty($cobertura['diarias'])) ? 'NÃO HÁ' : 'ATÉ '.$cobertura['diarias'].' DIÁRIAS'; ?></td>
                <td>R$<?php echo app_format_currency($cobertura['preco']); ?></td>
                <td>R$<?php echo app_format_currency($premio); ?></td>
                <td><?php echo $cobertura['franquia']; ?></td>
                <td><?php echo $cobertura['carencia']; ?></td>
            </tr>
        <?php } ?>

        <tr>
            <td colspan="2">Prêmio Total (com IOF de 0,38%)</td>
            <td>R$<?php echo app_format_currency($premio_total) ?></td>
        </tr>

    </table>
</div>
