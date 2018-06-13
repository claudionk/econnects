
    <div class="card">
        <div class="card-head style-primary">
            <header>Dados cadastrais</header>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <td width="50%">Código</td>
                    <td width="50%"><?php echo issetor($row['codigo'], "Não cadastrado") ?></td>
                </tr>

                <tr>
                    <td width="50%">Nome/Razão Social</td>
                    <td width="50%"><?php echo issetor($row['razao_nome'], "Não cadastrado") ?></td>
                </tr>

                <tr>
                    <td width="50%">CPF/CNPJ</td>
                    <td width="50%"><?php echo issetor($row['cnpj_cpf'], "Não cadastrado") ?></td>
                </tr>

                <tr>
                    <td width="50%">RG/IE</td>
                    <td width="50%"><?php echo issetor($row['ie_rg'], "Não cadastrado") ?></td>
                </tr>

                <tr>
                    <td width="50%">Data de nascimento</td>
                    <td width="50%"><?php echo issetor(app_date_mysql_to_mask($row['data_nascimento'], 'd/m/Y'), "Não cadastrado") ?></td>
                </tr>
            </table>
        </div>
    </div>


    <div class="card">
        <div class="card-head style-primary">
            <header>Endereço do cliente</header>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <td width="50%">CEP</td>
                    <td width="50%"><?php echo issetor($row['cep'], "Não cadastrado") ?></td>
                </tr>

                <tr>
                    <td width="50%">Endereço</td>
                    <td width="50%"><?php echo issetor($row['endereco'], "Não cadastrado") ?> <?php echo issetor($row['numero']) ?></td>
                </tr>

                <tr>
                    <td width="50%">Bairro</td>
                    <td width="50%"><?php echo issetor($row['bairro'], "Não cadastrado") ?></td>
                </tr>

                <tr>
                    <td width="50%">Cidade</td>
                    <td width="50%"><?php echo issetor($cidade['nome'], "Não cadastrado") ?></td>
                </tr>

                <tr>
                    <td width="50%">Estado</td>
                    <td width="50%"><?php echo issetor($cidade['localidade_estado_nome'], "Não cadastrado") ?></td>
                </tr>
            </table>
        </div>
    </div>

