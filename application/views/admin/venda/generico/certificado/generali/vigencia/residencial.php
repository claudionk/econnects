
<table cellpadding="2" cellspacing="0" class="container">
    <thead>
    </thead>
    <tbody>
        <tr valign="top">
            <td class="table-title" colspan="<?= ($isLista)? "3" : "2"; ?>"><?= ($isLista)? "VIG&Ecirc;NCIA DAS COBERTURAS DO SEGURO" : "VIG&Ecirc;NCIA DO SEGURO"; ?></td>
        </tr>
        
            <?php if($isLista):?>

                <?php foreach($content as $key => $value): ?>                
                    <tr style="background-color: #e2e2e2;">
                        <td class="table-cell-field"><b><?= $value['cobertura_nome']; ?></b></td>
                        <td class="table-cell-field">In&iacute;cio &agrave;s 24h do dia <?= app_date_mysql_to_mask($value["data_inicio_vigencia"], 'd/m/Y'); ?></td>
                        <td class="table-cell-field td-last">Fim &agrave;s 24h do dia <?= app_date_mysql_to_mask($value["data_fim_vigencia"], 'd/m/Y'); ?></td>
                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>

            <?php if(!$isLista):?>
                <tr style="background-color: #e2e2e2;">
                    <td class="table-cell-field">In&iacute;cio &agrave;s 24h do dia <?= $content["data_inicio_vigencia"]; ?></td>
                    <td class="table-cell-field td-last">Fim &agrave;s 24h do dia <?= $content["data_fim_vigencia"]; ?></td>
                </tr>
            <?php endif; ?>          
            
            <tr>
                <td class="table-cell-field td-last" colspan="2" height="6">O parcelamento do pr&ecirc;mio &eacute; financiado pela Representante, que o repassa na integralidade e &agrave; vista para a Seguradora. Para parcelamentos em mais de 5 parcelas h&aacute; incid&ecirc;ncia de juros, que n&atilde;o s&atilde;o repassados &agrave; Seguradora.</td>
            </tr>
        
        
    </tbody>
</table>