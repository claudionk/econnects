<table width="100%">
    <tbody><tr>
        <th width="50%" align="left"><?php echo $pagamento['tipo_pagamento']; ?></th>
        <td width="50%" align="left"><?php echo $pagamento['bandeira']; ?></td>
    </tr>
    </tbody></table>
<table width="100%">
    <tbody>
    <tr>
        <th width="33%" align="left">PARCELAS</th>
        <th width="33%" align="left">VALOR PARCELA</th>
        <th width="33%" align="left">VALOR TOTAL</th>
    </tr>
    <tr>
        <td><?php  echo $pagamento['num_parcela']; ?></td>
        <td>R$ <?php echo app_format_currency($pagamento['valor_parcela']);?></td>
        <td>R$ <?php echo app_format_currency($pagamento['valor_total']);?></td>
    </tr>
    </tbody>
</table>