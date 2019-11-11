<div class="layout-app" ng-controller="AppController">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <!-- col-separator.box -->
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                    <li class="active"><?php echo $page_subtitle;?></li>
                </ol>
            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("admin/implantacoes/index/")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                    <a href="<?php echo base_url("admin/implantacoes/printer/". issetor($row[$primary_key], 0)) ?>" class="btn  btn-app btn-primary" >
                        <i class="fa fa-print"></i> Imprimir
                    </a>
                </div>

            </div>
            <div class="col-separator col-unscrollable bg-none box col-separator-first">

                <!-- col-table -->
                <div class="col-table">

                <!-- col-table-row -->
                    <div class="col-table-row">

                        <!-- col-app -->
                        <div class="col-app col-unscrollable">

                            <!-- col-app -->
                            <div class="col-app">

                                <!-- Form -->
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">
                                    <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                    <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                                    <!-- Widget -->
                                    <div class="card">

                                        <div class="card-body">
                                            <!-- Row -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-head"><header>Configurações de Implantação: <?= $parceiro['nome'] ?> / <?= $row['nome'] ?> </header></div>
                                                        <div class="card-body">
                                                            <div class="col-md-3">Data de Configuraçao: </div>
                                                            <div class="col-md-3"><?php echo $row['data_configuracao'] ?></div>
                                                            <div class="col-md-6">&nbsp;</div>

                                                            <div class="col-md-3">Data de Aprovação: </div>
                                                            <div class="col-md-3">-</div>
                                                            <div class="col-md-3">Aprovado Por:</div>
                                                            <div class="col-md-3">-</div>

                                                            <div class="col-md-3">Data de Produção: </div>
                                                            <div class="col-md-3">-</div>
                                                            <div class="col-md-6">&nbsp;</div>

                                                            <div class="col-md-3">Data da Primeira Emissão: </div>
                                                            <div class="col-md-3"><?php echo $row['data_primeira_emissao'] ?></div>
                                                            <div class="col-md-6">&nbsp;</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- // Column END -->
                                            </div>
                                            <!-- // Row END -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-head"><header>Cadastro de Parceiros </header></div>
                                                        <?php foreach ($parceiros as $parc) { ?>
                                                        <div class="card-body">
                                                            <div class="col-md-3">Tipo de Parceiro: </div><div class="col-md-9"><?php echo $parc['parceiro_tipo']; ?></div>
                                                            <div class="col-md-3">Razão Social: </div><div class="col-md-9"><?php echo $parc['parceiro_nome']; ?></div>
                                                            <div class="col-md-3">CNPJ: </div><div class="col-md-9"><?php echo $parc['parceiro_cnpj']; ?></div>
                                                            <div class="col-md-3">Código Susep: </div><div class="col-md-9"><?php echo emptyor($parc['parceiro_codigo_susep'], '-'); ?></div>
                                                            <div class="col-md-3">Código do Parceiro: </div><div class="col-md-9"><?php echo emptyor($parc['parceiro_codigo_corretor'], '-'); ?></div>
                                                            <div class="col-md-3">Endereço: </div><div class="col-md-9"><?php echo $parc['endereco'] .", ". $parc['numero'] .", ". $parc['complemento'] .", ". $parc['bairro'] .", ". $parc['cidade'] ."-". $parc['uf']; ?></div>
                                                            <div class="col-md-12 text-bold">CADASTRO DE COMISSÃO</div>
                                                            <div class="col-md-3">Tipo de Comissão: </div><div class="col-md-9"><?php echo $parc['tipo_comissao']; ?></div>
                                                            <div class="col-md-3">Tipo de Cálculo: </div><div class="col-md-9"><?php echo $parc['comissao_tipo']; ?></div>
                                                            <div class="col-md-3">Percentual de Comissão: </div><div class="col-md-9"><?php echo $parc['comissao'] ." %"; ?></div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <!-- // Column END -->
                                            </div>
                                            <!-- // Row END -->

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-head"><header>Cadastro de Produtos </header></div>
                                                        <div class="card-body">
                                                            <div class="col-md-3">Nome Produto: </div><div class="col-md-9"><?php echo emptyor($row['nome'], '-') ?></div>
                                                            <div class="col-md-3">Slug do Produto: </div><div class="col-md-9"><?php echo emptyor($row['slug_produto'], '-') ?></div>
                                                            <!--div class="col-md-3">Código do Produto: </div><div class="col-md-9"><?php echo 'XXXX' ?></div-->
                                                            <div class="col-md-3">Processo SUSEP: </div><div class="col-md-9"><?php echo emptyor($row['codigo_susep'], '-') ?></div>
                                                            <div class="col-md-3">Código Sucursal: </div><div class="col-md-9"><?php echo emptyor($row['cod_sucursal'], '-') ?></div>
                                                            <div class="col-md-3">Código do Ramo: </div><div class="col-md-9"><?php echo emptyor($row['cod_ramo'], '-') ?></div>
                                                            <div class="col-md-3">Ramo / Produto: </div><div class="col-md-9"><?php echo emptyor($row['ramo']['nome'], '-') ?> / <?php echo emptyor($row['produto_nome'], '-') ?></div>
                                                            <div class="col-md-3">Código de Operação: </div><div class="col-md-9"><?php echo emptyor($row['cod_tpa'], '-') ?></div>
                                                            <div class="col-md-3">Venda Multi-Parceiros: </div><div class="col-md-9"><?php echo emptyor($row['venda_agrupada'], '-') ?></div>

                                                            <div class="col-md-12 text-bold">REGRAS DO PRODUTO</div>
                                                            <div class="col-md-3">Markup: </div><div class="col-md-9"><?php echo emptyor($row['configuracoes']['markup'], '-') ." %" ?></div>
                                                            <div class="col-md-3">Tipo de Cálculo: </div><div class="col-md-9"><?php echo emptyor($row['configuracoes']['calculo_tipo'], '-') ?></div>
                                                            <div class="col-md-3">Certificado / Bilhete: </div><div class="col-md-9"><?php echo emptyor($row['configuracoes']['apolice_sequencia'], '-') ?></div>
                                                            <div class="col-md-3">Formas de Pagamento: </div><div class="col-md-9"><?php echo emptyor($row['forma_pagamentos'], '-') ?></div>
                                                            <div class="col-md-3">Arrecadação: </div><div class="col-md-9"><?php echo emptyor($row['configuracoes']['arrecadacao'], '-') ?></div>
                                                            <div class="col-md-3">Canal de Emissão: </div><div class="col-md-9"><?php echo 'XXXX' ?></div>
                                                            
                                                            <div class="col-md-12 text-bold">SERVIÇOS</div>
                                                            <div class="col-md-3">Enriquecimento de CPF: </div><div class="col-md-9"><?php echo emptyor($row['servico']['cpf'], '-') ?></div>
                                                            <div class="col-md-3">E-mail com Comprovação: </div><div class="col-md-9"><?php echo emptyor($row['servico']['email_comp'], '-') ?></div>
                                                            <div class="col-md-3">SMS com Comprovação: </div><div class="col-md-9"><?php echo emptyor($row['servico']['sms_comp'], '-') ?></div>

                                                            <div class="col-md-12 text-bold">REGRAS DE CANCELAMENTO</div>
                                                            <div class="col-md-3">Canal de Cancelamento: </div><div class="col-md-9">XXXX</div>
                                                            <div class="col-md-12">
                                                                <table width="100%">
                                                                    <tr>
                                                                        <td>Antes do Início da Vigência:</td>
                                                                        <td><?php echo emptyor($row['cancelamento']['seg_antes_hab'], '-') ?></td>
                                                                        <td>Depois do Início da Vigência:</td>
                                                                        <td><?php echo emptyor($row['cancelamento']['seg_depois_hab'], '-') ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Minímo de Dias:</td>
                                                                        <td><?php echo issetor($row['cancelamento']['seg_antes_dias'], '-') ?></td>
                                                                        <td>Máximo de Dias:</td>
                                                                        <td><?php echo issetor($row['cancelamento']['seg_depois_dias'], '-') ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td>Devolução de 100% até (Dias):</td>
                                                                        <td><?php echo emptyor($row['cancelamento']['seg_depois_dias_carencia'], '-') ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td>Forma de Cálculo:</td>
                                                                        <td><?php echo emptyor($row['cancelamento']['calculo_tipo'], '-') ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Cálculo de Penalidade:</td>
                                                                        <td><?php echo emptyor($row['cancelamento']['seg_antes_calculo'], '-') ?></td>
                                                                        <td>Cálculo de Penalidade:</td>
                                                                        <td><?php echo emptyor($row['cancelamento']['seg_depois_calculo'], '-') ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Cancelamento por Inadimplência:</td>
                                                                        <td><?php echo emptyor($row['cancelamento']['inad_hab'], '-') ?></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Quantidade Máxima de Dias em Aberto:</td>
                                                                        <td><?php echo issetor($row['cancelamento']['inad_max_dias'], '-') ?></td>
                                                                        <td>Quantidade Máxima de Parcelas em Aberto:</td>
                                                                        <td><?php echo issetor($row['cancelamento']['inad_max_parcela'], '-') ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Permite Reativar:</td>
                                                                        <td><?php echo emptyor($row['cancelamento']['inad_reativacao_hab'], '-') ?></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                    </tr>
                                                                </table>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- // Column END -->
                                            </div>
                                            <!-- // Row END -->

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-head"><header>Cadastro de Planos </header></div>
                                                        <?php foreach ($row['planos'] as $plano) { ?>
                                                        <div class="card-body">
                                                            <div class="col-md-3">Nome Plano: </div><div class="col-md-9"><?php echo emptyor($plano['nome'], '-') ?></div>
                                                            <div class="col-md-3">Slug do Plano: </div><div class="col-md-9"><?php echo emptyor($plano['slug_plano'], '-') ?></div>
                                                            <div class="col-md-3">Código do Produto: </div><div class="col-md-9"><?php echo emptyor($plano['codigo_operadora'], '-') ?></div>
                                                            <div class="col-md-3">Modelo de Precificação: </div><div class="col-md-9"><?php echo emptyor($plano['precificacao_tipo'], '-') ?></div>
                                                            <div class="col-md-3">Moeda: </div><div class="col-md-9"><?php echo emptyor($plano['moeda'], '-') ?></div>
                                                            <div class="col-md-3">Idade Mínima: </div><div class="col-md-9"><?php echo issetor($plano['idade_minima'], '-') ?></div>
                                                            <div class="col-md-3">Idade Máxima </div><div class="col-md-9"><?php echo issetor($plano['idade_maxima'], '-') ?></div>
                                                            <div class="col-md-3">Vigência Máxima: </div><div class="col-md-9"><?php echo emptyor($plano['limite_vigencia'], '-') ?> <?php echo str_replace('_A', '', emptyor($plano['unidade_tempo'], '-')) ?></div>
                                                            <div class="col-md-3">Tempo de Uso: </div><div class="col-md-9"><?php echo (yes_no($plano['possui_limite_tempo']) == 'SIM' ? $plano['limite_tempo']." ". $plano['unidade_limite_tempo'] : 'NÃO') ?></div>
                                                            <div class="col-md-3">Tipo de Cálculo: </div><div class="col-md-9"><?php echo emptyor($row['configuracoes']['calculo_tipo'], '-') ?></div>
                                                            <div class="col-md-3">Regra para Início de Vigência: </div><div class="col-md-9"><?php echo emptyor($row['configuracoes']['apolice_vigencia_regra'], '-') ?></div>
                                                            <div class="col-md-3">Visualizar Bilhete: </div><div class="col-md-9">
                                                                <a href="<?php echo base_url("admin/parceiros_relacionamento_produtos/index/". $plano['produto_parceiro_id'] ) ?>" target="_blank" > <i class="fa fa-search"></i> </a>
                                                            </div>
                                                            <?php foreach ($plano['coberturas'] as $cob) { ?>
                                                            <div class="col-md-12 text-bold">COBERTURAS </div>
                                                            <div class="col-md-12">
                                                                <table width="100%">
                                                                <tr>
                                                                    <td>Tipo: </td>
                                                                    <td><?php echo emptyor($cob['cobertura_tipo'], '-') ?></td>
                                                                    <td>Código da Cobertura: </td>
                                                                    <td><?php echo emptyor($cob['cod_cobertura'], '-') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nome da Cobertura: </td>
                                                                    <td><?php echo emptyor($cob['cobertura_nome'], '-') ?></td>
                                                                    <td>Empresa Responsável: </td>
                                                                    <td><?php echo emptyor($cob['parceiro_nome'], '-') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Modelo Precificação: </td>
                                                                    <td><?php echo emptyor($plano['precificacao_tipo'], '-') ?></td>
                                                                    <td>Taxa / Tarifa ou Distribuição: </td>
                                                                    <td><?php echo emptyor($cob['porcentagem'], '-') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Modelo de Custo: </td>
                                                                    <td><?php echo ($cob['cobertura_custo'] == 'valor') ? 'VALOR FIXO' : 'PERCENTUAL SOBRE VALOR DA COBERTURA' ?></td>
                                                                    <td>Custo: </td>
                                                                    <td><?php echo emptyor($cob['custo'], '-') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>IOF: </td>
                                                                    <td><?php echo !empty($cob['usar_iof']) ? $cob['iof'] : $iof ?> %</td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4">DADOS DO BILHETE</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Descrição da Cobertura: </td>
                                                                    <td><?php echo emptyor($cob['descricao'], '&nbsp;') ?></td>
                                                                    <td>Limite: </td>
                                                                    <td><?php echo app_format_currency($cob['preco'], true) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Franquia: </td>
                                                                    <td><?php echo emptyor($cob['franquia'], '&nbsp;') ?></td>
                                                                    <td>Carência: </td>
                                                                    <td><?php echo emptyor($cob['carencia'], '&nbsp;') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Diárias: </td>
                                                                    <td><?php echo emptyor($cob['diarias'], '&nbsp;') ?></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Código do Produto: </td>
                                                                    <td><?php echo emptyor($cob['cod_produto'], '&nbsp;') ?></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Código Sucursal: </td>
                                                                    <td><?php echo emptyor($cob['cod_sucursal'], '&nbsp;') ?></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Código do Ramo: </td>
                                                                    <td><?php echo emptyor($cob['cod_ramo'], '&nbsp;') ?></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                            </table>
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                    </div>
                                                </div>
                                                <!-- // Column END -->
                                            </div>
                                            <!-- // Row END -->

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-head"><header>Regras de Capitalização </header></div>
                                                        <div class="card-body">
                                                            <div class="col-md-3">Nome da Campanha: </div><div class="col-md-9"><?php echo emptyor($row['capitalizacao']['capitalizacao_nome'], '&nbsp;') ?></div>
                                                            <div class="col-md-3">Empresa de Capitalização: </div><div class="col-md-9"><?php echo emptyor($row['capitalizacao']['capitalizacao_tipo'], '&nbsp;') ?></div>
                                                            <div class="col-md-3">Definição Data de Sorteio: </div><div class="col-md-9"><?php echo emptyor($row['capitalizacao']['capitalizacao_sorteio'], '&nbsp;') ?></div>
                                                            <div class="col-md-3">Quantidade de Sorteios: </div><div class="col-md-9"><?php echo emptyor($row['capitalizacao']['qnt_sorteio'], '&nbsp;') ?></div>
                                                            <div class="col-md-3">Dia de Corte: </div><div class="col-md-9"><?php echo emptyor($row['capitalizacao']['capitalizacao_dia_corte'], '&nbsp;') ?></div>
                                                            <div class="col-md-3">Qtde de Numeros da Sorte por Compra: </div><div class="col-md-9"><?php echo emptyor($row['capitalizacao']['capitalizacao_qtde_titulos_por_compra'], '&nbsp;') ?></div>
                                                            <div class="col-md-3">Valor do Sorteio: </div><div class="col-md-9"><?php echo app_format_currency($row['capitalizacao']['capitalizacao_valor_sorteio'], true) ?></div>
                                                            <div class="col-md-3">Custo: </div><div class="col-md-9"><?php echo app_format_currency($row['capitalizacao']['capitalizacao_valor_custo_titulo'], true) ?></div>
                                                            <div class="col-md-3">Tipo de Serie </div><div class="col-md-9"><?php echo emptyor($row['capitalizacao']['serie'], '&nbsp;') ?></div>
                                                            <div class="col-md-3">Responsavel por Gerar o Nro da Sorte: </div><div class="col-md-9"><?php echo emptyor($row['capitalizacao']['responsavel_num_sorte'], '&nbsp;') ?></div>
                                                            <div class="col-md-3">Forma de Distribuição: </div><div class="col-md-9"><?php echo emptyor($row['capitalizacao']['capitalizacao_nome'], '&nbsp;') ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- // Column END -->
                                            </div>
                                            <!-- // Row END -->

                                        </div>
                                    </div>
                                    <!-- // Widget END -->
                                    
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("admin/implantacoes/index/")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-arrow-left"></i> Voltar
                                            </a>
                                            <a href="<?php echo base_url("admin/implantacoes/printer/". issetor($row[$primary_key], 0)) ?>" class="btn  btn-app btn-primary" >
                                                <i class="fa fa-print"></i> Imprimir
                                            </a>
                                        </div>

                                    </div>
                                </form>
                                <!-- // Form END -->
                            </div>
                            <!-- // END col-app -->
                        </div>
                        <!-- // END col-app.col-unscrollable -->
                    </div>
                    <!-- // END col-table-row -->
                </div>
                <!-- // END col-table -->
            </div>
            <!-- // END col-separator.box -->
        </div>
    </div>
</div>
<script>
  AppController.controller("AppController", ["$scope", "$sce", "$http", "$filter", "$timeout", "$interval", function ( $scope, $sce, $http, $filter, $timeout, $interval ) {
    $scope.comissao = parseFloat( "<?php echo isset($row['comissao']) ? $row['comissao'] : '0'; ?>" );
    $scope.comissao_indicacao = parseFloat( "<?php echo isset($row['comissao_indicacao']) ? $row['comissao_indicacao'] : '0'; ?>" );
    $scope.repasse_maximo = parseFloat( "<?php echo isset($row['repasse_maximo']) ? $row['repasse_maximo'] : '0'; ?>" );
    
    $scope.desconto_valor = parseFloat( "<?php echo isset($row['desconto_valor']) ? $row['desconto_valor'] : '0'; ?>" );
    $scope.desconto_utilizado = parseFloat( "<?php echo isset($row['desconto_utilizado']) ? $row['desconto_utilizado'] : '0'; ?>" );
    $scope.desconto_saldo = parseFloat( "<?php echo isset($row['desconto_saldo']) ? $row['desconto_saldo'] : '0'; ?>" );
  }]);
</script>
