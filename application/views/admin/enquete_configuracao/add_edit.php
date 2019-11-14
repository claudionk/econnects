<div ng-controller="Enquete">

    <!-- Seção do topo -->
    <div class="section-header">
        <div class="row">
            <div class="col-md-6">
                <ul class="actions">
                    <li>
                        <a href="<?php echo base_url("{$controller_url}/index")?>" class="btn ink-reaction btn-floating-action btn-sm btn-primary">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </li>
                    <li><h2><?php echo $titulo;?></h2></li>
                </ul>

            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>

    <!-- Mensagens -->
    <?php $this->load->view('admin/partials/messages'); ?>

    <!-- Conteudo -->
    <div class="card">

        <form class="form" id="validateSubmitForm" validate="true" model_validate="<?php echo $model_name ?>" method="post" autocomplete="off" enctype="multipart/form-data">

            <!-- Título -->
            <div class="card-head style-gray-dark">
                <header><?php if($new_record) echo $titulo_adicionar; else echo $titulo_editar; ?> <?php echo $titulo_singular ?></header>
            </div>

            <!-- Conteúdo -->
            <div class="card-body">

                <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>

                <div class="row">

                    <div id="validation_errors"></div>


                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                $field_name = "enquete_id";
                                template_control("select", $field_name, "Enquete", $row[$field_name], array(
                                    'options' => $enquete_list,
                                    'valor' => $field_name,
                                    'descricao' => 'nome',
                                ));
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                $field_name = "envio_tipo";
                                template_control("select", $field_name, "Tipo de envio", $row[$field_name], array(
                                    'options' => array(
                                        array('valor' => 'sms', 'descricao' => "SMS"),
                                        array('valor' => 'email', 'descricao' => "E-mail"),
                                    ),
                                ));
                                ?>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <?php
                                $field_name = "envio_mensagem";
                                template_control("textarea", $field_name, "Mensagem", $row[$field_name], array(
                                ));
                                ?>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <h4>Critérios de Distribuição</h4>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <?php
                                $field_name = "gatilhos";


                                $value_gatilhos = isset($enquete_gatilho_configuracao) ? $enquete_gatilho_configuracao : array();

                                template_control("select", $field_name . '[]', "Gatilhos para esta enquete", $value_gatilhos, array(
                                    'options' => $enquete_gatilho_list,
                                    'valor' => 'enquete_gatilho_id',
                                    'descricao' => 'nome',
                                    'multiple' => true,
                                    'id' => $field_name,
                                ));
                                ?>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                $field_name = "clientes";
                                $value_clientes = isset($row[$field_name]) ? explode(",", $row[$field_name]) : array();

                                template_control("select", $field_name . '[]', "Clientes", $value_clientes, array(
                                    'options' => $cliente_list,
                                    'valor' => 'id_cliente',
                                    'descricao' => 'nome_fantasia',
                                    'multiple' => true,
                                    'id' => $field_name,
                                ));
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                $field_name = "estipulantes";

                                template_control("select", $field_name . '[]', "Estipulantes", $value_estipulantes, array(
                                    'multiple' => true,
                                    'id' => $field_name,
                                ));
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                $field_name = "prestacoes";
                                $value_prestacoes = isset($row[$field_name]) ? explode(",", $row[$field_name]) : array();

                                template_control("select", $field_name . '[]', "Prestações", $value_prestacoes, array(
                                    'options' => $prestacao_list,
                                    'valor' => 'cod_prestacao',
                                    'descricao' => 'prestacao',
                                    'multiple' => true,
                                    'id' => $field_name,
                                ));
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                $field_name = "contratos";

                                template_control("select", $field_name . '[]', "Contratos", $value_contratos, array(
                                    'options' => $contrato_list,
                                    'valor' => 'id_contrato',
                                    'descricao' => 'descricao',
                                    'multiple' => true,
                                    'id' => $field_name,
                                ));
                                ?>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                $field_name = "ativo";
                                template_control("select", $field_name, "Enquete ativa", $row[$field_name], array(
                                    'options' => array(
                                        array('descricao' => "Sim", 'valor' => 1),
                                        array('descricao' => "Não", 'valor' => 0),
                                    ),
                                    'id' => $field_name,
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Salvar</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    var estipulantes_valores = <?php echo isset($estipulantes_selecionados) ? json_encode($estipulantes_selecionados) : '[]'; ?>;

    var contratos_valores = <?php echo isset($contratos_selecionados) ? json_encode($contratos_selecionados) : '[]'; ?>;
</script>