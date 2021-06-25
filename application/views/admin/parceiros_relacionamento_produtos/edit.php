<?php
if($_POST)
  $row = $_POST;
?>
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
          <a href="<?php echo base_url("admin/parceiros_relacionamento_produtos/index/")?>" class="btn  btn-app btn-primary">
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
                  <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                  <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                  <!-- Widget -->
                  <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                      <h4 class="text-primary"><?php echo $page_subtitle;?></h4>
                    </div>
                    <!-- // Widget heading END -->

                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <?php $this->load->view('admin/partials/validation_errors');?>
                          <?php $this->load->view('admin/partials/messages'); ?>
                        </div>
                      </div>
                      <!-- Row -->
                      <div class="row innerLR">

                        <!-- Column -->
                        <div class="col-md-8">

                          <?php $field_name = 'produto_parceiro_id';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Produto: *</label>
                            <div class="col-md-8">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="">-- Selecione --</option>
                                <?php foreach($produtos as $linha) { ?>
                                <option name="" value="<?php echo $linha['produto_parceiro_id'] ?>"
                                        <?php if(isset($row)){if($row[$field_name] == $linha['produto_parceiro_id']) {echo " selected ";};}; ?> >
                                  <?php echo $linha['parceiro_nome'] . ' - '. $linha['nome']; ?>
                                </option>
                                <?php }  ?>
                              </select>
                            </div>
                          </div>

                          <?php $field_name = 'parceiro_id';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Relacionamento: *</label>
                            <div class="col-md-8">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="">-- Selecione --</option>
                                <?php foreach($parceiros as $linha) { ?>
                                <option name="" value="<?php echo $linha['parceiro_id'] ?>"
                                        <?php if(isset($row)){if($row[$field_name] == $linha['parceiro_id']) {echo " selected ";};}; ?> >
                                  <?php echo $linha['nome']; ?>
                                </option>
                                <?php }  ?>
                              </select>
                            </div>
                          </div>

                          <?php $field_name = 'parceiro_tipo_id';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo de Parceiro: *</label>
                            <div class="col-md-8">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="">-- Selecione --</option>
                                <?php foreach($tipos as $linha) { ?>
                                <option name="" value="<?php echo $linha['parceiro_tipo_id'] ?>"
                                        <?php if(isset($row)){if($row[$field_name] == $linha['parceiro_tipo_id']) {echo " selected ";};}; ?> >
                                  <?php echo $linha['nome']; ?>
                                </option>
                                <?php }  ?>
                              </select>
                            </div>
                          </div>

                          <?php $field_name = 'pai_id';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Hierarquia: *</label>
                            <div class="col-md-8">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="">-- principal --</option>
                                <?php foreach($pais as $linha) { ?>
                                <option name="" class="<?php echo $linha['produto_parceiro_id'] ?>"  value="<?php echo $linha['parceiro_relacionamento_produto_id'] ?>"
                                        <?php if(isset($row)){if($row[$field_name] == $linha['parceiro_relacionamento_produto_id']) {echo " selected ";};}; ?> >
                                  <?php echo $linha['parceiro_nome'] . ' - ' . $linha['produto_parceiro_nome']; ?>
                                </option>
                                <?php }  ?>
                              </select>
                            </div>
                          </div>

                          <?php $field_name = 'cod_parceiro';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Código do Parceiro:</label>
                            <div class="col-md-8">
                              <input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : ''; ?>" />
                            </div>
                          </div>

                        </div>

                      </div>
                      <div class="row">&nbsp;</div>
                      <div class="row">
                        <div class="col-md-8">
                          <div class="card">
                            <div class="card-head"><header>Comissão</header></div>
                            <div class="card-body">

                              <?php
                                $select_vigencia_id =  $this->uri->segment(5);
                              ?>

                              <?php $field_name = 'parceiro_relacionamento_produto_vigencia_id';?>
                              <div class="form-group">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Vigência *</label>
                                <div class="col-md-8">
                                  <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>" onChange="refresh_comissao();">
                                    <option name="" value="">-- Selecione --</option>
                                    <option name="" value="0">-- Incluir Nova Vigência --</option>
                                    <?php $select = false; foreach($vigencia as $linha) { ?>
                                    <option name="" value="<?php echo $linha['parceiro_relacionamento_produto_vigencia_id'] ?>"
                                        <?php if(isset($row)){
                                                 if($select_vigencia_id != ""){
                                                    if($row['parceiro_relacionamento_produto_id'] == $select_vigencia_id && $select == false) {
                                                      echo " selected "; $select = true;
                                                    };
                                                 }else{
                                                    if($row['parceiro_relacionamento_produto_id'] == $linha['parceiro_relacionamento_produto_id'] && $select == false) {
                                                      echo " selected "; $select = true; $select_vigencia_id = $linha['parceiro_relacionamento_produto_vigencia_id'];
                                                    };
                                                 };
                                              }; 
                                        ?>
                                    >
                                      <?php echo app_date_mysql_to_mask($linha['comissao_data_ini'],'d/m/Y') . ' - '. app_date_mysql_to_mask($linha['comissao_data_fim'],'d/m/Y'); ?>
                                    </option>
                                    <?php }  ?>
                                  </select>
                                </div>
                              </div>

                              <?php
                                $vigencia_id =  $this->uri->segment(5);
                                if ($vigencia_id == ""){
                                  $vigencia_id = $select_vigencia_id;
                                }
                                $row_vigencia = array();
                                $row_vigencia = $this->vigencia_model->filter_by_parceiro_relacionamento_produto_vigencia_id($vigencia_id);
                              ?>

                              <?php $field_name = 'comissao_data_ini';?>
                              <div class="form-group">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Data início *</label>
                                <div class="col-md-4"><input placeholder="__/__/____" class="form-control inputmask-date" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row_vigencia[0][$field_name]) ? app_date_mysql_to_mask($row_vigencia[0][$field_name]) : set_value($field_name); ?>" /></div>
                              </div>
                              <?php $field_name = 'comissao_data_fim';?>
                              <div class="form-group">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Data Final *</label>
                                <div class="col-md-4"><input placeholder="__/__/____" class="form-control inputmask-date" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row_vigencia[0][$field_name]) ? app_date_mysql_to_mask($row_vigencia[0][$field_name]) : set_value($field_name); ?>" /></div>
                              </div>
                              <?php $field_name = 'repasse_comissao';?>
                              <div class="form-group">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Repasse da comissão *</label>
                                <label class="radio-inline radio-styled radio-primary">
                                  <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                         value="1" <?php if (isset($row_vigencia[0][$field_name]) && $row_vigencia[0][$field_name] == '1') echo 'checked="checked"'; ?> />
                                  Sim
                                </label>
                                <label class="radio-inline radio-styled radio-primary">
                                  <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                         value="0" <?php if (isset($row_vigencia[0][$field_name]) && $row_vigencia[0][$field_name] == '0') echo 'checked="checked"'; ?> />
                                  Não
                                </label>
                              </div>

                              <?php $field_name = 'repasse_maximo';?>
                              <div class="form-group repasse_habilitado">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Repasse máximo(%) *</label>
                                <div class="col-md-4"><input ng-model="repasse_maximo" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/></div>
                              </div>

                              <?php $field_name = 'comissao_tipo';?> 
                              <div class="form-group"> 
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo de comissão *</label> 
                                <label>
                                  <label class="radio-styled radio-primary" style="display: block;"> 
                                    <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" 
                                           value="0" <?php if (isset($row_vigencia[0][$field_name]) && $row_vigencia[0][$field_name] == '0') echo 'checked="checked"'; ?> /> 
                                    Fixa 
                                  </label> 
                                  <label class="radio-styled radio-primary" style="display: block;"> 
                                    <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" 
                                           value="1" <?php if (isset($row_vigencia[0][$field_name]) && $row_vigencia[0][$field_name] == '1') echo 'checked="checked"'; ?> /> 
                                    Variável (<b>com</b> comissão do corretor)
                                  </label>
                                  <label class="radio-styled radio-primary" style="display: block;">
                                    <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" 
                                           value="2" <?php if (isset($row_vigencia[0][$field_name]) && $row_vigencia[0][$field_name] == '2') echo 'checked="checked"'; ?> /> 
                                    Variável (<b>sem</b> comissão do corretor)
                                  </label>
                                </label>
                              </div>

                              <?php $field_name = 'comissao';?>
                              <div class="form-group">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Comissão (%) *</label>
                                <div class="col-md-4"><input ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row_vigencia[0][$field_name]) ? str_replace('.',',', $row_vigencia[0][$field_name]): set_value($field_name); ?>"/></div>
                              </div>

                              <?php $field_name = 'comissao_indicacao';?>
                              <div class="form-group">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Comissão indicação (%) *</label>
                                <div class="col-md-4"><input ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row_vigencia[0][$field_name]) ? str_replace('.',',', $row_vigencia[0][$field_name]): set_value($field_name); ?>" /></div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- // Column END -->
                      </div>
                      <!-- // Row END -->
                      <div class="row">
                        <div class="col-md-8">
                          <div class="card">
                            <div class="card-head"><header>Desconto Condicional</header></div>
                            <div class="card-body">
                              <?php $field_name = 'desconto_habilitado';?>
                              <div class="form-group">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Habilitado *</label>
                                <label class="radio-inline radio-styled radio-primary">
                                  <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                         value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                  Sim
                                </label>
                                <label class="radio-inline radio-styled radio-primary">
                                  <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                         value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                  Não
                                </label>
                              </div>
                              <?php $field_name = 'desconto_valor';?>
                              <div class="form-group desconto_habilitado">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Valor Total</label>
                                <div class="col-md-4">
                                  <input ng-model="desconto_valor" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                                </div>
                              </div>
                              <?php $field_name = 'desconto_utilizado';?>
                              <div class="form-group desconto_habilitado">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Utlizado</label>
                                <div class="col-md-4">
                                  <input ng-model="desconto_utilizado" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                                </div>
                              </div>
                              <?php $field_name = 'desconto_saldo';?>
                              <div class="form-group desconto_habilitado">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Saldo</label>
                                <div class="col-md-4">
                                  <input ng-disabled="true" ng-model="desconto_saldo" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" ng-value="desconto_valor-desconto_utilizado"/>
                                </div>
                                
                              </div>
                              <?php $field_name = 'desconto_data_ini';?>
                              <div class="form-group desconto_habilitado">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Data início</label>
                                <div class="col-md-4"><input placeholder="__/__/____" class="form-control inputmask-date" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? app_date_mysql_to_mask($row[$field_name]) : set_value($field_name); ?>" /></div>
                              </div>
                              <?php $field_name = 'desconto_data_fim';?>
                              <div class="form-group desconto_habilitado">
                                <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Data Final</label>
                                <div class="col-md-4"><input placeholder="__/__/____" class="form-control inputmask-date" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? app_date_mysql_to_mask($row[$field_name]) : set_value($field_name); ?>" /></div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- // Column END -->
                      </div>
                    </div>
                  </div>
                  <!-- // Widget END -->
                  <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                      <a href="<?php echo base_url("admin/parceiros_relacionamento_produtos/index/")?>" class="btn  btn-app btn-primary">
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
