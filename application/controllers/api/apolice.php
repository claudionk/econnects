<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apolice extends CI_Controller {
  
  public function __construct() {
    parent::__construct();
    
    header( "Access-Control-Allow-Origin: *" );
    header( "Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS" );
    header( "Access-Control-Allow-Headers: Cache-Control, APIKEY, apikey, Content-type" );
    header( "Content-Type: application/json");

    $method = $_SERVER["REQUEST_METHOD"];
    if( $method == "OPTIONS" ) {
      die();
    }

    if( isset( $_SERVER["HTTP_APIKEY"] ) ) {
      $this->api_key = $_SERVER["HTTP_APIKEY"];
      $this->load->model( "usuario_webservice_model", "webservice" );
      
      $webservice = $this->webservice->checkKeyExpiration( $this->api_key );
      if( !sizeof( $webservice ) ) {
        die( json_encode( array( "status" => false, "message" => "APIKEY is invalid" ) ) );
      }
    } else {
      die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
    }
    $this->usuario_id = $webservice["usuario_id"];
    $this->load->database('default');
    
    $this->load->model('apolice_model', 'apolice');
    $this->load->model('apolice_status_model', 'apolice_status');
    $this->load->model('pedido_model', 'pedido');
    $this->load->model("fatura_model", "fatura");
    $this->load->model("fatura_parcela_model", "fatura_parcela");

    $this->load->helper("api_helper");
  }

  private function update( $apolice_id, $num_apolice ) {
    $this->db->query("UPDATE apolice SET num_apolice='$num_apolice' WHERE apolice_id=$apolice_id" );
    $result = $this->db->query("SELECT * FROM apolice WHERE apolice_id=$apolice_id" )->result_array();
    die( json_encode( array( "status" => (bool)sizeof($result), "apolice" => $result ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
  }
  
  public function index() {
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      if( $_SERVER["REQUEST_METHOD"] === "PUT" ) {
        $PUT = json_decode( file_get_contents( "php://input" ), true );
        if( !isset( $PUT["apolice_id"] ) ) {
          die( json_encode( array( "status" => false, "message" => "Campo apolice_id é obrigatório" ) ) );
        }
        if( !isset( $PUT["num_apolice"] ) ) {
          die( json_encode( array( "status" => false, "message" => "Campo num_apolice é obrigatório" ) ) );
        }
        $apolice_id = $PUT["apolice_id"];
        $num_apolice = $PUT["num_apolice"];
          
        $this->update( $apolice_id, $num_apolice );
      }
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    $apolice_id = null;
    if( isset( $GET["apolice_id"] ) ) {
      $apolice_id = $GET["apolice_id"];
    } 
    

    $num_apolice = null;
    if( isset( $GET["num_apolice"] ) ) {
      $num_apolice = $GET["num_apolice"];
    }

    $documento = null;
    if( isset( $GET["documento"] ) ) {
      $documento = $GET["documento"];
    }

    $pedido_id = null;
    if( isset( $GET["pedido_id"] ) ) {
      $pedido_id = $GET["pedido_id"];
    }
    
    $params = array();

    $params["apolice_id"] = $apolice_id;
    $params["num_apolice"] = $num_apolice;
    $params["documento"] = $documento;
    $params["pedido_id"] = $pedido_id;

    if($apolice_id || $num_apolice || $documento || $pedido_id ) {
      $pedidos = $this->pedido
        ->with_pedido_status()
        ->with_cotacao_cliente_contato()
        ->with_apolice()
        ->with_fatura()
        ->filterNotCarrinho()
        ->filterAPI($params)
        ->get_all();

      if($pedidos) {

        foreach ($pedidos as $pedido) {
          //Monta resposta da apólice
          $apolice = $this->apolice->getApolicePedido( $pedido["pedido_id"] );
          $apolice[0]["inadimplente"] = ($this->pedido->isInadimplente( $pedido["pedido_id"] ) === false ) ? 0 : 1;


          $faturas = $this->fatura->filterByPedido($pedido["pedido_id"])
            ->with_fatura_status()
            ->with_pedido()
            ->order_by("data_processamento")
            ->get_all();


          foreach ($faturas as $index => $fatura) {
            $faturas[$index]["parcelas"] = $this->fatura_parcela->with_fatura_status()
              ->filterByFatura($fatura["fatura_id"])
              ->order_by("num_parcela")
              ->get_all();

          }


          $resposta[] = array(
            "apolice" => api_retira_timestamps($apolice),
            "faturas" => api_retira_timestamps($faturas),
            "pedido" => api_retira_timestamps($pedido),
          );
        }

        die( json_encode( $resposta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
      } else {
        die( json_encode( array( "status" => false, "message" => "Não foi possível localizar a apólice com os parâmetros informados" ) ) );
        $response->setStatus(false);
      }


    } else {
      die( json_encode( array( "status" => false, "message" => "Parâmetros inválidos" ) ) );
    }
  }
  
  function cancelar() {
    if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
      $POST = json_decode( file_get_contents( "php://input" ), true );
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    $apolice_id = null;
    if( isset( $POST["apolice_id"] ) ) {
      $apolice_id = $POST["apolice_id"];
      $params["apolice_id"] = $apolice_id;
    } else {
      die( json_encode( array( "status" => false, "message" => "Campo apolice_id é obrigatório" ) ) );
    }

    
    $vigente = false;
    
    
    $ins_movimentacao = true;

    $this->load->model("produto_parceiro_cancelamento_model", "cancelamento");
    $this->load->model("apolice_model", "apolice");
    $this->load->model("fatura_model", "fatura");
    $this->load->model("apolice_equipamento_model", "apolice_equipamento");
    $this->load->model("apolice_generico_model", "apolice_generico");
    $this->load->model("apolice_seguro_viagem_model", "apolice_seguro_viagem");
    $this->load->model("pedido_transacao_model", "pedido_transacao");
    $this->load->model("pedido_model", "pedido");
    $this->load->model("apolice_movimentacao_model", "movimentacao");
    $this->load->model( "produto_parceiro_model", "produto_parceiro" );

    $pedido = $this->db->query( "SELECT pedido_id FROM apolice WHERE apolice_id=$apolice_id" )->result_array();
    if(!$pedido) {
      die( json_encode( array( "status" => false, "message" => "Apólice não encontrada" ) ) );
    }

    $pedido_id = $pedido[0]["pedido_id"];
    
    $pedido = $this->pedido->get($pedido_id);
    
    $ja_tem_pedido_cancelado = sizeof( $this->db->query( "SELECT * FROM fatura WHERE pedido_id=$pedido_id AND tipo='ESTORNO' AND deletado=0" )->result_array() ) > 0;
    if( $ja_tem_pedido_cancelado ) {
      die( json_encode( array( "status" => false, "message" => "Não é possível cancelar essa apólice. Motivo: a apólice já está cancelada." ) ) );
    }

    //pega as configurações de cancelamento do pedido
    $produto_parceiro = $this->pedido->getPedidoProdutoParceiro($pedido_id);


    $produto_parceiro = $produto_parceiro[0];
    $produto_parceiro_cancelamento = $this->pedido->cancelamento->filter_by_produto_parceiro($produto_parceiro["produto_parceiro_id"])->get_all();
    
    $produto = $this->produto_parceiro->with_produto()->get( $produto_parceiro["produto_parceiro_id"] );

    if( $produto ) {
      $produto_slug = $produto["produto_slug"];
      switch( $produto_slug ) {
        case "seguro_viagem":
          $vigente = sizeof( $this->db->query( "SELECT * FROM apolice_seguro_viagem WHERE apolice_id=$apolice_id AND STR_TO_DATE('" . date( "Y-m-d" ). "','%Y-%m-%d') BETWEEN data_ini_vigencia AND data_fim_vigencia" )->result_array() ) > 0;
          break;
        case "equipamento":
          $vigente = sizeof( $this->db->query( "SELECT * FROM apolice_equipamento WHERE apolice_id=$apolice_id AND STR_TO_DATE('" . date( "Y-m-d" ). "','%Y-%m-%d') BETWEEN data_ini_vigencia AND data_fim_vigencia" )->result_array() ) > 0;
          break;
        case "generico":
        case "seguro_saude":
          $vigente = sizeof( $this->db->query( "SELECT * FROM apolice_generico WHERE apolice_id=$apolice_id AND STR_TO_DATE('" . date( "Y-m-d" ). "','%Y-%m-%d') BETWEEN data_ini_vigencia AND data_fim_vigencia" )->result_array() ) > 0;
          break;
      }
    }

    $produto_parceiro_cancelamento = $produto_parceiro_cancelamento[0];

    $apolices = $this->apolice->getApolicePedido($pedido_id);

    $apolice = $apolices[0];

    $valor_estorno_total = 0;

    if($vigente == FALSE){
      //FAZ CALCULO DO VALOR COMPLETO

      foreach ($apolices as $apolice) {

        $valor_premio = $apolice["valor_premio_total"];

        $valor_estorno = app_calculo_valor($produto_parceiro_cancelamento["seg_antes_calculo"], $produto_parceiro_cancelamento["seg_antes_valor"], $valor_premio);

        $dados_apolice = array();

        $dados_apolice["data_cancelamento"] = date("Y-m-d H:i:s");
        $dados_apolice["valor_estorno"] = $valor_estorno;
        $valor_estorno_total += $valor_estorno;
        
        if( $produto ) {
          $produto_slug = $produto["produto_slug"];
          switch( $produto_slug ) {
            case "seguro_viagem":
              $this->apolice_seguro_viagem->update($apolice["apolice_seguro_viagem_id"],  $dados_apolice, TRUE);
              break;
            case "equipamento":
              $this->apolice_equipamento->update($apolice["apolice_equipamento_id"],  $dados_apolice, TRUE);
              break;
            case "generico":
            case "seguro_saude":
              $this->apolice_generico->update($apolice["apolice_generico_id"],  $dados_apolice, TRUE);
              break;
          }
        }


        if($ins_movimentacao) {
          $this->movimentacao->insMovimentacao("C", $apolice["apolice_id"]);
        }

      }


    } else {
      //FAZ CALCULO DO VALOR PARCIAL

      $dias_restantes = app_date_get_diff_dias(date("d/m/Y"), app_dateonly_mysql_to_mask($apolice["data_fim_vigencia"]), "D") + 1 ;
      $dias_utilizados = app_date_get_diff_dias(app_dateonly_mysql_to_mask($apolice["data_ini_vigencia"]), date("d/m/Y"),  "D") + 1;
      $dias_total = app_date_get_diff_dias(app_dateonly_mysql_to_mask($apolice["data_ini_vigencia"]), app_dateonly_mysql_to_mask($apolice["data_fim_vigencia"]),  "D");

      $porcento_nao_utilizado = ((($dias_restantes) / $dias_total) * 100);
      if ( !empty($produto_parceiro_cancelamento['seg_depois_dias_carencia']) && $dias_utilizados <= $produto_parceiro_cancelamento['seg_depois_dias_carencia']) {
        $dias_restantes = $dias_total;
      }

      $ret = array();
      $ret["dias_restantes"] = $dias_restantes;
      $ret["dias_utilizados"] = $dias_utilizados;
      $ret["dias_total"] = $dias_total;
      $ret["soma_restantes_utilizados"] = $dias_restantes + $dias_utilizados;
      $ret["porcento_nao_utilizado"] = $porcento_nao_utilizado;

      foreach ($apolices as $apolice) {

        $valor_premio = $apolice["valor_premio_total"];
        $ret["valor_premio"] = $valor_premio;

        $valor_premio = $valor_premio / $dias_total * $dias_restantes; // (($porcento_nao_utilizado / 100) * $valor_premio);
        $ret["valor_premio_restante"] = $valor_premio;

        $valor_estorno = app_calculo_valor($produto_parceiro_cancelamento["seg_depois_calculo"], $produto_parceiro_cancelamento["seg_depois_valor"], $valor_premio);
        $ret["valor_estorno"] = $valor_estorno;

        $dados_apolice = array();

        $dados_apolice["data_cancelamento"] = date("Y-m-d H:i:s");
        $dados_apolice["valor_estorno"] = $valor_estorno;
        $valor_estorno_total += $valor_estorno;
        if( $produto ) {
          $produto_slug = $produto["produto_slug"];
          switch( $produto_slug ) {
            case "seguro_viagem":
              $this->apolice_seguro_viagem->update($apolice["apolice_seguro_viagem_id"],  $dados_apolice, TRUE);
              break;
            case "equipamento":
              $this->apolice_equipamento->update($apolice["apolice_equipamento_id"],  $dados_apolice, TRUE);
              break;
            case "generico":
            case "seguro_saude":
              $this->apolice_generico->update($apolice["apolice_generico_id"],  $dados_apolice, TRUE);
              break;
          }
        }

        $this->movimentacao->insMovimentacao("C", $apolice["apolice_id"]);

      }
      
    }

    $this->pedido_transacao->insStatus($pedido_id, "cancelado", "PEDIDO CANCELADO COM SUCESSO");

    $this->fatura->insertFaturaEstorno($pedido_id, $valor_estorno_total);
    
    die( json_encode( array( "status" => true, "message" => "Apólice cancelada com sucesso", "ret" => $ret ) ) );

  }

}








