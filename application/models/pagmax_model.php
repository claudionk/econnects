<?php
class Pagmax_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'produto_parceiro_pagamento';
    protected $primary_key = 'produto_parceiro_pagamento_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome_fatura');

    public function __construct()
    {
        parent::__construct();

        //Carrega modelos
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('produto_parceiro_pagamento_model', 'parceiro_pagamento');
        $this->load->model('forma_pagamento_integracao_model', 'integracao');
        $this->load->model('fatura_model', 'fatura');
        $this->load->model('pedido_transacao_model', 'pedido_transacao');
        $this->load->model('apolice_model', 'apolice');
    }

    public function realiza_pagamento($pedido_id, $forma_pagamento_tipo_id)
    {

        log_message('debug', 'INICIO EFETUAR PAGMAX ' . $pedido_id);
        $this->load->model('recorrencia_model', 'recorrencia');
        $this->load->model('fatura_parcela_model', 'fatura_parcela');
        $this->load->library("Nusoap_lib");
        $this->load->library('encrypt');

        log_message('debug', 'BUSCA PEDIDO ' . $pedido_id);

        try {
            $pedido = $this->pedido->get($pedido_id);

            $parceiro_pagamento = $this->parceiro_pagamento
                ->with_forma_pagamento()
                ->with_forma_pagamento_tipo()
                ->get($pedido['produto_parceiro_pagamento_id']);

            log_message('debug', 'PARCEIRO PAGAMENTO');
            log_message('debug', print_r($parceiro_pagamento, true));
            log_message('debug', 'BUSCA INTEGRAÇÃO ');

            $integracao = $this->integracao->get_by_slug('pagmax');
            log_message('debug', 'BUSCANDO INTEGRAÇÃO - ' . count($integracao));

            //Busca a fatura
            $fatura = $this->fatura->get_many_by(array('pedido_id' => $pedido_id));
            $fatura         = $fatura[0];
            $faturas_parcelas = $this->fatura_parcela
                ->limit(1)
                ->order_by('num_parcela')
                ->get_many_by(array(
                    'fatura_id'          => $fatura['fatura_id'],
                    'fatura_status_id'   => 1,
                    'data_vencimento <=' => date('Y-m-d'),
                ));

            // sempre envia para o gatway apenas 1 parcela
            $fatura_parcela = $faturas_parcelas[0];

            $dados_transacao = array();
            $dados_transacao['processado']       = 1;
            $dados_transacao['result']           = '';
            $dados_transacao['message']          = '';
            $dados_transacao['tid']              = '';
            $dados_transacao['status']           = '';

            $merchantOrderId = rand(100, 999) . (int) $fatura_parcela["fatura_parcela_id"];
            $valor_parcela = $fatura_parcela["valor"];
            $num_parcela   = $pedido["num_parcela"];
            $valor_total   = ($num_parcela > 1) ? ($valor_parcela * $num_parcela) : $pedido["valor_total"];

            //Valor total do carrinho
            if ($this->session->userdata("pedido_carrinho_valor_total")) {
                $valor_total = $this->session->userdata("pedido_carrinho_valor_total");
            }
            $transactionAmount = ($integracao['producao'] == 1) ? (float) $valor_total : 150.00;
            $transactionAmount = (true == true) ? (float) $valor_total : 150.00;



            $usesandbox = ($integracao['producao'] == 1) ? false : true;
            $isdebit    = ($parceiro_pagamento['forma_pagamento_id'] == $this->config->item("FORMA_PAGAMENTO_CARTAO_DEBITO")) ? true : false;
            $return_url = ($parceiro_pagamento['forma_pagamento_id'] == $this->config->item("FORMA_PAGAMENTO_CARTAO_DEBITO")) ? $this->config->item('base_url') . "/index.php/pagmax360/retorno?pedido_id={$pedido_id}" : "";

            switch ($parceiro_pagamento['forma_pagamento_id']) {
                case $this->config->item("FORMA_PAGAMENTO_CARTAO_CREDITO"):
                case $this->config->item("FORMA_PAGAMENTO_CARTAO_DEBITO"):

                    $this->load->model('pedido_cartao_model', 'cartao');
                    $this->load->model('pedido_cartao_transacao_model', 'transacao');

                    log_message('debug', 'BUSCANDO CARTAO');
                    $cartao = $this->cartao->filter_by_pedido($pedido_id)->get_all(1, 0);

                    if (empty($cartao)) {
                        return [
                            'status' => false,
                            'message' => 'Nenhuma Transação Pendente'
                        ];
                    }

                    $reg = $cartao = $cartao[0];
                    $pedido_cartao_id = $cartao['pedido_cartao_id'];
                    log_message('debug', 'CARTAO ENCONTRADO ' . $pedido_cartao_id);

                    //verifica a quantidade de transações que deram erro
                    if ((intval($integracao['qnt_erros']) != 0 && $cartao['erros'] >= $integracao['qnt_erros']) || $cartao['erros'] > 5) {
                        $model->update($pedido_cartao_id, array('ativo' => 0), true);
                        $this->pedido_transacao->insStatus($pedido_id, 'pagamento_negado', "Transação não Efetuada");
                        log_message('debug', 'NEGANDO TRANSACAO ');

                        return [
                            'status' => true,
                            'message' => 'Transação não Efetuada'
                        ];
                    }

                    log_message('debug', 'EFETUAR PAGAMENTO PAGMAX ' . $pedido_cartao_id);

                    $dados_transacao['pedido_cartao_id'] = $pedido_cartao_id;

                    $validade       = $this->encrypt->decode($cartao['validade']);
                    $numero         = $this->encrypt->decode($cartao['numero']);
                    $bandeira       = $this->encrypt->decode($cartao['bandeira_cartao']);
                    $nome           = $this->encrypt->decode($cartao['nome']);
                    $codigo         = $this->encrypt->decode($cartao['codigo']);
                    $dia_vencimento = $this->encrypt->decode($cartao['dia_vencimento']);

                    if ($isdebit) {
                        $transactionType = "DebitCard";
                    } else {
                        $transactionType = "CreditCard";
                    }

                    $numInstallments    = $num_parcela;
                    $softDescriptor     = $parceiro_pagamento["nome_fatura"];
                    $returnUrl          = $return_url;
                    $cardNumber         = $numero;
                    $cardHolder         = $nome;
                    $cardExpirationDate = substr($validade, 4, 2) . "/" . substr($validade, 0, 4);
                    $cardSecurityCode   = $codigo;
                    $cardBrand          = $bandeira;

                    $JsonDataRequest = array(
                        "MerchantOrderId" => $merchantOrderId,
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

                    break;

                case $this->config->item("FORMA_PAGAMENTO_BOLETO"):

                    $this->load->model('pedido_boleto_model', 'boleto');

                    log_message('debug', 'BUSCANDO BOLETO');
                    $boleto = $this->boleto->filter_by_pedido($pedido_id)->get_all(1, 0);

                    if (empty($boleto)) {
                        return [
                            'status' => false,
                            'message' => 'Nenhuma Transação Pendente'
                        ];
                    }

                    $reg = $boleto = $boleto[0];
                    log_message('debug', 'BOLETO ENCONTRADO ' . $boleto['pedido_cartao_id']);

                    //verifica a quantidade de transações que deram erro
                    if ((intval($integracao['qnt_erros']) != 0 && $boleto['erros'] >= $integracao['qnt_erros']) || $boleto['erros'] > 5) {
                        $model->update($boleto['pedido_boleto_id'], array('ativo' => 0), true);
                        $this->pedido_transacao->insStatus($pedido_id, 'pagamento_negado', "Transação não Efetuada");
                        log_message('debug', 'NEGANDO TRANSACAO ');

                        return [
                            'status' => true,
                            'message' => 'Transação não Efetuada'
                        ];
                    }

                    $JsonDataRequest = array(
                        array(
                            "MerchantOrderId" => $merchantOrderId,
                            "Customer" => array(
                                "Name" => $boleto["sacado_nome"],
                                "Identity" => $boleto["sacado_documento"],
                                "Address" => array(
                                    "Street"     => $boleto["sacado_endereco"],
                                    "Number"     => $boleto["sacado_endereco_num"],
                                    "Complement" => $boleto["sacado_endereco_comp"],
                                    "ZipCode"    => $boleto["sacado_endereco_cep"],
                                    "District"   => $boleto["sacado_endereco_bairro"],
                                    "City"       => $boleto["sacado_endereco_cidade"],
                                    "State"      => $boleto["sacado_endereco_uf"],
                                ),
                            ),
                            "Payment"  => array(
                                "Type"           => "EletronicTransfer",
                                "Provider"       => "Simulado",
                                "Identification" => "08.267.567/0001-30",
                                "Amount"         => $transactionAmount,
                                "ReturnUrl"      => $returnUrl,
                                "BoletoNumber"   => $boleto["nosso_numero"],
                                "ExpirationDate" => $boleto["vencimento"],
                                "Instructions"   => $boleto["instrucoes"],
                                "Address"        => "Alameda Rio Negro, 500 - 6° andar - Alphaville - Barueri - São Paulo - CEP 06454-000",
                            ),
                        ),
                    );

                    break;

                case $this->config->item("FORMA_PAGAMENTO_CHECKOUT_PAGMAX"):

                    $softDescriptor = $parceiro_pagamento["nome_fatura"];
                    $JsonDataRequest = array(
                        "Transaction" => array(
                            "MerchantOrderID" => $merchantOrderId,
                            "MerchantSoftDescriptor" => $softDescriptor,
                        ),
                        "Sale" => array(
                            "Amount" => $transactionAmount,
                        )
                    );

                    break;

                default:
                    $reg = null;
                    break;
            }

            $Json = json_encode($JsonDataRequest);

            $result = $this->executa_pagamento($Json, ['pedido_id' => $pedido_id, 'fatura_parcela_id' => $fatura_parcela['fatura_parcela_id'], 'pedido_cartao_id' => $pedido_cartao_id], $dados_transacao, $reg);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        unset($_SESSION[null]['pedido_carrinho_valor_total']);

        return $result;
    }


    /**
     * Faz a comunicação com a API de pagamento
     * @param $pedido_cartao_id
     * @throws Exception
     */
    public function executa_pagamento($Json, $dados, $dados_transacao, $cartao = null)
    {
        $this->load->library("Pagmax360");
        $Pagmax360 = new Pagmax360();

        $Pagmax360->merchantId  = $this->config->item("Pagmax360_merchantId");
        $Pagmax360->merchantKey = $this->config->item("Pagmax360_merchantKey");
        $Pagmax360->Environment = $this->config->item("Pagmax360_Environment");

        $result = [
            'status' => true,
            'message' => 'Transação Efetuada com Sucesso'
        ];

        $pedido_id = $dados['pedido_id'];
        $fatura_parcela_id = $dados['fatura_parcela_id'];
        $pedido_cartao_id = $dados['pedido_cartao_id'];

        try {
            log_message('debug', 'FAZ CHAMADA WS ', print_r($Json, true));

            // Pagmax 360 v2
            $Response = $Pagmax360->createTransaction($Pagmax360->merchantId, $Pagmax360->merchantKey, $Json, $Pagmax360->Environment, $pedido_id);
            $Response = json_decode($Response);
            error_log(print_r($Response, true) . "\n", 3, "/var/log/httpd/myapp.log");
            if (isset($Response->{"Code"}) || sizeof($Response) == 0 || (isset($Response->{"status"}) && empty($Response->{"status"}))) {
                $tipo_mensagem = "msg_erro";

                if (isset($Response["error"]) && isset($Response["error"]["Code"])) {
                    $msgErro = issetor($Response["error"]["Message"], "Falha na transacao") . " (Erro " . $Response["error"]["Code"] . ")";
                } else {
                    if (isset($Response[0]["Code"]) && isset($Response[0]["Message"])) {
                        $msgErro = $Response[0]["Message"] . " (Code " . $Response[0]["Code"] . ")";
                    } elseif (isset($Response->{"status"})) {
                        $msgErro = $Response->{"message"};
                    } else {
                        $msgErro = "Falha de comunicação (Erro 0)";
                    }
                }

                $dados_transacao["result"]      = "ERRO";
                $dados_transacao["message"]     = $msgErro;
                $dados_transacao["slug_status"] = 'erro';

                $result = [
                    'status' => false,
                    'message' => $msgErro,
                ];
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
                        "pedido_id" => $pedido_id, "Capture" => $Response->{"Payment"}->{"Capture"}, "Tid" => $Response->{"Payment"}->{"Tid"}, "ProofOfSale" => $Response->{"Payment"}->{"ProofOfSale"}, "AuthorizationCode" => $Response->{"Payment"}->{"AuthorizationCode"}, "ReceivedDate" => $Response->{"Payment"}->{"ReceivedDate"}, "CapturedDate" => $Response->{"Payment"}->{"CapturedDate"}, "PaymentId" => $Response->{"Payment"}->{"PaymentId"}, "RecurrentPaymentId" => $Response->{"Payment"}->{"RecurrentPayment"}->{"RecurrentPaymentId"},
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

                        $result['url'] = $redirect;

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

            $result = [
                'status' => false,
                'message' => 'Erro Acessando modulo de pagamento'
            ];
        }

        $erro = false;
        try {

            $pedido  = $this->pedido->get($pedido_id);

            log_message('debug', 'RETORNO CHAMADA WS ');
            log_message('debug', print_r($Response, true));
            if ($dados_transacao['result'] == "OK") {

                if ($dados_transacao['status'] == 6) {

                    $this->load->model('cotacao_model', 'cotacao');
                    log_message('debug', 'PAGAMENTO EFETUADO ');
                    $this->fatura->pagamentoCompletoEfetuado($fatura_parcela_id);
                    log_message('debug', 'ATUALIZA FATURA ');
                    $this->apolice->insertApolice($pedido['pedido_id']);
                    log_message('debug', 'INSERE APOLICE ');
                    $this->pedido_transacao->insStatus($pedido['pedido_id'], 'pagamento_confirmado', "$statusMessage [$TID]");
                    log_message('debug', 'INSERE STATUS PEDIDO OK ');

                    //Retorna pedido e cotação
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
                    $result = [
                        'status' => false,
                        'message' => 'Aguardando autorização da Operadora'
                    ];
                }
            } elseif ($dados_transacao['result'] == "REDIRECT") {

                $this->pedido_transacao->insStatus($pedido['pedido_id'], 'aguardando_pagamento_debito', "TRANSAÇÃO CRIADA COM SUCESSO. AGUARDANDO PAGAMENTO.");
                log_message('debug', ' INSERE STATUS DO PEDIDO DEBITO');
            } else {

                $cotacao = $this->cotacao->get_cotacao_produto($pedido['cotacao_id']);

                if ($cartao) {
                    log_message('debug', 'ERRO NO PAGAMENTO PAGAMENTO ');
                    $this->cartao->update($pedido_cartao_id, array('erros' => $cartao['erros'] + 1), true);
                }

                $this->pedido_transacao->insStatus($pedido['pedido_id'], $dados_transacao["slug_status"], "Transação não Efetuada");
                log_message('debug', ' INSERE STATUS DO PEDIDO NEGADO');

                $erro = true;
                $result = [
                    'status' => false,
                    'message' => 'Transação não Efetuada'
                ];
            }

            unset($dados_transacao["slug_status"]);

            if ($erro) {
                log_message('debug', ' ENVIANDO EMAIL APOLICE NAO GERADA PEDIDO ID ' . $pedido['pedido_id']);
                $this->apolice->disparaEventoErroApolice($pedido['pedido_id']);
            }

            if ($cartao) {

                $this->cartao->update($pedido_cartao_id, array('processado' => 1), true);
                log_message('debug', 'UPDATE NO CARTÃO ');

                $this->transacao->insert($dados_transacao, true);
                log_message('debug', 'UPDATE NA TRANSCAO ');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $result['response'] = $Response;
        return $result;
    }
}
