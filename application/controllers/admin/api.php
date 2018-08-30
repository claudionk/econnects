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
    }
    
    public function equipamento($ean){
        $retorno = soap_curl([
            'url' => "http://econnects-h.jelastic.saveincloud.net/api/equipamento?ean=". $ean,
            'method' => 'GET',
            'fields' => '',
            'header' => array(
                "accept: application/json",
                "APIKEY: ". app_get_token()
            )
        ]);

        if (empty($retorno)) return;
        if (!empty($retorno["error"])) return;
        if (empty($retorno["response"])) return;

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno["response"]);
        return;
    }

    public function email($email){
        $email = urldecode($email);

        $retorno = soap_curl([
            'url' => "http://econnects-h.jelastic.saveincloud.net/api/info/email?email=". $email ."&remetente=ti@sissolucoes.com.br",
            'method' => 'GET',
            'fields' => '',
            'header' => array(
                "accept: application/json",
                "APIKEY: ". app_get_token()
            )
        ]);

        if (empty($retorno)) return;
        if (!empty($retorno["error"])) return;
        if (empty($retorno["response"])) return;

        $response = json_decode($retorno["response"]);

        $this->output
            ->set_content_type('application/json')
            ->set_output(!empty($response->{$email}->status) && $response->{$email}->code == '250');
        return;
    }

}
