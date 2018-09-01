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
    }
    
    private function execute($url, $method = 'GET', $fields = ''){
        $retorno = soap_curl([
            'url' => $url,
            'method' => $method,
            'fields' => $fields,
            'header' => array(
                "accept: application/json",
                "APIKEY: ". app_get_token()
            )
        ]);

        if (empty($retorno)) return;
        if (!empty($retorno["error"])) return;
        if (empty($retorno["response"])) return;
        $retornoJson = json_decode($retorno["response"]);

        if (isset($retornoJson->status) && empty($retornoJson->status)) {
            header('X-Error-Message: '. $retornoJson->message, true, 500);
            die();
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

}
