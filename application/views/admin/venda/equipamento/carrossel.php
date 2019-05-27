<?php
    $exite_cobertura = false;
?>

<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome(); ?></li>
    </ol>
</div>


<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/equipamento/{$row['produto_parceiro_id']}/1/$cotacao_id") ?>"
           class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
        <a href="javascript:void(0);" class="btn btn-app btn-primary btn-salvar-cotacao">
            <i class="fa fa-edit"></i> Salvar Cotação
        </a>
        <a class="btn pull-right btn_dados_segurado  btn-app btn-primary">
            <i class="fa fa-arrow-right"></i> Próximo
        </a>
    </div>
</div>

<!-- col-app -->
<div class="card" ng-controller="AppController">
    <!-- col-app -->
    <div class="card-body">
        <!-- Form -->
        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off"
              enctype="multipart/form-data">
            <input type="hidden" id="<?php echo $primary_key ?>" name="<?php echo $primary_key ?>"
                   value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
            <input type="hidden" id="url_calculo" name="url_calculo"
                   value="<?php echo base_url("{$current_controller_uri}/calculo"); ?>"/>
            <input type="hidden" id="produto_parceiro_plano_id" name="produto_parceiro_plano_id" value="<?php if ( !empty($carrinho[0]['plano_id']) ) { echo $carrinho[0]['plano_id']; } else { echo '0'; } ?>"/>
            <input type="hidden" id="parceiro_id" name="parceiro_id" value="<?php echo $parceiro_id; ?>"/>
            <input type="hidden" id="cotacao_id" name="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>
            <input type="hidden" id="equipamento_id" name="equipamento_id" value="<?php if (isset($equipamento_id)) echo $equipamento_id; ?>"/>
            <input type="hidden" id="equipamento_marca_id" name="equipamento_marca_id" value="<?php if (isset($equipamento_marca_id)) echo $equipamento_marca_id; ?>"/>
            <input type="hidden" id="equipamento_categoria_id" name="equipamento_categoria_id" value="<?php if (isset($equipamento_categoria_id)) echo $equipamento_categoria_id; ?>"/>
            <input type="hidden" id="salvar_cotacao" name="salvar_cotacao" value=""/>
            <?php $configuracao['quantidade_cobertura'] = ((isset($configuracao['quantidade_cobertura'])) && ($configuracao['quantidade_cobertura'] < count($coberturas) )) ? $configuracao['quantidade_cobertura']  : count($coberturas); ?>
            <input type="hidden" id="quantidade_cobertura" name="quantidade_cobertura" value="<?php  echo (isset($configuracao['quantidade_cobertura'])) ? $configuracao['quantidade_cobertura'] : 10;  ?>"/>
            <input type="hidden" id="total_cobertura" name="total_cobertura" value="<?php  echo count($coberturas);  ?>"/>

            <div class="row">
                <div class="col-md-6">
                    <?php $this->load->view('admin/partials/validation_errors'); ?>
                    <?php $this->load->view('admin/partials/messages'); ?>
                </div>
            </div>

            <!-- Row -->
            <div class="row">

                <!-- Column -->
                <div class="col-md-12">

                    <h2 class="text-light text-center"><?php echo app_produto_traducao('Cotação do Seguro', $row['produto_parceiro_id']); ?><br>
                        <small class="text-primary"><?php echo app_produto_traducao('Código da Cotação:', $row['produto_parceiro_id']); ?> <?php echo $cotacao_codigo; ?></small>
                    </h2>

                    <?php $this->load->view('admin/venda/step', array('step' => 2, 'produto_parceiro_id' => $row['produto_parceiro_id'])); ?>


                    <div class="row">

                        <div class="col-xs-12">
                            <?php if((count($coberturas) > 0) && (count($planos) > 1)) : ?>
                            <h4>Comparar os planos disponíveis</h4>

                            <div class="row">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6" id="click_comparar_cotacoes">
                                    <i class="fa fa-arrow-down text-primary"></i>
                                    Clique aqui para comparar as demais cotações

                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-5 no-padding carrossel-left">
                                        <div class="vendas_bloco_left">
                                            <div id="vendas_coberturas">
                                                <table class="table_coberturas"  <?php if(count($coberturas) == 0) { echo 'style="border:none;"'; } ?>>
                                                    <thead>
                                                    <tr>
                                                        <?php if(count($coberturas) > 0) : ?>
                                                            <th><?php echo app_produto_traducao('Coberturas', $row['produto_parceiro_id']); ?></th>
                                                        <?php else:  ?>
                                                            <td style="background-color: #FFFFFF;"></td>
                                                        <?php endif; ?>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 1; foreach ($coberturas as $cobertura) : ?>
                                                        <tr class="<?php echo ($i % 2 == 0) ? '' : 'odd'; ?>">
                                                            <td><?php echo $cobertura['nome']; ?></td>
                                                        </tr>

                                                        <?php $i++; endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <div class="clearfix"></div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-xs-7 no-padding">
                                        <div class="vendas_bloco_right">
                                            <div id="vendas_planos" style="">
                                                <?php if(count($planos) > 1): ?>
                                                    <a href="#" class="plano_prev">prev</a>
                                                <?php endif;?>
                                                <div id="slider-one-container" class="plano_slider <?php if(count($planos) == 1) {echo 'unico-plano'; } ?>">
                                                    <ul id="plano_slider_one" class="slide-container">
                                                        <?php foreach ($planos as $plano): ?>
                                                            <li class="">
                                                                <table class="plano_table">
                                                                    <thead>
                                                                    <tr>
                                                                        <th class="plano_header">
                                                                            <i class="fa fa-unlock-alt cadeado one text-primary"></i>
                                                                            <span class="plano_nome_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"><?php echo $plano['nome']; ?></span>
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php $i = 1;
                                                                    foreach ($coberturas as $cobertura) : ?>
                                                                        <tr class="<?php echo ($i % 2 == 0) ? '' : 'odd'; ?>">
                                                                            <td>
                                                                                <?php if($cobertura['cobertura_tipo_id'] == 1 ) : ?>
                                                                                    <?php 
                                                                                      if( ( $key = array_search($cobertura['cobertura_id'], array_column($plano['cobertura'], 'cobertura_id') ) ) === FALSE ) {
                                                                                        echo '-';
                                                                                      } else { 
                                                                                        //$plano["cobertura"][$key]["mostrar"];
                                                                                        if( $plano["precificacao_tipo_id"] == 2 ) {
                                                                                          if( $plano["cobertura"][$key]["mostrar"] == "preco" ) {
                                                                                            if( isset( $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']] ) ) {
                                                                                              echo $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']];
                                                                                            } else {
                                                                                              echo "<font color=\"red\">Configuração Inválida</font>";
                                                                                            }
                                                                                          } elseif( $plano["cobertura"][$key]["mostrar"] == "importancia_segurada" ) {
                                                                                            echo "Até R$" . number_format( $cotacao_salva["nota_fiscal_valor"], 2, ",", "." );
                                                                                          } else {
                                                                                            if( isset( $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']] ) ) {
                                                                                              echo $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']];
                                                                                            } else {
                                                                                              echo "<font color=\"red\">Configuração Inválida</font>";
                                                                                            }
                                                                                          }
                                                                                        } else {
                                                                                          if( isset( $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']] ) ) {
                                                                                            echo $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']];
                                                                                          } else {
                                                                                            echo "<font color=\"red\">Configuração Inválida</font>";
                                                                                          }
                                                                                        }
                                                                                      }
                                                                                    ?>
                                                                                <?php else : ?>
                                                                                    <?php if (($key = array_search($cobertura['cobertura_id'], array_column($plano['cobertura'], 'cobertura_id'))) === FALSE) : ?>
                                                                                        -
                                                                                    <?php else : ?>
                                                                                        <?php $exite_cobertura = true; ?>
                                                                                        <div class="checkbox">
                                                                                            <label>
                                                                                                <?php $coberturas_selecionadas = (isset($carrinho_hidden['cobertura_adicional'])) ? explode(';', $carrinho_hidden['cobertura_adicional']) : array(); ?>
                                                                                                <input class="ck-cobertura-adicional" name="ck_cobertura_adicional[]" type="checkbox" value="<?php echo "{$plano['produto_parceiro_plano_id']};{$plano['cobertura'][$key]['cobertura_plano_id']}"; ?>" <?php if(isset($carrinho_hidden['plano']) && $carrinho_hidden['plano'] == $plano['produto_parceiro_plano_id'] && isset($carrinho_hidden['cobertura_adicional']) && in_array($plano['cobertura'][$key]['cobertura_plano_id'], $coberturas_selecionadas)) {echo ' checked'; } ?>>
                                                                                                <span class="sp-cobertura-adicional_<?php echo "{$plano['produto_parceiro_plano_id']}_{$plano['cobertura'][$key]['cobertura_plano_id']}"; ?>" ><?php if( isset($plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']]) ) { echo $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']]; } else { echo "<font color=\"red\">Configuração Inválida</font>"; }?></span>
                                                                                            </label>
                                                                                        </div>
                                                                                    <?php endif;?>
                                                                                <?php endif;?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php $i++; endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <div class="clearfix"></div>
                                                </div>
                                              
                                                <?php if(count($planos) > 1): ?>
                                                <div id="slider-two-container" class="plano_slider">
                                                    <ul id="plano_slider_two" class="slide-container">
                                                        <?php foreach ($planos as $plano): ?>
                                                            <li class="">
                                                                <table class="plano_table">
                                                                    <thead>
                                                                    <tr>
                                                                        <th class="plano_header plano_nome_one_<?php echo $plano['produto_parceiro_plano_id']; ?>">
                                                                          <?php echo $plano['nome']; ?> 
                                                                      </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php $i = 1;
                                                                    foreach ($coberturas as $cobertura) : ?>
                                                                        <tr class="<?php echo ($i % 2 == 0) ? '' : 'odd'; ?>">
                                                                            <td>
                                                                                                 <?php if($cobertura['cobertura_tipo_id'] == 1 ) : ?>
                                                                                    <?php 
                                                                                      if( ( $key = array_search($cobertura['cobertura_id'], array_column($plano['cobertura'], 'cobertura_id') ) ) === FALSE ) {
                                                                                        echo '-';
                                                                                      } else { 
                                                                                        //$plano["cobertura"][$key]["mostrar"];
                                                                                        if( $plano["precificacao_tipo_id"] == 2 ) {
                                                                                          if( $plano["cobertura"][$key]["mostrar"] == "preco" ) {
                                                                                            if( isset( $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']] ) ) {
                                                                                              echo $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']];
                                                                                            } else {
                                                                                              echo "<font color=\"red\">Configuração Inválida</font>";
                                                                                            }
                                                                                            
                                                                                          } elseif( $plano["cobertura"][$key]["mostrar"] == "importancia_segurada" ) {
                                                                                            echo "Até R$" . number_format( $cotacao_salva["nota_fiscal_valor"], 2, ",", "." );
                                                                                          } else {
                                                                                            if( isset( $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']] ) ) {
                                                                                              echo $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']];
                                                                                            } else {
                                                                                              echo "<font color=\"red\">Configuração Inválida</font>";
                                                                                            }
                                                                                          }
                                                                                        } else {
                                                                                          if( isset( $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']] ) ) {
                                                                                            echo $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']];
                                                                                          } else {
                                                                                            echo "<font color=\"red\">Configuração Inválida</font>";
                                                                                          }
                                                                                        }
                                                                                      }
                                                                                    ?>
                                                                                <?php else : ?>
                                                                                    <?php if (($key = array_search($cobertura['cobertura_id'], array_column($plano['cobertura'], 'cobertura_id'))) === FALSE) : ?>
                                                                                        -
                                                                                    <?php else : ?>
                                                                                        <?php $exite_cobertura = true; ?>
                                                                                        <div class="checkbox">
                                                                                            <label>
                                                                                                <?php $coberturas_selecionadas = (isset($carrinho_hidden['cobertura_adicional'])) ? explode(';', $carrinho_hidden['cobertura_adicional']) : array(); ?>
                                                                                                <input class="ck-cobertura-adicional" name="ck_cobertura_adicional[]" type="checkbox" value="<?php echo "{$plano['produto_parceiro_plano_id']};{$plano['cobertura'][$key]['cobertura_plano_id']}"; ?>" <?php if(isset($carrinho_hidden['plano']) && $carrinho_hidden['plano'] == $plano['produto_parceiro_plano_id'] && isset($carrinho_hidden['cobertura_adicional']) && in_array($plano['cobertura'][$key]['cobertura_plano_id'], $coberturas_selecionadas)) {echo ' checked'; } ?>>
                                                                                                <span class="sp-cobertura-adicional_<?php echo "{$plano['produto_parceiro_plano_id']}_{$plano['cobertura'][$key]['cobertura_plano_id']}"; ?>" ><?php if( isset($plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']]) ) { echo $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']]; } else { echo "<font color=\"red\">Configuração Inválida</font>"; }?></span>
                                                                                            </label>
                                                                                        </div>
                                                                                    <?php endif;?>
                                                                                <?php endif;?>

                                                                            </td>
                                                                        </tr>
                                                                        <?php $i++; endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <a href="#" class="plano_next" style="right: 5px !important;"></a>
                                                <div class="clearfix"></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-xs-5 no-padding carrossel-left">
                                    <div class="col-xs-6" >
                                        <div class="vendas_bloco_left">
                                            <?php if((count($coberturas) > 0) && ($configuracao['quantidade_cobertura'] < count($coberturas))) : ?>
                                                <a href="#" class="coberturas_ver_tudo">Ver todas as coberturas </a>
                                            <?php endif; ?>
                                            <p style="display: none; clear: both; width: 126px; margin-top: 15px;">
                                                <a href="javascript: void(0);" id="btn_recalcular">Recalcular > </a>
                                            </p>

                                        </div>
                                    </div>
                                    <div class="col-xs-6 no-padding">
                                        <div class="comissao_row">
                                            <ul>
                                                <!--
                                                <li>Até 65 anos</li>
                                                <li>65 a 70 anos</li>-->
                                                <?php if(app_has_config_campo('servico_produto', $row['produto_parceiro_id'])) :  ?>
                                                    <li><?php echo app_produto_traducao('Produto / Serviço', $row['produto_parceiro_id']); ?></li>
                                                    <li><?php echo app_produto_traducao('Unidade de venda', $row['produto_parceiro_id']); ?></li>
                                                    <li><?php echo app_produto_traducao('Quantidade Mínima', $row['produto_parceiro_id']); ?></li>
                                                <?php endif; ?>

                                                <?php if(app_has_config_campo('cotacao_quantidade', $row['produto_parceiro_id'])) :  ?>
                                                    <li><?php echo app_produto_traducao('Quantidade', $row['produto_parceiro_id']); ?></li>
                                                <?php endif; ?>

                                                <?php if ($exite_cobertura) : ?>
                                                    <li><?php echo app_produto_traducao('Coberturas adicionais', $row['produto_parceiro_id']); ?></li>
                                                <?php endif; ?>
                                                <?php if ($configuracao['calculo_tipo_id'] == 1 && $configuracao['repasse_comissao'] == 1) : ?>
                                                <li style="font-weight: bold;"><?php echo app_produto_traducao('PRÊMIO NET', $row['produto_parceiro_id']); ?></li>
                                                    <li><?php echo app_produto_traducao('Comissão', $row['produto_parceiro_id']); ?></li>
                                                    <li><?php echo app_produto_traducao('Repasse de Comissão', $row['produto_parceiro_id']); ?></li>
                                                    <li style="font-weight: bold;"><?php echo app_produto_traducao('Comissão Corretor', $row['produto_parceiro_id']); ?></li>
                                                <?php endif; ?>
                                                <?php if ($desconto['habilitado']) : ?>
                                                    <li><?php echo app_produto_traducao('Desconto Condicional', $row['produto_parceiro_id']); ?></li>
                                                <?php endif; ?>
                                                <li style="text-transform: uppercase; font-weight: bold;" class="li_premio_liquido"><?php echo app_produto_traducao('PRÊMIO LÍQUIDO', $row['produto_parceiro_id']); ?></li>
                                                <?php foreach ($regra_preco as $regra) : ?>
                                                    <li style="text-transform: uppercase; font-weight: bold;"><?php echo $regra['regra_preco_nome']; ?></li>
                                                <?php endforeach; ?>
                                                <?php if($desconto_upgrade == 1) : ?>
                                                    <li><?php echo app_produto_traducao('DESCONTO UPGRADE', $row['produto_parceiro_id']); ?></li>
                                                <?php endif;?>
                                                <li class="li_premio_liquido_total" style="text-transform: uppercase; font-weight: bold;"><?php echo app_produto_traducao('PRÊMIO LÍQUIDO TOTAL', $row['produto_parceiro_id']); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-7">
                                    <div class="vendas_bloco_right">
                                        <div id="vendas_planos_preco">
                                            <div class="col-xs-<?php if(count($planos) > 1) {echo '6';} else {echo '12'; } ?> no-padding">
                                                <div id="slider-preco-one-container" class="preco_slider">
                                                <ul id="plano_slider_preco_one" class="slide-container">
                                                    <?php foreach ($planos as $plano): ?>
                                                        <li class="">
                                                            <?php $div = 1;  ?>
                                                            <input type="hidden" class="desconto_condicional_valor" id="desconto_condicional_valor_one_<?php echo $plano['produto_parceiro_plano_id']; ?>" value="0">
                                                            <table class="preco_table">
                                                                <tbody>
                                                                <?php if(app_has_config_campo('servico_produto', $row['produto_parceiro_id'])) :  ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                            <?php echo isset($servico_produto['nome']) ? $servico_produto['nome'] : '---';?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                            <?php echo isset($servico_produto['unidade']) ? $servico_produto['unidade'] : '---';?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                            <?php echo isset($servico_produto['quantidade_minima']) ? $servico_produto['quantidade_minima'] : '1';?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>


                                                                <?php if(app_has_config_campo('cotacao_quantidade', $row['produto_parceiro_id'])) :  ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><input type="text"
                                                                                   class="quantidade inputmask-numero quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                   id="quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                   name="quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                   value="<?php if (isset($carrossel['quantidade'])) {
                                                                                       echo $carrossel['quantidade'];
                                                                                   } else {
                                                                                       echo '1';
                                                                                   } ?>">
                                                                        </td>
                                                                    </tr>
                                                                <?php else: ?>
                                                                    <input type="hidden"
                                                                           class="quantidade inputmask-numero quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           id="quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           name="quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           value="<?php if (isset($carrossel['quantidade'])) {
                                                                               echo $carrossel['quantidade'];
                                                                           } else {
                                                                               echo '1';
                                                                           } ?>">
                                                                <?php endif; ?>

                                                                <?php if($exite_cobertura) : ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                            <input name="cobertura_adicional_valores_one_<?php echo "{$plano['produto_parceiro_plano_id']}"; ?>" id="cobertura_adicional_valores_one_<?php echo "{$plano['produto_parceiro_plano_id']}"; ?>" type="hidden" value="">
                                                                            <span class="valor_cobertura_adicional valor_cobertura_adicional_one_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                  <!--- Aba 1 -->
                                                                <?php if ($configuracao['calculo_tipo_id'] == 1 && $configuracao['repasse_comissao'] == 1) : ?>
                                                                <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                    <td><span
                                                                                class="premio_bruto premio_bruto_one_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                    </td>
                                                                </tr>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                    <span><?php echo app_format_currency($configuracao['comissao'], false, 2); ?>
                                                                        %</span><input type="hidden" class="comissao"
                                                                                       id="comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                       value="<?php echo $configuracao['comissao']; ?>">
                                                                        </td>
                                                                    </tr>

                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><input type="text"
                                                                                   name="repasse_comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                   class="repasse_comissao inputmask-porcento"
                                                                                   id="repasse_comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                   value="<?php if (isset($carrossel['repasse_comissao'])) echo $carrossel['repasse_comissao']; ?>">
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><span class="comissao_corretor"
                                                                                  id="comissao_corretor_one_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                        </td>
                                                                    </tr>
                                                                <?php else: ?>
                                                                    <input type="hidden" class="comissao"
                                                                           id="comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           value="<?php echo $configuracao['comissao']; ?>">
                                                                    <input type="hidden"
                                                                           name="repasse_comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           class="repasse_comissao inputmask-porcento"
                                                                           id="repasse_comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           value="<?php if (isset($carrossel['repasse_comissao'])) echo $carrossel['repasse_comissao']; ?>">
                                                                <?php endif; ?>
                                                                <?php if ($desconto['habilitado']) : ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>" >
                                                                        <td><input
                                                                                    class="desconto_condicional inputmask-porcento"
                                                                                    type="text"
                                                                                    name="desconto_condicional_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                    id="desconto_condicional_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                    value="<?php if (isset($carrossel['desconto_condicional'])) echo $carrossel['desconto_condicional']; ?>">
                                                                        </td>
                                                                    </tr>
                                                                <?php else : ?>
                                                                    <input
                                                                            class="desconto_condicional inputmask-porcento"
                                                                            type="hidden"
                                                                            name="desconto_condicional_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                            id="desconto_condicional_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                            value="<?php if (isset($carrossel['desconto_condicional'])) echo $carrossel['desconto_condicional']; ?>">


                                                                <?php endif; ?>
                                                                <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?> ">
                                                                    <td><span
                                                                                class="premio_liquido premio_liquido_one_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                    </td>
                                                                </tr>
                                                                <?php foreach ($regra_preco as $regra) : ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><span
                                                                                    class="iof regra_preco_one_<?php echo $plano['produto_parceiro_plano_id']; ?>_<?php echo $regra['produto_parceiro_regra_preco_id'] ?>"><?php echo app_format_currency($regra['parametros'], false, 2); ?>%</span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                                <?php if($desconto_upgrade == 1) : ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><span
                                                                                    class="desconto_upgrade desconto_upgrade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                    <td><span
                                                                                class="premio_total premio_total_one_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                    </td>
                                                                </tr>
                                                                <tr class="">
                                                                    <td class="td-add-car">
                                                                        <a class="add-car" href="javascript: void(0);" data-plano="<?php echo $plano['produto_parceiro_plano_id']; ?>">
                                                                            Escolher
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <div class="clearfix"></div>
                                            </div>
                                            </div>

                                            <?php if(count($planos) > 1): ?>
                                            <div class="col-xs-6 no-padding">
                                                <div id="slider-preco-two-container" class="preco_slider">
                                                <ul id="plano_slider_preco_two" class="slide-container">
                                                    <?php foreach ($planos as $plano): ?>
                                                        <li class="">
                                                            <?php $div = 1; ?>
                                                            <table class="preco_table">
                                                                <tbody>
                                                                <?php if(app_has_config_campo('servico_produto', $row['produto_parceiro_id'])) :  ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                            <?php echo isset($servico_produto['nome']) ? $servico_produto['nome'] : '---';?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                            <?php echo isset($servico_produto['unidade']) ? $servico_produto['unidade'] : '---';?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                            <?php echo isset($servico_produto['quantidade_minima']) ? $servico_produto['quantidade_minima'] : '1';?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <?php if(app_has_config_campo('cotacao_quantidade', $row['produto_parceiro_id'])) :  ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><input type="text"
                                                                                   class="quantidade inputmask-numero quantidade_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                   id="quantidade_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                   name="quantidade_two_<?php echo $plano['produto_parceiro_plano_id']; ?> "
                                                                                   value="<?php if (isset($carrossel['quantidade'])) {
                                                                                       echo $carrossel['quantidade'];
                                                                                   } else {
                                                                                       echo '1';
                                                                                   } ?>">
                                                                        </td>
                                                                    </tr>
                                                                <?php else:  ?>
                                                                    <input type="hidden"
                                                                           class="quantidade inputmask-numero quantidade_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           id="quantidade_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           name="quantidade_two_<?php echo $plano['produto_parceiro_plano_id']; ?> "
                                                                           value="<?php if (isset($carrossel['quantidade'])) {
                                                                               echo $carrossel['quantidade'];
                                                                           } else {
                                                                               echo '1';
                                                                           } ?>">
                                                                <?php endif; ?>
                                                                <?php if($exite_cobertura) : ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                            <input name="cobertura_adicional_valores_two_<?php echo "{$plano['produto_parceiro_plano_id']}"; ?>" id="cobertura_adicional_valores_two_<?php echo "{$plano['produto_parceiro_plano_id']}"; ?>"type="hidden" value="">
                                                                            <span class="valor_cobertura_adicional valor_cobertura_adicional_two_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                  <!--- Aba N -->
                                                                <?php if ($configuracao['calculo_tipo_id'] == 1 && $configuracao['repasse_comissao'] == 1) : ?>
                                                                  <tr  class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                    <td><span
                                                                              class="premio_bruto premio_bruto_two_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                    </td>
                                                                  </tr>
                                                                    <tr  class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td>
                                                                    <span><?php echo app_format_currency($configuracao['comissao'], false, 2); ?>
                                                                        %</span><input type="hidden" class="comissao"
                                                                                       id="comissao_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                       value="<?php echo $configuracao['comissao']; ?>">
                                                                        </td>
                                                                    </tr>

                                                                    <tr  class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><input type="text"
                                                                                   name="repasse_comissao_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                   class="repasse_comissao inputmask-porcento"
                                                                                   id="repasse_comissao_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                   value="<?php if (isset($carrossel['repasse_comissao'])) echo $carrossel['repasse_comissao']; ?>">
                                                                        </td>
                                                                    </tr>
                                                                    <tr  class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                    <td><span class="comissao_corretor"
                                                                              id="comissao_corretor_two_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                    </td>
                                                                    </tr>
                                                                <?php else: ?>
                                                                    <input type="hidden" class="comissao"
                                                                           id="comissao_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           value="<?php echo $configuracao['comissao']; ?>">
                                                                    <input type="hidden"
                                                                           name="repasse_comissao_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           class="repasse_comissao inputmask-porcento"
                                                                           id="repasse_comissao_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                           value="<?php if (isset($carrossel['repasse_comissao'])) echo $carrossel['repasse_comissao']; ?>">
                                                                <?php endif; ?>
                                                                <?php if ($desconto['habilitado']) : ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><input
                                                                                    class="desconto_condicional inputmask-porcento"
                                                                                    type="text"
                                                                                    name="desconto_condicional_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                    id="desconto_condicional_two_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                                    value="<?php if (isset($carrossel['desconto_condicional'])) echo $carrossel['desconto_condicional']; ?>">
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                    <td><span
                                                                                class="premio_liquido premio_liquido_two_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                    </td>
                                                                </tr>
                                                                <?php foreach ($regra_preco as $regra) : ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><span
                                                                                    class="iof regra_preco_two_<?php echo $plano['produto_parceiro_plano_id']; ?>_<?php echo $regra['produto_parceiro_regra_preco_id'] ?>"><?php echo app_format_currency($regra['parametros'], false, 2); ?>%</span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                                <?php if($desconto_upgrade == 1) : ?>
                                                                    <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                        <td><span
                                                                                    class="desconto_upgrade desconto_upgrade_two_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <tr class="<?php if($div%2==0) {echo 'odd';} $div++;  ?>">
                                                                    <td><span
                                                                                class="premio_total premio_total_two_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="td-add-car"><a class="add-car"
                                                                                              href="javascript: void(0);"
                                                                                              data-plano="<?php echo $plano['produto_parceiro_plano_id']; ?>">Escolher</a></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <div class="clearfix"></div>
                                            </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">

                                    <h4>Carrinho de compras</h4>
                                    <input type="hidden" id="quantidade" name="quantidade"
                                           value="<?php if (isset($carrinho_hidden['quantidade'])) echo $carrinho_hidden['quantidade']; ?>"/>
                                    <input type="hidden" id="plano" name="plano"
                                           value="<?php if (isset($carrinho_hidden['plano'])) echo $carrinho_hidden['plano']; ?>"/>
                                    <input type="hidden" id="plano_nome" name="plano_nome"
                                           value="<?php if (isset($carrinho_hidden['plano_nome'])) echo $carrinho_hidden['plano_nome']; ?>"/>
                                    <input type="hidden" id="valor" name="valor"
                                           value="<?php if (isset($carrinho_hidden['valor'])) echo $carrinho_hidden['valor']; ?>"/>
                                    <input type="hidden" id="comissao_repasse" name="comissao_repasse"
                                           value="<?php if (isset($carrinho_hidden['comissao_reoasse'])) echo $carrinho_hidden['comissao_repasse']; ?>"/>
                                    <input type="hidden" id="desconto_condicional" name="desconto_condicional"
                                           value="<?php if (isset($carrinho_hidden['desconto_condicional'])) echo $carrinho_hidden['desconto_condicional']; ?>"/>
                                    <input type="hidden" id="desconto_condicional_valor" name="desconto_condicional_valor"
                                           value="<?php if (isset($carrinho_hidden['desconto_condicional_valor'])) echo $carrinho_hidden['desconto_condicional_valor']; ?>"/>
                                    <input type="hidden" id="valor_total" name="valor_total"
                                           value="<?php if (isset($carrinho_hidden['valor_total'])) echo $carrinho_hidden['valor_total']; ?>"/>
                                    <input type="hidden" id="cobertura_adicional" name="cobertura_adicional"
                                           value="<?php if (isset($carrinho_hidden['cobertura_adicional'])) echo $carrinho_hidden['cobertura_adicional']; ?>"/>
                                    <input type="hidden" id="cobertura_adicional_valor" name="cobertura_adicional_valor"
                                           value="<?php if (isset($carrinho_hidden['cobertura_adicional_valor'])) echo $carrinho_hidden['cobertura_adicional_valor']; ?>"/>
                                    <input type="hidden" id="cobertura_adicional_valor_total" name="cobertura_adicional_valor_total"
                                           value="<?php if (isset($carrinho_hidden['cobertura_adicional_valor_total'])) echo $carrinho_hidden['cobertura_adicional_valor_total']; ?>"/>

                                    <table class="table table-bordered table-primary table-carrinho">

                                        <!-- Table heading -->
                                        <thead>
                                        <tr>
                                            <th class="center">Item</th>
                                            <th width='65%'>Plano</th>
                                            <th width='5%'>Quantidade</th>
                                            <th width='10%'>Valor</th>
                                            <th class="center" width='15%'>Ações</th>
                                        </tr>
                                        </thead>
                                        <!-- // Table heading END -->

                                        <!-- Table body -->
                                        <tbody class="body-carrinho">
                                        <?php if (count($carrinho) == 0) { ?>

                                            <tr>
                                                <td colspan="5"> Seu Carrinho esta vazio</td>
                                            </tr>
                                        <?php } else { ?>

                                            <?php foreach ($carrinho as $item) : ?>
                                                <!-- Table row -->
                                                <tr class="plano-carrinho-<?php echo $item['plano_id']; ?>">
                                                    <td><?php echo $item['item']; ?></td>
                                                    <td><?php echo $item['plano']; ?></td>
                                                    <td><?php echo isset($item['quantidade']) ? $item['quantidade'] : '1'; ?></td>
                                                    <td><?php echo $item['valor']; ?></td>
                                                    <td class="center">
                                                        <a href="javascript:void(0);"
                                                           data-plano="<?php echo $item['plano_id']; ?>"
                                                           class="btn btn-sm btn-danger delete-carrinho"> <i
                                                                class="fa fa-eraser"></i> Excluir </a>
                                                    </td>
                                                </tr>
                                                <!-- // Table row END -->
                                            <?php endforeach;
                                        } ?>

                                        </tbody>
                                        <!-- // Table body END -->

                                    </table>
                                </div>
                            </div>

                        </div>


                        <!-- // Column END -->
                    </div>
                    <!-- // Row END -->
                    <div class="separator"></div>
                    <!-- Form actions -->
                    <!--
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Próximo</button>
                    </div> -->
                    <!-- // Form actions END -->

                </div>
            </div>
            <!-- // Widget END -->


        </form>


        <!-- // Form END -->
    </div>
    <!-- // END col-app -->
</div>
<!-- // END col-app.col-unscrollable -->

<!-- Modal salvar cotação-->

<div class="card">
    <div class="card-body">

        <a href="<?php echo base_url("{$current_controller_uri}/equipamento/{$row['produto_parceiro_id']}/1/$cotacao_id") ?>"
           class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
            <a href="javascript:void(0);" class="btn btn-app btn-primary btn-salvar-cotacao">
                <i class="fa fa-edit"></i> Salvar Cotação
            </a>
        <a class="btn pull-right btn_dados_segurado  btn-app btn-primary">
            <i class="fa fa-arrow-right"></i> Próximo
        </a>
    </div>
</div>
<script>
    var layout = "base";
</script>
<script>
  AppController.controller("AppController", ["$scope", "$sce", "$http", "$filter", "$timeout", "$interval", function ( $scope, $sce, $http, $filter, $timeout, $interval ) {
    $scope.cotacao_id = "<?php echo $cotacao_id ?>";
    $scope.Token = "<?php echo app_get_token() ?>";
    $scope.AuthHeaders = {"apikey": $scope.Token };
    
    console.log( "Cotação: " + $scope.cotacao_id );
    console.log( "Token: " + $scope.Token );
    
    var URL = "<?= $this->config->item('base_url') ?>api/cotacao/calculo?cotacao_id=" + $scope.cotacao_id;
    $http.get( URL, { headers: $scope.AuthHeaders } )
      .success( function( data ) {
      $scope.Calculo = data;
      console.log( $scope.Calculo );
      //toastr.success("Calculo efetuado com sucesso via API!", "Calcular cotação");
    });

    
  }]);
</script>








