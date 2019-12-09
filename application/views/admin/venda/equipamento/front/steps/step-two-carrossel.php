<?php $exite_cobertura = false; ?>

<!-- col-app -->
<div class="">
    <!-- col-app -->
    <div class="" style="background-color: #eee">
        <!-- Form -->
        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
            <!--  -->
            <input type="hidden" id="getToken" value="<?php echo $this->input->get('token'); ?>" />
            <input type="hidden" id="getLayout" value="<?php echo $this->input->get('layout'); ?>" />
            <input type="hidden" id="getColor" value="<?php echo $this->input->get('color'); ?>" />
            <!--  -->

            <input type="hidden" id="<?php echo $primary_key ?>" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
            <input type="hidden" id="url_calculo" name="url_calculo" value="<?php echo base_url("{$current_controller_uri}/calculo"); ?>"/>
            <input type="hidden" id="produto_parceiro_plano_id" name="produto_parceiro_plano_id" value="0"/>
            <input type="hidden" id="parceiro_id" name="parceiro_id" value="<?php echo $parceiro_id; ?>"/>
            <input type="hidden" id="cotacao_id" name="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>
            <input type="hidden" id="salvar_cotacao" name="salvar_cotacao" />
            <?php $configuracao['quantidade_cobertura'] = ((!empty($configuracao['quantidade_cobertura_front'])) && ($configuracao['quantidade_cobertura_front'] < count($coberturas) )) ? $configuracao['quantidade_cobertura_front'] : count($coberturas); ?>
            <input type="hidden" id="quantidade_cobertura" name="quantidade_cobertura" value="<?php  echo (isset($configuracao['quantidade_cobertura_front'])) ? $configuracao['quantidade_cobertura_front'] : 10;  ?>"/>
            <input type="hidden" id="total_cobertura" name="total_cobertura" value="<?php  echo count($coberturas);  ?>"/>

            <div class="row">
                <div class="col-md-6">
                    <?php $this->load->view('admin/partials/validation_errors'); ?>
                    <?php $this->load->view('admin/partials/messages'); ?>
                </div>
            </div>

            <!-- Row -->
            <div class="row">

                <?php $this->load->view('admin/venda/equipamento/front/step', array('step' => 2, 'produto_parceiro_id' =>  issetor($produto_parceiro_id), 'title' => 'COTAÇÕES')); ?>


                <div id="carousel-example-generic" class="carousel slide">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    </ol>

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">
                        <?php
                        foreach ($planos as $key => $plano): //echo '<pre>',print_r($plano);
                        ?>

                            <div class="item <?php if($key == 0){ echo 'active'; } ?>">
                                <div class="col-xs-8 col-xs-offset-2 block-plans">
                                    <div class="card card-type-pricing text-center">
                                        <div class="card-body">
                                            <h2 class="text-light plano_nome_one_<?php echo $plano['produto_parceiro_plano_id']; ?> name-plan"><?php echo $plano['nome'] ?></h2>
                                            <div class="price">
                                                <h1 class="text-xl moeda-plan">R$</h1>
                                                <h2>
                                                    <span class="text-xl price-plan" id="price<?php echo $plano['produto_parceiro_plano_id']; ?>"> </span>
                                                </h2>
                                                <h1 class="text-xl moeda-plan" id="cents<?php echo $plano['produto_parceiro_plano_id']; ?>"></h1>
                                                <span></span>
                                            </div>

                                            <ul class="list details-plan">
                                                <?php
                                                $array_cobertura = array();
                                                foreach ($plano['cobertura'] as $cobertura){
                                                    $array_cobertura[] = $cobertura['cobertura_id'];
                                                }

                                                $array_modal = array();
                                                $qtde_cob_view = 0;
                                                $hide_cobs = false;
                                                foreach($merge_coberturas as $key => $merge)
                                                {
                                                    if ($qtde_cob_view >= $configuracao['quantidade_cobertura'] && !$hide_cobs)
                                                    {
                                                        $hide_cobs = true;
                                                        echo "<li><ul class='list details-hide-plan hidden'>";
                                                    }
                                                    $class = 'fa fa-times-circle error';
                                                    if(in_array($merge, $array_cobertura)){
                                                        $array_modal[] = $key;
                                                        $class = 'fa fa-chevron-circle-right success';
                                                    }
                                                    echo '<li><i class="'.$class.'" aria-hidden="true"></i> '.$key.'</li>';
                                                    $qtde_cob_view++;
                                                }

                                                if ($hide_cobs)
                                                {
                                                    echo "</ul></li>";
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                            if ($hide_cobs)
                                            {
                                                echo "<div class='details-plan-more'>+ coberturas</div>";
                                            }

                                            if (count($coberturas) > 3){
                                            ?>
                                                <a href="#" class="more-plan color-primary price-moeda-<?php echo $plano['produto_parceiro_plano_id']; ?>" data-toggle="modal" data-target="#modalCoberturas"
                                                   data-title="<?php echo $plano['nome']; ?>"
                                                   data-price="00"
                                                   data-cents="00"
                                                   data-coberturas="<?php echo implode(',',$array_modal); ?>">Saiba mais</a>
                                            <?php 
                                            }
                                            ?>
                                            <div class="this-plan">
                                                <a class="btn btn-primary add-car this-plan-btn background-primary border-primary" data-plano="<?php echo $plano['produto_parceiro_plano_id']; ?>" href="javascript: void(0);">
                                                    QUERO ESTE
                                                </a>
                                            </div>
                                        </div>

                                        <div class="card-body coberturas">
                                            <ul class="list-unstyled">
                                                <?php if(app_has_config_campo('cotacao_quantidade', $row['produto_parceiro_id'])) :  ?>
                                                    <li class="row cobertura">
                                                        <div class="form-group col-md-8">
                                                            <label class="control-label" for="quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>">Quantidade:</label>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <input type="text"
                                                                   class="form-control quantidade inputmask-numero"
                                                                   id="quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                   name="quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                                   value="<?php if (isset($carrossel['quantidade'])) {
                                                                       echo $carrossel['quantidade'];
                                                                   } else {
                                                                       echo '1';
                                                                   } ?>">
                                                        </div>
                                                    </li>

                                                <?php else: ?>

                                                    <input type="hidden"
                                                           class="quantidade inputmask-numero"
                                                           id="quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                                           name="quantidade_one_<?php echo $plano['produto_parceiro_plano_id']; ?> "
                                                           value="<?php if (isset($carrossel['quantidade'])) {
                                                               echo $carrossel['quantidade'];
                                                           } else {
                                                               echo '1';
                                                           } ?>">
                                                <?php endif; ?>

                                                <?php $i = 1; foreach ($coberturas as $cobertura) : ?>
                                                    <li class="row cobertura list_cobertura_<?php echo $i?>">
                                                        <div class="col-md-8 no-padding"><?php echo $cobertura['nome'] ?></div>
                                                        <div class="col-md-4 no-padding">
                                                            <?php if($cobertura['cobertura_tipo_id'] == 1 ) : ?>
                                                                <?php echo (($key = array_search($cobertura['cobertura_id'], array_column($plano['cobertura'], 'cobertura_id'))) === FALSE) ? '-' : $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']]; ?>
                                                            <?php else : ?>
                                                                <?php if (($key = array_search($cobertura['cobertura_id'], array_column($plano['cobertura'], 'cobertura_id'))) === FALSE) : ?>
                                                                    <span>---</span>
                                                                <?php else : ?>
                                                                    <?php $exite_cobertura = true; ?>
                                                                    <div class="checkbox">
                                                                        <label>
                                                                            <?php $coberturas_selecionadas = (isset($carrinho_hidden['cobertura_adicional'])) ? explode(';', $carrinho_hidden['cobertura_adicional']) : array(); ?>
                                                                            <input class="ck-cobertura-adicional" name="ck_cobertura_adicional[]" type="checkbox" value="<?php echo "{$plano['produto_parceiro_plano_id']};{$plano['cobertura'][$key]['cobertura_plano_id']}"; ?>" <?php if(isset($carrinho_hidden['plano']) && $carrinho_hidden['plano'] == $plano['produto_parceiro_plano_id'] && isset($carrinho_hidden['cobertura_adicional']) && in_array($plano['cobertura'][$key]['cobertura_plano_id'], $coberturas_selecionadas)) {echo ' checked'; } ?>>
                                                                            <span class="sp-cobertura-adicional_<?php echo "{$plano['produto_parceiro_plano_id']}_{$plano['cobertura'][$key]['cobertura_plano_id']}"; ?>" >
                                                                                <?php echo $plano['cobertura'][$key][$plano['cobertura'][$key]['mostrar']]; ?>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                <?php endif;?>
                                                            <?php endif;?>
                                                        </div>
                                                    </li>
                                                    <?php $i++; endforeach;

                                                if(count($coberturas) > 0) : ?>
                                                    <!--<li class="row cobertura">
                                                        <button type="button" class="btn btn-block ink-reaction btn-primary-dark coberturas_ver_tudo_front">Ver coberturas</button>
                                                    </li>-->
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>

<?php 
/* Bloco que efetua o cálculo
 */
?>
<div style="display:none;">
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
    <div class="row">
        <div class="col-md-12">
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
        </div>
    </div>
</div>
<?php 
/* fim do Bloco
 */
?>


            </div>
        </form>
        <!-- // Form END -->
    </div>
    <!-- // END col-app -->
</div>
<!-- // END col-app.col-unscrollable -->

<!-- modal more info -->
<div class="modal fade" id="modalCoberturas" role="dialog">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <h2 class="title modal-title">BÁSICO</h2>
                <!--div class="price-block">
                    <small class="cifrao moeda">R$</small>
                    <span class="price modal-price"> 00 </span>
                    <small class="cifrao modal-cents">,00</small>
                </div-->
                <ul class="list details-plan"></ul>
            </div>
        </div>
    </div>
</div>

<div class="card" style="display: none;">
    <div class="card-body">

        <a href="<?php echo base_url("{$current_controller_uri}/equipamento/{$row['produto_parceiro_id']}/1/$cotacao_id") ?>"
           class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>

        <a class="btn btn_dados_segurado pull-right btn-app btn-primary">
            <i class="fa fa-arrow-right"></i> Próximo passo
        </a>
    </div>
</div>

<script>
    var layout = "front";
</script>

<?php $this->load->view('admin/venda/equipamento/components/btn-info'); ?>

<?php $this->load->view('admin/venda/equipamento/components/btn-whatsapp'); ?>

<?php $this->load->view('admin/venda/equipamento/components/footer'); ?>