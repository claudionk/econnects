
<?php $this->load->view("admin/partials/page_head"); ?>
<style type="text/css">
    .carregando { 
        display: none; width: 100%; height:100%; text-align:center; position:absolute; left:0; top:0; z-index: 1000; background-color:transparent; opacity:0.85 
    }
    .engrenagem { color: #191A1A; position: relative; font-size: 192px; top: 25%; z-index: 999  }
    .ajsSel { height: 42px !important; }
    .ajsRow { margin-left: 0px !important; margin-right: 0px !important; }
    #banco option { text-transform: uppercase !important; }
    #formulario_conta_bancaria label { color:#353132 !important; opacity: 0.5; }
</style>
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
                                <th width='10%' class="center">ID</th>
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
                                            <td><?php echo $row['cnpj_cpf']; ?></td>
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
            <a id="btnCancelar" href="javascript:void(0)" class="btn  btn-app btn-primary">
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

<!-- MODALS -->
<div class="modal fade" id="viewModalCancelamentoError" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">CANCELAMENTO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="viewModalCancelamento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">CANCELAMENTO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="text-align:center;font-size:15px;margin:20px 0;">O valor a ser devolvido é de <strong id="vldevolucao" style="color:red;"></strong></div>
        <div style="text-align:center;margin: 20px 0;"><b>Data vigência:</b> <u id="dtvigenciaini"></u> <b>a</b> <u id="dtvigenciafim"></u></div>
        <div style="text-align:center;margin: 0 0 20px 0;">Quantidade de dias utilizados: <b><u id="qtdutilizados"></u></b></div>
      </div>
      <div class="modal-footer">
        <div style="font-size: 15px;text-align: center;margin: 10px 0 20px 0;color: red;">Tem certeza que deseja continuar?</div>
        <button id="btnNao" type="button" class="btn btn-primary" data-dismiss="modal">NÃO</button>
        <button id="btnSim" type="button" class="btn btn-secondary" >SIM</button>
      </div>
    </div>
  </div>
</div>

<!-- FIM MODALS -->

<!-- DADOS BANCÁRIOS -->
<div class="modal fade" id="formulario_conta_bancaria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
    <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" action="<?= $this->config->item('URL_sisconnects'); ?>admin/pedido/adicionar_dados_bancarios" autocomplete="off">
        <div class="modal-dialog" role="document">
            <input type="hidden" class="form-control" name="pedido_id" value="<?php echo $pedido_id; ?>">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">CANCELAMENTO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="row ajsRow">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">A conta bancária pertence ao</label>
                            <select required id="segurado" name="segurado" class="form-control conta_terceiro">
                                <option value="S" selected>Segurado</option>
                                <option value="T">Terceiro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Tipo de favorecido</label>
                            <select required id="tipofavorecido" name="tipofavorecido" class="form-control">
                                <option value="PF">Pessoa física</option>
                                <option value="PJ">Pessoa jurídica</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Tipo de conta</label>
                            <select required name="tipoconta" class="form-control">
                                <option value="corrente">Conta Corrente</option>
                                <option value="conta_facil">Conta Fácil</option>
                                <option value="poupanca">Conta Poupança</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row ajsRow">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Nome do favorecido</label>
                            <input required type="text" class="form-control" name="nome" value="">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">CPF/CNPJ Favorecido</label>
                            <input required type="text" class="form-control" name="cpf_cnpj" value="">
                        </div>
                    </div>
                </div>
                <div class="row ajsRow">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Banco do favorecido</label>
                            <select required id="banco" name="banco" class="form-control ajsSel">
                                <option value="" style="display:none">Selecione o banco</option>
                                <?php foreach ($bancos as $banco) :  ?>
                                    <option value="<?php echo $banco->codigo ?>"><?php echo $banco->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-8 no-padding">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="">Agência</label>
                                <input required type="number" min="0"  pattern="[0-9]+" maxlength="4" class="form-control" id="" name="agencia" value="">
                            </div>
                            <div class="col-sm-4">
                                <label for="">Conta</label>
                                <input required type="number" min="0" class="form-control" id="conta" name="conta" value="">
                            </div>
                            <div class="col-sm-2 ">
                                <label for="">Dígito</label>
                                <input pattern="[a-zA-Z0-9]+" maxlength="1" required type="text" class="form-control" id="digito" name="digito" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row ajsRow">
                    <div class="col-sm-12">
                        <br>
                        <font color="red"><b>Importante: </b></font>Preencha os dados corretamente.
                        <br>
                        <br>
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Sair</button>
                <button type="submit" class="btn btn-secondary">Confirmar Cancelamento</button>
              </div>
            </div>
        </div>
    </form>
</div>
<div id="aviso_ordem_pagamento" class="hidden">
    <div class="row">
        <div class="col-sm-12">
            <?php if( $this->session->userdata('seg_cli') == 18 ) { ?>
                <p class="text-danger">O pagamento será realizado através de ordem de pagamento.</p>
            <?php } ?>
        </div>
    </div>
</div>
<!-- FIM DADOS BANCARIOS -->
<!-- LOADING -->
<div class="carregando">
    <div class="engrenagem"><i class="fa fa-gear fa-spin" aria-hidden="true"></i></div>
</div>
<!-- FIM LOADING -->
<script type="text/javascript">
jQuery(function($){
    $("#btnSim").click(function(e){
        e.preventDefault();
        $(".carregando").show();
        $('#viewModalCancelamento').modal('hide');
        $("#formulario_conta_bancaria").modal();
        $(".carregando").hide();
    });

    $("#btnCancelar").click(function(){
        // $("#viewProsseguirCancelamento").modal('show');
        var pedido_id  = "<?php echo $pedido_id; ?>";
        var apolice_id = "<?php echo $apolices[0]['apolice_id']; ?>";
        $.ajax({
            type: "post",
            url: base_url + "api/apolice/calculoCancelar",
            data: JSON.stringify({ apolice_id : apolice_id }),
            headers: {
                "apikey": "<?php echo app_get_token() ?>",
                "content-type": "application/json",
                "cache-control": "no-cache"
            },
            beforeSend: function(){
                $(".carregando").show();
            },
            success: function(data){
                if (!data.status) {
                    if (data.mensagem) {
                        $('#viewModalCancelamentoError .modal-body').html(data.mensagem);
                        $('#viewModalCancelamentoError').modal('show');
                        return;
                    }
                } else {

                    _inicio_vigencia = String( data.dados[0].apolices.data_ini_vigencia).replace(/^(....).(..).(..)/,"$3/$2/$1");
                    _fim_vigencia = String( data.dados[0].apolices.data_fim_vigencia).replace(/^(....).(..).(..)/,"$3/$2/$1");

                    $(".carregando").hide();
                    $("#vldevolucao").html("").append(data.valor_estorno_total);
                    $("#dtvigenciaini").html("").append(_inicio_vigencia);
                    $("#dtvigenciafim").html("").append(_fim_vigencia);
                    $("#qtdutilizados").html("").append(data.dias_utilizados);
                    $('#viewModalCancelamento').modal('show');

                }
            },
            error: function(data){
                console.log(data);
            },
            complete: function(){
                $(".carregando").hide();
            }
        });
    });

    $('input[name="cpf_cnpj"]').mask('000.000.000-00', {reverse: true});
    $('input[name="agencia"]').mask('0000', {reverse: true});
    $("#tipofavorecido").change(function(e){
       if($("#tipofavorecido option:selected").val() == 'PJ')
       {    
            $('input[name="cpf_cnpj"]').val('');
            $('input[name="cpf_cnpj"]').mask('00.000.000/0000-00', {reverse: true});  
       } 
       else if ($("#tipofavorecido option:selected").val() == 'PF'){
            $('input[name="cpf_cnpj"]').val('');
            $('input[name="cpf_cnpj"]').mask('000.000.000-00', {reverse: true});
       }
    });
});
</script>