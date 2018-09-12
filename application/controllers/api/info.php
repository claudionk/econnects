<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Info extends CI_Controller {
  public $api_key;
  public $usuario_id;

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
    
    if( isset( $_SERVER["HTTP_APIKEY"] ) ) {
      $this->api_key = $_SERVER["HTTP_APIKEY"];
      $this->load->model( "usuario_webservice_model", "webservice" );
      $this->load->model( "produto_parceiro_servico_log_model", "produto_parceiro_servico_log" );
      $this->load->model( "produto_parceiro_servico_model", "produto_parceiro_servico" );
      
      $webservice = $this->webservice->checkKeyExpiration( $this->api_key );
      if( !sizeof( $webservice ) ) {
        die( json_encode( array( "status" => false, "message" => "APIKEY is invalid" ) ) );
      }
    } else {
      die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
    }
    $this->usuario_id = $webservice["usuario_id"];
  }
  
  public function imei() {
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    if( !isset( $GET["imei"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo IMEI é obrigatório" ) ) );
    }
    $IMEI = $GET["imei"];
    
    if( !isset( $GET["produto_parceiro_id"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo produto_parceiro_id é obrigatório" ) ) );
    }
    
    $this->produto_parceiro_id = preg_replace( "/[^0-9]/", "", $GET["produto_parceiro_id"] );
    
    $this->setTokenWS();
    if( !$this->token ) {
      die( json_encode( array( "status" => false, "message" => "Serviço de informação não configurado" ) ) );
    }
    
    $protected_url = "http://ws.ifaro.com.br/WSDados.svc?wsdl";
    
    $client = new SoapClient( $protected_url, array( "soap_version" => SOAP_1_1, "trace" => false, "keep_alive" => false, "exceptions" => true ) );
    $actionHeader = array( new SoapHeader("http://www.w3.org/2005/08/addressing","Action", "http://tempuri.org/IWSDados/ConsultaImei") );
    $client->__setSoapHeaders( $actionHeader );

    $parameters = array( "ConsultaImei" => array( "imei" => $IMEI, "token" => $this->token ) );
    $resultado = $client->__soapCall( "ConsultaImei", $parameters );
    die( json_encode( (array) $resultado->{"ConsultaImeiResult"}, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

    
  }
  
  public function index() {
    $this->load->model('base_pessoa_model', 'base_pessoa');
    
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    if( !isset( $GET["doc"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo CPF é obrigatório" ) ) );
    }
    
    if( !isset( $GET["produto_parceiro_id"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo Produto_Parceiro_Id é obrigatório" ) ) );
    }
    
    $CPF = preg_replace( "/[^0-9]/", "", $GET["doc"] );
    $this->produto_parceiro_id = preg_replace( "/[^0-9]/", "", $GET["produto_parceiro_id"] );
    
    $this->setToken();
    if( !$this->token ) {
      die( json_encode( array( "status" => false, "message" => "Serviço de informação não configurado" ) ) );
    }
    $Token = $this->token;
    $Url = self::API_ENDPOINT . "ConsultaPessoa/$CPF/$Token";
    

    $pessoa = $this->base_pessoa->getByDoc( $CPF, $this->produto_parceiro_id, "ifaro_pf" );
    if( isset( $pessoa["ultima_atualizacao"] ) ) {
      $ultima_atualizacao = date_create($pessoa["ultima_atualizacao"]);
      $proxima_atualizacao = date_add( $ultima_atualizacao, date_interval_create_from_date_string( "3 months" ) );
    }
    if( strtotime( date( "Y-m-d H:i:s" ) ) < strtotime( $proxima_atualizacao->format( "Y-m-d H:i:s" ) ) ) {
      die( json_encode( $pessoa, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }
    
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

    $data_log["produto_parceiro_servico_log_id"] = 0;
    $data_log["produto_parceiro_servico_id"] = $this->produto_parceiro_servico_id;
    $data_log["url"] = $Url;
    $data_log["consulta"] = "ConsultaPessoa";
    $data_log["retorno"] = $Response;
    $data_log["time_envio"] = $Antes->format( "H:i:s" );
    $data_log["time_retorno"] = $Depois->format( "H:i:s" );
    $data_log["parametros"] = $Url;
    $data_log["data_log"] = $Antes->format( "Y-m-d H:i:s" );
    $data_log["ip"] = ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ? $_SERVER[ "REMOTE_ADDR" ] : "" );
    $this->produto_parceiro_servico_log->insLog( $this->produto_parceiro_servico_id, $data_log );
    
    $response = json_decode( $Response, true );
    
    $this->load->model("produto_parceiro_campo_model", "produto_parceiro_campo");
    $campos = $this->produto_parceiro_campo->with_campo()->with_campo_tipo()->filter_by_produto_parceiro( $this->produto_parceiro_id )->filter_by_campo_tipo_slug( "dados_segurado" )->order_by( "ordem", "ASC" )->get_all();

    $erros = array();

    $validacao = array();
    foreach( $campos as $campo ) {
      $validacao[] = array(
        "field" => $campo["campo_nome_banco"],
        "label" => $campo["campo_nome"],
        "rules" => $campo["validacoes"],
        "groups" => "cotacao",
        "value" => isset($response[$campo["campo_nome_banco"]]) ? $response[$campo["campo_nome_banco"]] : ""
      );
    }

    $validacao_ok = true;
    foreach( $validacao as $check ) {
      if( strpos( $check["rules"], "enriquecimento" ) !== false && $check["value"] == "" ) {
        $validacao_ok = false;
        $erros[] = $check;
      }
    }

    if( !$validacao_ok ) {
      die( json_encode( array( "status" => false, "message" => "Erro de validação", "erros" => $erros ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }
    
    if( isset( $response["TipoRetorno"] ) && intval( $response["TipoRetorno"] ) == 0 ) {
      $this->updateBase( $pessoa["base_pessoa_id"], $response );
      $pessoa = $this->base_pessoa->getByDoc( $CPF, $this->produto_parceiro_id, "ifaro_pf" );
      die( json_encode( $pessoa, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    } else {
      die( json_encode( array( "status" => false, "message" => "Não foi possível concluir a consulta" ) ) );
    }
  }
  
  public function setToken() {

    $config = $this->produto_parceiro_servico
      ->with_servico()
      ->with_servico_tipo()
      ->filter_by_servico_tipo( "ifaro_pf" )
      ->filter_by_produto_parceiro( $this->produto_parceiro_id )
      ->get_all();
    
    if( $config ) {
      $this->produto_parceiro_servico_id = $config[0]["produto_parceiro_servico_id"];

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

      $data_log["produto_parceiro_servico_log_id"] = 0;
      $data_log["produto_parceiro_servico_id"] = $this->produto_parceiro_servico_id;
      $data_log["url"] = $Url;
      $data_log["consulta"] = "GetToken";
      $data_log["retorno"] = $Response;
      $data_log["time_envio"] = $Antes->format( "H:i:s" );
      $data_log["time_retorno"] = $Depois->format( "H:i:s" );
      $data_log["parametros"] = $Url;
      $data_log["data_log"] = $Antes->format( "Y-m-d H:i:s" );
      $data_log["ip"] = ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ? $_SERVER[ "REMOTE_ADDR" ] : "" );
      $this->produto_parceiro_servico_log->insLog( $this->produto_parceiro_servico_id, $data_log );

      $response = json_decode( $Response, true );
      if( isset( $response["TipoRetorno"] ) && intval( $response["TipoRetorno"] ) == 0 ) {
        $this->token = $response["Token"];
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }

  }

  public function setTokenWS() {

    $config = $this->produto_parceiro_servico
      ->with_servico()
      ->with_servico_tipo()
      ->filter_by_servico_tipo( "ifaro_pf" )
      ->filter_by_produto_parceiro( $this->produto_parceiro_id )
      ->get_all();
    
    if( $config ) {
      $this->produto_parceiro_servico_id = $config[0]["produto_parceiro_servico_id"];

      $Login = base64_encode( $config[0]["servico_usuario"] );
      $Senha = base64_encode( $config[0]["servico_senha"] );
      $Url = self::API_TOKEN . "$Login/$Senha";

      $Antes = new DateTime();

      $protected_url = "http://ws.ifaro.com.br/Seguranca.svc?wsdl";
      
      $client = new SoapClient( $protected_url, array( "soap_version" => SOAP_1_1, "trace" => false, "keep_alive" => false, "exceptions" => true ) );
      $actionHeader = array( new SoapHeader("http://www.w3.org/2005/08/addressing","Action", "http://tempuri.org/ISegurancaApi/GetTokenAPI") );
      $client->__setSoapHeaders( $actionHeader );

      $parameters = array( "GetToken" => array( "login" => $Login, "senha" => $Senha ) );
      $Response = (array)$client->__soapCall( "GetToken", $parameters );
      $Response = $Response["GetTokenResult"];

      $Depois = new DateTime();

      $data_log["produto_parceiro_servico_log_id"] = 0;
      $data_log["produto_parceiro_servico_id"] = $this->produto_parceiro_servico_id;
      $data_log["url"] = $protected_url;
      $data_log["consulta"] = "GetTokenWS";
      $data_log["retorno"] = json_encode( $Response );
      $data_log["time_envio"] = $Antes->format( "H:i:s" );
      $data_log["time_retorno"] = $Depois->format( "H:i:s" );
      $data_log["parametros"] = $protected_url;
      $data_log["data_log"] = $Antes->format( "Y-m-d H:i:s" );
      $data_log["ip"] = ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ? $_SERVER[ "REMOTE_ADDR" ] : "" );
      $this->produto_parceiro_servico_log->insLog( $this->produto_parceiro_servico_id, $data_log );

      $response = (array)$Response;
      if( isset( $response["TipoRetorno"] ) && intval( $response["TipoRetorno"] ) == 0 ) {
        $this->token = $response["Token"];
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }

  }
  
  public function getToken() {
    return $this->token;
 	 }

  private function updateBase( $base_pessoa_id, $ifaro ) {
    
    $this->load->model('base_pessoa_model', 'base_pessoa');
    $this->load->model('base_pessoa_contato_model', 'base_pessoa_contato');
    $this->load->model('base_pessoa_empresa_model', 'base_pessoa_empresa');
    $this->load->model('base_pessoa_endereco_model', 'base_pessoa_endereco');
    
    $result = array();

    $DataNascimento = date_create_from_format( "d/m/Y", $ifaro["DataNascimento"] );
    $result["DADOS_CADASTRAIS"] = array( "CPF" => $ifaro["CPF"],
                                        "NOME" => $ifaro["Nome"],
                                        "NOME_ULTIMO" => trim( strrchr( $ifaro["Nome"], " " ) ),
                                        "SEXO" => $ifaro["Sexo"],
                                        "NOME_MAE" => $ifaro["Mae"],
                                        "DATANASC" => date_format( $DataNascimento, "Y-m-d" ),
                                        "IDADE" => $ifaro["Idade"],
                                        "SIGNO" => $ifaro["Signo"],
                                        "RG" => $ifaro["RG"],
                                        "SITUACAO_RECEITA" => "REGULAR" );

    $iTelefones = $ifaro["Telefones"];
    foreach( $iTelefones as $row ) {
      $Telefones[] = array("TELEFONE" => "(" . trim( $row["DD"] ) . ") " . $row["Numero"], 
                           "RANKING" => ( $row["Tipo"] == "TELEFONE MÓVEL" ? 90 : $row["Ranking"] ) );
    }
    $result["TELEFONES"] = $Telefones;

    $iEmails = $ifaro["Emails"];
    foreach( $iEmails as $row ) {
      $Emails[] = array( "EMAIL" => trim( $row["EmailEndereco"] ), "RANKING" => $row["Ranking"] );
    }
    $result["EMAILS"] = $Emails;

    $iEnderecos = $ifaro["Enderecos"];
    foreach( $iEnderecos as $row ) {
      $Enderecos[] = array("LOGRADOURO" => trim( $row["Logadouro"] ), 
                           "NUMERO" => trim( $row["Numero"] ), 
                           "COMPLEMENTO" => trim( $row["Complemento"] ), 
                           "BAIRRO" => trim( $row["Bairro"] ), 
                           "CIDADE" => trim( $row["Cidade"] ), 
                           "UF" => trim( $row["UF"] ), 
                           "CEP" => trim( $row["CEP"] ), 
                           "RANKING" => $row["Ranking"] );
    }
    $result["ENDERECOS"] = $Enderecos;

    if( $result && isset( $result["DADOS_CADASTRAIS"] ) ) {
      if( $base_pessoa_id > 0 ) {
        $this->base_pessoa_contato->delete_by( array( "base_pessoa_id" => $base_pessoa_id ) );
        $this->base_pessoa_empresa->delete_by( array( "base_pessoa_id" => $base_pessoa_id ) );
        $this->base_pessoa_endereco->delete_by( array( "base_pessoa_id" => $base_pessoa_id ) );
        $this->base_pessoa->update_base_pessoa( $base_pessoa_id, $result );
      } else {
        $this->base_pessoa->update_base_pessoa( $base_pessoa_id, $result );
      }
      return $result;
    } else {
      return array();
    }
  }
  
  public function dns() {
    
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    if( !isset( $GET["dominio"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo dominio é obrigatório" ) ) );
    }
    $dominio = $GET["dominio"];

    if( !isset( $GET["tipo"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo tipo é obrigatório" ) ) );
    }
    $tipo = $GET["tipo"];
    
    $consulta = dns_get_record( $dominio, DNS_MX );

    die( json_encode( $consulta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
  }
  
  public function email() {

    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }

    if( !isset( $GET["email"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo dominio é obrigatório" ) ) );
    }
    $email = $GET["email"];
    
    if( !isset( $GET["remetente"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo dominio é obrigatório" ) ) );
    }
    $sender = $GET["remetente"];

    $SMTP = new validaEmail();
    $results = $SMTP->validate( $email, $sender );
    
    //die( print_r( $results ) );

    die( json_encode( $results, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
  }

}

class validaEmail {

  var $sock;
  var $user;
  var $domain;
  var $port = 25;
  var $max_conn_time = 30;
  var $max_read_time = 5;

  public function __construct() {
  }
  
  function validate( $email, $sender ) {

    $results = array();
    
    if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
      $results[$email]["status"] = false;
      $results[$email]["code"] = "550";
      $results[$email]["message"] = "Invalid email address ($email)";
    }
    
    if( !filter_var( $sender, FILTER_VALIDATE_EMAIL ) ) {
      $results[$sender]["status"] = false;
      $results[$sender]["code"] = "550";
      $results[$sender]["message"] = "Invalid sender email address ($sender)";
    }
    
    $parts = explode( "@", $email );
    $domain = array_pop( $parts );
    $user = implode( "@", $parts );

    $parts = explode( "@", $sender );
    $from_domain = array_pop( $parts );
    $from_user = implode( "@", $parts );

    $mx_records = dns_get_record( $domain, DNS_MX );

    $timeout = $this->max_conn_time;

    $mx_hosts = array();
    foreach( $mx_records as $mx_record ) {
      //
      // Treat invalid domains such as Office 365 ones
      //
      if( strpos( $mx_record["target"], ".invalid" ) === false ) {
        $mx_hosts[] = $mx_record["target"];
      }
    }
    
    foreach( $mx_hosts as $host ) {
      if ($this->sock = fsockopen($host, $this->port, $errno, $errstr, (float) $timeout) ) {
        stream_set_timeout($this->sock, $this->max_read_time);
        if($this->sock) {
          $reply = fread($this->sock, 2082);
          preg_match( "/^([0-9]{3}) /ims", $reply, $matches );
          $code = isset( $matches[1] ) ? $matches[1] : "";

          if($code != '220') {
            $results[$email]["status"] = false;
            $results[$email]["code"] = $code;
            $results[$email]["message"] = $reply;
            continue;
          }

          $this->send( "HELO $from_domain" );
          $this->send( "MAIL FROM: <$sender>" );

          $reply = $this->send( "RCPT TO: <$email>" );

          preg_match( "/^([0-9]{3}) /ims", $reply, $matches );
          $code = isset( $matches[1] ) ? $matches[1] : "";
          $results[$email]["status"] = ( $code == "250" ? true : false );
          $results[$email]["code"] = $code;
          $results[$email]["message"] = $reply;

          $this->send( "RSET" );

          $this->send( "quit" );
          fclose($this->sock);
        } else {
          $results[$email]["status"] = false;
          $results[$email]["code"] = "554";
          $results[$email]["message"] = "Could not establish connection to the MX host for domain $domain";
          break;
        }
      }
    }
    if( sizeof( $results ) == 0 ) {
      $results[$email]["status"] = false;
      $results[$email]["code"] = "400";
      $results[$email]["message"] = "No MX record for domain $domain";
    }
    return $results;
  }

  function send($msg) {
    fwrite($this->sock, $msg."\r\n");
    $reply = fread($this->sock, 2082);
    return $reply;
  }
  
  function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
  
}






