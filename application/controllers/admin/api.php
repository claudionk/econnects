<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Api extends Site_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->url = "http://econnects-h.jelastic.saveincloud.net/api/";
        // $this->url = "http://localhost/econnects/api/";
        $this->stop = false;
    }
    
    private function execute($url, $method = 'GET', $fields = []){
        $retorno = soap_curl([
            'url' => $url,
            'method' => $method,
            'fields' => $fields,
            'header' => [
                "accept: application/json",
                "Content-Type: application/json",
                "APIKEY: ". app_get_token()
            ]
        ]);

        if ($this->stop){
            echo "metodo: $method<br>";
            echo "<pre>";print_r($fields);echo "</pre>";
            echo "<pre>";print_r($retorno);echo "</pre>";
        }

        if (empty($retorno)) return;
        if (!empty($retorno["error"])) return;
        if (empty($retorno["response"])) return;
        $retornoJson = json_decode($retorno["response"]);
        if ($this->stop){
            print_r($retornoJson);
        }

        if (isset($retornoJson->status) && empty($retornoJson->status)) {
            header('X-Error-Message: '. $retornoJson->message, true, 500);
            $retorno["response"] = $retornoJson->message;
        }

        return $retorno["response"];
    }

    public function equipamento($ean){
        $retorno = $this->execute($this->url."equipamento?ean=". $ean);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function email($email){
        $email = urldecode($email);
        $result = false;
        $retorno = $this->execute($this->url."info/email?email=". $email ."&remetente=ti@sissolucoes.com.br");

        if (!empty($retorno)) {
            $response = json_decode($retorno);
            $result = !empty($response->{$email}->status) && $response->{$email}->code == '250';
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output($result);
        return;
    }

    public function enriqueceCPF($cpf, $produto_parceiro_id){
        $retorno = $this->execute($this->url."info?doc={$cpf}&produto_parceiro_id={$produto_parceiro_id}");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function enriqueceEAN($ean){
        $retorno = $this->execute($this->url."equipamento?ean=$ean");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function cotacao_campos($produto_parceiro_id){
        $retorno = $this->execute($this->url."campos?produto_parceiro_id=$produto_parceiro_id");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function insereCotacao(){
        // $this->stop=true;
        $json = file_get_contents( "php://input" );

        $retorno = $this->execute($this->url."cotacao", 'POST', $json);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function forma_pagamento_cotacao($cotacao_id){
        $retorno = $this->execute($this->url."pagamento/forma_pagamento_cotacao?cotacao_id=$cotacao_id");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function pagamento_campos($forma_pagamento_id){
        $retorno = $this->execute($this->url."pagamento/campos?forma_pagamento_id=$forma_pagamento_id");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function pagamento_pagar(){
        $json = file_get_contents( "php://input" );
        $retorno = $this->execute($this->url."pagamento/pagar", "POST", $json);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function calculo_premio($cotacao_id){
        $retorno = $this->execute($this->url."cotacao/calculo?cotacao_id=$cotacao_id");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

}
