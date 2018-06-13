
<?php $this->load->view("admin/partials/page_head"); ?>

<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">

    <div class="col-md-12">
        <div class="card card-underline">
            <div class="card-head">
                <ul class="nav nav-tabs pull-right" data-toggle="tabs">
                    <li class="active"><a href="#pedido-detalhe">RESUMO</a></li>
                    <li class=""><a href="#pedido-itens">DETALHES</a></li>
                    <li class=""><a href="#pedido-capitalizacao">CAPITALIZAÇÃO</a></li>
                    <li class=""><a href="#pedido-faturamento">FATURAMENTO</a></li>
                    <li class=""><a href="#pedido-cartao">CARTÕES</a></li>
                    <li class=""><a href="#pedido-historico">HISTÓRICO</a></li>
                    <li class=""><a href="#pedido-apolice">Apólices / Bilhete</a></li>
                </ul>
                <header>PEDIDO</header>
            </div>
            <div class="card-body tab-content">
                <div class="tab-pane active" id="pedido-detalhe">
                    <h3 class="text-light">RESUMO</h3>
                    <dl class="dl-horizontal">
                        <dt>Código:</dt>
                        <dd><?php echo $pedido['codigo'] ?></dd>
                        <dt>Status / Data:</dt>
                        <dd><?php echo $pedido['pedido_status_nome'] ?> - <?php echo app_date_mysql_to_mask($pedido['status_data'], 'd/m/Y H:i'); ?></dd>
                        <dt>Data do pedido</dt>
                        <dd><?php echo app_date_mysql_to_mask($pedido['criacao'], 'd/m/Y H:i'); ?></dd>
                        <dt>Valor total:</dt>
                        <dd>R$ <?php echo app_format_currency($pedido['valor_total']) ?></dd>
                        <dt>Números de parcela(s):</dt>
                        <dd><?php echo $pedido['num_parcela'] ?></dd>
                        <dt>Valor da parcela(s):</dt>
                        <dd>R$ <?php echo app_format_currency($pedido['valor_parcela']) ?></dd>
                    </dl>
                </div>
                <div class="tab-pane" id="pedido-itens">
                    <?php foreach ($itens as $item) : ?>
                        <div class="col-md-12">
                            <h3 class="text-light"><?php echo $item['produto_nome'] ?> <strong><?php echo $item['plano_nome'] ?></strong></h3>
                            <dl class="dl-horizontal">

                                <?php if($produto['slug'] == 'seguro_viagem') : ?>
                                    <dt>Motivo Viagem:</dt>
                                    <dd><?php echo $item['motivo_nome'] ?></dd>
                                    <dt>Origem / Destino:</dt>
                                    <dd><?php echo $item['origem_nome'] ?> - <?php echo $item['destino_nome'] ?></dd>
                                    <dt>Vigência:</dt>
                                    <dd><?php echo app_date_mysql_to_mask($item['data_saida'], 'd/m/Y'); ?> até <?php echo app_date_mysql_to_mask($item['data_retorno'], 'd/m/Y'); ?></dd>
                                    <dt>Passageiros</dt>
                                    <dd><?php echo $item['num_passageiro'] ?></dd>
                                    <dt>Repasse comissão:</dt>
                                    <dd><?php echo app_format_currency($item['repasse_comissao'], false, 3); ?> %</dd>
                                    <dt>Comissão Corretor:</dt>
                                    <dd><?php echo app_format_currency($item['comissao_corretor'], false, 3); ?> %</dd>
                                    <dt>Desconto Condicional:</dt>
                                    <dd><?php echo app_format_currency($item['desconto_condicional'], false, 3); ?> %</dd>
                                    <dt>Prêmio Líquido:</dt>
                                    <dd>R$ <?php echo app_format_currency($item['premio_liquido'], false, 2); ?></dd>
                                    <dt>IOF:</dt>
                                    <dd><?php echo app_format_currency($item['iof'], false, 3); ?> %</dd>
                                    <dt>Prêmio Líquido Total:</dt>
                                    <dd>R$ <?php echo app_format_currency($item['premio_liquido_total'], false, 2); ?></dd>
                                    <dt>Câmbio</dt>
                                    <dd><?php echo $item['moeda'] ?> - R$ <?php echo app_format_currency($item['cambio']) ?> - <?php echo app_date_mysql_to_mask($item['data_cambio'], 'd/m/Y'); ?></dd>
                                <?php elseif($produto['slug'] == 'equipamento') : ?>

                                    <dt>Categoria do Equipamento:</dt>
                                    <dd><?php echo issetor($item['equipamento_categoria_nome']) ?></dd>

                                    <dt>Marca do Equipamento:</dt>
                                    <dd><?php echo issetor($item['equipamento_marca_nome']) ?></dd>

                                    <dt>Equipamento:</dt>
                                    <dd><?php echo issetor($item['equipamento_nome']) ?></dd>

                                    <dt>Valor Nota Fiscal:</dt>
                                    <dd><?php echo app_format_currency($item['nota_fiscal_valor']) ?></dd>
                                    <dt>Número Nota Fiscal:</dt>
                                    <dd><?php echo $item['nota_fiscal_numero'] ?></dd>
                                    <dt>Data Nota Fiscal:</dt>
                                    <dd><?php echo app_date_mysql_to_mask($item['nota_fiscal_data'], 'd/m/Y') ?></dd>
                                    <?php if(isset($apolices[0]) && isset($apolices[0]['data_ini_vigencia']) && isset($apolices[0]['data_fim_vigencia'])) : ?>
                                        <dt>Vigência:</dt>
                                        <dd><?php echo app_date_mysql_to_mask($apolices[0]['data_ini_vigencia'], 'd/m/Y'); ?> até <?php echo app_date_mysql_to_mask($apolices[0]['data_fim_vigencia'], 'd/m/Y'); ?></dd>
                                    <?php endif; ?>
                                    <dt>Repasse comissão:</dt>
                                    <dd><?php echo app_format_currency($item['repasse_comissao'], false, 3); ?> %</dd>
                                    <dt>Comissão Corretor:</dt>
                                    <dd><?php echo app_format_currency($item['comissao_corretor'], false, 3); ?> %</dd>
                                    <dt>Desconto Condicional:</dt>
                                    <dd><?php echo app_format_currency($item['desconto_condicional'], false, 3); ?> %</dd>
                                    <dt>Prêmio Líquido:</dt>
                                    <dd>R$ <?php echo app_format_currency($item['premio_liquido'], false, 2); ?></dd>
                                    <dt>IOF:</dt>
                                    <dd><?php echo app_format_currency($item['iof'], false, 3); ?> %</dd>
                                    <dt>Prêmio Líquido Total:</dt>
                                    <dd>R$ <?php echo app_format_currency($item['premio_liquido_total'], false, 2); ?></dd>
                                <?php elseif($produto['slug'] == 'generico') : ?>
                                    <?php if(isset($apolices[0]) && isset($apolices[0]['data_ini_vigencia']) && isset($apolices[0]['data_fim_vigencia'])) : ?>
                                        <dt>Vigência:</dt>
                                        <dd><?php echo app_date_mysql_to_mask($apolices[0]['data_ini_vigencia'], 'd/m/Y'); ?> até <?php echo app_date_mysql_to_mask($apolices[0]['data_fim_vigencia'], 'd/m/Y'); ?></dd>
                                    <?php endif; ?>
                                    <dt>Repasse comissão:</dt>
                                    <dd><?php echo app_format_currency($item['repasse_comissao'], false, 3); ?> %</dd>
                                    <dt>Comissão Corretor:</dt>
                                    <dd><?php echo app_format_currency($item['comissao_corretor'], false, 3); ?> %</dd>
                                    <dt>Desconto Condicional:</dt>
                                    <dd><?php echo app_format_currency($item['desconto_condicional'], false, 3); ?> %</dd>
                                    <dt>Prêmio Líquido:</dt>
                                    <dd>R$ <?php echo app_format_currency($item['premio_liquido'], false, 2); ?></dd>
                                    <dt>IOF:</dt>
                                    <dd><?php echo app_format_currency($item['iof'], false, 3); ?> %</dd>
                                    <dt>Prêmio Líquido Total:</dt>
                                    <dd>R$ <?php echo app_format_currency($item['premio_liquido_total'], false, 2); ?></dd>
                                <?php endif; ?>



                            </dl>
                        </div>
                        <?php if($item['cobertura_adicionais']) : ?>
                        <?php $valor_total = 0; ?>
                        <div class="col-md-6">
                            <h3 class="text-light">COBERTURAS ADICIONAIS</strong></h3>
                            <table class="table table-hover">

                                <thead>
                                    <tr>
                                        <th width="80%">COBERTURA</th>
                                        <th width="20%">VALOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($item['cobertura_adicionais'] as $cobertura_adicional) : ?>
                                        <tr>
                                            <td><?php echo $cobertura_adicional['nome']; ?></td>
                                            <td><?php  echo app_format_currency($cobertura_adicional['valor'], false, 2 ); ?></td>
                                        </tr>
                                    <?php $valor_total += $cobertura_adicional['valor']; ?>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td class="text-right"><strong>TOTAL: </strong></td>
                                        <td><?php  echo app_format_currency($valor_total, false, 2 ); ?></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="tab-pane" id="pedido-capitalizacao">
                    <?php if($capitalizacoes) : ?>

                        <div class="row">
                            <div class="col-sm-12">
                                <h3 class="text-light">TÍTULOS CAPITALIZAÇÃO</h3>

                                <table class="table table-responsive">
                                    <thead>
                                    <tr>
                                        <td>Nome</td>
                                        <td>Número</td>
                                        <td>Data Utilização</td>
                                        <td>Sorteado</td>
                                        <td>Valor Sorteio</td>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($capitalizacoes as $capitalizacao) { ?>
                                        <tr>
                                            <td><?php echo $capitalizacao['nome'] ?></td>
                                            <td><?php echo $capitalizacao['numero'] ?></td>
                                            <td><?php echo app_date_mysql_to_mask($capitalizacao['data_compra'], 'd/m/Y H:i'); ?></td>
                                            <td><?php echo ($capitalizacao['contemplado'] == 1) ? 'Sim' : 'Não' ?></td>
                                            <td>R$<?php echo app_format_currency($capitalizacao['valor_sorteio'], false, 2); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="tab-pane" id="pedido-faturamento">
                    <h3 class="text-light">Faturas</h3>
                    <dl class="dl-horizontal">
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th  width='10%' class="center">ID</th>
                                <th width='10%'>Tipo</th>
                                <th width='20%'>Processado</th>
                                <th width='10%'>Parcelas</th>
                                <th width='15%'>Valor Total</th>
                                <th width='20%'>Status</th>
                                <th class="center" width='25%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($faturas as $row) :?>
                                <tr>

                                    <td class="center"><?php echo $row[$primary_key];?></td>
                                    <td><?php echo $row['tipo'];?></td>
                                    <td><?php echo app_date_mysql_to_mask($row['data_processamento']) ;?></td>
                                    <td><?php echo $row['num_parcela'];?></td>
                                    <td><?php echo app_format_currency($row['valor_total']);?></td>
                                    <td><?php echo $row['fatura_status_nome'];?></td>
                                    <td class="center">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary btn-parcelas" data-fatura="<?php echo $row[$primary_key];?>">  <i class="fa fa-arrow-circle-down"></i>  Parcelas </a>
                                    </td>
                                </tr>

                                <tr style="display: none;" class="grid-parcela grid-grouped-<?php echo $row[$primary_key]; ?>">
                                    <td colspan="7">
                                        <table class="table">

                                            <!-- Table heading -->
                                            <thead>
                                            <tr>
                                                <th width='10%'>Parcela</th>
                                                <th width='20%'>Processado</th>
                                                <th width='10%'>Vencimento</th>
                                                <th width='15%'>Pagamento</th>
                                                <th width='15%'>Valor</th>
                                                <th width='20%'>Status</th>
                                            </tr>
                                            </thead>
                                            <!-- // Table heading END -->

                                            <!-- Table body -->
                                            <tbody>

                                            <!-- Table row -->
                                            <?php foreach($row['parcelas'] as $parcela) :?>
                                                <tr>

                                                    <td><?php echo $parcela['num_parcela'];?></td>
                                                    <td><?php echo app_date_mysql_to_mask($parcela['data_processamento']) ;?></td>
                                                    <td><?php echo app_date_mysql_to_mask($parcela['data_vencimento'], 'd/m/Y') ;?></td>
                                                    <td><?php echo app_date_mysql_to_mask($parcela['data_pagamento']) ;?></td>
                                                    <td><?php echo app_format_currency($parcela['valor']);?></td>
                                                    <td><?php echo $parcela['fatura_status_nome'];?></td>
                                                </tr>
                                            <?php endforeach;?>
                                            <!-- // Table row END -->

                                            </tbody>
                                            <!-- // Table body END -->

                                        </table>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>

                    </dl>

                </div>
                <div class="tab-pane" id="pedido-cartao">
                    <h3 class="text-light">Cartões e Transações</h3>
                    <dl class="dl-horizontal">
                        <?php $this->load->view("admin/pedido/trocar_cartao") ?>
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th  width='10%' class="center">ID</th>
                                <th width='20%'>Bandeira</th>
                                <th width='30%'>Número</th>
                                <th width='10%'>Vencimento</th>
                                <th width='10%'>Ativo</th>
                                <th class="center" width='20%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($cartoes as $row) :?>
                                <tr>

                                    <td class="center"><?php echo $row['pedido_cartao_id'];?></td>
                                    <td><?php echo $row['bandeira_cartao'];?></td>
                                    <td><?php echo $row['numero'] ;?></td>
                                    <td><?php echo $row['validade'];?></td>
                                    <td><?php echo ($row['ativo'] == 1) ? 'SIM' : 'NÃO';?></td>
                                    <td class="center">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-toggle="collapse" data-parent="#accordion_transacoes_<?php echo $row['pedido_cartao_id'] ?>" data-target="#accordion_transacoes_<?php echo $row['pedido_cartao_id'] ?>"> <i class="fa fa-arrow-circle-down"></i>  Transações </a>
                                    </td>
                                </tr>

                                <tr class="grid-parcela collapsed collapse" id="accordion_transacoes_<?php echo $row['pedido_cartao_id'] ?>">
                                    <td colspan="6">

                                        <table class="table">

                                            <!-- Table heading -->
                                            <thead>
                                            <tr>
                                                <th width='15%'>Código da Transação #TID</th>
                                                <th width='20%'>Processado</th>
                                                <th width='10%'>Resultado</th>
                                                <th width='15%'>Mensagem</th>
                                                <th width='15%'>Data de Criação</th>
                                            </tr>
                                            </thead>
                                            <!-- // Table heading END -->

                                            <!-- Table body -->
                                            <tbody>

                                            <!-- Table row -->
                                            <?php foreach($row['transacoes'] as $transacao) : ?>
                                                <tr>
                                                    <td><?php echo $transacao['tid'] ?></td>
                                                    <td><?php echo $transacao['processado'] ? "Sim" : "Não" ?></td>
                                                    <td><?php echo $transacao['result'] ?></td>
                                                    <td><?php echo $transacao['message'] ?></td>
                                                    <td><?php echo app_date_mysql_to_mask($transacao['criacao']) ?></td>
                                                </tr>
                                            <?php endforeach;?>
                                            <!-- // Table row END -->

                                            <?php if(sizeof($row['transacoes']) == 0) {?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-danger">Não possuem transações para este cartão.</td>
                                                </tr>
                                            <?php } ?>

                                            </tbody>
                                            <!-- // Table body END -->

                                        </table>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- // Table row END -->



                            </tbody>
                            <!-- // Table body END -->

                        </table>

                    </dl>

                </div>
                <div class="tab-pane" id="pedido-historico">
                    <?php if($transacoes) : ?>

                        <div class="row">
                            <div class="col-sm-12">
                                <h3 class="text-light">Histórico</h3>

                                <table class="table table-responsive">
                                    <thead>
                                    <tr>
                                        <td>Código</td>
                                        <td>Mensagem</td>
                                        <td>Status</td>
                                        <td>Data</td>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($transacoes as $transacao) { ?>
                                        <tr>
                                            <td><?php echo $transacao['pedido_transacao_id'] ?></td>
                                            <td><?php echo $transacao['mensagem'] ?></td>
                                            <td><?php echo $transacao['pedido_status_nome'] ?></td>
                                            <td><?php echo app_date_mysql_to_mask($transacao['criacao'], 'd/m/Y H:i'); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="tab-pane" id="pedido-apolice">
                    <?php if($apolices) : ?>

                        <div class="row">
                            <div class="col-sm-12">
                                <h3 class="text-light">Apólices</h3>

                                <table class="table table-hover">

                                    <!-- Table heading -->
                                    <thead>
                                    <tr>
                                        <th width="15%" class="center">Bilhete</th>
                                        <th width='35%'>Nome</th>
                                        <th width='25%'>CPF</th>
                                        <th class="center" width='25%'>Ações</th>
                                    </tr>
                                    </thead>
                                    <!-- // Table heading END -->

                                    <!-- Table body -->
                                    <tbody>

                                    <!-- Table row -->
                                    <?php foreach($apolices as $row) :?>
                                        <tr>

                                            <td class="center"><?php echo $row['num_apolice'];?></td>
                                            <td><?php echo $row['nome'];?></td>
                                            <td><?php echo (app_verifica_cpf_cnpj($row['cnpj_cpf']) == 'CPF') ? app_cpf_to_mask($row['cnpj_cpf']) : app_cnpj_to_mask($row['cnpj_cpf']); ?></td>
                                            <td class="center">
                                                <a target="_blank" href="<?php echo base_url("admin/apolice/certificado/{$row['apolice_id']}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-print"></i>  Imprimir </a>
                                                <a target="_blank" href="<?php echo base_url("admin/apolice/certificado/{$row['apolice_id']}/pdf")?>" class="btn btn-sm btn-primary">  <i class="fa fa-file-pdf-o"></i>  PDF </a>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                    <!-- // Table row END -->

                                    </tbody>
                                    <!-- // Table body END -->

                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div><!--end .card -->
    </div>

</div>

<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>

        <?php if (isset($upgrade) && $upgrade) : ?>
            <a href="<?php echo base_url("admin/pedido_upgrade/upgrade/{$pedido_id}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Upgrade
            </a>
        <?php endif; ?>

        <?php if($cancelamento == 1) : ?>
            <a href="<?php echo base_url("{$current_controller_uri}/cancelar/{$pedido_id}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-exclamation-circle"></i> Cancelar
            </a>
            <a href="<?php echo base_url("{$current_controller_uri}/cancelar_aprovacao/{$pedido_id}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-exclamation-circle"></i> Cancelar (Aprovação)
            </a>

        <?php endif; ?>
        <?php if($cancelamento_aprovar == 1) : ?>
            <a href="<?php echo base_url("{$current_controller_uri}/cancelar_aprovar/{$pedido_id}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-exclamation-circle"></i> Aprovar Cancelamento
            </a>
        <?php endif; ?>
    </div>
</div>