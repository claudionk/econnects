<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Gateway extends Admin_Controller
{

    protected $noLogin      = true;

    public function __construct()
    {
        parent::__construct();

        //Carrega modelos
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('produto_parceiro_pagamento_model', 'parceiro_pagamento');
        $this->load->model('pedido_cartao_model', 'cartao');
        $this->load->model('pedido_cartao_transacao_model', 'transacao');
        $this->load->model('forma_pagamento_integracao_model', 'integracao');
        $this->load->model('pedido_transacao_model', 'pedido_transacao');
        $this->load->model('apolice_model', 'apolice');

        $this->pedido->disablelog();
    }

    public function index($pedido_id = 0)
    {

        if (!$pedido_id) {
            return;
        }

        $pedido = $this->pedido->get($pedido_id);
        if (!$pedido) {
            return;
        }

        $parceiro_pagamento = $this->parceiro_pagamento->with_forma_pagamento()
            ->with_forma_pagamento_tipo()
            ->get($pedido['produto_parceiro_pagamento_id']);

        if ($parceiro_pagamento['forma_pagamento_integracao_id'] == $this->config->item("INTEGRACAO_PAGMAX")) {
            $this->pagmax($pedido_id);
        }
    }

    public function run()
    {
        error_log("Gateway\n", 3, "/var/log/httpd/360.log");
        $i = 1;
        while ($i > 0) {
            //ini_set("memory_limit","512M");
            //set_time_limit(999999);
            //ini_set ('max_execution_time', 999999);
            log_message('debug', '---------- EXECUTANDO PAGMAX');
            $this->pagmax();
            $this->pagmax_efetuar_estorno();
            print($i . " -- " . date('H:i:s:u') . "  <br/>");
            log_message('debug', '---------- PAGMAX EXECUTADO');
            sleep(3);
            $i--;
        }
    }

    public function pagmax($pedido_id = 0)
    {
        log_message('debug', 'INICIO PAGMAX');

        $pedidos = $this->pedido->getPedidoPagamentoPendente($pedido_id);

        log_message('debug', 'BUSCANDO PEDIDOS PENDENTES - ' . count($pedidos));

        //error_log( print_r( $cartao, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

        $integracao = $this->integracao->get_by_slug('pagmax');
        log_message('debug', 'BUSCANDO INTEGRAÇÃO - ' . count($integracao));
        foreach ($pedidos as $index => $pedido) {

            log_message('debug', 'PEDIDO ' . $pedido['pedido_id']);
            //verifica se exite cartão não processado
            try {
                //faz um lock no pedido
                log_message('debug', 'LOCK NO PEDIDO ' . $pedido['pedido_id']);
                $dados_pedido = array('lock' => 1);
                $this->pedido->update($pedido['pedido_id'], $dados_pedido, true);

                log_message('debug', 'BUSCANDO CARTAO');
                $cartao = $this->cartao->filter_by_pedido($pedido['pedido_id'])
                    ->get_all(1, 0);

                //error_log( "BUSCANDO CARTÃO #3\n", 3, "/var/log/httpd/myapp.log" );
                //error_log( print_r( $pedido, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

                if (count($cartao) > 0) {
                    $cartao = $cartao[0];
                    log_message('debug', 'CARTAO ENCONTRADO ' . $cartao['pedido_cartao_id']);
                    //verifica se existe processamento pendente
                    $transacao = $this->transacao->filter_by_cartao($cartao['pedido_cartao_id'])->get_all();

                    log_message('debug', 'BUSCANDO TRANSAÇÕES ' . count($transacao));

                    //verifica a quantidade de transações que deram erro
                    /*
                    $total = $this->transacao->filter_by_cartao_erro($cartao['pedido_cartao_id'])
                    ->get_total();
                     */

                    //log_message('debug', 'QUANTIDADE DE TRANSAÇÕES COM ERRO '. $total);

                    if ((intval($integracao['qnt_erros']) != 0 && $cartao['erros'] >= $integracao['qnt_erros']) || $cartao['erros'] > 5) {
                        $this->cartao->update($cartao['pedido_cartao_id'], array('ativo' => 0), true);
                        $this->pedido_transacao->insStatus($pedido['pedido_id'], 'pagamento_negado', "Transação não Efetuada");
                        log_message('debug', 'NEGANDO TRANSACAO ');

                    } else {
                        log_message('debug', 'EFETUAR PAGAMENTO PAGMAX ' . $cartao['pedido_cartao_id']);
                        $this->pagmax_efetuar_pagamento($cartao['pedido_cartao_id']);
                    }

                }

                log_message('debug', 'UNLOCK NO PEDIDO ' . $pedido['pedido_id']);
                $dados_pedido = array('lock' => 0);
                $this->pedido->update($pedido['pedido_id'], $dados_pedido, true);
            } catch (Exception $e) {
                log_message('debug', 'UNLOCK NO PEDIDO (ERROR) ' . $pedido['pedido_id']);
                $dados_pedido = array('lock' => 0);
                $this->pedido->update($pedido['pedido_id'], $dados_pedido, true);
            }
        }

    }

    public function ins_apolice()
    {
        $pedidos = func_get_args();
        $this->load->model('apolice_model', 'apolice');
        foreach ($pedidos as $pedido_id) {
            $this->apolice->insertApolice($pedido_id);
        }

    }

    /**
     * Efetuar pagamento pagmax
     * @param $pedido_cartao_id
     * @throws Exception
     */
    public function pagmax_efetuar_pagamento($pedido_cartao_id)
    {
        $this->load->library("Pagmax360");
        $Pagmax360 = new Pagmax360();

        $Pagmax360->merchantId  = $this->config->item("Pagmax360_merchantId");
        $Pagmax360->merchantKey = $this->config->item("Pagmax360_merchantKey");
        $Pagmax360->Environment = $this->config->item("Pagmax360_Environment");

        try
        {
            log_message('debug', 'INICIO EFETUAR PAGMAX ' . $pedido_cartao_id);
            $this->load->model('fatura_model', 'fatura');
            $this->load->model('recorrencia_model', 'recorrencia');

            $this->load->model('fatura_parcela_model', 'fatura_parcela');
            $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
            $this->load->library("Nusoap_lib");
            $this->load->library('encrypt');

            log_message('debug', 'BUSCA CARTAO ' . $pedido_cartao_id);
            $cartao = $this->cartao->get($pedido_cartao_id);
            log_message('debug', 'BUSCA PEDIDO ' . $cartao['pedido_id']);
            $pedido = $this->pedido->get($cartao['pedido_id']);

            $parceiro_pagamento = $this->parceiro_pagamento->with_forma_pagamento()
                ->with_forma_pagamento_tipo()
                ->get($pedido['produto_parceiro_pagamento_id']);

            //$parceiro_pagamento['produto_parceiro_pagamento_id'] = $parceiro_pagamento["forma_pagamento_id"];

            //error_log( "Forma de Pagamento (Gateway): " . print_r( $parceiro_pagamento, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

            log_message('debug', 'PARCEIRO PAGAMENTO');
            log_message('debug', print_r($parceiro_pagamento, true));
            log_message('debug', 'BUSCA INTEGRAÇÃO ');
            $integracao = $this->integracao->get_by_slug('pagmax');

            //Busca a fatura
            $fatura = $this->fatura->get_many_by(array('pedido_id' => $cartao['pedido_id']));

            $fatura         = $fatura[0];
            $fatura_parcela = $this->fatura_parcela
                ->limit(1)
                ->order_by('num_parcela')
                ->get_many_by(array(
                    'fatura_id'          => $fatura['fatura_id'],
                    'fatura_status_id'   => 1,
                    'data_vencimento <=' => date('Y-m-d'),
                ));

            $fatura_parcela = $fatura_parcela[0];

            $valor_parcela = $fatura_parcela["valor"];
            $num_parcela   = $pedido["num_parcela"];
            $valor_total   = ($num_parcela > 1) ? ($valor_parcela * $num_parcela) : $pedido["valor_total"];

            $validade       = $this->encrypt->decode($cartao['validade']);
            $numero         = $this->encrypt->decode($cartao['numero']);
            $bandeira       = $this->encrypt->decode($cartao['bandeira_cartao']);
            $nome           = $this->encrypt->decode($cartao['nome']);
            $codigo         = $this->encrypt->decode($cartao['codigo']);
            $dia_vencimento = $this->encrypt->decode($cartao['dia_vencimento']);

            $valor_total = ($integracao['producao'] == 1) ? $valor_total : 150.00;

            $dados_transacao                     = array();
            $dados_transacao['pedido_cartao_id'] = $pedido_cartao_id;
            $dados_transacao['processado']       = 1;
            $dados_transacao['result']           = '';
            $dados_transacao['message']          = '';
            $dados_transacao['tid']              = '';
            $dados_transacao['status']           = '';

            $usesandbox = ($integracao['producao'] == 1) ? false : true;

            $isdebit    = ($parceiro_pagamento['forma_pagamento_id'] == $this->config->item("FORMA_PAGAMENTO_CARTAO_DEBITO")) ? true : false; //ReturnURL
            $return_url = ($parceiro_pagamento['forma_pagamento_id'] == $this->config->item("FORMA_PAGAMENTO_CARTAO_DEBITO")) ? $this->config->item('base_url') . "/index.php/pagmax360/retorno?pedido_id={$pedido['pedido_id']}" : "";

            //base_url("admin/venda/seguro_viagem/41/5/{$pedido['pedido_id']}/?retorno=pagmax") : '';

            //@todo Teste prod
            // $valor_total = 1.00;

            //Monta array com dados

            // Pagmax 360 v1
            $param = array(
                'CCType'                   => $bandeira, //app_get_card_type($numero), //Tipo do cartão
                'CCNumber'                 => $numero, //Número do cartão de crédito
                'CCExp'                    => $validade, //Expira em
                'CCCVV2'                   => $codigo, //Código confirmação (CVC)
                'CCHolderName'             => $nome, //Nome

                'MerchantAcquirerHashID'   => ($integracao['producao'] == 1) ? $integracao['chave_acesso'] : $integracao['chave_acesso_homolog'],
                'MerchantOrderDescription' => "Código: {$pedido['codigo']}",
                'MerchantOrderNum'         => (int) $fatura_parcela['fatura_parcela_id'],
                'MerchantTotal'            => (float) $valor_total,
                'MerchantSoftDescriptor'   => $parceiro_pagamento['nome_fatura'], //nome da fatura 13 Caracteres

                'DoCapture'                => true,
                'UseSandbox'               => $usesandbox,
                'ProcessOutside'           => false,
                'ReturnURL'                => $return_url,
                'NumInstallments'          => (int) $num_parcela,
                'isDebit'                  => $isdebit,
            );

            // Pagmax 360 v2
            $merchantOrderId = (int) $fatura_parcela["fatura_parcela_id"];
            if ($isdebit) {
                $transactionType = "DebitCard";
            } else {
                $transactionType = "CreditCard";
            }
            $transactionAmount  = (float) $valor_total;
            $numInstallments    = $num_parcela;
            $softDescriptor     = $parceiro_pagamento["nome_fatura"];
            $returnUrl          = $return_url;
            $cardNumber         = $numero;
            $cardHolder         = $nome;
            $cardExpirationDate = substr($validade, 4, 2) . "/" . substr($validade, 0, 4);
            $cardSecurityCode   = $codigo;
            $cardBrand          = $bandeira;

            if ($parceiro_pagamento['forma_pagamento_id'] == $this->config->item("FORMA_PAGAMENTO_CARTAO_DEBITO") || $parceiro_pagamento['forma_pagamento_id'] == $this->config->item("FORMA_PAGAMENTO_CARTAO_CREDITO")) {

                $JsonDataRequest = array(
                    "MerchantOrderId" => rand(100, 999) . $merchantOrderId,
                    "Payment"         => array(
                        "Type"             => "$transactionType",
                        "Amount"           => $transactionAmount,
                        "Installments"     => $numInstallments,
                        "Capture"          => true,
                        "SoftDescriptor"   => $softDescriptor,
                        "returnUrl"        => $returnUrl,
                        "$transactionType" => array(
                            "CardNumber"     => $cardNumber,
                            "Holder"         => $cardHolder,
                            "ExpirationDate" => $cardExpirationDate,
                            "SecurityCode"   => $cardSecurityCode,
                            "Brand"          => $cardBrand,
                            "SaveCard"       => true,
                        ),
                    ),
                );

                // valida recorrência
                $JsonDataRequest = $this->parceiro_pagamento->getRecurrent($parceiro_pagamento['forma_pagamento_id'], $pedido['produto_parceiro_pagamento_id'], $JsonDataRequest, $dia_vencimento);
            }

            if ($parceiro_pagamento['forma_pagamento_id'] == $this->config->item("FORMA_PAGAMENTO_TRANSF_BRADESCO") || $parceiro_pagamento['forma_pagamento_id'] == $this->config->item("FORMA_PAGAMENTO_TRANSF_BB")) {
                $JsonDataRequest = array(
                    array(
                        "MerchantOrderId" => rand(100, 999) . $merchantOrderId,
                        "Payment"         => array(
                            "Type"      => "EletronicTransfer",
                            "Amount"    => $transactionAmount,
                            "Provider"  => "Simulado",
                            "ReturnUrl" => $returnUrl,
                        ),
                    ),
                );
            }

            if ($parceiro_pagamento['forma_pagamento_id'] == $this->config->item("FORMA_PAGAMENTO_BOLETO")) {
                $JsonDataRequest = array(
                    array(
                        "MerchantOrderId" => rand(100, 999) . $merchantOrderId,
                        "Customer"        => array(
                            "Name"     => $CustomerName,
                            "Identity" => "13452235874",
                            "Address"  => array(
                                "Street"     => "Alameda Rio Negro",
                                "Number"     => "500",
                                "Complement" => "6° andar",
                                "ZipCode"    => "06454-000",
                                "District"   => "-",
                                "City"       => "Barueri",
                                "State"      => "SP",
                                "Country"    => "BRA",
                            ),
                        ),
                        "Payment"         => array(
                            "Type"           => "Boleto",
                            "Amount"         => $transactionAmount,
                            "Provider"       => "Simulado",
                            "Address"        => "Alameda Rio Negro, 500 - 6° andar - Alphaville - Barueri - São Paulo - CEP 06454-000",
                            "BoletoNumber"   => $merchantOrderId,
                            "Assignor"       => "SIS - Soluções Intergradas em Serviços Ltda",
                            "ExpirationDate" => "2018-07-10",
                            "Identification" => "08.267.567/0001-30",
                            "Instructions"   => "Aceitar somente ate a data de vencimento, apos essa data juros de 1% dia.",
                        ),
                    ),
                );
            }

            $Json = json_encode($JsonDataRequest);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        try {
            log_message('debug', 'FAZ CHAMADA WS ', print_r($param, true));

            // Pagmax 360 v1
            /*
            $client = new nusoap_client($integracao['url']);
            $client->setUseCurl(true);
            $response = $client->call('CCauthorize', $param);
            $response = json_decode($response);
            $dados_transacao['result'] = $response->result;
             */

            // Pagmax 360 v2
            $Response = $Pagmax360->createTransaction($Pagmax360->merchantId, $Pagmax360->merchantKey, $Json, $Pagmax360->Environment, $pedido['pedido_id']);
            $Response = json_decode($Response);
            error_log(print_r($Response, true) . "\n", 3, "/var/log/httpd/myapp.log");
            if (isset($Response->{"Code"}) || sizeof($Response) == 0 || (isset($Response->{"status"}) && empty($Response->{"status"}))) {
                $tipo_mensagem = "msg_erro";
                if (sizeof($Response) == 0) {
                    $msgErro = "Processing Center Error (1)";
                } elseif (isset($Response->{"status"})) {
                    $msgErro = $Response->{"message"};
                } else {
                    $msgErro = $Response->{"Message"} . " (" . $Response->{"Code"} . ")";
                }
                $dados_transacao["result"]      = "ERRO";
                $dados_transacao["message"]     = $msgErro;
                $dados_transacao["slug_status"] = 'erro';
                //error_log( "Erro em Pagmax360->createTransaction\n", 3, "/var/log/httpd/myapp.log" );
            } else {
                // Processa retorno do Gateway
                $statusCode      = $Response->{"Payment"}->{"Status"}->{"Code"};
                $Status          = $Pagmax360->returnTransactionStatusCode(intval($statusCode));
                $statusMessage   = $Status["Message"];
                $TID             = $Response->{"Payment"}->{"PaymentId"};
                $transactionDate = $Response->{"Payment"}->{"ReceivedDate"};

                if (isset($Response->{"Payment"}->{"CreditCard"}->{"CardToken"})) {
                    $cardNumber = $Response->{"Payment"}->{"CreditCard"}->{"CardToken"};
                }

                if (isset($Response->{"Payment"}->{"DebitCard"}->{"CardToken"})) {
                    $cardNumber = $Response->{"Payment"}->{"DebitCard"}->{"CardToken"};
                }

                if (isset($Response->{"Payment"}->{"RecurrentPayment"})) {
                    $this->recorrencia->insert(array(
                        "pedido_id" => $pedido['pedido_id']
                        , "Capture" => $Response->{"Payment"}->{"Capture"}
                        , "Tid" => $Response->{"Payment"}->{"Tid"}
                        , "ProofOfSale" => $Response->{"Payment"}->{"ProofOfSale"}
                        , "AuthorizationCode" => $Response->{"Payment"}->{"AuthorizationCode"}
                        , "ReceivedDate" => $Response->{"Payment"}->{"ReceivedDate"}
                        , "CapturedDate" => $Response->{"Payment"}->{"CapturedDate"}
                        , "PaymentId" => $Response->{"Payment"}->{"PaymentId"}
                        , "RecurrentPaymentId" => $Response->{"Payment"}->{"RecurrentPayment"}->{"RecurrentPaymentId"},
                    ));
                }

                $dados_transacao["status"] = $statusCode;
                //$dados_transacao["data_processado"] = date( $Response->{"Payment"}->{"ReceivedDate"} );

                //error_log( "Code: $statusCode - Message: $statusMessage\n", 3, "/var/log/httpd/myapp.log" );

                switch (intval($statusCode)) {
                    case 0:
                        //Transação em andamento
                        if (isset($Response->{"Payment"}->{"Url"}) && $Response->{"Payment"}->{"Url"} != "") {
                            $redirect = $Response->{"Payment"}->{"Url"};
                        }
                        if (isset($Response->{"Payment"}->{"AuthenticationUrl"}) && $Response->{"Payment"}->{"AuthenticationUrl"} != "") {
                            $redirect = $Response->{"Payment"}->{"AuthenticationUrl"};
                        }
                        $dados_transacao["result"]      = "REDIRECT";
                        $dados_transacao["tid"]         = $TID;
                        $dados_transacao["message"]     = $statusMessage;
                        $dados_transacao["status"]      = 1;
                        $dados_transacao["url"]         = $redirect;
                        $dados_transacao["processado"]  = 0;
                        $dados_transacao["slug_status"] = 'aguardando_pagamento_debito';
                        break;
                    case 1:
                        //Transação autorizada
                        $dados_transacao["result"]      = "AUTORIZADA";
                        $dados_transacao["tid"]         = $TID;
                        $dados_transacao["message"]     = $statusMessage;
                        $dados_transacao["status"]      = 4;
                        $dados_transacao["processado"]  = 0;
                        $dados_transacao["slug_status"] = 'pagamento_confirmado';
                        break;
                    case 2:
                        //Transação capturada
                        $dados_transacao["result"]  = "OK";
                        $dados_transacao["tid"]     = $TID;
                        $dados_transacao["message"] = $statusMessage;
                        $dados_transacao["status"]  = 6;
                        //$dados_transacao["data_processado"] = date( $Response->{"Payment"}->{"CapturedDate"} );
                        $dados_transacao["processado"]  = 1;
                        $dados_transacao["slug_status"] = 'pagamento_confirmado';
                        break;
                    case 3:
                        //Transação negada
                        $dados_transacao["result"]      = "NEGADA";
                        $dados_transacao["tid"]         = $TID;
                        $dados_transacao["message"]     = $statusMessage;
                        $dados_transacao["status"]      = 5;
                        $dados_transacao["processado"]  = 1;
                        $dados_transacao["slug_status"] = 'pagamento_negado';
                        break;

                    case 10:
                        //Transação cancelada
                        $dados_transacao["result"]  = "CANCELADA";
                        $dados_transacao["message"] = $statusMessage;
                        $dados_transacao["status"]  = 9;
                        //$dados_transacao["data_processado"] = date( $Response->{"Payment"}->{"VoidedDate"} );
                        $dados_transacao["processado"]  = 1;
                        $dados_transacao["slug_status"] = 'cancelado';
                        break;
                    case 11:
                        //Transação reembolsada
                        $dados_transacao["result"]      = "REEMBOLSADA";
                        $dados_transacao["message"]     = $statusMessage;
                        $dados_transacao["status"]      = 9;
                        $dados_transacao["processado"]  = 1;
                        $dados_transacao["slug_status"] = 'cancelado_stornado';
                        break;
                    case 12:
                        //Transação pendente
                        $dados_transacao["result"]      = "PENDENTE";
                        $dados_transacao["message"]     = $statusMessage;
                        $dados_transacao["status"]      = 1;
                        $dados_transacao["processado"]  = 1;
                        $dados_transacao["slug_status"] = 'aguardando_pagamento';
                        break;
                    case 13:
                        //Transação abortada
                        $dados_transacao["result"]      = "ABORTADA";
                        $dados_transacao["message"]     = $statusMessage;
                        $dados_transacao["status"]      = 5;
                        $dados_transacao["processado"]  = 1;
                        $dados_transacao["slug_status"] = 'abortado';
                        break;
                    case 20:
                        //Transação agendada
                        $dados_transacao["result"]      = "AGENDADA";
                        $dados_transacao["message"]     = $statusMessage;
                        $dados_transacao["status"]      = 1;
                        $dados_transacao["processado"]  = 0;
                        $dados_transacao["slug_status"] = 'aguardando_liberacao';
                        break;
                }

            }
        } catch (Exception $e) {
            log_message('debug', 'ERRO CHAMADA WS ', print_r($e, true));
            $dados_transacao['result']      = 'ERRO';
            $dados_transacao['message']     = 'Erro Acessando modulo de pagamento';
            $dados_transacao['status']      = $e->getMessage();
            $dados_transacao["slug_status"] = 'erro';
        }

        //error_log( print_r( $dados_transacao, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

        //print_r($response);

        $erro = false;
        try {

            log_message('debug', 'RETORNO CHAMADA WS ');
            log_message('debug', print_r($Response, true));
            if ($dados_transacao['result'] == "OK") {
                //$dados_transacao['result'] = 'OK';
                //$dados_transacao['message'] = 'Transação Efetuada com Sucesso';
                //$dados_transacao['tid'] = $TID;
                //$dados_transacao['status'] = isset($response->Status) ? $response->Status : '';
                //$dados_transacao['url'] = isset($response->url) ? $response->url : '';

                if ($dados_transacao['status'] == 6) {

                    log_message('debug', 'PAGAMENTO EFETUADO ');
                    $this->fatura->pagamentoCompletoEfetuado($fatura_parcela['fatura_parcela_id']);
                    log_message('debug', 'ATUALIZA FATURA ');
                    $this->apolice->insertApolice($pedido['pedido_id']);
                    log_message('debug', 'INSERE APOLICE ');
                    $this->pedido_transacao->insStatus($pedido['pedido_id'], 'pagamento_confirmado', "$statusMessage [$TID]");
                    log_message('debug', 'INSERE STATUS PEDIDO OK ');

                    //Retorna pedido e cotação
                    $pedido  = $this->pedido->get($pedido['pedido_id']);
                    $cotacao = $this->cotacao->get($pedido['cotacao_id']);

                    //Verifica se a cotação é um upgrade
                    if ((int) $cotacao['cotacao_upgrade_id'] > 0) {
                        //Seta cancelada e realiza update
                        $cotacao_antiga = $this->cotacao->get($cotacao['cotacao_upgrade_id']);
                        $pedido_antigo  = $this->pedido->get_by(array('cotacao_id' => $cotacao['cotacao_upgrade_id']));

                        //Se achar
                        /* @todo Quando for upgrade rever */
                        if ($pedido_antigo) {
                            //Muda status para cancelado
                            $pedido_antigo['pedido_status_id'] = 5;

                            //Realiza update
                            $this->pedido->update($pedido_antigo['pedido_id'], $pedido_antigo);
                            $this->pedido_transacao->insStatus($pedido_antigo['pedido_id'], 'cancelado', "PEDIDO CANCELADO PARA UPGRADE");

                            $faturas_pedido_antigo = $this->fatura->get_many_by(array("pedido_id" => $pedido_antigo['pedido_id']));

                            foreach ($faturas_pedido_antigo as $fatura) {
                                unset($fatura['fatura_id']);
                                $fatura['pedido_id'] = $pedido['pedido_id'];

                                $this->fatura->insert($fatura);
                            }

                            $this->pedido->executa_extorno_upgrade($pedido_antigo['pedido_id']);

                        }
                    }
                } else {
                    $this->pedido_transacao->insStatus($pedido['pedido_id'], 'aguardando_liberacao', "Aguardando autorização da Operadora");
                    log_message('debug', 'INSERE STATUS PEDIDO - LIBERACAO PAGAMENTO ');

                    $erro = true;

                }

            } elseif ($dados_transacao['result'] == "REDIRECT") {

                //$dados_transacao['result'] = 'REDIRECT';
                //$dados_transacao['message'] = isset($response->message) ? $response->message : '';
                //$dados_transacao['tid'] = isset($response->TID) ? $response->TID : '';
                //$dados_transacao['status'] = isset($response->Status) ? $response->Status : '';
                //$dados_transacao['url'] = isset($response->url) ? $response->url : '';
                //$dados_transacao['processado'] = 0;

                $this->pedido_transacao->insStatus($pedido['pedido_id'], 'aguardando_pagamento_debito', "TRANSAÇÃO CRIADA COM SUCESSO. AGUARDANDO PAGAMENTO.");
                log_message('debug', ' INSERE STATUS DO PEDIDO DEBITO');

            } else {
                //log_message('error', 'pagmax: ' . print_r($response, true));

                $pedido  = $this->pedido->get($pedido['pedido_id']);
                $cotacao = $this->cotacao->get_cotacao_produto($pedido['cotacao_id']);

                $configuracao = $this->produto_parceiro_configuracao
                    ->filter_by_produto_parceiro($cotacao['produto_parceiro_id'])
                    ->get_all();

                $configuracao = $configuracao[0];

                log_message('debug', 'ERRO NO PAGAMENTO PAGAMENTO ');

                //$dados_transacao['result'] = isset($response->result) ? $response->result : '';
                //$dados_transacao['message'] = isset($response->message) ? $response->message : '';
                //$dados_transacao['status'] = (isset($response->Status)) ? $response->Status : '';

                $this->cartao->update($pedido_cartao_id, array('erros' => $cartao['erros'] + 1), true);

                //     if($configuracao['pagamento_tipo'] == 'RECORRENTE'){
                //         $this->pedido_transacao->insStatus($pedido['pedido_id'], 'cliente_inadimplente', "Transação não Efetuada");
                //         log_message('debug', ' INSERE STATUS DO PEDIDO NEGADO');
                //         log_message('debug', ' ENVIANDO EMAIL APOLICE NAO GERADA PEDIDO ID ' . $pedido['pedido_id']);
                //         //$this->apolice->disparaEventoErroApolice($pedido['pedido_id']);
                //         log_message('debug', ' ENVIANDO EMAIL APOLICE NAO GERADA PEDIDO ID ' . $pedido['pedido_id']);
                //         $erro = true;

                //    }else{
                $this->pedido_transacao->insStatus($pedido['pedido_id'], $dados_transacao["slug_status"], "Transação não Efetuada");
                log_message('debug', ' INSERE STATUS DO PEDIDO NEGADO');
                log_message('debug', ' ENVIANDO EMAIL APOLICE NAO GERADA PEDIDO ID ' . $pedido['pedido_id']);
                $this->apolice->disparaEventoErroApolice($pedido['pedido_id']);
                log_message('debug', ' ENVIANDO EMAIL APOLICE NAO GERADA PEDIDO ID ' . $pedido['pedido_id']);
                $erro = true;
                // }
            }

            //error_log( print_r( $dados_transacao, true ) . "\n", 3, "/var/log/httpd/360.log" );

            unset($dados_transacao["slug_status"]);

            if ($erro) {
                $this->apolice->disparaEventoErroApolice($pedido['pedido_id']);
            }

            $this->cartao->update($pedido_cartao_id, array('processado' => 1), true);
            log_message('debug', 'UPDATE NO CARTÃO ');
            $this->transacao->insert($dados_transacao, true);
            log_message('debug', 'UPDATE NA TRANSCAO ');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    public function consulta($pedido_id = 0)
    {

        $pedido_id = ($pedido_id == 0) ? $this->input->post('pedido_id') : $pedido_id;

        $this->load->model('pedido_cartao_model', 'cartao');

        $pedido = $this->pedido->with_pedido_status()->filter_by_pedido($pedido_id)->get_all();

        $result                      = array();
        $result['result']            = false;
        $result['status_pedido']     = '';
        $result['status_slug']       = '';
        $result['status_id']         = '';
        $result['class_pagamento']   = 'btn-danger';
        $result['transacao_result']  = '';
        $result['transacao_message'] = '';
        $result['transacao_tid']     = '';
        $result['transacao_url']     = '';

        if (count($pedido) > 0) {
            $pedido = $pedido[0];

            $class = "btn-info";
            if ($pedido['pedido_status_id'] == 3) {
                $class = 'btn-success';
            } elseif ($pedido['pedido_status_id'] == 4) {
                $class = 'btn-danger';
            }

            $status = $this->cartao->get_last_transacao($pedido_id);

            if ($status) {
                $status = $status[0];
            }

            $result['result']            = ($pedido['pedido_status_id'] == 3 || $pedido['pedido_status_id'] == 4) ? true : false;
            $result['status_pedido']     = $pedido['pedido_status_nome'];
            $result['status_slug']       = $pedido['pedido_status_slug'];
            $result['status_id']         = $pedido['pedido_status_id'];
            $result['class_pagamento']   = $class;
            $result['transacao_result']  = (isset($status['result'])) ? $status['result'] : '';
            $result['transacao_message'] = (isset($status['message'])) ? $status['message'] : '';
            $result['transacao_tid']     = (isset($status['tid'])) ? $status['tid'] : '';
            $result['transacao_url']     = (isset($status['url'])) ? $status['url'] : '';

        } else {
            $result['mensagem'] = "PEDIDO NÃO ENCONTRADO";
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function pedido($cotacao_id = 0)
    {

        $cotacao_id = ($cotacao_id == 0) ? $this->input->post('cotacao_id') : $cotacao_id;

        $this->load->model('pedido_model', 'cartao');

        $pedido = $this->pedido->get_many_by(array("pedido.cotacao_id" => $cotacao_id));

        $result                     = array();
        $result["result"]           = false;
        $result["pedido_id"]        = "";
        $result["pedido_status_id"] = "";
        $result["mensagem"]         = "";

        if (count($pedido) > 0) {
            $pedido                     = $pedido[0];
            $result["result"]           = true;
            $result["pedido_status_id"] = $pedido["pedido_status_id"];
            $result["pedido_id"]        = $pedido["pedido_id"];

        } else {
            $result["mensagem"] = "PEDIDO NÃO ENCONTRADO";
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function pagmax_efetuar_estorno($pedido_id = 0)
    {

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

            $dados_transacao                    = array();
            $dados_transacao['estorno_data']    = date('Y-m-d H:i:s');
            $dados_transacao['estorno_status']  = '';
            $dados_transacao['estorno_result']  = '';
            $dados_transacao['estorno_message'] = '';

            $usesandbox = ($integracao['producao'] == 1) ? false : true;
            $tid        = $pedido['tid'];

            //Monta array com dados
            $param = array(
                'TID'                    => $tid,
                'MerchantAcquirerHashID' => ($integracao['producao'] == 1) ? $integracao['chave_acesso'] : $integracao['chave_acesso_homolog'],
                'UseSandbox'             => $usesandbox,
            );

            try {
                $client = new nusoap_client($integracao['url']);
                $client->setUseCurl(true);
                $response                          = $client->call('TIDvoid', $param);
                $response                          = json_decode($response);
                $dados_transacao['estorno_result'] = $response->result;
            } catch (Exception $e) {
                $dados_transacao['estorno_result']  = 'ERRO';
                $dados_transacao['estorno_message'] = 'Erro Acessando modulo de pagamento';
                $dados_transacao['estorno_status']  = $e->getMessage();

            }

            $erro = false;
            try {

                if ($dados_transacao['estorno_result'] == "ESTORNADO") {
                    $dados_transacao['estorno_message'] = 'Estorno efetuado com Sucesso';
                    $dados_transacao['estorno_status']  = isset($response->Status) ? $response->Status : '';

                    if ($dados_transacao['estorno_status'] == 9) {

                        $this->pedido_transacao->insStatus($pedido['pedido_id'], 'cancelado_stornado', "Pedido estornado com sucesso [{$response->TID}]");

                        //muda status das faturas
                        $this->fatura->estornoCompletoEfetuado($pedido['pedido_id']);

                    }

                } else {
                    $dados_transacao['estorno_message'] = isset($response->message) ? $response->message : '';
                    $dados_transacao['estorno_status']  = isset($response->Status) ? $response->Status : '';

                }

                $this->pedido_cartao_transacao->update($pedido['pedido_cartao_transacao_id'], $dados_transacao, true);

            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

        }
    }

}
