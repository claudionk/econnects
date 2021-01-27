<table cellpadding="0" cellspacing="0" style="width: 100%;">
    <tbody>
        <tr>            
            <td valign="top" style="border-right: 1px solid #ddd">
                <table class="container" cellpadding="2" cellspacing="0">
                    <thead></thead><tbody>
                        <tr>
                            <td class="table-title" height="3" colspan="2">COBERTURAS CONTRATADAS</td>
                        </tr>
                        <tr>            
                            <td class="table-cell-field"><b>Descri&ccedil;&atilde;o</b></td>
                            <td class="table-cell-field td-last"><b>Pr&ecirc;mio por Cobertura (R$)</b></td>
                        </tr>
                        <?php foreach ($coberturas_all as $i => $cobertura) : ?>
                            <?php if($cobertura["assistencia"] == 0): ?>
                                <tr>
                                    <td class="table-cell-field"><b><?= $cobertura['cobertura_nome']; ?></b></td>
                                    <td class="table-cell-field td-last">R$ <?= app_format_currency($cobertura['premio_liquido_total']); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>								
                    </tbody>
                </table>
            </td>
            <td valign="top">
                <table class="container" cellpadding="2" cellspacing="0">
                    <thead></thead><tbody>
                        <tr>
                            <td class="table-title" height="3" colspan="2">ASSISTÊNCIAS/SERVIÇOS CONTRATADOS</td>
                        </tr>
                        <tr>            
                            <td class="table-cell-field"><b>Descri&ccedil;&atilde;o</b></td>
                            <td class="table-cell-field td-last"><b>Valor Assistência/Serviço</b></td>
                        </tr>
                        <?php foreach ($coberturas_all as $i => $cobertura) : ?>
                            <?php if($cobertura["assistencia"] == 1): ?>
                                <tr>
                                    <td class="table-cell-field"><b><?= $cobertura['cobertura_nome']; ?></b></td>
                                    <td class="table-cell-field td-last">R$ <?= app_format_currency($cobertura['premio_liquido_total']); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>								
                    </tbody>
                </table>
            </td>
            
        </tr>        
    </tbody>
</table>


