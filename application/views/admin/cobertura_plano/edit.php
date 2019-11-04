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
          <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_plano_id}")?>" class="btn  btn-app btn-primary">
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
                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
                  <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                  <input type="hidden" name="produto_parceiro_plano_id" value="<?php echo $produto_parceiro_plano_id; ?>"/>
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
                        <div class="col-md-12">

                          <?php $field_name = 'cobertura_tipo_id';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Cobertura *</label>
                            <div class="col-md-2">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="">Selecione</option>
                                <?php

                                foreach($coberturas_tipo as $linha) { ?>
                                <option name="" value="<?php echo $linha[$field_name] ?>"
                                        <?php if(isset($row['cobertura_cobertura_tipo_id'])){if($row['cobertura_cobertura_tipo_id'] == $linha[$field_name]) {echo " selected ";};}; ?>
                                        <?php if(isset($row['cobertura_tipo_id'])){if($row['cobertura_tipo_id'] == $linha[$field_name]) {echo " selected ";};}; ?>
                                        >
                                  <?php echo $linha['nome']; ?>
                                </option>
                                <?php }  ?>
                              </select>
                            </div>
                            <?php $field_name = 'cobertura_id';?>
                            <div class="col-md-6">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="">Selecione</option>
                                <?php

                                foreach($coberturas as $linha) { ?>
                                <option name="" class="<?php echo $linha['cobertura_tipo_id'] ?>" value="<?php echo $linha[$field_name] ?>"
                                        <?php if(isset($row[$field_name])){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                  <?php echo $linha['nome']; ?>
                                </option>
                                <?php }  ?>
                              </select>
                            </div>
                          </div>
                          <?php $field_name = 'parceiro_id';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Parceiro *</label>
                            <div class="col-md-8">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="">Selecione</option>
                                <?php foreach($parceiros as $linha) : ?>
                                <option name="" value="<?php echo $linha[$field_name] ?>"
                                        <?php if(isset($row[$field_name])){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                  <?php echo $linha['nome']; ?>
                                </option>
                                <?php endforeach;  ?>
                              </select>
                            </div>
                          </div>
                          <?php $field_name = 'mostrar';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Precificação *</label>
                            <div class="col-md-4">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="preco"
                                        <?php if(isset($row[$field_name])){if($row[$field_name] == 'preco') {echo " selected ";};}; ?> >
                                  VALOR DA COBERTURA
                                </option>
                                <option name="" value="importancia_segurada"
                                        <?php if(isset($row[$field_name])){if($row[$field_name] == 'importancia_segurada') {echo " selected ";};}; ?> >
                                  TAXA SOBRE IMPORTÂNCIA SEGURADA
                                </option>
                                <option name="" value="descricao"
                                        <?php if(isset($row[$field_name])){if($row[$field_name] == 'descricao') {echo " selected ";};}; ?> >
                                  SOMENTE DESCRIÇÃO
                                </option>
                              </select>
                            </div>
                          </div>
                          <?php $field_name = 'descricao';?>
                          <div class="form-group <?php echo $field_name ?>">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Descrição</label>
                            <div class="col-md-4"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                          </div>
                          <?php $field_name = 'preco';?>
                          <div class="form-group <?php echo $field_name ?>">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Valor (Preço)</label>
                            <div class="col-md-4">
                              <input ng-model="preco" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                            </div>
                          </div>
                          <?php $field_name = 'porcentagem';?>
                          <div class="form-group <?php echo $field_name ?>">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Taxa (%) </label>
                            <div class="col-md-4">
                              <input ng-model="porcentagem" ui-number-mask="10" class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Tipo Custo *</label>
                            <?php $field_name = 'cobertura_custo';?>
                            <div class="col-md-4">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="">Selecione</option>
                                <option name="" value="valor"
                                        <?php if(isset($row)){if($row[$field_name] == 'valor') {echo " selected ";};}; ?> >VALOR FIXO
                                </option>
                                <option name="" value="porcentagem"
                                        <?php if(isset($row)){if($row[$field_name] == 'porcentagem') {echo " selected ";};}; ?> >PERCENTUAL SOBRE VALOR DA COBERTURA
                                </option>
                              </select>
                            </div>
                          </div>
                          <?php $field_name = 'custo';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Custo</label>
                            <div class="col-md-4">
                              <input ng-model="custo" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                            </div>
                          </div>

                          <?php $field_name = 'usar_iof';?>
                          <div class="form-group">
                              <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Usuar IOF *</label>
                              <div class="switch">
                                <label>
                                  NÃO
                                  <input type="checkbox" name="usar_iof" id="iof_on_off">
                                  <span class="lever"></span>
                                  SIM
                                </label>
                              </div>
                          </div>

                          <?php $field_name = 'iof';?>
                          <div class="form-group" id="field_iof">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">IOF</label>
                            <div class="col-md-4">
                              <input ng-model="iof" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                            </div>
                          </div>

                          <?php $field_name = 'diarias';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Diárias</label>
                            <div class="col-md-4">
                              <input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo $row[$field_name]; ?>" />
                            </div>
                          </div>

                          <?php $field_name = 'franquia';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Franquia</label>
                            <div class="col-md-4">
                              <input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo $row[$field_name]; ?>" />
                            </div>
                          </div>

                          <?php $field_name = 'carencia';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Carência</label>
                            <div class="col-md-4">
                              <input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo $row[$field_name]; ?>" />
                            </div>
                          </div>

                          <div class="form-group">
                            <div class="col-md-12">
                              <h4>Dados do CTA</h4>
                              <hr>
                            </div>
                          </div>
                          <?php $field_name = 'cod_sucursal';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Código da Sucursal</label>
                            <div class="col-md-4"><input class="form-control" ui-number-mask="2" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                          </div>
                          <?php $field_name = 'cod_cobertura';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Código da Cobertura</label>
                            <div class="col-md-4"><input class="form-control" ui-number-mask="5" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                          </div>
                          <?php $field_name = 'cod_ramo';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Código do Ramo</label>
                            <div class="col-md-4"><input class="form-control" ui-number-mask="2" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                          </div>
                          <?php $field_name = 'cod_produto';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Código do Produto</label>
                            <div class="col-md-4"><input class="form-control" ui-number-mask="5" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                          </div>

                        </div>
                        <!-- // Column END -->
                      </div>
                      <!-- // Row END -->

                    </div>
                  </div>
                  <!-- // Widget END -->
                  <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                      <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_plano_id}")?>" class="btn  btn-app btn-primary">
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
  AppController.controller("AppController", ['$scope', '$http', '$filter', '$mdDialog', function ( $scope, $http, $filter, $mdDialog ) {
    $scope.preco = parseFloat( "<?php echo isset($row['preco']) ? $row['preco'] : '0'; ?>" );
    $scope.custo = parseFloat( "<?php echo isset($row['custo']) ? $row['custo'] : '0'; ?>" );
    $scope.porcentagem = parseFloat( "<?php echo isset($row['porcentagem']) ? $row['porcentagem'] : '0'; ?>" );
    $scope.iof = parseFloat( "<?php echo isset($row['iof']) ? $row['iof'] : '0'; ?>" );
  }]);
</script>
<style type="text/css">
.switch,
.switch * {
    -webkit-tap-highlight-color: transparent;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none
}
.switch label {
    cursor: pointer
}
.switch label input[type=checkbox] {
    opacity: 0;
    width: 0;
    height: 0
}
.switch label input[type=checkbox]:checked+.lever {
    background-color: #eb0038
}
.switch label input[type=checkbox]:checked+.lever:before,
.switch label input[type=checkbox]:checked+.lever:after {
    left: 18px
}
.switch label input[type=checkbox]:checked+.lever:after {
    background-color: #eb0038 
}
.switch label .lever {
    content: "";
    display: inline-block;
    position: relative;
    width: 36px;
    height: 14px;
    background-color: rgba(0, 0, 0, 0.38);
    border-radius: 15px;
    margin-right: 10px;
    -webkit-transition: background 0.3s ease;
    transition: background 0.3s ease;
    vertical-align: middle;
    margin: 0 16px
}
.switch label .lever:before,
.switch label .lever:after {
    content: "";
    position: absolute;
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    left: 0;
    top: -3px;
    -webkit-transition: left 0.3s ease, background .3s ease, -webkit-box-shadow 0.1s ease, -webkit-transform .1s ease;
    transition: left 0.3s ease, background .3s ease, -webkit-box-shadow 0.1s ease, -webkit-transform .1s ease;
    transition: left 0.3s ease, background .3s ease, box-shadow 0.1s ease, transform .1s ease;
    transition: left 0.3s ease, background .3s ease, box-shadow 0.1s ease, transform .1s ease, -webkit-box-shadow 0.1s ease, -webkit-transform .1s ease
}
.switch label .lever:before {
    background-color: rgba(38, 166, 154, 0.15)
}
.switch label .lever:after {
    background-color: #F1F1F1;
    -webkit-box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, 0.2), 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
    box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, 0.2), 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 1px 5px 0px rgba(0, 0, 0, 0.12)
}
input[type=checkbox]:checked:not(:disabled) ~ .lever:active::before,
input[type=checkbox]:checked:not(:disabled).tabbed:focus ~ .lever::before {
    -webkit-transform: scale(2.4);
    transform: scale(2.4);
    background-color: rgba(38, 166, 154, 0.15)
}
input[type=checkbox]:not(:disabled) ~ .lever:active:before,
input[type=checkbox]:not(:disabled).tabbed:focus ~ .lever::before {
    -webkit-transform: scale(2.4);
    transform: scale(2.4);
    background-color: rgba(0, 0, 0, 0.08)
}
.switch input[type=checkbox][disabled]+.lever {
    cursor: default;
    background-color: rgba(0, 0, 0, 0.12)
}
.switch label input[type=checkbox][disabled]+.lever:after,
.switch label input[type=checkbox][disabled]:checked+.lever:after {
    background-color: #949494
}
</style>
<script type="text/javascript">
  var flag_iof = "<?php echo isset($row['iof']) ? $row['iof'] : ''; ?>";

  if(flag_iof == '' || flag_iof == '0.00000')
    $("#field_iof").hide();
  else 
    $("#iof_on_off").click();

  $("#iof_on_off").change(function(){
    if($(this).is(":checked"))
      $("#field_iof").show();
    else
      $("#field_iof").hide();
  });
</script>