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
      <!-- col-separator.box -->
      <div class="section-header">
        <ol class="breadcrumb">
          <li class="active"><?php echo app_recurso_nome();?></li>
        </ol>

      </div>

      <div class="card">

        <!-- Widget heading -->
        <div class="card-body">

          <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
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
                  <input type="hidden" name="produto_parceiro_id" value="<?php echo $produto_parceiro_id; ?>"/>
                  <!-- Widget -->
                  <div class="row">
                    <div class="col-md-6">
                      <?php $this->load->view('admin/partials/validation_errors');?>
                      <?php $this->load->view('admin/partials/messages'); ?>
                    </div>

                  </div>

                  <div class="card">


                    <?php  $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>

                    <div class="card-body tab-content">

                      <!-- Row -->
                      <div class="row innerLR">

                        <div class="relativeWrap">
                          <div class="widget widget-tabs widget-tabs-double-2 widget-tabs-responsive">

                            <!-- Tabs Heading -->

                            <!-- // Tabs Heading END -->

                            <section>
                              <div class="section-body">
                                <div class="">
                                  <!-- BEGIN FIXED TIMELINE -->
                                  <ul class="timeline collapse-lg timeline-hairline">
                                    <li class="timeline-inverted">
                                      <div class="timeline-circ circ-xl style-primary"><span class="glyphicon glyphicon-leaf"></span></div>
                                      <div class="timeline-entry">
                                        <div class="card style-default-bright">
                                          <div class="card-body small-padding">
                                            <span class="text-xl text-primary">Regras de Negócios</span><br>
                                            <ul class="timeline collapse-lg timeline-hairline">
                                              <li class="timeline-inverted">
                                                <div class="timeline-circ style-primary"></div>
                                                <div class="timeline-entry">
                                                  <div class="card style-default-bright">
                                                    <div class="card-body small-padding">
                                                      <span class="text-lg text-primary">Comissões</span><br>
                                                      <?php $this->load->view('admin/produtos_parceiros_resumo/comissao', array('row' => $row));?>

                                                    </div><!--end .card-body -->
                                                  </div><!--end .card -->
                                                </div><!--end .timeline-entry -->
                                              </li>
                                              <li class="timeline-inverted">
                                                <div class="timeline-circ style-primary"></div>
                                                <div class="timeline-entry">
                                                  <div class="card style-default-bright">
                                                    <div class="card-body small-padding">
                                                      <span class="text-lg text-primary">Acréscimo no Prêmio</span><br>
                                                      <?php
                                                      $this->load->view('admin/produtos_parceiros_resumo/regra_preco', array('row' => $row));?>

                                                    </div><!--end .card-body -->
                                                  </div><!--end .card -->
                                                </div><!--end .timeline-entry -->
                                              </li>
                                              <li class="timeline-inverted">
                                                <div class="timeline-circ style-primary"></div>
                                                <div class="timeline-entry">
                                                  <div class="card style-default-bright">
                                                    <div class="card-body small-padding">
                                                      <span class="text-lg text-primary">Desconto</span><br>
                                                      <?php
                                                      $this->load->view('admin/produtos_parceiros_resumo/desconto_comissao', array('row' => $row));?>

                                                    </div><!--end .card-body -->
                                                  </div><!--end .card -->
                                                </div><!--end .timeline-entry -->
                                              </li>
                                              <li class="timeline-inverted">
                                                <div class="timeline-circ style-primary"></div>
                                                <div class="timeline-entry">
                                                  <div class="card style-default-bright">
                                                    <div class="card-body small-padding">
                                                      <span
                                                            class="text-lg text-primary">Cancelamento</span><br>
                                                      <?php $this->load->view('admin/produtos_parceiros_resumo/cancelamento', array('row' => $row));?>
                                                    </div><!--end .card-body -->
                                                  </div><!--end .card -->
                                                </div><!--end .timeline-entry -->
                                              </li>
                                              <li class="timeline-inverted">
                                                <div class="timeline-circ style-primary"></div>
                                                <div class="timeline-entry">
                                                  <div class="card style-default-bright">
                                                    <div class="card-body small-padding">
                                                      <span class="text-lg text-primary">Geral</span><br>
                                                      <?php $this->load->view('admin/produtos_parceiros_resumo/geral', array('row' => $row));?>
                                                    </div><!--end .card-body -->
                                                  </div><!--end .card -->
                                                </div><!--end .timeline-entry -->
                                              </li>
                                            </ul>



                                          </div><!--end .card-body -->
                                        </div><!--end .card -->
                                      </div><!--end .timeline-entry -->
                                    </li>
                                  </ul>
                                  <!-- END FIXED TIMELINE -->

                                </div><!--end .container -->
                              </div><!--end .section-body -->
                            </section>

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

                      <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
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
    $scope.seg_antes_valor = parseFloat( "<?php echo isset($row['cancelamento']['seg_antes_valor']) ? $row['cancelamento']['seg_antes_valor'] : '0'; ?>" );
    $scope.seg_depois_valor = parseFloat( "<?php echo isset($row['cancelamento']['seg_depois_valor']) ? $row['cancelamento']['seg_depois_valor'] : '0'; ?>" );
    $scope.inad_reativacao_valor = parseFloat( "<?php echo isset($row['cancelamento']['inad_reativacao_valor']) ? $row['cancelamento']['inad_reativacao_valor'] : '0'; ?>" );
    
    $scope.valor = parseFloat( "<?php echo isset($row['valor']) ? $row['valor'] : '0'; ?>" );
    $scope.utilizado = parseFloat( "<?php echo isset($row['utilizado']) ? $row['utilizado'] : '0'; ?>" );
    $scope.saldo = parseFloat( "<?php echo isset($row['saldo']) ? $row['saldo'] : '0'; ?>" );
    
    $scope.parametros = parseFloat( "<?php echo isset($row['parametros']) ? $row['parametros'] : '0'; ?>" );
    
    $scope.markup = parseFloat( "<?php echo isset($row['markup']) ? $row['markup'] : '0'; ?>" );
    
    $scope.comissao = parseFloat( "<?php echo isset($row['comissao']) ? $row['comissao'] : '0'; ?>" );
    $scope.comissao_indicacao = parseFloat( "<?php echo isset($row['comissao_indicacao']) ? $row['comissao_indicacao'] : '0'; ?>" );
    $scope.repasse_maximo = parseFloat( "<?php echo isset($row['repasse_maximo']) ? $row['repasse_maximo'] : '0'; ?>" );
    
    $scope.padrao_comissao = parseFloat( "<?php echo isset($row['padrao_comissao']) ? $row['padrao_comissao'] : '0'; ?>" );
    $scope.padrao_comissao_indicacao = parseFloat( "<?php echo isset($row['padrao_comissao_indicacao']) ? $row['padrao_comissao_indicacao'] : '0'; ?>" );
    $scope.padrao_repasse_maximo = parseFloat( "<?php echo isset($row['padrao_repasse_maximo']) ? $row['padrao_repasse_maximo'] : '0'; ?>" );
  }]);
</script>

