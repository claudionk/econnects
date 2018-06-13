
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
                    <li class=""><a href="#pedido-faturamento">FATURAMENTO</a></li>
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
                        <h3 class="text-light"><?php echo $item['produto_nome'] ?> <strong><?php echo $item['plano_nome'] ?></strong></h3>
                        <dl class="dl-horizontal">
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
                        </dl>
                    <?php endforeach; ?>
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
                                        <th class="center">Bilhete</th>
                                        <th width='65%'>Nome</th>
                                        <th width='65%'>CPF</th>
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
            <a href="<?php echo base_url("{$current_controller_uri}/upgrade/{$pedido_id}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Upgrade
            </a>
        <?php endif; ?>

    </div>
</div>