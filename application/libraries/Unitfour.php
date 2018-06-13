<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unitfour {

    private $_ci;

    public $client;
    public $token;
    public $token_validade;
    public $produto_parceiro_id;
    public $usuario;
    public $senha;
    public $parametros;
    public $produto_parceiro_servico_id;


    const API_URL_WSDL = 'http://wsi3.unitfour.com.br/intouchws.asmx?WSDL';
    const SOAP_VERSAO = SOAP_1_2;


    public function __construct($params  = array()){


        $this->_ci =& get_instance();

        $this->_ci->load->model('produto_parceiro_servico_log_model', 'produto_parceiro_servico_log');

        foreach ($params as $property => $value){
            $this->$property = $value;
        }
        try{
            $this->_ci->load->library("Nusoap_lib");
            $this->client = new nusoap_client(self::API_URL_WSDL, array('soap_version' => self::SOAP_VERSAO));
            if(($this->client->fault) || ($e = $this->client->getError()) ){
                $error = ($this->client->fault) ? $this->client->fault : $e;
                throw new Exception($error);
                log_message('error',$error);
            }elseif($this->produto_parceiro_id){
               $this->setToken();
            }
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
            log_message('error',$e->getMessage());
        }
    }

    private function setToken(){
        try{

            $this->_ci->load->model('produto_parceiro_servico_model', 'produto_parceiro_servico');

            $config = $this->_ci->produto_parceiro_servico
                           ->with_servico()
                           ->with_servico_tipo()
                           ->filter_by_servico_tipo('unitfour_pf')
                           ->filter_by_produto_parceiro($this->produto_parceiro_id)
                           ->get_all();


            if($config){
                $config = $config[0];

                $this->usuario = $config['servico_usuario'];
                $this->senha = $config['servico_senha'];
                $this->parametros = $config['servico_parametros'];
                $this->produto_parceiro_servico_id = $config['produto_parceiro_servico_id'];


                $param = array(
                    'usuario' => $this->usuario,
                    'senha' => $this->senha,
                    'cliente' => $this->parametros,
                );



                $response = $this->client->call('GerarToken', $param);

                if(($this->client->fault) || ($e = $this->client->getError()) ){
                    $error = ($this->client->fault) ? $this->client->fault : $e;
                    //throw new Exception($error);
                    return FALSE;
                    log_message('error',$error);
                }
                log_message('error', 'GerarToken' . print_r($response, true));
                if(isset($response['GerarTokenPassandoVersaoWebServiceResult'])){
                    libxml_use_internal_errors(true);
                    $xml = simplexml_load_string($response['GerarTokenPassandoVersaoWebServiceResult']);
                    if($xml === FALSE || count(libxml_get_errors()) > 1){
                        $this->token = $response['GerarTokenPassandoVersaoWebServiceResult'];
                    }else{
                        $json = json_encode($xml);
                        $response = json_decode($json,TRUE);
                        if(isset($response[0])){
                            //throw new Exception($response[0]);
                            return FALSE;
                            log_message('error',$response[0]);
                        }else{
                            //throw new Exception(print_r($response, true));
                            return FALSE;
                            log_message('error',print_r($response, true));
                        }
                    }

                }else{
                    return FALSE;
                }

                return TRUE;

            }else {
                return FALSE;
            }

        }
        catch(Exception $e){
            //throw new Exception($e->getMessage());
            return FALSE;
            log_message('error',$e->getMessage());
        }
    }

    public function getBasePessoaPF($doc){


        try{

            $param = array(
                'usuario' => $this->usuario,
                'senha' => $this->senha,
                'cliente' => $this->parametros,
                'token' => $this->token,
                'documento' => $doc,
            );

            $data_log = array(
                'url' => self::API_URL_WSDL,
                'consulta' => 'LocalizaPessoasTk',
                'parametros' => print_r($param, true),
                'retorno' => '',
                'time_envio' => date('H:i:s:u'),
                'ip' => $this->_ci->input->ip_address(),
            );

            $response = $this->client->call('LocalizaPessoasTk', $param);


            log_message('error', 'LocalizaPessoasTk' . print_r($response, true));
            if(($this->client->fault) || ($e = $this->client->getError()) ){
                $error = ($this->client->fault) ? $this->client->fault : $e;
                return array();
                log_message('error',$error);
            }



            if(isset($response['LocalizaPessoasTkResult'])){
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($response['LocalizaPessoasTkResult']);
                if($xml === FALSE || count(libxml_get_errors()) > 1){
                    return array();
                }else{
                    $json = json_encode($xml);
                    $response = json_decode($json,TRUE);

                    $data_log['retorno'] = print_r($response, true);
                    $data_log['time_retorno'] = date('H:i:s:u');

                    $this->_ci->produto_parceiro_servico_log->insLog($this->produto_parceiro_servico_id, $data_log);
                    log_message('error', 'LocalizaPessoasTk' . print_r($response, true));
                    return $response;
                }

            }else{
                return array();
            }


        }
        catch(Exception $e){
            //throw new Exception($e->getMessage());
            return array();
            log_message('error',$e->getMessage());
        }


    }


    public function getToken(){
        return $this->token;
    }

}