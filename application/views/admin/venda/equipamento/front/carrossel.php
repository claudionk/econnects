<?php
    $exite_cobertura = false;
?>

<!-- col-app -->
<div class="card">
    <!-- col-app -->
    <div class="card-body" style="background-color: #eee">
        <!-- Form -->
        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off"
              enctype="multipart/form-data">
            <input type="hidden" id="<?php echo $primary_key ?>" name="<?php echo $primary_key ?>"
                   value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
            <input type="hidden" id="url_calculo" name="url_calculo"
                   value="<?php echo base_url("{$current_controller_uri}/calculo"); ?>"/>
            <input type="hidden" id="produto_parceiro_plano_id" name="produto_parceiro_plano_id" value="0"/>
            <input type="hidden" id="parceiro_id" name="parceiro_id" value="<?php echo $parceiro_id; ?>"/>
            <input type="hidden" id="cotacao_id" name="cotacao_id"
                   value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>
            <input type="hidden" id="salvar_cotacao" name="salvar_cotacao" value=""/>
            <?php $configuracao['quantidade_cobertura'] = ((isset($configuracao['quantidade_cobertura_front'])) && ($configuracao['quantidade_cobertura_front'] < count($coberturas) )) ? $configuracao['quantidade_cobertura_front']  : count($coberturas); ?>
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

                <!-- Column -->
                <div class="col-md-12">

                    <h2 class="text-light text-center">Cotação de Seguro<br><small class="text-primary">Selecione o plano e as coberturas que você deseja!</small></h2>

                    <?php $this->load->view('admin/venda/equipamento/front/step', array('step' => 2, 'produto_parceiro_id' =>  issetor($produto_parceiro_id))); ?>



                    <div class="row">


                        <?php foreach ($planos as $plano): ?>
                            <div class="col-sm-4">

                                <div class="card card-type-pricing text-center">

                                    <div class="card-body">
                                        <h2 class="text-light plano_nome_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"><?php echo $plano['nome'] ?></h2>
                                        <div class="price">
                                            <H1 class="text-xl">R$</H1>

                                            <h2>
                                                    <span class="text-xl">
                                                    <span class="premio_total premio_total_one_<?php echo $plano['produto_parceiro_plano_id']; ?>">---</span></span>
                                            </h2>

                                            <span></span>
                                        </div>
                                        <br>

                                        <!--p class="opacity-50"><em>Rame aute irure dolor in reprehenderit pariatur.</em></p-->

                                    </div><!--end .card-body -->


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
                                        <?php $i++; endforeach; ?>
                                            <?php if(count($coberturas) > 0) : ?>
                                                <!--<li class="row cobertura">
                                                    <button type="button" class="btn btn-block ink-reaction btn-primary-dark coberturas_ver_tudo_front">Ver coberturas</button>
                                                </li>-->
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <a class="btn btn-primary add-car" href="javascript: void(0);" data-plano="<?php echo $plano['produto_parceiro_plano_id']; ?>">
                                            QUERO ESTE >
                                        </a>
                                    </div><!--end .card-body -->
                                </div><!--end .card -->
                            </div><!--end .col -->
                        <?php endforeach; ?>

                        <?php foreach ($planos as $plano): ?>

                            <?php $div = 1;  ?>
                            <input type="hidden" class="desconto_condicional_valor" id="desconto_condicional_valor_one_<?php echo $plano['produto_parceiro_plano_id']; ?>" value="0">

                            <?php if($exite_cobertura) : ?>
                                <input name="cobertura_adicional_valores_one_<?php echo "{$plano['produto_parceiro_plano_id']}"; ?>" id="cobertura_adicional_valores_one_<?php echo "{$plano['produto_parceiro_plano_id']}"; ?>" type="hidden" value="">
                            <?php endif; ?>
                                <input type="hidden" class="comissao"
                                       id="comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                       value="<?php echo $configuracao['comissao']; ?>">
                            <?php if ($configuracao['repasse_comissao'] == 1) : ?>

                                <input type="hidden"
                                       name="repasse_comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                       class="repasse_comissao inputmask-porcento"
                                       id="repasse_comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                       value="<?php if (isset($carrossel['repasse_comissao'])) echo $carrossel['repasse_comissao']; ?>">

                            <?php else: ?>
                                <input type="hidden"
                                       name="repasse_comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                       class="repasse_comissao inputmask-porcento"
                                       id="repasse_comissao_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                       value="<?php if (isset($carrossel['repasse_comissao'])) echo $carrossel['repasse_comissao']; ?>">
                            <?php endif; ?>


                            <?php if ($desconto['habilitado']) : ?>
                                <input class="desconto_condicional inputmask-porcento"
                                       type="text"
                                       name="desconto_condicional_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                       id="desconto_condicional_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                       value="<?php if (isset($carrossel['desconto_condicional'])) echo $carrossel['desconto_condicional']; ?>">

                            <?php else : ?>
                                <input
                                        class="desconto_condicional inputmask-porcento"
                                        type="hidden"
                                        name="desconto_condicional_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                        id="desconto_condicional_one_<?php echo $plano['produto_parceiro_plano_id']; ?>"
                                        value="<?php if (isset($carrossel['desconto_condicional'])) echo $carrossel['desconto_condicional']; ?>">
                            <?php endif; ?>

                        <?php endforeach; ?>

                    </div>

                </div>

                <div class="col-md-12" style="background: #FFFFFF">

                    <h2 class="text-light text-center"><small class="text-primary">Plano Selecionado</small></h2>
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
<!--                            <th class="center">Item</th>-->
                            <th width='70%'>Plano</th>
<!--                            <th width='5%'>Quantidade</th>-->
                            <th width='30%'>Valor</th>
<!--                            <th class="center" width='15%'>Ações</th>-->
                        </tr>
                        </thead>
                        <!-- // Table heading END -->

                        <!-- Table body -->
                        <tbody class="body-carrinho">
                        <?php if (count($carrinho) == 0) { ?>

                            <tr>
                                <td colspan="5"> Nenhum Plano Selecionado</td>
                            </tr>
                        <?php } else { ?>

                            <?php foreach ($carrinho as $item) : ?>
                                <!-- Table row -->
                                <tr class="plano-carrinho-<?php echo $item['plano_id']; ?>">
<!--                                    <td>--><?php //echo $item['item']; ?><!--</td>-->
                                    <td><?php echo $item['plano']; ?></td>
<!--                                    <td>--><?php //echo $item['quantidade']; ?><!--</td>-->
                                    <td><?php echo $item['valor']; ?></td>
<!--                                    <td class="center">-->
<!--                                        <a href="javascript:void(0);"-->
<!--                                           data-plano="--><?php //echo $item['plano_id']; ?><!--"-->
<!--                                           class="btn btn-sm btn-danger delete-carrinho"> <i-->
<!--                                                    class="fa fa-eraser"></i> Excluir </a>-->
<!--                                    </td>-->
                                </tr>
                                <!-- // Table row END -->
                            <?php endforeach;
                        } ?>

                        </tbody>
                        <!-- // Table body END -->

                    </table>
                </div>

            </div>

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

        <a class="btn btn_dados_segurado pull-right btn-app btn-primary">
            <i class="fa fa-arrow-right"></i> Próximo passo
        </a>
    </div>
</div>


<script>
    var layout = "front";
</script>