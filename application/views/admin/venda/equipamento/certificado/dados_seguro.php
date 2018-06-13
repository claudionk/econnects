<div class="premio">
    <table class="tabela_premio" width="100%" border="0">

        <thead>
            <tr>
                <td>Coberturas:</td>
<!--                <td>Capital Segurado Máximo¹:</td>
                <td>Prêmio²:</td>-->
            </tr>
        </thead>

        <?php $premio_total = 0; foreach ($coberturas as $i => $cobertura) {

            //$premio = $pagamento['valor_total']/(1-(7.38/100)) * $cobertura['porcentagem'];
            //$premio_total += $premio;
            ?>
            <tr>
                <td><?php echo ($i+1) . " - " . $cobertura['cobertura_nome']; ?></td>
<!--                <td>R$<?php echo app_format_currency($cobertura['preco']); ?></td>
                <td>R$<?php echo app_format_currency($premio); ?></td>-->
            </tr>
        <?php } ?>

        <tr>
            <td colspan="2">Prêmio Total <?php if($dados['iof'] != '0.00'): ?> (com IOF de <?php echo app_format_currency($dados['iof']) ?>%) <?php endif; ?> : R$<?php echo app_format_currency($dados['premio_liquido_total']) ?></td>
           <!-- <td></td>-->
        </tr>

    </table>
</div>
