<table cellpadding="0" cellspacing="0" class="t2">
    <tbody>
        <tr>
            <td class="tr5 td22">&nbsp;</td>
            <td class="tr6 td23" rowspan="2">
            <p class="p8 ft11">Descri&ccedil;&atilde;o</p>
            </td>
            <td class="tr5 td24">
            <p class="p9 ft11">Limites M&aacute;ximos de</p>
            </td>
            <td class="tr6 td25" rowspan="2">
            <p class="p8 ft11">Franquia</p>
            </td>
            <td class="tr6 td26" rowspan="2">
            <p class="p9 ft11">Pr&ecirc;mio por Cobertura (R$)</p>
            </td>
        </tr>
        <tr>
            <td class="tr7 td22">&nbsp;</td>
            <td class="tr5 td27" rowspan="2">
            <p class="p9 ft11">Indeniza&ccedil;&atilde;o</p>
            </td>
        </tr>
        <tr>
            <td class="tr8 td22">&nbsp;</td>
            <td class="tr8 td28">
            <p class="p2 ft12">&nbsp;</p>
            </td>
            <td class="tr8 td29">
            <p class="p2 ft12">&nbsp;</p>
            </td>
            <td class="tr8 td30">
            <p class="p2 ft12">&nbsp;</p>
            </td>
        </tr>
        <?php $premio_total = 0; foreach ($coberturas_all as $i => $cobertura) {
        $premio = $cobertura['premio_liquido_total'];
        $premio_total += $premio;
        ?>
        <tr>
            <td class="tr9 td22">&nbsp;</td>
            <td class="tr9 td31">
            <p class="p8 ft11"><?= $cobertura['cobertura_nome']; ?></p>
            </td>
            <td class="tr9 td32">
            <p class="p11 ft6">R$ <?= app_format_currency($cobertura['importancia_segurada']); ?></p>
            </td>
            <td class="tr9 td33">
            <p class="p12 ft13"><?= isempty($cobertura['franquia'], 'Não Há'); ?></p>
            </td>
            <td class="tr9 td34">
            <p class="p13 ft13">R$ <?= app_format_currency($premio); ?></p>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td class="tr7 td22">&nbsp;</td>
            <td class="tr7 td28">
            <p class="p2 ft14">&nbsp;</p>
            </td>
            <td class="tr7 td27">
            <p class="p2 ft14">&nbsp;</p>
            </td>
            <td class="tr7 td29">
            <p class="p2 ft14">&nbsp;</p>
            </td>
            <td class="tr7 td30">
            <p class="p2 ft14">&nbsp;</p>
            </td>
        </tr>
    </tbody>
</table>

