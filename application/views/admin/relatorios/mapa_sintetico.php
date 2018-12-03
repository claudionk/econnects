<style type="text/css">
    .rotate {
        /*writing-mode: vertical-rl !important;text-orientation: upright;*/
        -ms-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -webkit-transform: rotate(-90deg);
        transform: rotate(-90deg);
        font-size: 28px;
        margin: 0 -40px;
    }
    .baseLine td {
        border-bottom: 1px solid #c9c9c9;
    }
    .baseLineTD {
        border-bottom: 1px solid #c9c9c9;
        font-weight: bold;
    }
    .divisorLeft {
        border-right: 1px solid #c9c9c9;
    }
</style>
<table class="table table-striped">
    <tr>
        <td style="min-width:100px;"></td>
        <td style="min-width:220px;"></td>
        <td style="min-width:200px;" align="center" class="baseLineTD">VENDAS</td>
        <td style="min-width:200px;" align="center" class="baseLineTD">CANCELAMENTOS</td>
        <td style="min-width:200px;" align="center" class="baseLineTD">TOTAL</td>
    </tr>
    <?php
    if (isset($result)) {
        if (empty($result)) {
            ?><tr>
                <td colspan="8"> Nenhum resultado encontrado.</td>
            </tr><?php
        } else {
            $tot_f = count($result);
            $t_quant = 0;
            $t_premio = 0;
            $t_iof = 0;
            $t_premio = 0;
            $t_comissaor = 0;
            $t_comissaoc = 0;

            foreach ($result as $key => $row) { 
                if($key < $tot_f){
                    $t_quant += abs(($row['V_quantidade_RF'] +  $row['V_quantidade_QA']) - ($row['C_quantidade_RF'] + $row['C_quantidade_QA']));
                    $t_premio += app_rel_sintetico_ajuste_num( ($row['V_PB_RF'] + $row['V_PB_QA']) - ($row['C_PB_RF'] + $row['C_PB_QA']) );
                    $t_iof += app_rel_sintetico_ajuste_num( ($row['V_IOF_RF'] + $row['V_IOF_QA']) - ($row['C_IOF_RF'] + $row['C_IOF_QA']) );
                    $t_premio += app_rel_sintetico_ajuste_num( ($row['V_PL_RF'] + $row['V_PL_QA'])-($row['C_PL_RF'] + $row['C_PL_QA']) );
                    $t_comissaor += app_rel_sintetico_ajuste_num( ($row['V_pro_labore_RF'] + $row['V_pro_labore_QA'])-($row['C_pro_labore_RF'] + $row['C_pro_labore_QA']) );
                    $t_comissaoc += app_rel_sintetico_ajuste_num( ($row['V_valor_comissao_RF'] + $row['V_valor_comissao_QA'])-($row['C_valor_comissao_RF'] + $row['C_valor_comissao_QA']) );
                }
                ?>
                <tr>
                    <td rowspan="6" style="vertical-align: middle;" class="baseLineTD">
                        <div class=""><?= $row['desc'] ?></div>
                    </td>
                    <td class="divisorLeft">Quantidade de Registros</td>
                    <td class="divisorLeft"><?= $row['V_quantidade_RF'] + $row['V_quantidade_QA'] ?></td>
                    <td class="divisorLeft"><?= $row['C_quantidade_RF'] + $row['C_quantidade_QA'] ?></td>
                    <td class="divisorLeft">
                    <?php 
                        if($key == $tot_f)
                            echo $t_quant;
                        else
                            echo abs(($row['V_quantidade_RF'] +  $row['V_quantidade_QA']) - ($row['C_quantidade_RF'] + $row['C_quantidade_QA'])) 
                    
                    ?>
                    </td>
                </tr>
                <tr>
                    <td class="divisorLeft">Prêmio Bruto</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_PB_RF'] + $row['V_PB_QA'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_PB_RF'] + $row['C_PB_QA'], true) ?></td>
                    <td class="divisorLeft">
                    <?php
                        if($key == $tot_f)
                            echo app_format_currency( $t_premio, true );
                        else
                            echo app_format_currency( app_rel_sintetico_ajuste_num( ($row['V_PB_RF'] + $row['V_PB_QA']) - ($row['C_PB_RF'] + $row['C_PB_QA']) ), true ) 
                    ?>
                    </td>
                </tr>
                <tr>
                    <td class="divisorLeft">IOF</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_IOF_RF'] + $row['V_IOF_QA'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_IOF_RF'] + $row['C_IOF_QA'], true) ?></td>
                    <td class="divisorLeft">
                    <?php
                        if($key == $tot_f)
                            echo $t_iof;
                        else
                            echo app_format_currency( app_rel_sintetico_ajuste_num( ($row['V_IOF_RF'] + $row['V_IOF_QA']) - ($row['C_IOF_RF'] + $row['C_IOF_QA']) ), true ) ?></td>
                </tr>
                <tr>
                    <td class="divisorLeft">Prêmio Líquido</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_PL_RF'] + $row['V_PL_QA'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_PL_RF'] + $row['C_PL_QA'], true) ?></td>
                    <td class="divisorLeft">
                    <?php 
                        if($key == $tot_f)
                            echo $t_premio;
                        else
                            echo app_format_currency( app_rel_sintetico_ajuste_num( ($row['V_PL_RF'] + $row['V_PL_QA'])-($row['C_PL_RF'] + $row['C_PL_QA']) ), true ) ?></td>
                </tr>
                <tr>
                    <td class="divisorLeft">Comissão Representante</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_pro_labore_RF'] + $row['V_pro_labore_QA'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_pro_labore_RF'] + $row['C_pro_labore_QA'], true) ?></td>
                    <td class="divisorLeft">
                    <?php
                        if($key == $tot_f)
                            echo $t_comissaor;
                        else
                            echo app_format_currency( app_rel_sintetico_ajuste_num( ($row['V_pro_labore_RF'] + $row['V_pro_labore_QA'])-($row['C_pro_labore_RF'] + $row['C_pro_labore_QA']) ), true ) ?></td>
                </tr>
                <tr class="baseLine">
                    <td class="divisorLeft">Comissão Corretor</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_valor_comissao_RF'] + $row['V_valor_comissao_QA'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_valor_comissao_RF'] + $row['C_valor_comissao_QA'], true) ?></td>
                    <td class="divisorLeft">
                    <?php 
                        if($key == $tot_f)
                            echo $t_comissaoc;
                        else 
                            echo app_format_currency( app_rel_sintetico_ajuste_num( ($row['V_valor_comissao_RF'] + $row['V_valor_comissao_QA'])-($row['C_valor_comissao_RF'] + $row['C_valor_comissao_QA']) ), true); ?>
                    </td>
                </tr>
                <?php

            }
        }
    }
    ?>
</table>