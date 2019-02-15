<table width="100%">
    <tbody>
    <tr>
        <th>SEGURADO</th>
        <td>{segurado_nome}</td>
        <th>CPF/CNPJ</th>
        <td>{segurado_cnpj_cpf}</td>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <th>CEP</th>
        <td><?php echo $segurado['endereco_cep']; ?></td>
        <th>ENDERE&Ccedil;O</th>
        <td><?php echo "{$segurado['endereco']}, {$segurado['endereco_numero']}"; ?></td>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <th>BAIRRO</th>
        <td><?php echo $segurado['endereco_bairro']; ?></td>
        <th>CIDADE</th>
        <td><?php echo $segurado['endereco_cidade']; ?></td>
        <th>ESTADO</th>
        <td><?php echo $segurado['endereco_estado']; ?></td>
    </tr>
    </tbody>
</table>
<table width="100%">
    <tbody>
    <tr>
        <th>Nome:</th>
        <td bgcolor="#ECECEC">{segurado_nome}</td>
        <th>CPF:</th>
        <td bgcolor="#ECECEC">{segurado_cnpj_cpf}</td>
        <th>Dt. Nasc.:</th>
        <td bgcolor="#ECECEC">{segurado_data_nascimento}</td>
    </tr>
    <tr>
        <th>Endereço:</th>
        <td bgcolor="#ECECEC">{segurado_endereco}</td>
        <th>Bairro:</th>
        <td bgcolor="#ECECEC" colspan="3">{segurado_bairro}</td>
    </tr>
    <tr>
        <th>Cidade:</th>
        <td bgcolor="#ECECEC">{segurado_cidade}</td>
        <th>Estado:</th>
        <td bgcolor="#ECECEC">{segurado_estado}</td>
        <th>CEP:</th>
        <td bgcolor="#ECECEC">{segurado_cep}</td>
    </tr>
    <tr>
        <th>Origem:</th>
        <td bgcolor="#ECECEC">{origem}</td>
        <th>Destino:</th>
        <td bgcolor="#ECECEC">{destino}</td>
        <th>Plano:</th>
        <td bgcolor="#ECECEC">{plano}</td>
    </tr>
    </tbody>
</table>
<hr />