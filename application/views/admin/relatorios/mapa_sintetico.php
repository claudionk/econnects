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
    <tr class="baseLine">
        <td style="min-width:100px;"></td>
        <td style="min-width:220px;"></td>
        <td style="min-width:200px;" align="center">VENDAS</td>
        <td style="min-width:200px;" align="center">CANCELAMENTOS</td>
        <td style="min-width:200px;" align="center">TOTAL</td>
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
                    $t_quant += abs($row['V_quantidade'] - $row['C_quantidade']);
                    $t_premio += app_rel_sintetico_ajuste_num( $row['V_PB'] - $row['C_PB'] );
                    $t_iof += app_rel_sintetico_ajuste_num( $row['V_IOF'] - $row['C_IOF']);
                    $t_premio += app_rel_sintetico_ajuste_num( $row['V_PL'] - $row['C_PL']);
                    $t_comissaor += app_rel_sintetico_ajuste_num( $row['V_pro_labore'] - $row['C_pro_labore']);
                    $t_comissaoc += app_rel_sintetico_ajuste_num( $row['V_valor_comissao'] - $row['C_valor_comissao']);
                }
                ?>
                <tr>
                    <td rowspan="6" style="vertical-align: middle;" class="baseLineTD">
                        <div><?= $row['desc'] ?></div>
                    </td>
                    <td class="divisorLeft">Quantidade de Registros</td>
                    <td class="divisorLeft"><?= $row['V_quantidade'] ?></td>
                    <td class="divisorLeft"><?= $row['C_quantidade'] ?></td>
                    <td class="divisorLeft">
                    <?php 
                        if($key == $tot_f)
                            echo $t_quant;
                        else
                            echo abs( $row['V_quantidade'] - $row['C_quantidade'] );
                    
                    ?>
                    </td>
                </tr>
                <tr>
                    <td class="divisorLeft">Prêmio Bruto</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_PB'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_PB'], true) ?></td>
                    <td class="divisorLeft">
                    <?php
                        if($key == $tot_f)
                            echo app_format_currency( $t_premio, true );
                        else
                            echo app_format_currency( app_rel_sintetico_ajuste_num( $row['V_PB'] - $row['C_PB'] ), true );
                    ?>
                    </td>
                </tr>
                <tr>
                    <td class="divisorLeft">IOF</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_IOF'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_IOF'], true) ?></td>
                    <td class="divisorLeft">
                    <?php
                        if($key == $tot_f)
                            echo $t_iof;
                        else
                            echo app_format_currency( app_rel_sintetico_ajuste_num( $row['V_IOF'] - $row['C_IOF'] ), true ); ?></td>
                </tr>
                <tr>
                    <td class="divisorLeft">Prêmio Líquido</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_PL'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_PL'], true) ?></td>
                    <td class="divisorLeft">
                    <?php 
                        if($key == $tot_f)
                            echo $t_premio;
                        else
                            echo app_format_currency( app_rel_sintetico_ajuste_num( $row['V_PL'] - $row['C_PL'] ), true ); ?></td>
                </tr>
                <tr>
                    <td class="divisorLeft">Comissão Representante</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_pro_labore'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_pro_labore'], true) ?></td>
                    <td class="divisorLeft">
                    <?php
                        if($key == $tot_f)
                            echo $t_comissaor;
                        else
                            echo app_format_currency( app_rel_sintetico_ajuste_num( $row['V_pro_labore'] - $row['C_pro_labore'] ), true ); ?></td>
                </tr>
                <tr class="baseLine">
                    <td class="divisorLeft">Comissão Corretor</td>
                    <td class="divisorLeft"><?= app_format_currency($row['V_valor_comissao'], true) ?></td>
                    <td class="divisorLeft"><?= app_format_currency($row['C_valor_comissao'], true) ?></td>
                    <td class="divisorLeft">
                    <?php 
                        if($key == $tot_f)
                            echo $t_comissaoc;
                        else 
                            echo app_format_currency( app_rel_sintetico_ajuste_num( $row['V_valor_comissao'] - $row['C_valor_comissao'] ), true); ?>
                    </td>
                </tr>
                <?php

            }
        }
    }
    ?>
</table>