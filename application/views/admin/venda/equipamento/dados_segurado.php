
<?php if ($layout != "front") { ?>
<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo $page_title; ?></li>
    </ol>
</div>


<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/equipamento/{$carrossel['produto_parceiro_id']}/2/{$cotacao_id}")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
        <a class="btn pull-right btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-arrow-right"></i> Próximo
        </a>
    </div>
</div>

<?php } ?>
<!-- col-app -->
<div class="card">

    <!-- col-app -->
    <div class="card-body" <?php echo ((isset($layout)) && ($layout == 'front')) ? 'style="background-color: #eee"' : ''; ?>>

        <!-- Form -->
        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="produto_parceiro_id" value="<?php if (isset($carrossel['produto_parceiro_id'])) echo $carrossel['produto_parceiro_id']; ?>"/>
            <input type="hidden" name="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>

            <h2 class="text-light text-center"><?php echo app_produto_traducao('Dados da Contratação', $carrossel['produto_parceiro_id']); ?><br>
                <small class="text-primary"><?php echo app_produto_traducao('Informe os dados pessoais da contratação', $carrossel['produto_parceiro_id']); ?></small>
            </h2>

            <?php
                if((isset($layout)) && ($layout == 'front')) {
                    $this->load->view('admin/venda/equipamento/front/step', array('step' => 3, 'produto_parceiro_id' => $carrossel['produto_parceiro_id'] ));
                }else{
                    $this->load->view('admin/venda/step', array('step' => 3, 'produto_parceiro_id' => $carrossel['produto_parceiro_id'] ));
                }
            ?>

                <div class="row">
                    <div class="col-md-6">
                        <?php //$this->load->view('admin/partials/validation_errors');?>
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>


                <?php
                    $planos = explode(';', $carrossel['plano']);
                    $plano_nome = explode(';', $carrossel['plano_nome']);
                    $valor_total = explode(';', $carrossel['valor_total']);
                    $expanded = " aria-expanded=\"true\"";
                    $collapse = " in";
                    foreach ($planos as $index => $plano) :
                ?>
                        <div class="panel-group col-md-12" id="accordion6">



                                        <div class="card panel expanded">
                                            <div class="card-head style-primary" data-toggle="collapse" data-parent="#accordion6" data-target="#accordion6-1" <?php echo $expanded; ?>>
                                                <header><?php echo app_produto_traducao('Contratante', $carrossel['produto_parceiro_id']); ?></header>
                                                <div class="tools">
                                                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                                </div>
                                            </div>
                                            <div id="accordion6-1" class="collapse<?php echo $collapse; ?>" <?php echo $expanded; ?>>

                                                <div class="panel-body">
                                                    <?php
                                                    $data_row = array(
                                                        "plano_{$plano}_cnpj_cpf" => $cotacao['cnpj_cpf'],
                                                        "plano_{$plano}_nome" => $cotacao['nome'],
                                                        "plano_{$plano}_rg" => $cotacao['rg'],
                                                        "plano_{$plano}_contato_telefone" => $cotacao['telefone'],
                                                        "plano_{$plano}_data_nascimento" => $cotacao['data_nascimento'],
                                                        "plano_{$plano}_equipamento_id" => $cotacao['equipamento_id'],
                                                        "plano_{$plano}_equipamento_categoria_id" => $cotacao['equipamento_categoria_id'],
                                                        "plano_{$plano}_equipamento_marca_id" => $cotacao['equipamento_marca_id'],
                                                        "equipamento_id" => $cotacao['equipamento_id'],
                                                        "equipamento_categoria_id" => $cotacao['equipamento_categoria_id'],
                                                        "equipamento_marca_id" => $cotacao['equipamento_marca_id'],
                                                    );

                                                    $dados_sessao = $this->session->userdata("cotacao_" . $carrossel['produto_parceiro_id']);

                                                    ?>

                                                    <?php foreach ($campos as $campo): ?>

                                                        <?php

                                                        $data_campo = array();


                                                        //Seta valor do campo
                                                        if(isset($_POST["plano_{$plano}_{$campo['campo_nome_banco']}"]) && !empty($_POST["plano_{$plano}_{$campo['campo_nome_banco']}"]))
                                                        {
                                                            $data_row["plano_{$plano}_{$campo['campo_nome_banco']}"] = $_POST["plano_{$plano}_{$campo['campo_nome_banco']}"];
                                                        }
                                                        elseif (isset($row["plano_{$plano}_{$campo['campo_nome_banco']}"]) && !empty($row["plano_{$plano}_{$campo['campo_nome_banco']}"]))
                                                        {
                                                            $data_row["plano_{$plano}_{$campo['campo_nome_banco']}"] = $row["plano_{$plano}_{$campo['campo_nome_banco']}"];
                                                        }
                                                        //Verifica na sessão
                                                        else if (isset($dados_sessao) && isset($dados_sessao[$campo['campo_nome_banco']]))
                                                        {
                                                            $data_row["plano_{$plano}_{$campo['campo_nome_banco']}"] = $dados_sessao[$campo['campo_nome_banco']];
                                                        }
                                                        //Verifica na sessão
                                                        else if (isset($dados_sessao) && isset($dados_sessao[$campo['campo_nome_banco_equipamento']]))
                                                        {
                                                            $data_row["plano_{$plano}_{$campo['campo_nome_banco']}"] = $dados_sessao[$campo['campo_nome_banco_equipamento']];
                                                        }

                                                        $data_campo['passageiro'] = 1;
                                                        $data_campo['row'] = $data_row;
                                                        $data_campo['plano_id'] = $plano;
                                                        $data_campo['field_name'] = "plano_{$plano}_{$campo['campo_nome_banco']}";
                                                        $data_campo['field_label'] = $campo['campo_nome'];
                                                        $data_campo['list'] = isset($list) ? $list : array();
                                                        $data_campo['tamanho'] = $campo['tamanho'] == 0 ? 6 : $campo['tamanho'];
                                                        $data_campo['class'] = $campo['campo_classes'];
                                                        $data_campo['opcoes'] = $campo['campo_opcoes'];

                                                        ?>

                                                        <?php $this->load->view('admin/campos_sistema/'. $campo['campo_slug'], $data_campo);?>


                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php
                                    $expanded = '';
                                    $collapse = '';
                                    ?>

                        </div>
                    <?php endforeach; ?>

            <!-- // Widget END -->
        </form>
        <!-- // Form END -->
    </div>
</div>

<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/equipamento/{$carrossel['produto_parceiro_id']}/2/{$cotacao_id}")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
        <a class="btn pull-right btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-arrow-right"></i> Próximo
        </a>
    </div>
</div>

