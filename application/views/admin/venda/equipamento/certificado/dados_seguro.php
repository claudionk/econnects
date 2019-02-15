<div class="premio">
    <table class="tabela_premio" width="100%" border="0">

        <thead>
            <tr>
                <td>Coberturas:</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </thead>

        <?php $premio_total = 0; foreach ($coberturas as $i => $cobertura) {   ?>
            <tr>
                <td>
                    <span style="float: left"><?php echo ($i+1) . " - " . $cobertura['cobertura_nome']; ?></span>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <?php } ?>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2">Prêmio Total <?php if($dados['iof'] != '0.00'): ?> (com IOF de <?php echo app_format_currency($dados['iof']) ?>%) <?php endif; ?> : R$<?php echo app_format_currency($dados['premio_liquido_total']) ?></td>
        </tr>
    </table>
</div>