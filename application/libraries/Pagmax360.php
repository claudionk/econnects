<?php

//
// Classe:                 Pagmax360
// Autor:                Luis Schedel
// Descrição:            Classe para implementar as funcionalidades da API Pagmax 360
// Data de criação:        25/09/2017
//
class Pagmax360
{

    public $CI;
    public $merchantId;
    public $merchantKey;
    public $Environment;
    private $UrlPagmax = "https://gw.pagmax.com.br";

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function __get($key)
    {
        return $this->$key;
    }

    //
    // Método:             getCardToken()
    // Autor:                Luis Schedel
    // Descrição:            Função para "Tokenização" de um determinado Cartão de Crédito. O CardToken pode ser salvo no banco de dados.
    //                    Utiliza o método HTTP POST para acessar a API Pagmax 360.
    // Data de criação:    25/09/2017
    //
    // Parâmetros de Entrada:
    //
    // $merchantId    ->     Código do estabelecimento Pagmax
    // $merchantKey    ->     Chave de acesso do estabelecimento na API Pagmax 360
    // $Json            ->     JSON com as informações necessárias para obter o token
    // $Env            ->     String definindo o ambiente de execução do método: Utilizar 360 para produção ou teste para desenvolvimento e homologação
    //
    //
    // Retorno:
    //
    // String JSON com o retorno da chamada
    //
    // Em caso de falha, essa função retorna um JSON com o Código (Code) e a Mensagem de erro (Message).
    // Em caso de sucesso, o retorno é um JSON somente com o elemento CardToken
    //
    // Exemplo de chamada: $JsonString = $Pagmax360->getCardToken( $merchantId, $merchantKey, $Json, {['360|teste']} );
    //
    // Mais informações sobre a estrutura do JSON estão disponíveis em https://gerenciador.pagmax.com.br/docs/360.php
    //
    public function getCardToken($merchantId, $merchantKey, $Json, $Env = "teste", $pedido_id = 0)
    {
        $Url    = "{$this->UrlPagmax}api/$Env/card";
        $myCurl = curl_init();
        curl_setopt($myCurl, CURLOPT_URL, $Url);
        curl_setopt($myCurl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($myCurl, CURLOPT_POST, 1);
        curl_setopt($myCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($myCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "merchantId: $merchantId", "merchantKey: $merchantKey"));
        curl_setopt($myCurl, CURLOPT_POSTFIELDS, $Json);
        curl_setopt($myCurl, CURLOPT_TIMEOUT, 15);
        curl_setopt($myCurl, CURLOPT_CONNECTTIMEOUT, 15);
        $Response = curl_exec($myCurl);
        curl_close($myCurl);

        // Loga as informações em arquivo texto
        $agora = new DateTime();
        error_log($agora->format("Y-m-d H:i:s") . " - POST - $Url\n", 3, "/var/log/httpd/360.log");

        // Loga as informações no banco de dados
        $JsonObject = json_decode($Json);
        $logPagmax  = $this->CI->db->query("call sp_loga_transacao_pagmax( $merchantId, '$Url', '$Json', '$Response', '$pedido_id' )");

        return $Response;
    }

    //
    // Método:             createTransaction()
    // Autor:                Luis Schedel
    // Descrição:            Função para criar uma transação utilizando Cartão de Crédito, Cartão de Débito, Boleto ou Transferência Eletrônica.
    //                    Utiliza o método HTTP POST para acessar a API Pagmax 360.
    // Data de criação:    25/09/2017
    //
    // Parâmetros de Entrada:
    //
    // $merchantId    ->     Código do estabelecimento Pagmax
    // $merchantKey    ->     Chave de acesso do estabelecimento na API Pagmax 360
    // $Json            ->     JSON com as informações necessárias para obter o token
    // $Env            ->     String definindo o ambiente de execução do método: Utilizar 360 para produção ou teste para desenvolvimento e homologação
    //
    //
    // Retorno:
    //
    // String JSON com o retorno da chamada
    //
    // Em caso de falha, essa função retorna um JSON com o Código (Code) e a Mensagem de erro (Message).
    // Em caso de sucesso, o retorno é um JSON com as informações da transação.
    //
    // Exemplo de chamada: $JsonString = $Pagmax360->createTransaction( $merchantId, $merchantKey, $Json, {['360|teste']} );
    //
    // Mais informações sobre a estrutura do JSON estão disponíveis em https://gerenciador.pagmax.com.br/docs/360.php
    //
    public function createTransaction($merchantId, $merchantKey, $Json, $Env = "teste", $pedido_id = 0)
    {
        $Url    = "{$this->UrlPagmax}api/$Env/transaction";
        $Url    = "{$this->UrlPagmax}/v3/api/transaction";
        $myCurl = curl_init();
        curl_setopt($myCurl, CURLOPT_URL, $Url);
        curl_setopt($myCurl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($myCurl, CURLOPT_POST, 1);
        curl_setopt($myCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($myCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "merchantId: $merchantId", "merchantKey: $merchantKey"));
        curl_setopt($myCurl, CURLOPT_POSTFIELDS, $Json);
        curl_setopt($myCurl, CURLOPT_TIMEOUT, 15);
        curl_setopt($myCurl, CURLOPT_CONNECTTIMEOUT, 15);
        $Response = curl_exec($myCurl);
        curl_close($myCurl);

        // Loga as informações em arquivo texto
        $agora = new DateTime();
        error_log($agora->format("Y-m-d H:i:s") . " - POST - $Url\n", 3, "/var/log/httpd/360.log");

        // Loga as informações no banco de dados
        $JsonObject = json_decode($Json);
        $logPagmax  = $this->CI->db->query("call sp_loga_transacao_pagmax( $merchantId, '$Url', NULL, '$Response', '$pedido_id' )");

        return $Response;
    }

    //
    // Método:             voidTransaction()
    // Autor:                Luis Schedel
    // Descrição:            Função para cancelar (estornar) uma transação que já tenha sido autorizada ou capturada.
    //                    Utiliza o método HTTP PUT para acessar a API Pagmax 360.
    // Data de criação:    25/09/2017
    //
    // Parâmetros de Entrada:
    //
    // $merchantId    ->     Código do estabelecimento Pagmax
    // $merchantKey    ->     Chave de acesso do estabelecimento na API Pagmax 360
    // $paymentId        ->     ID do Pagamento a ser estornado, obtido na criação da transação (método criarTransação).
    // $Env            ->     String definindo o ambiente de execução do método: Utilizar 360 para produção ou teste para desenvolvimento e homologação
    //
    //
    // Retorno:
    //
    // String JSON com o retorno da chamada
    //
    // Em caso de falha, essa função retorna um JSON com o Código (Code) e a Mensagem de erro (Message).
    // Em caso de sucesso, o retorno é um JSON com as informações da transação.
    //
    // Exemplo de chamada: $JsonString = $Pagmax360->voidTransaction( $merchantId, $merchantKey, $paymentId, {['360|teste']} );
    //
    // Mais informações sobre a estrutura do JSON estão disponíveis em https://gerenciador.pagmax.com.br/docs/360.php
    //
    public function voidTransaction($merchantId, $merchantKey, $PaymentID, $Env = "teste", $pedido_id = 0)
    {
        $Url    = "{$this->UrlPagmax}api/$Env/transaction/$PaymentID/void";
        $Url    = "{$this->UrlPagmax}/v3/api/transaction/$PaymentID/void";
        $myCurl = curl_init();
        curl_setopt($myCurl, CURLOPT_URL, $Url);
        curl_setopt($myCurl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($myCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "merchantId: $merchantId", "merchantKey: $merchantKey", "Content-Length: 0"));
        curl_setopt($myCurl, CURLOPT_TIMEOUT, 15);
        curl_setopt($myCurl, CURLOPT_CONNECTTIMEOUT, 15);
        $Response = curl_exec($myCurl);
        curl_close($myCurl);

        // Loga as informações em arquivo texto
        $agora = new DateTime();
        error_log($agora->format("Y-m-d H:i:s") . " - PUT - $Url\n", 3, "/var/log/httpd/360.log");

        // Loga as informações no banco de dados
        $JsonObject = json_decode($Response);
        $logPagmax  = $this->CI->db->query("call sp_loga_transacao_pagmax( $merchantId, '$Url', NULL, '$Response', '$pedido_id' )");

        return $Response;
    }

    //
    // Método:             captureTransaction()
    // Autor:                Luis Schedel
    // Descrição:            Função para capturar uma transação que já tenha sido autorizada ou capturada. Utiliza o método HTTP PUT para acessar a API Pagmax 360.
    // Data de criação:    25/09/2017
    //
    // Parâmetros de Entrada:
    //
    // $merchantId    ->     Código do estabelecimento Pagmax
    // $merchantKey    ->     Chave de acesso do estabelecimento na API Pagmax 360
    // $paymentId        ->     ID do Pagamento a ser capturado, obtido na criação da transação (método criarTransação).
    // $Env            ->     String definindo o ambiente de execução do método: Utilizar 360 para produção ou teste para desenvolvimento e homologação
    //
    //
    // Retorno:
    //
    // String JSON com o retorno da chamada
    //
    // Em caso de falha, essa função retorna um JSON com o Código (Code) e a Mensagem de erro (Message).
    // Em caso de sucesso, o retorno é um JSON com as informações da transação.
    //
    // Exemplo de chamada: $JsonString = $Pagmax360->captureTransaction( $merchantId, $merchantKey, $paymentId, {['360|teste']} );
    //
    // Mais informações sobre a estrutura do JSON estão disponíveis em https://gerenciador.pagmax.com.br/docs/360.php
    //
    public function captureTransaction($merchantId, $merchantKey, $PaymentID, $Env = "teste", $pedido_id = 0)
    {
        $Url    = "{$this->UrlPagmax}api/$Env/transaction/$PaymentID/capture";
        $Url    = "{$this->UrlPagmax}/v3/api/transaction/$PaymentID/capture";
        $myCurl = curl_init();
        curl_setopt($myCurl, CURLOPT_URL, $Url);
        curl_setopt($myCurl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($myCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "merchantId: $merchantId", "merchantKey: $merchantKey", "Content-Length: 0"));
        curl_setopt($myCurl, CURLOPT_TIMEOUT, 15);
        curl_setopt($myCurl, CURLOPT_CONNECTTIMEOUT, 15);
        $Response = curl_exec($myCurl);
        curl_close($myCurl);

        // Loga as informações em arquivo texto
        $agora = new DateTime();
        error_log($agora->format("Y-m-d H:i:s") . " - PUT - $Url\n", 3, "/var/log/httpd/360.log");

        // Loga as informações no banco de dados
        $JsonObject = json_decode($Response);
        $logPagmax  = $this->CI->db->query("call sp_loga_transacao_pagmax( $merchantId, '$Url', NULL, '$Response', '$pedido_id' )");

        return $Response;
    }

    //
    // Método:             getTransaction()
    // Autor:                Luis Schedel
    // Descrição:            Função para obter detalhes sobre uma transação. Deve ser chamada após os métodos de criação, captura e cancelamento
    //                    para verificar a alteração do status.  Utiliza o método HTTP GET para acessar a API Pagmax 360.
    // Data de criação:    25/09/2017
    //
    // Parâmetros de Entrada:
    //
    // $merchantId    ->     Código do estabelecimento Pagmax
    // $merchantKey    ->     Chave de acesso do estabelecimento na API Pagmax 360
    // $paymentId        ->     ID do Pagamento a ser capturado, obtido na criação da transação (método criarTransação).
    // $Env            ->     String definindo o ambiente de execução do método: Utilizar 360 para produção ou teste para desenvolvimento e homologação
    //
    //
    // Retorno:
    //
    // String JSON com o retorno da chamada
    //
    // Em caso de falha, essa função retorna um JSON com o Código (Code) e a Mensagem de erro (Message).
    // Em caso de sucesso, o retorno é um JSON somente com o elemento CardToken
    //
    // Exemplo de chamada: $JsonString = $Pagmax360->getTransaction( $merchantId, $merchantKey, $paymentId, {['360|teste']} );
    //
    // Mais informações sobre a estrutura do JSON estão disponíveis em https://gerenciador.pagmax.com.br/docs/360.php
    //
    public function getTransaction($merchantId, $merchantKey, $PaymentID, $Env = "teste", $pedido_id = 0)
    {
        $Url    = "{$this->UrlPagmax}api/$Env/transaction/$PaymentID";
        $Url    = "{$this->UrlPagmax}/v3/api/transaction/$PaymentID";
        $myCurl = curl_init();
        curl_setopt($myCurl, CURLOPT_URL, $Url);
        curl_setopt($myCurl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($myCurl, CURLOPT_POST, 0);
        curl_setopt($myCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($myCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "merchantId: $merchantId", "merchantKey: $merchantKey"));
        curl_setopt($myCurl, CURLOPT_TIMEOUT, 15);
        curl_setopt($myCurl, CURLOPT_CONNECTTIMEOUT, 15);
        $Response = curl_exec($myCurl);
        curl_close($myCurl);

        // Loga as informações em arquivo texto
        $agora = new DateTime();
        error_log($agora->format("Y-m-d H:i:s") . " - GET - $Url\n", 3, "/var/log/httpd/360.log");

        // Loga as informações no banco de dados
        $JsonObject = json_decode($Response);
        $logPagmax  = $this->CI->db->query("call sp_loga_transacao_pagmax( $merchantId, '$Url', NULL, '$Response', '$pedido_id' )");

        return $Response;
    }

    //
    // Método:             getTransactionByMerchantOrderId()
    // Autor:                Luis Schedel
    // Descrição:            Função para obter detalhes sobre uma transação, passando como parâmetro o MerchantOrderId (sis_exp_franquia_cartao.order_num).
    //                    Deve ser chamada após os métodos de criação, captura e cancelamento, somente se o retorno de uma delas for vazio (nulo). Utiliza o método HTTP GET para acessar a API Pagmax 360.
    // Data de criação:    22/01/2018
    //
    // Parâmetros de Entrada:
    //
    // $merchantId        ->     Código do estabelecimento Pagmax
    // $merchantKey        ->     Chave de acesso do estabelecimento na API Pagmax 360
    // $MerchantOrderId    ->     ID do pedido (sis_exp_franquia_cartao.order_num).
    // $Env                ->     String definindo o ambiente de execução do método: Utilizar 360 para produção ou teste para desenvolvimento e homologação
    //
    //
    // Retorno:
    //
    // String JSON com o retorno da chamada
    //
    public function getTransactionByMerchantOrderId($merchantId, $merchantKey, $MerchantOrderId, $Env = "teste", $pedido_id = 0)
    {
        $Url    = "{$this->UrlPagmax}api/$Env/MerchantOrderId/$MerchantOrderId";
        $Url    = "{$this->UrlPagmax}/v3/api/MerchantOrder/$MerchantOrderId";
        $myCurl = curl_init();
        curl_setopt($myCurl, CURLOPT_URL, $Url);
        curl_setopt($myCurl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($myCurl, CURLOPT_POST, 0);
        curl_setopt($myCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($myCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "merchantId: $merchantId", "merchantKey: $merchantKey"));
        curl_setopt($myCurl, CURLOPT_TIMEOUT, 15);
        curl_setopt($myCurl, CURLOPT_CONNECTTIMEOUT, 15);
        $Response = curl_exec($myCurl);
        curl_close($myCurl);

        // Loga as informações em arquivo texto
        $agora = new DateTime();
        error_log($agora->format("Y-m-d H:i:s") . " - GET - $Url\n", 3, "/var/log/httpd/360.log");

        // Loga as informações no banco de dados
        $JsonObject = json_decode($Response);
        $logPagmax  = $this->CI->db->query("call sp_loga_transacao_pagmax( $merchantId, '$Url', NULL, '$Response', '$pedido_id' )");

        return $Response;
    }

    public function returnTransactionStatusCode($statusCode)
    {
        $statusCodes = array(
            array("Code" => "0", "Message" => "EM_ANDAMENTO"),
            array("Code" => "1", "Message" => "AUTORIZADA"),
            array("Code" => "2", "Message" => "CAPTURADA"),
            array("Code" => "3", "Message" => "NEGADA"),
            array("Code" => "10", "Message" => "CANCELADA"),
            array("Code" => "11", "Message" => "REEMBOLSADA"),
            array("Code" => "12", "Message" => "PENDENTE"),
            array("Code" => "13", "Message" => "ABORTADA"),
            array("Code" => "20", "Message" => "AGENDADA"),
        );
        $key = array_search((string) $statusCode, array_column($statusCodes, "Code"));
        return $statusCodes[$key];
    }

}
