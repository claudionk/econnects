<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pagmax360 extends Public_Controller
{
  protected $noLogin = true;

  function __construct() {
    parent::__construct();
    header( "Access-Control-Allow-Origin: *" );
    header( "Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS" );
    header( "Access-Control-Allow-Headers: Origin, Content-Type, Accept, Access-Control-Request-Method, Authorization" );
    header( "Content-Type: application/json" );

    $method = $_SERVER["REQUEST_METHOD"];
    if( $method == "OPTIONS" ) {
      die();
    }

    date_default_timezone_set('America/Sao_Paulo');
    $this->load->database('default');

    $this->load->model('pedido_model', 'pedido');
    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('produto_parceiro_pagamento_model', 'parceiro_pagamento');
    $this->load->model('pedido_cartao_model', 'cartao');
    $this->load->model('pedido_cartao_transacao_model', 'transacao');
    $this->load->model('forma_pagamento_integracao_model', 'integracao');
    $this->load->model('pedido_transacao_model', 'pedido_transacao');
    $this->load->model('apolice_model', 'apolice');  
  }

  public function retorno() {
    extract( $_GET );
    error_log( "Retorno Pagmax: " . print_r( $_GET, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
  }
  
  public function processa_pagamentos_pendentes() {
    $json = file_get_contents( "php://input" );
    $input = json_decode( $json );

    $pedido_id = $input->{"pedido_id"};

    $pedido = $this->pedido->get($pedido_id);
    $pedido_cartao = $this->cartao->filter_by_pedido($pedido_id)->get_all(1,0);
    $transacao = $this->transacao->filter_by_cartao($pedido_cartao[0]['pedido_cartao_id'])->get_all(1,0);
    if( sizeof( $transacao ) ) {
      $array = array( "result" => $transacao[0]["result"], "message" => $transacao[0]["message"], "paymentId" => $transacao[0]["tid"], "url" => $transacao[0]["url"] );
    } else {
      $array = array( "result" => "OK", "message" => "Não existem transações pendentes para esse pedido ($pedido_id)." );
    }
    die( json_encode( $array ) );
    
    $this->pagmax_efetuar_pagamento($pedido_cartao_id);

    $array = array( "status" => "OK", "TransactionType" => $TransactionType, "Pedido" => $pedido );
    die( json_encode( $array ) );
  }

  
  public function pagmax($pedido_id = 0) {
    $pedidos = $this->pedido->getPedidoPagamentoPendente( $pedido_id );
    
    $array =  array( "status" => "OK", "Pedido" => $pedido_id, "Message" => print_r( $pedidos, true ) );
    die( json_encode( $array ) );
    
    $integracao = $this->integracao->get_by_slug('pagmax');
    foreach ($pedidos as $index => $pedido) {
      try {
        $dados_pedido = array('lock' => 1);
        $this->pedido->update($pedido['pedido_id'], $dados_pedido, TRUE);

        $cartao = $this->cartao->filter_by_pedido($pedido['pedido_id'])->get_all(1, 0);
        if (count($cartao) > 0) {
          $cartao = $cartao[0];
          $transacao = $this->transacao->filter_by_cartao($cartao['pedido_cartao_id'])->get_all();
          if (intval( $integracao['qnt_erros']  ) != 0 && $cartao['erros'] >= $integracao['qnt_erros']) {
            $this->cartao->update($cartao['pedido_cartao_id'], array('ativo' => 0), TRUE);
            $this->pedido_transacao->insStatus($pedido['pedido_id'], 'pagamento_negado', "Transação não Efetuada");
          } else {
            $this->pagmax_efetuar_pagamento($cartao['pedido_cartao_id']);
          }
        }
        $dados_pedido = array('lock' => 0);
        $this->pedido->update($pedido['pedido_id'], $dados_pedido, TRUE);
      } catch(Exception $e) {
        log_message('debug', 'UNLOCK NO PEDIDO (ERROR) ' . $pedido['pedido_id']);
        $dados_pedido = array('lock' => 0);
        $this->pedido->update($pedido['pedido_id'], $dados_pedido, TRUE);
      }
    }
  }


  public function ins_apolice(){
    $pedidos = func_get_args();
    $this->load->model('apolice_model', 'apolice');
    foreach ($pedidos as $pedido_id) {
      $this->apolice->insertAplice($pedido_id);
    }
  }

  public function pagmax_efetuar_pagamento($pedido_cartao_id) {
    $this->load->library("Pagmax360");
    $Pagmax360 = new Pagmax360();
    
    $Pagmax360->merchantId = $this->config->item("Pagmax360_merchantId");
    $Pagmax360->merchantKey = $this->config->item("Pagmax360_merchantKey");
    $Pagmax360->Environment = $this->config->item("Pagmax360_Environment");

    try {
      log_message('debug', 'INICIO EFETUAR PAGMAX ' . $pedido_cartao_id);
      $this->load->model('fatura_model', 'fatura');
      $this->load->model('fatura_parcela_model', 'fatura_parcela');
      $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
      $this->load->library("Nusoap_lib");
      $this->load->library('encrypt');

      $cartao = $this->cartao->get($pedido_cartao_id);
      $pedido = $this->pedido->get($cartao['pedido_id']);


      $parceiro_pagamento = $this->parceiro_pagamento->with_forma_pagamento()
        ->with_forma_pagamento_tipo()
        ->get($pedido['produto_parceiro_pagamento_id']);

      $integracao = $this->integracao->get_by_slug('pagmax');

      $fatura = $this->fatura->get_many_by(array(
        'pedido_id' => $cartao['pedido_id']
      ));

      $fatura = $fatura[0];
      $fatura_parcela = $this->fatura_parcela
        ->limit(1)
        ->order_by('num_parcela')
        ->get_many_by(array(
          'fatura_id' => $fatura['fatura_id'],
          'fatura_status_id' => 1,
          'data_vencimento <=' => date('Y-m-d'),
        ));


      $fatura_parcela = $fatura_parcela[0];
      $valor_parcela = $fatura_parcela["valor"];
      $num_parcela = $pedido["num_parcela"];
      $valor_total = ($num_parcela > 1) ? ($valor_parcela * $num_parcela) : $pedido["valor_total"];
      
      $validade = $this->encrypt->decode($cartao['validade']);
      $numero = $this->encrypt->decode($cartao['numero']);
      $bandeira = $this->encrypt->decode($cartao['bandeira_cartao']);
      $nome = $this->encrypt->decode($cartao['nome']);
      $codigo = $this->encrypt->decode($cartao['codigo']);

      $valor_total = ($integracao['producao'] == 1) ? $valor_total : 150.00; 

      $dados_transacao = array();
      $dados_transacao['pedido_cartao_id'] = $pedido_cartao_id;
      $dados_transacao['processado'] = 1;
      $dados_transacao['result'] = '';
      $dados_transacao['message'] = '';
      $dados_transacao['tid'] = '';
      $dados_transacao['status'] = '';


      $usesandbox = ($integracao['producao'] == 1) ? FALSE : TRUE;
      $isdebit = ($parceiro_pagamento['produto_parceiro_pagamento_id'] == 6 ) ? TRUE : FALSE; 
      $return_url = ($parceiro_pagamento['produto_parceiro_pagamento_id'] == 6 ) ? base_url("admin/venda/seguro_viagem/41/5/{$pedido['pedido_id']}/?retorno=pagmax") : '';

      $merchantOrderId = (int)$fatura_parcela["fatura_parcela_id"];
      if( (bool) intval( $isdebit ) ) { 
        $transactionType = "DebitCard";
      } else {
        $transactionType = "CreditCard";
      }
      $transactionAmount = (float)$valor_total;
      $numInstallments = $num_parcela;
      $softDescriptor = $parceiro_pagamento["nome_fatura"];
      $returnUrl = $return_url;
      $cardNumber = $numero;
      $cardHolder = $nome;
      $cardExpirationDate = substr( $validade, 4, 2 ) . "/" . substr( $validade, 0, 4 );
      $cardSecurityCode = $codigo;
      $cardBrand = $bandeira;
        
      $JsonDataRequest = array("MerchantOrderId" => rand(100,999) . $merchantOrderId, 
                                 "Payment" =>  array( 
                                   "Type" => "$transactionType",
                                   "Amount" =>  $transactionAmount,
                                   "Installments" =>  $numInstallments,
                                   "Capture" =>  true,
                                   "SoftDescriptor" => $softDescriptor,
                                   "returnUrl" => $returnUrl,
                                   "$transactionType" => array( 
                                     "CardNumber" => $cardNumber,
                                     "Holder" => $cardHolder,
                                     "ExpirationDate" => $cardExpirationDate,
                                     "SecurityCode" => $cardSecurityCode,
                                     "Brand" => $cardBrand,
                                     "SaveCard" => true
                                   )
                                 )
                              );

      if( $transactionType == "CreditCard" ) {
        unset( $JsonDataRequest["Payment"]["returnUrl"] );
      }

      $Json = json_encode( $JsonDataRequest );
      
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }

	die( array( "Message" => $Json ) );
    
    try {
      // Pagmax 360 v2
      $Response = $Pagmax360->createTransaction( $Pagmax360->merchantId, $Pagmax360->merchantKey, $Json, $Pagmax360->Environment );
      $Response = json_decode( $Response );
      
      if( isset( $Response->{"Code"} ) || sizeof( $Response ) == 0 ) {

        $tipo_mensagem = "msg_erro";
        if( sizeof( $Response ) == 0 ) {
          $msgErro = "Processing Center Error (1)";
        } else {
          $msgErro = $Response->{"Message"} . " (" . $Response->{"Code"} . ")";
        }
        $dados_transacao["result"] = "ERRO";
        $dados_transacao["message"] = $msgErro;
      } else {
        $statusCode = $Response->{"Payment"}->{"Status"}->{"Code"};
        $Status = $Pagmax360->returnTransactionStatusCode( intval( $statusCode ) );
        $statusMessage = $Status["Message"];
        $TID = $Response->{"Payment"}->{"PaymentId"};
        $transactionDate = $Response->{"Payment"}->{"ReceivedDate"};
        
        if( isset( $Response->{"Payment"}->{"CreditCard"}->{"CardToken"} ) ) {
          $cardNumber = $Response->{"Payment"}->{"CreditCard"}->{"CardToken"};
        }

        if( isset( $Response->{"Payment"}->{"DebitCard"}->{"CardToken"} ) ) {
          $cardNumber = $Response->{"Payment"}->{"DebitCard"}->{"CardToken"};
        }

        $dados_transacao["status"] = $statusCode;
        $dados_transacao["data_processado"] = date( $Response->{"Payment"}->{"ReceivedDate"} );

        switch( intval( $statusCode ) ) {
          case 0:
            //Transação em andamento
            if( isset( $Response->{"Payment"}->{"Url"} ) &&  $Response->{"Payment"}->{"Url"} != "" ) {
              $redirect = $Response->{"Payment"}->{"Url"};
            }
            if( isset( $Response->{"Payment"}->{"AuthenticationUrl"} ) && $Response->{"Payment"}->{"AuthenticationUrl"} != "" ) {
              $redirect = $Response->{"Payment"}->{"AuthenticationUrl"};
            }
            $dados_transacao["result"] = "REDIRECT";
            $dados_transacao["tid"] = $TID;
            $dados_transacao["message"] = $statusMessage;
            $dados_transacao["status"] = 1;
            $dados_transacao["url"] = $redirect;
            $dados_transacao["processado"] = 0;
            break;
          case 1:
            //Transação autorizada
            $dados_transacao["result"] = "AUTORIZADA";
            $dados_transacao["tid"] = $TID;
            $dados_transacao["message"] = $statusMessage;
            $dados_transacao["status"] = 4;
            $dados_transacao["processado"] = 0;
            break;
          case 2:
            //Transação capturada
            $dados_transacao["result"] = "OK";
            $dados_transacao["tid"] = $TID;
            $dados_transacao["message"] = $statusMessage;
            $dados_transacao["status"] = 6;
            //$dados_transacao["data_processado"] = date( $Response->{"Payment"}->{"CapturedDate"} );
            $dados_transacao["processado"] = 1;
            break;
          case 3:
            //Transação negada
            $dados_transacao["result"] = "NEGADA";
            $dados_transacao["tid"] = $TID;
            $dados_transacao["message"] = $statusMessage;
            $dados_transacao["status"] = 5;
            $dados_transacao["processado"] = 1;
            break;
          case 10:
            //Transação cancelada
            $dados_transacao["result"] = "CANCELADA";
            $dados_transacao["message"] = $statusMessage;
            $dados_transacao["status"] = 9;
            //$dados_transacao["data_processado"] = date( $Response->{"Payment"}->{"VoidedDate"} );
            $dados_transacao["processado"] = 1;
            break;
          case 11:
            //Transação reembolsada
            $dados_transacao["result"] = "REEMBOLSADA";
            $dados_transacao["message"] = $statusMessage;
            $dados_transacao["status"] = 9;
            $dados_transacao["processado"] = 1;
            break;
          case 12:
            //Transação pendente
            $dados_transacao["result"] = "PENDENTE";
            $dados_transacao["message"] = $statusMessage;
            $dados_transacao["status"] = 1;
            $dados_transacao["processado"] = 1;
            break;
          case 13:
            //Transação abortada
            $dados_transacao["result"] = "ABORTADA";
            $dados_transacao["message"] = $statusMessage;
            $dados_transacao["status"] = 5;
            $dados_transacao["processado"] = 1;
            break;
          case 20:
            //Transação agendada
            $dados_transacao["result"] = "AGENDADA";
            $dados_transacao["message"] = $statusMessage;
            $dados_transacao["status"] = 1;
            $dados_transacao["processado"] = 0;
            break;
        }
      }
    } catch(Exception $e) {
      $dados_transacao['result'] = 'ERRO';
      $dados_transacao['message'] = 'Erro Acessando modulo de pagamento';
      $dados_transacao['status'] = $e->getMessage();
    }

    $erro = false;
    try {
      if($dados_transacao['result'] == "OK"){
        if($dados_transacao['status'] == 6) {
          $this->fatura->pagamentoCompletoEfetuado($fatura_parcela['fatura_parcela_id']);
          $this->apolice->insertApolice($pedido['pedido_id']);
          $this->pedido_transacao->insStatus($pedido['pedido_id'], 'pagamento_confirmado', "$statusMessage [$TID]");
          $pedido = $this->pedido->get($pedido['pedido_id']);
          $cotacao = $this->cotacao->get($pedido['cotacao_id']);

          if((int)$cotacao['cotacao_upgrade_id'] > 0) {
            $cotacao_antiga = $this->cotacao->get($cotacao['cotacao_upgrade_id']);
            $pedido_antigo = $this->pedido->get_by(array('cotacao_id' => $cotacao['cotacao_upgrade_id']));

            if($pedido_antigo) {
              $pedido_antigo['pedido_status_id'] = 5;
              $this->pedido->update($pedido_antigo['pedido_id'], $pedido_antigo);
              $this->pedido_transacao->insStatus($pedido_antigo['pedido_id'], 'cancelado', "PEDIDO CANCELADO PARA UPGRADE");
              $faturas_pedido_antigo = $this->fatura->get_many_by(array("pedido_id" => $pedido_antigo['pedido_id']));

              foreach($faturas_pedido_antigo as $fatura) {
                unset($fatura['fatura_id']);
                $fatura['pedido_id'] = $pedido['pedido_id'];
                $this->fatura->insert($fatura);
              }
              $this->pedido->executa_extorno_upgrade($pedido_antigo['pedido_id']);
            }
          }
        } else {
          $this->pedido_transacao->insStatus($pedido['pedido_id'], 'aguardando_liberacao', "Aguardando autorização da Operadora");
          $erro = true;
        }
      } elseif($dados_transacao['result'] == "REDIRECT") {
        $this->pedido_transacao->insStatus($pedido['pedido_id'], 'aguardando_pagamento_debito', "TRANSAÇÃO CRIADA COM SUCESSO. AGUARDANDO PAGAMENTO.");
      }else{
        $pedido = $this->pedido->get($pedido['pedido_id']);
        $cotacao = $this->cotacao->get_cotacao_produto($pedido['cotacao_id']);
        $configuracao = $this->produto_parceiro_configuracao
          ->filter_by_produto_parceiro($cotacao['produto_parceiro_id'])
          ->get_all();

        $configuracao = $configuracao[0];
        $this->cartao->update($pedido_cartao_id, array('erros' => $cartao['erros']+1), TRUE);
        $this->pedido_transacao->insStatus($pedido['pedido_id'], 'pagamento_negado', "Transação não Efetuada");
        $this->apolice->disparaEventoErroApolice($pedido['pedido_id']);
        $erro = true;
      }

      if($erro) {
        $this->apolice->disparaEventoErroApolice($pedido['pedido_id']);
      }

      $this->cartao->update($pedido_cartao_id, array('processado' => 1), TRUE);
      $this->transacao->insert($dados_transacao, TRUE);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }

  }

  public function consulta($pedido_id = 0){


    $pedido_id = ($pedido_id == 0) ? $this->input->post('pedido_id') : $pedido_id;

    $this->load->model('pedido_cartao_model', 'cartao');

    $pedido = $this->pedido->with_pedido_status()->filter_by_pedido($pedido_id)->get_all();

    $result = array();
    $result['result'] = FALSE;
    $result['status_pedido'] = '';
    $result['status_id'] = '';
    $result['class_pagamento'] = 'btn-danger';
    $result['transacao_result'] = '';
    $result['transacao_message'] = '';
    $result['transacao_tid'] = '';
    $result['transacao_url'] = '';

    if(count($pedido) > 0 ) {
      $pedido = $pedido[0];

      $class = "btn-info";
      if($pedido['pedido_status_id'] == 3 ){
        $class = 'btn-success';
      }elseif($pedido['pedido_status_id'] == 4 ){
        $class = 'btn-danger';
      }


      $status = $this->cartao->get_last_transacao($pedido_id);

      if($status) {
        $status = $status[0];
      }

      $result['result'] = ($pedido['pedido_status_id'] == 3 || $pedido['pedido_status_id'] == 4) ? TRUE : FALSE;
      $result['status_pedido'] = $pedido['pedido_status_nome'];
      $result['status_id'] = $pedido['pedido_status_id'];
      $result['class_pagamento'] = $class;
      $result['transacao_result'] = (isset($status['result'])) ? $status['result'] : '';
      $result['transacao_message'] = (isset($status['message'])) ? $status['message'] : '';
      $result['transacao_tid'] = (isset($status['tid'])) ? $status['tid'] : '';
      $result['transacao_url'] = (isset($status['url'])) ? $status['url'] : '';



    }else{
      $result['mensagem'] = "PEDIDO NÃO ENCONTRADO";
    }


    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($result));
  }

  public function pedido($cotacao_id = 0){


    $cotacao_id = ($cotacao_id == 0) ? $this->input->post('cotacao_id') : $cotacao_id;

    $this->load->model('pedido_model', 'cartao');

    $pedido = $this->pedido->get_many_by(
      array(
        'pedido.cotacao_id' => $cotacao_id
      ));

    $result = array();
    $result['result'] = FALSE;
    $result['pedido_id'] = '';
    $result['pedido_status_id'] = '';
    $result['mensagem'] = '';

    if(count($pedido) > 0 ) {
      $pedido = $pedido[0];
      $result['result'] = TRUE;
      $result['pedido_status_id'] = $pedido['pedido_status_id'];
      $result['pedido_id'] = $pedido['pedido_id'];

    }else{
      $result['mensagem'] = "PEDIDO NÃO ENCONTRADO";
    }


    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($result));
  }

  public function pagmax_efetuar_estorno($pedido_id = 0){


    $this->load->model('fatura_model', 'fatura');
    $this->load->model('apolice_model', 'apolice');
    $this->load->model('pedido_model', 'pedido');
    $this->load->model('forma_pagamento_integracao_model', 'forma_pagamento_integracao');
    $this->load->model('pedido_transacao_model', 'pedido_transacao');
    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('pedido_cartao_transacao_model', 'pedido_cartao_transacao');

    $this->load->library("Nusoap_lib");


    $pedidos = $this->pedido->getPedidoCanceladoEstorno($pedido_id);


    foreach ($pedidos as $pedido) {


      $integracao = $this->forma_pagamento_integracao->get_by_slug('pagmax');


      $dados_transacao = array();
      $dados_transacao['estorno_data'] = date('Y-m-d H:i:s');
      $dados_transacao['estorno_status'] = '';
      $dados_transacao['estorno_result'] = '';
      $dados_transacao['estorno_message'] = '';


      $usesandbox = ($integracao['producao'] == 1) ? FALSE : TRUE;
      $tid = $pedido['tid'];

      //Monta array com dados
      $param = array(
        'TID' => $tid,
        'MerchantAcquirerHashID' => ($integracao['producao'] == 1) ? $integracao['chave_acesso'] : $integracao['chave_acesso_homolog'],
        'UseSandbox' => $usesandbox,
      );


      try {
        $client = new nusoap_client($integracao['url']);
        $client->setUseCurl(true);
        $response = $client->call('TIDvoid', $param);
        $response = json_decode($response);
        $dados_transacao['estorno_result'] = $response->result;
      } catch (Exception $e) {
        $dados_transacao['estorno_result'] = 'ERRO';
        $dados_transacao['estorno_message'] = 'Erro Acessando modulo de pagamento';
        $dados_transacao['estorno_status'] = $e->getMessage();

      }


      $erro = false;
      try {

        if ($dados_transacao['estorno_result'] == "ESTORNADO") {
          $dados_transacao['estorno_message'] = 'Estorno efetuado com Sucesso';
          $dados_transacao['estorno_status'] = isset($response->Status) ? $response->Status : '';


          if ($dados_transacao['estorno_status'] == 9) {

            $this->pedido_transacao->insStatus($pedido['pedido_id'], 'cancelado_stornado', "Pedido estornado com sucesso [{$response->TID}]");

            //muda status das faturas
            $this->fatura->estornoCompletoEfetuado($pedido['pedido_id']);

          }


        }else{
          $dados_transacao['estorno_message'] = isset($response->message) ? $response->message : '';
          $dados_transacao['estorno_status'] = isset($response->Status) ? $response->Status : '';

        }


        $this->pedido_cartao_transacao->update($pedido['pedido_cartao_transacao_id'], $dados_transacao, TRUE);

      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }

    }
  }


  
}




