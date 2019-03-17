<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Endereco extends CI_Controller {
    public $api_key;
    public $usuario_id;

    public function __construct() {
        parent::__construct();

        header( "Access-Control-Allow-Origin: *" );
        header( "Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS" );
        header( "Access-Control-Allow-Headers: Cache-Control, APIKEY, apikey, Content-Type" );
        header( "Content-Type: application/json");

        $method = $_SERVER["REQUEST_METHOD"];
        if( $method == "OPTIONS" ) {
            die();
        }

        if( !isset( $_SERVER["HTTP_APIKEY"] ) ) {
            die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
        }

        $this->load->database('default');
        $this->api_key = $_SERVER["HTTP_APIKEY"];
        $this->load->model( "usuario_webservice_model", "webservice" );

        $webservice = $this->webservice->checkKeyExpiration( $this->api_key );
        if( !sizeof( $webservice ) ) {
            die( json_encode( array( "status" => false, "message" => "APIKEY is invalid" ) ) );
        }

    }

    public function cep($cep) {
        if( $_SERVER["REQUEST_METHOD"] !== "GET" ) {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        if( empty( $cep ) ) {
            die( json_encode( array( "status" => false, "message" => "Campo CEP é obrigatório" ) ) );
        }

        $cep = app_clear_number($cep);
        $result = array(
            'success' => false,
            'data' => array(),
            'message' => "Nenhum endereço encontrado para o CEP [$cep]",
        );

        if(strlen($cep) == 8){
            // $result = $this->cepByRepublicaVirtual($cep);
            // if( empty($result['success']) )
                $result = $this->cepByCorreios($cep);
        }

        if( !empty($result['success']) ) {
            $result['data'] = $this->defineUF($result['data'], $result['data']['uf']);
            $result['data'] = $this->defineCity($result['data'], $result['data']['cidade']);

            die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        } else {
            die( json_encode( array( "status" => false, "message" => $result['message'] ) ) );
        }

    }

    private function cepByCorreios($cep){
        $result = array(
            'success' => false,
            'data' => array(),
            'message' => "Nenhum endereço encontrado para o CEP [$cep]",
        );

        $protectedUrl = "https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl";
        $client = new SoapClient( $protectedUrl, array( 'soap_version' => SOAP_1_1, 'trace' => true, 'keep_alive' => true, 'exceptions' => true ) );

        $parameters = array( "cep" => $cep );

        try {

            $data = $client->consultaCep($parameters);
            $data = $data->return;

            // Converte o resultado em caracteres maiusculos
            array_walk($data, function(&$value){
                if(!is_array($value)) $value = strtoupper($value);
            });

            $result = [
                'data' => [
                    'bairro' => $data->bairro,
                    'cep' => $data->cep,
                    'cidade' => $data->cidade,
                    'logradouro' => $data->end,
                    'uf' => $data->uf,
                ],
                'success' => true,
                'message' => '',
            ];

        } catch (SoapFault $e) { 
            $result['message'] = $e;
        }

        return $result;
    }

    private function cepByRepublicaVirtual($cep){
        $result = array(
            'success' => false,
            'data' => array(),
            'message' => "Nenhum endereço encontrado para o CEP [$cep]",
        );

        $url = "http://cep.republicavirtual.com.br/web_cep.php?cep={$cep}&formato=json";
        $data = (array) json_decode(file_get_contents($url));

        if(isset($data['resultado']) && $data['resultado'] > 0 ){

            // Converte o resultado em caracteres maiusculos
            array_walk($data, function(&$value){
                if(!is_array($value)) $value = strtoupper($value);
            });

            $result = [
                'data' => [
                    'bairro' => $data['bairro'],
                    'cep' => $cep,
                    'cidade' => $data['cidade'],
                    // 'tipo_logradouro' => $data['tipo_logradouro'],
                    'logradouro' => $data['tipo_logradouro'].' '.$data['logradouro'],
                    'uf' => $data['uf'],
                ],
                'success' => true,
                'message' => '',
            ];

        }

        return $result;
    }

    private function defineUF(&$result, $uf)
    {
        $this->load->model('localidade_estado_model', 'estado');
        $this->load->model('localidade_cidade_model', 'cidade');

        if( !empty($uf) ){

            $estado = $this->estado->get_by_sigla($uf);
            if($estado){
                $result['estado_id'] = $estado['localidade_estado_id'];
                $result['estado_uf'] = $estado['sigla'];

                $cidades = $this->cidade->getCidadesPorEstado($result['estado_id']);
                $result['cidades'] = $cidades;
            }else {
                $result['estado_id'] = 0;
                $result['cidades'] = array();
            }

        }

        return $result;
    }

    private function defineCity(&$result, $cidade)
    {
        $this->load->model('localidade_cidade_model', 'cidade');

        if( !empty($cidade) ){

            $estado = $this->cidade->get_by_nome($cidade);
            if($estado){
                $result['cidade_id'] = $estado['localidade_cidade_id'];
            }else {
                $result['cidade_id'] = 0;
            }

        }

        return $result;
    }

}
