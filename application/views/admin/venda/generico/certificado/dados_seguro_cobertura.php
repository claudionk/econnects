<div class="premio">
    <table class="tabela_premio" width="100%" border="0">

        <thead>
            <tr>
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
                <td><?= $cobertura['cobertura']; ?></td>
                <td></td>
                <td></td>
                <td><?= $cobertura['carencia']; ?></td>
                <td>R$<?= app_format_currency($premio); ?></td>
            </tr>
        <?php } ?>
        </tbody>

    </table>
</div>
