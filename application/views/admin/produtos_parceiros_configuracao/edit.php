<?php
if ($_POST) {
    $row = $_POST;
}

?>
<div class="layout-app">
  <!-- row -->
  <div class="row row-app">
    <!-- col -->
    <div class="col-md-12">
      <!-- col-separator.box -->
      <!-- col-separator.box -->
      <div class="section-header">
        <ol class="breadcrumb">
          <li class="active"><?php echo app_recurso_nome(); ?></li>
        </ol>

      </div>

      <div class="card">

        <!-- Widget heading -->
        <div class="card-body">

          <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}") ?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
          </a>
          <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Salvar
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
                  <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) {echo $row[$primary_key];}?>"/>
                  <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                  <input type="hidden" name="produto_parceiro_id" value="<?php echo $produto_parceiro_id; ?>"/>
                  <!-- Widget -->
                  <div class="row">
                    <div class="col-md-6">
                      <?php $this->load->view('admin/partials/validation_errors');?>
                      <?php $this->load->view('admin/partials/messages');?>
                    </div>

                  </div>

                  <div class="card">


                    <?php $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>

                    <div class="card-body tab-content">

                      <!-- Row -->
                      <div class="row innerLR">


                        <!-- Tabs Heading -->

                        <!-- // Tabs Heading END -->

                        <div class="widget-body">
                          <div class="tab-content">
                            <div class="col-md-12">
                              <div class="card tabs-left style-default-light">
                                <!-- Tab content -->
                                <?php $this->load->view('admin/produtos_parceiros_configuracao/sub_tab_regra_negocio');?>
                                <div class="card-body tab-content style-default-bright">
                                  <div id="tabGeral" class="tab-pane active widget-body-regular">


                                    <?php $field_name = 'salvar_cotacao_formulario';?>
                                    <div class="radio radio-styled">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Formulário Salvar cotação *</label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') {echo 'checked="checked"';}?> />
                                        Sim
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') {echo 'checked="checked"';}?> />
                                        Não
                                      </label>
                                    </div>
                                    <?php $field_name = 'venda_habilitada_admin';?>
                                    <div class="radio radio-styled">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Venda pelo Admin *</label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') {echo 'checked="checked"';}?> />
                                        Sim
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') {echo 'checked="checked"';}?> />
                                        Não
                                      </label>
                                    </div>
                                    <?php $field_name = 'venda_habilitada_web';?>
                                    <div class="radio radio-styled">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Venda pela WEB *</label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') {echo 'checked="checked"';}?> />
                                        Sim
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') {echo 'checked="checked"';}?> />
                                        Não
                                      </label>
                                    </div>
                                    <div class="form-group" id="url_venda_online">
                                      <div class="col-md-4 text-right">
                                        URL de venda online
                                      </div>
                                      <div class="col-md-8">
                                        <?php echo issetor($url_venda_online) ?>
                                      </div>
                                    </div>

                                    <?php $field_name = 'venda_carrinho_compras';?>
                                    <div class="radio radio-styled">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Venda com carrinho de compras *</label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') {echo 'checked="checked"';}?> />
                                        Sim
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') {echo 'checked="checked"';}?> />
                                        Não
                                      </label>
                                    </div>
                                    <?php // $field_name = 'venda_multiplo_cartao';?>
                                    <!--
                                        <div class="radio radio-styled">
                                        <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Venda com multiplos cartões *</label>
                                        <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                        value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') {echo 'checked="checked"';}?> />
                                        Sim
                                        </label>
                                        <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                        value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') {echo 'checked="checked"';}?> />
                                        Não
                                        </label>
                                        </div> -->
                                    <?php $field_name = 'calculo_tipo_id';?>
                                    <div class="radio radio-styled">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Tipo de cálculo</label>
                                      <div class="col-md-4">
                                        <select class="form-control" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>">
                                          <option  value="">Selecione</option>
                                          <?php foreach ($calculo_tipo as $linha) {?>
                                          <option  value="<?php echo $linha[$field_name] ?>"
                                                  <?php if (isset($row[$field_name])) {if ($row[$field_name] == $linha[$field_name]) {echo " selected ";};};?> >
                                            <?php echo $linha['nome']; ?>
                                          </option>
                                          <?php }?>
                                        </select>
                                      </div>
                                    </div>
                                    <br />
                                    <?php $field_name = 'apolice_sequencia';?>
                                    <div class="radio radio-styled">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Certificado *</label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') {echo 'checked="checked"';}?> />
                                        <span>Sequencial </span>
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') {echo 'checked="checked"';}?> />
                                        <span>Range</span>
                                      </label>
                                    </div>
                                    <?php $field_name = 'quantidade_cobertura_front';?>
                                    <div class="row">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Quantidade de Coberturas Exibidas (Mobile) *</label>
                                      <div class="col-md-4"><input class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                    </div>
                                    <?php $field_name = 'quantidade_cobertura';?>
                                    <div class="row">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Quantidade de Coberturas Exibidas *</label>
                                      <div class="col-md-4"><input class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                    </div>

                                    <?php $field_name = 'apolice_vigencia';?>
                                    <div class="row">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Data Base para Início da Vigência *</label>
                                      <div class="col-md-4">
                                        <select class="form-control" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>">
                                          <option value="">Selecione</option>
                                          <option value="S" <?php if (isset($row[$field_name])) {if ($row[$field_name] == 'S') {echo " selected ";};};?> >Data de Criação</option>
                                          <option value="N" <?php if (isset($row[$field_name])) {if ($row[$field_name] == 'N') {echo " selected ";};};?> >Data da Nota Fiscal</option>
                                          <option value="E" <?php if (isset($row[$field_name])) {if ($row[$field_name] == 'E') {echo " selected ";};};?> >Específica (Somente via API)</option>
                                        </select>
                                      </div>
                                    </div>
                                    <br>

                                    <?php $field_name = 'apolice_vigencia_regra';?>
                                    <div class="row">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Regra para Início da Vigência *</label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="N" <?php if (isset($row[$field_name]) && $row[$field_name] == 'N') {echo 'checked="checked"';}?> />
                                        <span>Não possui</span>
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="M" <?php if (isset($row[$field_name]) && $row[$field_name] == 'M') {echo 'checked="checked"';}?> />
                                        <span>A partir da Meia Noite</span>
                                      </label>
                                    </div>

                                    <?php $field_name = 'conclui_em_tempo_real';?>
                                    <div class="row">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Efetiva a Apólice em Tempo Real *</label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') {echo 'checked="checked"';}?> />
                                        <span>Sim</span>
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') {echo 'checked="checked"';}?> />
                                        <span>Não</span>
                                      </label>
                                    </div>

                                    <?php $field_name = 'gera_num_apolice_cotacao';?>
                                    <div class="row">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name; ?>">Gera o número da Apólice na Cotação *</label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') {echo 'checked="checked"';}?> />
                                        <span>Sim</span>
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                               value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') {echo 'checked="checked"';}?> />
                                        <span>Não</span>
                                      </label>
                                    </div>

                                    <?php $field_name = 'ir_cotacao_salva';?>
                                      <div class="row">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Ir Para cotação salva após CPF *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="<?php echo $field_name; ?>_sim" name="<?php echo $field_name; ?>" class="required styled"
                                                     value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                              <span>Sim</span>
                                          </label>
                                          <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="<?php echo $field_name; ?>_nao" name="<?php echo $field_name; ?>" class="required styled"
                                                     value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                              <span>Não</span>
                                          </label>
                                      </div>

                                    <?php $field_name = 'endosso_controle_cliente';?>
                                    <div class="row">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Controle de endosso *</label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio_endosso" name="<?php echo $field_name; ?>" class="required styled"
                                               value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                        <span>Único</span>
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio_endosso" name="<?php echo $field_name; ?>" class="required styled"
                                               value="1" <?php if ((isset($row[$field_name]) && $row[$field_name] == '1')) echo 'checked="checked"'; ?> />
                                        <span>Mensal</span>
                                      </label>
                                      <label class="radio-inline radio-styled radio-primary">
                                        <input type="radio" id="radio_endosso" name="<?php echo $field_name; ?>" class="required styled"
                                               value="2" <?php if (isset($row[$field_name]) && $row[$field_name] == '2') echo 'checked="checked"'; ?> />
                                        <span>Parcelado</span>
                                      </label>
                                    </div>

                                      <?php $field_name = 'ir_cotacao_salva';?>
                                      <div class="radio radio-styled">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Ir Para cotação salva após CPF *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="<?php echo $field_name; ?>_nao" name="<?php echo $field_name; ?>" class="required styled"
                                                     value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                              <span>Não</span>
                                          </label>
                                          <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="<?php echo $field_name; ?>_sim" name="<?php echo $field_name; ?>" class="required styled"
                                                     value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                              <span>Sim</span>
                                          </label>
                                      </div>

                                    <?php $field_name = 'canal_emissao';?>
                                    <div class="checkbox checkbox-styled">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Canal de Emissão *</label>
                                      <?php foreach ($canais_emissao as $canal) { ?>
                                      <label class="checkbox-inline checkbox-styled checkbox-primary">
                                        <input type="checkbox" id="<?php echo $field_name ?>" name="<?php echo $field_name; ?>[]" class="required styled" value="<?php echo $canal['canal_id'] ?>" <?php if ($canal['checado'] == 1) echo 'checked="checked"'; ?> />
                                        <span><?php echo $canal['nome'] ?></span>
                                      </label>
                                      <?php } ?>
                                    </div>

                                    <?php $field_name = 'canal_cancelamento';?>
                                    <div class="checkbox checkbox-styled">
                                      <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Canal de Cancelamento *</label>
                                      <?php foreach ($canais_cancelamento as $canal) { ?>
                                      <label class="checkbox-inline checkbox-styled checkbox-primary">
                                        <input type="checkbox" id="<?php echo $field_name ?>" name="<?php echo $field_name; ?>[]" class="required styled" value="<?php echo $canal['canal_id'] ?>" <?php if ($canal['checado'] == 1) echo 'checked="checked"'; ?> />
                                        <span><?php echo $canal['nome'] ?></span>
                                      </label>
                                      <?php } ?>
                                    </div>
                                  </div>

                                </div>

                              </div>
                            </div>

                          </div>
                        </div>

                      </div>
                      <!-- // Row END -->

                    </div>
                  </div>
                  <!-- // Widget END -->
                  <!-- Widget heading -->
                  <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">

                      <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}") ?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                      </a>
                      <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                        <i class="fa fa-edit"></i> Salvar
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
