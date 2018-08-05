<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ifaro {

  private $_ci;

  public $client;
  public $token;
  public $token_validade;
  public $produto_parceiro_id;
  public $usuario;
  public $senha;
  public $parametros;
  public $produto_parceiro_servico_id;


  const API_ENDPOINT = "http://ws.ifaro.com.br/APIDados.svc/";
  const API_TOKEN = "http://ws.ifaro.com.br//Seguranca.svc/API/GetToken/";

  public function __construct( $params = array() ) {
    $this->_ci =& get_instance();
    $this->_ci->load->model('produto_parceiro_servico_log_model', 'produto_parceiro_servico_log');

    foreach ($params as $property => $value){
      $this->$property = $value;
    }

    if( $this->produto_parceiro_id ) {
      $this->setToken();
    }
  }

  public function setToken() {
    $this->_ci->load->model('produto_parceiro_servico_model', 'produto_parceiro_servico');

    $config = $this->_ci->produto_parceiro_servico
      ->with_servico()
      ->with_servico_tipo()
      ->filter_by_servico_tipo('ifaro_pf')
      ->filter_by_produto_parceiro($this->produto_parceiro_id)
      ->get_all();
    
    if( $config ) {
      $this->produto_parceiro_servico_id = $config[0]["produto_parceiro_servico_id"];
    }
    
    $Login = base64_encode( $config[0]["servico_usuario"] );
    $Senha = base64_encode( $config[0]["servico_senha"] );
    $Url = self::API_TOKEN . "$Login/$Senha";

    $Antes = new DateTime();
    
    $myCurl = curl_init();
    curl_setopt( $myCurl, CURLOPT_URL, $Url );
    curl_setopt( $myCurl, CURLOPT_FRESH_CONNECT, 1 );
    curl_setopt( $myCurl, CURLOPT_POST, 0 );
    curl_setopt( $myCurl, CURLOPT_VERBOSE, 0);
    curl_setopt( $myCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $myCurl, CURLOPT_TIMEOUT, 15 );
    curl_setopt( $myCurl, CURLOPT_CONNECTTIMEOUT, 15 );
    $Response = curl_exec( $myCurl );
    curl_close( $myCurl );
    
    $Depois = new DateTime();

    $data_log["produto_parceiro_servico_id"] = $this->produto_parceiro_servico_id;
    $data_log["url"] = $Url;
    $data_log["consulta"] = "GetToken";
    $data_log["retorno"] = $Response;
    $data_log["time_envio"] = $Antes->format( "H:i:s" );
    $data_log["time_retorno"] = $Depois->format( "H:i:s" );
    $data_log["parametros"] = $Url;
    $data_log["data_log"] = $Antes->format( "Y-m-d H:i:s" );
    $data_log["ip"] = ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ? $_SERVER[ "REMOTE_ADDR" ] : "" );
    $this->_ci->produto_parceiro_servico_log->insLog( $this->produto_parceiro_servico_id, $data_log );

    $response = json_decode( $Response, true );
    if( isset( $response["TipoRetorno"] ) && intval( $response["TipoRetorno"] ) == 0 ) {
      $this->token = $response["Token"];
      return true;
    } else {
      return false;
    }
  }
  
  public function getToken() {
    return $this->token;
  }
  
  public function getBasePessoaPF( $doc ) {
    $this->setToken();
    $Token = $this->token;
    $CPF = $doc;
    $Url = self::API_ENDPOINT . "ConsultaPessoa/$CPF/$Token";

    $Antes = new DateTime();
    
    $myCurl = curl_init();
    curl_setopt( $myCurl, CURLOPT_URL, $Url );
    curl_setopt( $myCurl, CURLOPT_FRESH_CONNECT, 1 );
    curl_setopt( $myCurl, CURLOPT_POST, 0 );
    curl_setopt( $myCurl, CURLOPT_VERBOSE, 0);
    curl_setopt( $myCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $myCurl, CURLOPT_TIMEOUT, 15 );
    curl_setopt( $myCurl, CURLOPT_CONNECTTIMEOUT, 15 );
    $Response = curl_exec( $myCurl );
    curl_close( $myCurl );
    
    $Depois = new DateTime();

    $data_log["produto_parceiro_servico_id"] = $this->produto_parceiro_servico_id;
    $data_log["url"] = $Url;
    $data_log["consulta"] = "ConsultaPessoa";
    $data_log["retorno"] = $Response;
    $data_log["time_envio"] = $Antes->format( "H:i:s" );
    $data_log["time_retorno"] = $Depois->format( "H:i:s" );
    $data_log["parametros"] = $Url;
    $data_log["data_log"] = $Antes->format( "Y-m-d H:i:s" );
    $data_log["ip"] = ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ? $_SERVER[ "REMOTE_ADDR" ] : "" );
    $this->_ci->produto_parceiro_servico_log->insLog( $this->produto_parceiro_servico_id, $data_log );
    
    $response = json_decode( $Response, true );
    
    if( isset( $response["TipoRetorno"] ) && intval( $response["TipoRetorno"] ) == 0 ) {
      return $response;
    } else {
      return array();
    }
  }
  
}
