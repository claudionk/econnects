<div class="premio">
    <table class="tabela_premio" width="100%" border="0">

        <thead>
            <tr>
                <td>Descrição:</td>
                <td>Capital Segurado Máximo¹:</td>
                <td>Prêmio²:</td>
            </tr>
        </thead>

        <?php $premio_total = 0; foreach ($coberturas as $i => $cobertura) {

            $premio = $premio_bruto * ($cobertura['porcentagem']/100);
            $premio_total += $premio;
            ?>
            <tr>
                <td><?php echo ($i+1) . " - " . $cobertura['cobertura_nome']; ?></td>
                <td>R$<?php echo app_format_currency($cobertura['preco']); ?></td>
                <td>R$<?php echo app_format_currency($premio); ?></td>
            </tr>
        <?php } ?>

        <tr>
            <td colspan="2">Prêmio Total (com IOF de 0,38%)</td>
            <td>R$<?php echo app_format_currency($premio_total) ?></td>
        </tr>

    </table>
</div>
