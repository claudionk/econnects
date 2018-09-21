<?php include_once( "header.php" ); ?>
<!-- Cadastro de Indicadores -->
<div class="col-sm-12" ng-show="TelaIndicadores" ng-cloak>
  <div class="panel panel-default no-print">
    <div class="panel-heading text-left" style="font-size:1.3em">
      Cadastro de Indicadores<span class="pull-right"><i title="Novo Indicador" ng-disabled="MostraFormIndicador" ng-click="FormAdicionaIndicador()" style="color:green;line-height:30px;cursor:pointer" class="fa fa-plus" aria-hidden="true"></i></span>
    </div>
    <div class="panel-body">
      <div class="row" ng-show="MostraFormIndicador">
        <form name="frmIndicadores">
          <div class="col-sm-12 col-xs-12">
            <div class="panel panel-default no-print">
              <div class="panel-heading text-left" style="font-size:1.2em">
                {{LabelFormIndicador}} de Indicador
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-8 col-xs-12">
                    <label>Descrição</label>
                    <input class="form-control" type="text" ng-model="Indicador.nome" ng-change="CriaSlugIndicador()" ng-required="true">
                  </div>
                  <div class="col-sm-2 col-xs-12">
                    <label>Tipo</label>
                    <select class="form-control" ng-model="Indicador.tipo" ng-init="Indicador.tipo='N'" ng-required="true">
                      <option value="M">Moeda</option>
                      <option value="N">Numérico</option>
                      <option value="P">Percentual</option>
                    </select>
                  </div>
                  <div class="col-sm-2 col-xs-12">
                    <label>Calculado</label>
                    <select class="form-control" ng-model="Indicador.calculado" ng-init="Indicador.calculado='N'" ng-required="true">
                      <option value="S">Sim</option>
                      <option value="N">Não</option>
                    </select>
                  </div>
                  <div class="col-sm-12 col-xs-12" ng-show="Indicador.calculado=='S'">
                    <br>
                    <label>Fórmula</label>
                    <input class="form-control" type="text" ng-model="Indicador.formula" ng-disabled="Indicador.calculado!='S'" list="ListIndicadores" ng-required="Indicador.calculado=='S'">
                    <datalist id="ListIndicadores" ng-model="selectedIndicador">
                      <option ng-repeat="Records in Indicadores | filter:{slug:Indicador.formular} | limitTo:10" value="{{Records.slug}}">{{Records.slug}}</option>
                    </datalist>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-sm-8 col-xs-12">
                    <label>Slug</label>
                    <input class="form-control" type="text" ng-model="Indicador.slug" ng-disabled="true" ng-required="true">
                  </div>
                  <div class="col-sm-2 col-xs-12">
                    <label>Grupo</label>
                    <select class="form-control" ng-model="Indicador.grupo_id" ng-required="true">
                      <option value="">Selecione...</option>
                      <option ng-repeat="Grupo in Grupos" value="{{Grupo.id}}">{{Grupo.nome}}</option>
                    </select>
                  </div>
                  <div class="col-sm-2 col-xs-12">
                    <label>Status</label>
                    <select class="form-control" ng-model="Indicador.status" ng-init="Indicador.status='S'" ng-required="true">
                      <option value="S">Habilitado</option>
                      <option value="N">Desabilitado</option>
                    </select>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-sm-2 col-xs-12">
                    <label>Mostra na Tela</label>
                    <select class="form-control" ng-model="Indicador.mostra" ng-init="Indicador.mostra='S'" ng-required="true">
                      <option value="S">Sim</option>
                      <option value="N">Não</option>
                    </select>
                  </div>
                  <div class="col-sm-2 col-xs-12">
                    <label>Obrigatório</label>
                    <select class="form-control" ng-model="Indicador.obrigatorio" ng-init="Indicador.obrigatorio='S'" ng-required="Indicador.mostra=='S'" ng-disabled="Indicador.mostra=='N'">
                      <option value="S">Sim</option>
                      <option value="N">Não</option>
                    </select>
                  </div>
                  <div class="col-sm-2 col-xs-12">
                    <label>Estilo</label>
                    <select class="form-control" ng-model="Indicador.estilo" ng-init="Indicador.estilo='B'" ng-required="Indicador.mostra=='S'" ng-disabled="Indicador.mostra=='N'">
                      <option value="B">Block</option>
                      <option value="I">Inline</option>
                    </select>
                  </div>
                  <div class="col-sm-2 col-xs-12">
                    <label>Separador</label>
                    <select class="form-control" ng-model="Indicador.separador" ng-init="Indicador.separador='N'" ng-required="Indicador.mostra=='S'" ng-disabled="Indicador.mostra=='N'">
                      <option value="N">Nenhum</option>
                      <option value="A">Antes</option>
                      <option value="D">Depois</option>
                    </select>
                  </div>
                  <div class="col-sm-4 col-xs-12">
                    <label>Help (Tooltip)</label>
                    <input class="form-control" type="text" ng-model="Indicador.help" ng-required="Indicador.mostra=='S'" ng-disabled="Indicador.mostra=='N'">
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-sm-12 col-xs-12">
                    <button ng-click="MostraFormIndicador=false" class="btn btn-danger pull-left">Cancelar <i class="fa fa-refresh"></i></button>
                    <button ng-disabled="frmIndicadores.$invalid" ng-click="salvaIndicador()" class="btn btn-danger pull-right">Salvar <i class="fa fa-save"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="row" ng-show="!MostraFormIndicador">
        <div class="col-sm-12 col-xs-12">
          <table id="cadastroIndicador" class="table table-hover table-striped table-condensed" style="font-size: 14px" width="100%">
            <thead style="background-color: rgb(208,4,4); color: #efefef; padding: 20px">
              <tr style="background-color: rgb(208,4,4); color: #efefef; padding: 20px">
                <th style="text-align:left">Descrição</th>
                <th style="text-align:left">Grupo</th>
                <th style="text-align:center">Tipo</th>
                <th style="text-align:center">Calculado</th>
                <th style="text-align:left">Fórmula</th>
                <th style="text-align:center">Estilo</th>
                <th style="text-align:center">Habilitado</th>
                <th></th>
              </tr>
            </thead>
            <tbody ui-sortable="sortableOptions" ng-model="Indicadores">
              <tr style="cursor:grab" ng-repeat="Indicador in Indicadores" ng-dblclick="FormEditarIndicador($index)" ng-show="Indicador.status!='D'">
                <td nowrap width="25%">
                  {{Indicador.nome}}
                </td>
                <td width="10%">
                  {{Indicador.grupo_nome}}
                </td>
                <td align="center" width="4%">
                  {{Indicador.tipo}}
                </td>
                <td align="center" width="4%">
                  {{Indicador.calculado}}
                </td>
                <td style="max-width: 500px; overflow:hidden">
                  {{Indicador.formula}}
                </td>
                <td align="center" width="4%">
                  {{Indicador.estilo}}
                </td>
                <td align="center" width="4%">
                  {{Indicador.status}}
                </td>
                <td nowrap width="2%">
                  <button ng-click="removeIndicador($index)" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once( "footer.php" ); ?>
<script>
  var IndicadoresController = angular.module( "AmericanaApp", [ "ngMaterial", "ngSanitize", "ngRoute", "ui.utils.masks", "ui.sortable" ]);

  IndicadoresController.filter("unique", function() {
    return function(collection, primaryKey, secondaryKey) { //optional secondary key
      var output = [], 
          keys = [];

      angular.forEach(collection, function(item) {
        var key;
        secondaryKey === undefined ? key = item[primaryKey] : key = item[primaryKey][secondaryKey];

        if(keys.indexOf(key) === -1) {
          keys.push(key);
          output.push(item);
        }
      });

      return output;
    };
  });

  IndicadoresController.filter('cnpj', function(){
    return function(cnpj){
      if( typeof cnpj != typeof undefined ) {
        cnpj = cnpj.replace(/\D/g, "");
        return cnpj.substr(0, 2) + "." + cnpj.substr(2, 3) + "." + cnpj.substr(5, 3) + "/" + cnpj.substr(8,4) + "-" + cnpj.substr(12,2);
      }
      return null;
    };
  });

  IndicadoresController.filter('cpf', function(){
    return function(cpf){
      if( typeof cpf != typeof undefined ) {
        cpf = cpf.replace(/\D/g, "");
        return cpf.substr(0, 3) + "." + cpf.substr(3, 3) + "." + cpf.substr(6, 3) + "-" + cpf.substr(9,2);
      }
      return null;
    };
  });

  IndicadoresController.filter('telefone', function(){
    return function(telefone){
      if( typeof telefone != typeof undefined ) {
        if( telefone.length == 10 ) {
          return "(" + telefone.substr(0, 2) + ") " + telefone.substr(2, 4) + "-" + telefone.substr(6, 4);
        }
        if( telefone.length == 11 ) {
          return "(" + telefone.substr(0, 2) + ") " + telefone.substr(2, 5) + "-" + telefone.substr(7, 4);
        }

      }
      return telefone;
    };
  });

  IndicadoresController.directive("datepicker", function () {
    function link(scope, element, attrs, controller) {
      element.datepicker({
        onSelect: function (dt) {
          scope.$apply(function () {
            controller.$setViewValue(dt);   
          });
        },
        dateFormat: "dd/mm/yy",
        minDate: "-3Y",
        maxDate: "+15D"
      });
    }

    return {
      require: 'ngModel',
      link: link
    };
  });

  IndicadoresController.controller( "IndicadoresController", ["$scope", "$http", "$filter", "$mdDialog", "$mdMenu", "$timeout", "$interval", function ( $scope, $http, $filter, $mdDialog, $mdMenu, $timeout, $interval ) {

    $scope.dataLoading = false;
    var dataOcorrencia = new Date();
    dataOcorrencia.setDate( dataOcorrencia.getDate() );
    $scope.dataOcorrencia = dataOcorrencia.toLocaleDateString();
    $scope.UsuarioNome = "<?php echo $UsuarioNome ?>";
    $scope.UsuarioPerfil = "<?php echo $UsuarioPerfil ?>";
    $scope.FranqueadoID = "<?php echo $FranqueadoID ?>";
    $scope.FranqueadoCidade = "<?php echo $FranqueadoCidade ?>";
    $scope.FranqueadoEstado = "<?php echo $FranqueadoEstado ?>";

    $scope.myTabIndex = 0;

    $scope.Alert = function ( ev, title, message ) {
      $mdDialog.show(
        $mdDialog.confirm()
        .parent( angular.element(document.querySelector( "#popupContainer" ) ) )
        .clickOutsideToClose( false )
        .title( title )
        .htmlContent( message )
        .ariaLabel( title )
        .ok('OK')
        .targetEvent(ev)
      );
    }

    function DialogController($scope, $mdDialog) {
      $scope.hide = function() {
        $mdDialog.hide();
      };

      $scope.cancel = function() {
        $mdDialog.cancel();
      };

      $scope.answer = function() {
        $mdDialog.hide(answer);
      };

      $scope.login = function() {
        $mdDialog.hide( { Username: this.dialog.username, Password: this.dialog.password } );
      };
    }

    $scope.Logoff = function() {
      $http.get( "logoff_controller.php" )
        .success(function (data) {
        location.reload();
      })
    }

    $scope.MostraEntradaDados = function() {
      $scope.TelaEntradaDados = true;
      $scope.TelaRelatorios = false;
      $scope.TelaGrupos = false;
      $scope.TelaIndicadores = false;
      $scope.TelaGraficos = false;
      $scope.TelaParametros = false;
      $scope.TelaRegras = false;
      $scope.getGrupos();
      $scope.getIndicadores();
    }

    $scope.MostraRelatorios = function() {
      $scope.TelaEntradaDados = false;
      $scope.TelaRelatorios = true;
      $scope.TelaGrupos = false;
      $scope.TelaIndicadores = false;
      $scope.TelaGraficos = false;
      $scope.TelaParametros = false;
      $scope.TelaRegras = false;

      $scope.dataLoading = true;
      $http.get( "main_controller.php?type=listaIndicadoresRelatorio" )
        .success(function (data) {
        $scope.IndicadoresRelatorio = data;
        $http.get( "main_controller.php?type=relatorioIndicadores" )
          .success(function (data) {
          $scope.Relatorio = data;
          $scope.getFranqueados();
        })
      })
        .finally( function() {
        $scope.dataLoading = false;
      })
    }

    $scope.MostraRegras = function() {
      $scope.TelaEntradaDados = false;
      $scope.TelaRelatorios = false;
      $scope.TelaGrupos = false;
      $scope.TelaIndicadores = false;
      $scope.TelaGraficos = false;
      $scope.TelaParametros = false;
      $scope.TelaRegras = true;
      $scope.getRegras();
      $scope.getIndicadoresRegra();
      $scope.getIndicadores();
    }

    $scope.MostraIndicadores = function() {
      $scope.TelaEntradaDados = false;
      $scope.TelaRelatorios = false;
      $scope.TelaGrupos = false;
      $scope.TelaIndicadores = true;
      $scope.TelaGraficos = false;
      $scope.TelaParametros = false;
      $scope.TelaRegras = false;
      $scope.getIndicadores();
    }

    $scope.MostraParametros = function() {
      $scope.TelaEntradaDados = false;
      $scope.TelaRelatorios = false;
      $scope.TelaGrupos = false;
      $scope.TelaIndicadores = false;
      $scope.TelaGraficos = false;
      $scope.TelaParametros = true;
      $scope.TelaRegras = false;
    }

    $scope.TelaEntradaDados = false;
    $scope.Indicadores = [];
    $scope.Indicadores.VendaBruta = 0;
    $scope.Indicadores.VendaLiquida = 0;
    $scope.Indicadores.VendaGenericos = 0;
    $scope.Indicadores.VendaMedicamentos = 0;
    $scope.Indicadores.VendaMedicamentosPBM = 0;
    $scope.Indicadores.VendaPerfumaria = 0;
    $scope.Indicadores.VendaSimilares = 0;
    $scope.Indicadores.Compras = 0;
    $scope.Indicadores.Despesas = 0;
    $scope.Indicadores.ClientesAtendidos = 0;

    $scope.setOrder = function ( fieldName ) {
      if( fieldName == "Periodo" || fieldName == "Franqueado" || fieldName == "Cidade" || fieldName == "UF" ) {
        if( fieldName == $scope.OrderOrderBy ) {
          $scope.OrderReverse = !$scope.OrderReverse;
          return;
        }
        $scope.OrderReverse = false;
        $scope.OrderOrderBy = fieldName;
      }
    }


    $scope.Regras = [];
    $scope.Regras.cor = "#f0f0f0";

    $scope.OrderReverse = false;
    $scope.OrderOrderBy = "Periodo";

    $scope.sortableOptions = {
      stop: function(e, ui) {
        if( $scope.TelaGrupos ) {
          $scope.ordenaGrupos();
        }
        if( $scope.TelaIndicadores ) {
          $scope.ordenaIndicadores();
        }
      }
    };      

    $scope.getFranqueados = function() {
      $http.get( "main_controller.php?type=listaFranqueados" )
        .success(function (data) {
        $scope.Franqueados = data;
      })
    }

    $scope.getGrupos = function() {
      $http.get( "main_controller.php?type=listaGrupos" )
        .success(function (data) {
        $scope.Grupos = data;
      })
    }

    $scope.getIndicadores = function() {
      $http.get( "main_controller.php?type=listaIndicadores" )
        .success(function (data) {
        $scope.Indicadores = data;
      })
    }

    $scope.getRegras = function() {
      $http.get( "main_controller.php?type=listaRegras" )
        .success(function (data) {
        $scope.Regras = data;
      })
    }

    $scope.getIndicadoresRegra = function( regra_id ) {
      var URL = "main_controller.php?type=listaIndicadoresRegra";
      if( typeof regra_id != typeof undefined && regra_id != "" ) {
        URL = "main_controller.php?type=listaIndicadoresRegra&regra_id=" + regra_id;
      }
      $http.get( URL )
        .success(function (data) {
        $scope.IndicadoresRegra = data;
        //console.log( $scope.IndicadoresRegra );
      })
    }

    $scope.getParametros = function() {
      $http.get( "main_controller.php?type=listaParametros" )
        .success(function (data) {
        $scope.Parametros = data;
      })
    }

    $scope.removeRegra = function( index ) {
      //console.log( $scope.Regras[index] );
      var confirm = $mdDialog.confirm()
      .title("Aviso")
      .htmlContent( "Tem certeza que deseja remover essa Regra?" )
      .ariaLabel("OK")
      .targetEvent( $scope.$event )
      .ok("SIM")
      .cancel("NÃO");

      $mdDialog.show( confirm ).then( function() {
        $http({
          method: "POST",
          url: "main_controller.php?type=removeRegra",
          data: $scope.Regras[index]
        })
          .success( function(data) {
          $scope.Regras = data;
        })
          .finally( function() {
          $scope.dataLoading = false;
        });
      });
    }

    $scope.removeGrupo = function( index ) {
      var confirm = $mdDialog.confirm()
      .title("Aviso")
      .htmlContent( "Tem certeza que deseja remover esse Grupo de Indicadores?" )
      .ariaLabel("OK")
      .targetEvent( $scope.$event )
      .ok("SIM")
      .cancel("NÃO");

      $mdDialog.show( confirm ).then( function() {
        $http({
          method: "POST",
          url: "main_controller.php?type=removeIndicadorGrupo",
          data: $scope.Grupos[index]
        })
          .success( function(data) {
          $scope.Grupos = data;
        })
          .finally( function() {
          $scope.dataLoading = false;
        });
        $scope.ordenaGrupos();
      });
    }

    $scope.removeIndicador = function( index ) {
      var msgComplementar = "";
      if( $scope.Indicadores[index].obrigatorio == "S" ) {
        msgComplementar = "Esse indicador é um campo obrigatório nos Informes de Indicadores.";
      }
      var confirm = $mdDialog.confirm()
      .title("Aviso")
      .htmlContent( msgComplementar + " Tem certeza que deseja remover esse Indicador?" )
      .ariaLabel("OK")
      .targetEvent( $scope.$event )
      .ok("SIM")
      .cancel("NÃO");

      $mdDialog.show( confirm ).then( function() {
        $http({
          method: "POST",
          url: "main_controller.php?type=removeIndicador",
          data: $scope.Indicadores[index]
        })
          .success( function(data) {
          $scope.Indicadores = data;
        })
          .finally( function() {
          $scope.dataLoading = false;
        });
        //$scope.Indicadores.splice( index, 1 );
        $scope.ordenaIndicadores();
      });

    }

    $scope.ordenaGrupos = function() {
      var i = 1;
      $scope.dataLoading = true;
      angular.forEach( $scope.Grupos, function( value, key ) {
        $scope.Grupos[key].order_by = i;
        i++;
      });
      $http({
        method: "POST",
        url: "main_controller.php?type=ordenaGrupos",
        data: $scope.Grupos
      })
        .success( function(data) {
        $scope.Grupos = data;
      })
        .finally( function() {
        $scope.dataLoading = false;
      });

    }

    $scope.salvaFranqueadoNaoParticipantes = function() {
      $scope.dataLoading = true;
      $http({
        method: "POST",
        url: "main_controller.php?type=salvaFranqueadoNaoParticipantes",
        data: { id: $scope.FranqueadoNaoParticipante }
      })
        .success( function(data) {
        $scope.FranqueadosNaoParticipantes = data;
      })
        .finally( function() {
        $scope.dataLoading = false;
      });
    }

    $scope.listaNaoParticipantes = function(){
      $scope.dataLoading = true;
      $http.get( "main_controller.php?type=listaFranqueadosNaoParticipantes" )
        .success( function( data ) {
        $scope.FranqueadosNaoParticipantes = data;
      })
        .finally( function() {
        $scope.dataLoading = false;
      });
    }

    $scope.removeNaoParticipante = function( FranqueadoID ) {
      $scope.dataLoading = true;
      $http({
        method: "POST",
        url: "main_controller.php?type=removeFranqueadoNaoParticipantes",
        data: { id: FranqueadoID }
      })
        .success( function(data) {
        $scope.FranqueadosNaoParticipantes = data;
      })
        .finally( function() {
        $scope.dataLoading = false;
      });
    }

    $scope.ordenaIndicadores = function() {
      var i = 1;
      $scope.dataLoading = true;
      angular.forEach( $scope.Indicadores, function( value, key ) {
        $scope.Indicadores[key].order_by = i;
        i++;
      });
      $http({
        method: "POST",
        url: "main_controller.php?type=ordenaIndicadores",
        data: $scope.Indicadores
      })
        .success( function(data) {
        $scope.Indicadores = data;
      })
        .finally( function() {
        $scope.dataLoading = false;
      });
    }

    $scope.salvaParametros = function() {
      //console.log( $scope.Parametros );
    }

    $scope.salvaRegra = function() {
      $scope.dataLoading = true;
      $http({
        method: "POST",
        url: "main_controller.php?type=salvaRegra",
        data: $scope.Regra
      })
        .success( function ( data ) {
      })
        .finally( function () {
        $scope.dataLoading = false;
        $scope.MostraFormRegra = false;
        $scope.getRegras();
        $scope.getIndicadoresRegra();
      })}

    $scope.salvaGrupo = function() {
      if( typeof $scope.Grupo.id == typeof undefined || !$scope.Grupo.id ) {
        $scope.Grupos.push( $scope.Grupo = { id: 0, nome: $scope.Grupo.nome, slug: $scope.Grupo.slug, mostra: $scope.Grupo.mostra, status: $scope.Grupo.status, order_by: 0 } );
        $scope.Grupo = $scope.Grupos[$scope.Grupos.length-1];
      }
      $scope.dataLoading = true;
      $http({
        method: "POST",
        url: "main_controller.php?type=salvaGrupo",
        data: $scope.Grupo
      })
        .success( function ( data ) {
      })
        .finally( function () {
        //$scope.ordenaGrupos();
        $scope.dataLoading = false;
        $scope.MostraFormGrupo = false;
        $scope.getGrupos();
      })
    }

    $scope.salvaIndicador = function() {
      if( typeof $scope.Indicador.id == typeof undefined || !$scope.Indicador.id ) {
        $scope.Indicadores.push( { 
          order_by: 0,
          id: 0,
          slug: $scope.Indicador.slug,
          nome: $scope.Indicador.nome,
          tipo: $scope.Indicador.tipo,
          calculado: $scope.Indicador.calculado,
          formula: $scope.Indicador.formula,
          status: $scope.Indicador.status,
          mostra: $scope.Indicador.mostra,
          separador: $scope.Indicador.separador,
          estilo: $scope.Indicador.estilo,
          help: $scope.Indicador.help,
          grupo_id: $scope.Indicador.grupo_id,
          obrigatorio: $scope.Indicador.obrigatorio
        } );
        $scope.Indicador = $scope.Indicadores[$scope.Indicadores.length-1];
      }
      $scope.dataLoading = true;
      $http({
        method: "POST",
        url: "main_controller.php?type=salvaIndicador",
        data: $scope.Indicador
      })
        .success( function ( data ) {
      })
        .finally( function () {
        //$scope.ordenaIndicadores();
        $scope.dataLoading = false;
        $scope.MostraFormIndicador = false;
        $scope.getIndicadores();
      })
    }

    if( $scope.UsuarioPerfil == "F" ) {
      $scope.Alert( $scope.$event, "Aviso", "Olá " + $scope.UsuarioNome + ". Seja bem vindo ao Portal de Gestão de Indicadores. <font color=red>Você ainda tem dias para informar seus indicadores. Não deixe para última hora!</font>" );
      //} else {
      //  $scope.MostraIndicadores();
    }

    $scope.FormAdicionaRegra = function() {
      if( $scope.MostraFormRegra != true ) {
        $scope.MostraFormRegra = true;
        $scope.LabelFormRegra = "Inclusão";
        $scope.Regra = { id: 0, nome: "", cor: "", Indicadores: $scope.IndicadoresRegra };
        //console.log( $scope.Regra );
      } else {
        $scope.Alert( $scope.$event, "Aviso", "É necessário concluir a operação de <b>" + $scope.LabelFormRegra.toLowerCase() + "</b> antes de iniciar uma nova." );
      }
    }

    $scope.FormAdicionaGrupo = function() {
      if( $scope.MostraFormGrupo != true ) {
        $scope.MostraFormGrupo = true;
        $scope.LabelFormGrupo = "Inclusão";
        $scope.Grupo = { id: 0, nome: "", slug: "", mostra: "S", status: "S", order_by: 0 };
      } else {
        $scope.Alert( $scope.$event, "Aviso", "É necessário concluir a operação de <b>" + $scope.LabelFormGrupo.toLowerCase() + "</b> antes de iniciar uma nova." );
      }
    }

    $scope.FormAdicionaIndicador = function() {
      if( $scope.MostraFormIndicador != true ) {
        $scope.getGrupos();
        $scope.Indicador = {};
        $scope.MostraFormIndicador = true;
        $scope.LabelFormIndicador = "Inclusão";
        $scope.Indicador = {
          order_by: 0,
          id: 0,
          slug: "",
          nome: "",
          tipo: "N",
          calculado: "N",
          formula: "",
          status: "S",
          mostra: "S",
          separador: "N",
          estilo: "B",
          help: "",
          grupo_id: "",
          obrigatorio: "S"};
      } else {
        $scope.Alert( $scope.$event, "Aviso", "É necessário concluir a operação de <b>" + $scope.LabelFormIndicador.toLowerCase() + "</b> antes de iniciar uma nova." );
      }
    }

    $scope.FormEditarRegra = function( index ) {
      if( $scope.MostraFormRegra != true ) {
        $scope.Regra = $scope.Regras[index];
        $scope.getIndicadoresRegra( $scope.Regra.id );
        $scope.Regra.Indicadores = $scope.IndicadoresRegra;
        $scope.MostraFormRegra = true;
        $scope.LabelFormRegra = "Alteração";
      } else {
        $scope.Alert( $scope.$event, "Aviso", "É necessário concluir a operação de <b>" + $scope.LabelFormGrupo.toLowerCase() + "</b> antes de iniciar uma nova." );
      }
    }

    $scope.FormEditarGrupo = function( index ) {
      if( $scope.MostraFormGrupo != true ) {
        $scope.Grupo = $scope.Grupos[index];
        $scope.MostraFormGrupo = true;
        $scope.LabelFormGrupo = "Alteração";
      } else {
        $scope.Alert( $scope.$event, "Aviso", "É necessário concluir a operação de <b>" + $scope.LabelFormGrupo.toLowerCase() + "</b> antes de iniciar uma nova." );
      }
    }

    $scope.FormEditarIndicador = function( index ) {
      if( $scope.MostraFormIndicador != true ) {
        $scope.getGrupos();
        $scope.Indicador = $scope.Indicadores[index];
        $scope.MostraFormIndicador = true;
        $scope.LabelFormIndicador = "Alteração";
      } else {
        $scope.Alert( $scope.$event, "Aviso", "É necessário concluir a operação de <b>" + $scope.LabelFormIndicador.toLowerCase() + "</b> antes de iniciar uma nova." );
      }
    }

    $scope.CriaSlugIndicador = function() {
      var r = $scope.Indicador.nome.toLowerCase();
      r = r.replace(new RegExp(/%/g),"perc");
      r = r.replace(new RegExp(/\s/g),"_");
      r = r.replace(new RegExp(/[àáâãäå]/g),"a");
      r = r.replace(new RegExp(/æ/g),"ae");
      r = r.replace(new RegExp(/ç/g),"c");
      r = r.replace(new RegExp(/[èéêë]/g),"e");
      r = r.replace(new RegExp(/[ìíîï]/g),"i");
      r = r.replace(new RegExp(/ñ/g),"n");                
      r = r.replace(new RegExp(/[òóôõö]/g),"o");
      r = r.replace(new RegExp(/œ/g),"oe");
      r = r.replace(new RegExp(/[ùúûü]/g),"u");
      r = r.replace(new RegExp(/[ýÿ]/g),"y");
      r = r.replace(new RegExp(/\W/g),"");
      $scope.Indicador.slug = r;
      //$scope.Indicador.nome = $scope.UCWords( $scope.Indicador.nome );
    }

    $scope.CriaSlugGrupo = function() {
      var r = $scope.Grupo.nome.toLowerCase();
      r = r.replace(new RegExp(/%/g),"perc");
      r = r.replace(new RegExp(/\s/g),"_");
      r = r.replace(new RegExp(/[àáâãäå]/g),"a");
      r = r.replace(new RegExp(/æ/g),"ae");
      r = r.replace(new RegExp(/ç/g),"c");
      r = r.replace(new RegExp(/[èéêë]/g),"e");
      r = r.replace(new RegExp(/[ìíîï]/g),"i");
      r = r.replace(new RegExp(/ñ/g),"n");                
      r = r.replace(new RegExp(/[òóôõö]/g),"o");
      r = r.replace(new RegExp(/œ/g),"oe");
      r = r.replace(new RegExp(/[ùúûü]/g),"u");
      r = r.replace(new RegExp(/[ýÿ]/g),"y");
      r = r.replace(new RegExp(/\W/g),"");
      $scope.Grupo.slug = "grupo_" + r;
      //$scope.Indicador.nome = $scope.UCWords( $scope.Indicador.nome );
    }

    $scope.UCWords = function( string ) {
      string = string.toLowerCase();
      return (string + '').replace(/^([a-z])|\s+([a-z])/g, function( $1 ) {
        return $1.toUpperCase();
      });
    }

    $scope.Grupos = [];
    $scope.MostraFormIndicador = false;
    $scope.MostraFormGrupo = false;
    $scope.DefaultOrderBy = 0;
    $scope.DefaultStatus = 1;
    $scope.MeusIndicadores = {};

    $scope.EnviaIndicadores = function() {
      $scope.CalculaIndicadores();
      $scope.dataLoading = true;
      $http({
        method: "POST",
        url: "main_controller.php?type=salvaInforme",
        data: $scope.MeusIndicadores
      })
        .success( function(data) {
        if( data.status == true ) {
          $scope.Alert( $scope.$event, "Aviso", "Caro Franqueado, obrigado por concluir o Informe de Indicadores. Até o próximo informe!" );
        } else {
          $scope.Alert( $scope.$event, "Aviso", "Caro Franqueado, ocorreu uma falha ao enviar o seu informe. Falha: " + data.message );
        }
      })
        .error( function (data, status) {
        $scope.dataLoading = false;
        $scope.Alert( $scope.$event, "Alerta", "Ocorreu uma falha ao enviar o seu Informe de Indicadores. Por favor, tente novamente. Se a falha persistir, entre em contato como o administrador do sistema." );
      })
        .finally( function() {
        $scope.dataLoading = false;
      });
    }

    $scope.CalculaIndicadores = function() {
      angular.forEach( $scope.Indicadores, function( value, key ) {
        if( value.mostra != typeof undefined && value.mostra == 'S' ) {
          //console.log( "Verifica Números #1 - Sem fórmulas" );
          if( typeof value.formula == typeof undefined || value.formula == null || value.formula == "" ) {
            var numero = eval( "$('#"+value.slug+"').val();" );
            numero = numero.replace( ".", "" );
            numero = numero.replace( ",", "." );
            eval( value.slug + "=" + parseFloat( numero ).toFixed(2) );
            $scope.MeusIndicadores[value.slug] = parseFloat( eval( value.slug ) ).toFixed(2);
          }
        }
      });
      angular.forEach( $scope.Indicadores, function( value, key ) {
        if( value.mostra != typeof undefined && value.mostra == 'S' ) {
          //console.log( "Verifica Números #2 - Com fórmulas" );
          if( typeof value.formula != typeof undefined && value.formula != null && value.formula != "" ) {
            eval( value.slug + "=" + value.formula );
            eval( "$('#"+value.slug+"').val("+value.slug+");" );
            $scope.MeusIndicadores[value.slug] = parseFloat( eval( value.slug ) ).toFixed(2);
          }
        }
      });
    }

    $scope.ClasseIndicador = function( estilo ) {
      if( estilo == "I" ) {
        return "form-inline";	
      }
    }

    $http.get( "main_controller.php?type=listaIndicadores" )
      .success(function (data) {
      $scope.Indicadores = data;
      angular.forEach( $scope.Indicadores, function( value, key ) {
        eval( "var "+value.slug+"=null;" );
      });
    });

    $scope.Periodos = <?php echo $Periodos ?>;
    if( $scope.Periodos.length >= 1 ) {
      $scope.MeusIndicadores["Periodo"] = $scope.Periodos[0].DB;
    }

    $scope.FormataCampos = function( campo, valor, record ) {
      return valor;
      //console.log( record );
      if( typeof valor == "number" ) {
        var ret = ""
        if( campo.substr( 0, 1 ) == "%" ) {
          ret = $filter( "number" )( valor, 2 ) + "%";
        } else {
          ret = $filter( "currency" )( valor, "R$", 2 );
        }
        return ret;
      } else {
        return valor;
      }
    }

    $scope.FiltroPeriodo = "";
    $scope.FiltroFranqueado = "";

    $scope.Regra = {};

    $scope.getParametros();

    $scope.CustomFilter = function( item ) {
      if( $scope.FiltroPeriodo === "" && $scope.FiltroFranqueado === "" ) {
        return item;
      } else {
        if( item.Franqueado === $scope.FiltroFranqueado ) {
          return item;
        } else {
          if( item.Franqueado === $scope.FiltroFranqueado ) {
            return item;
          }
        }
      }
    }

    $scope.PlotaEvolucao = function() {
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      var URL = "main_controller.php?type=relatorioEvolucao&FranqueadoID=" + encodeURIComponent($scope.FiltroFranqueado) + "&IndicadorID=" + $scope.FiltroIndicador + "&PeriodoDe=" + $scope.FiltroPeriodoDe + "&PeriodoAte=" + $scope.FiltroPeriodoAte;
      //URL = encodeURI(URL);
      console.log( URL );
      var jsonData = $.ajax({
        url: URL,
        dataType: "json",
        async: false
      }).responseText;

      //console.log( jsonData );

      function drawChart() {
        if( $scope.UsuarioPerfil == "A" ) {
          var data = new google.visualization.DataTable( jsonData  );
        }

        var options = {
          title: "Evolução",
          legend: { position: "right" }
        };

        var chart = new google.visualization.LineChart(document.getElementById("evolucao_3d"));
        chart.draw(data, options);
      }
    }

    $scope.SelecionaIndicador = function() {
      $scope.dataLoading = true;
      $http.get( "main_controller.php?type=relatorioIndicadores&IndicadorID=" + $scope.FiltroIndicador + "&PeriodoDe=" + $scope.FiltroPeriodoDe + "&PeriodoAte=" + $scope.FiltroPeriodoAte )
        .success(function (data) {
        $scope.Relatorio = data;
        if( $scope.FiltroIndicador ) {
          $scope.PlotaEvolucao();
        }
        $scope.getFranqueados();
      })
        .finally( function() {
        $scope.dataLoading = false;
      })
    }

    $scope.FranqueadosExtended = [];
    $http.get( "main_controller.php?type=listaFranqueadosExtended" )
      .success(function (data) {
      $scope.FranqueadosExtended = data;
    });

    $scope.listaNaoParticipantes();

  }]);
</script>
<script type="text/javascript">
  $("#btnExport").click(function(e) {
    e.preventDefault();
    alasql('SELECT * INTO XLSX("Relatorio.xlsx",{headers:false}) FROM HTML("#Relatorio",{headers:true})');  });
</script>
</html>







