<?php
//print_pre($coberturas_all);
$aGeneraliResidenciaCobertura = array();
$aGeneraliResidenciaAssistencia = array();

foreach ($coberturas_all as $i => $cobertura) {
    
    $cobertura_slug         = $cobertura["cobertura_slug"];
    $assistencia            = $cobertura["assistencia"];
    $cobertura_plano_descricao         = $cobertura["cobertura_plano_descricao"];
    $premio_liquido_total   = (float) $cobertura["premio_liquido_total"];
    $importancia_segurada   = (float) $cobertura["importancia_segurada"];

    $data = array();
    $data["cobertura_plano_descricao"]         = $cobertura_plano_descricao;
    $data["premio_liquido_total"]   = $premio_liquido_total;
    $data["importancia_segurada"]   = $importancia_segurada;

    if ($assistencia == 0) {

        if(in_array($cobertura_slug, array("incendio", "queda_raio", "explosao"))){
            $cobertura_slug = "incendio_queda_raio_explosao";            
        }

        if (isset($aGeneraliResidenciaCobertura[$cobertura_slug])) {
            $aGeneraliResidenciaCobertura[$cobertura_slug]["premio_liquido_total"] += $premio_liquido_total;
        } else {
            $aGeneraliResidenciaCobertura[$cobertura_slug] = $data;
        }
        
    } else {

        if (!isset($aGeneraliResidenciaAssistencia[$cobertura_slug])) {
            $aGeneraliResidenciaAssistencia[$cobertura_slug] = $data;
        } 
        
    }

}

?>

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
                        <?php foreach ($aGeneraliResidenciaCobertura as $cobertura) : ?>
                        
                            <tr>
                                <td class="table-cell-field"><b><?= $cobertura['cobertura_plano_descricao']; ?></b></td>
                                <td class="table-cell-field td-last">R$ <?= app_format_currency($cobertura['premio_liquido_total']); ?></td>
                            </tr>
                        
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
                        <?php foreach ($aGeneraliResidenciaAssistencia as $cobertura) : ?>
                            
                                <tr>
                                    <td class="table-cell-field"><b><?= $cobertura['cobertura_plano_descricao']; ?></b></td>
                                    <td class="table-cell-field td-last"><?= ($cobertura['importancia_segurada'] != 0)? "R$ ".app_format_currency($cobertura['importancia_segurada']): "Contratada"; ?></td>
                                </tr>
                            
                        <?php endforeach; ?>								
                    </tbody>
                </table>
            </td>
            
        </tr>        
    </tbody>
</table>


