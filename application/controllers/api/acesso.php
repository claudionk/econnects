<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Acesso extends CI_Controller {
  public $api_key;
  
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
    
  }
  
  public function index() {
    $GET = $_GET;
    
    $this->load->model( "usuario_model", "usuario" );
    $this->load->model( "usuario_webservice_model", "webservice" );
    
    if( !isset( $GET["email"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo email é obrigatório" ) ) );
    } else {
      $email = $GET["email"];
    }

    $usuario = $this->usuario->get_by( array( "email" => $GET["email"] ) );
    if( !$usuario ) {
      die( json_encode( array( "status" => false, "message" => "Usuário não cadastrado" ) ) );
    }
    $webservice = $this->webservice->getByUsuarioID( $usuario["usuario_id"] );
    

    if( $webservice ) {

      if( date( "Y-m-d H:i:s", strtotime( $webservice["validade"] ) ) < date("Y-m-d H:i:s") ) {
        $dados_webservice = array();
        $dados_webservice["validade"] = date("Y-m-d H:i:s", strtotime( "+12 hours", strtotime( date("Y-m-d H:i:s") ) ) );
        $dados_webservice["api_key"] = hash( "sha256", $dados_webservice["validade"].$usuario["email"] );
        $this->webservice->update( $webservice["usuario_webservice_id"], $dados_webservice, TRUE ) ;
        $webservice = $this->webservice->getByUsuarioID( $usuario["usuario_id"] );
      }
      
    }else{
      
      $dados_webservice = array();
      $dados_webservice["usuario_id"] = $usuario["usuario_id"];
      $dados_webservice["validade"] = date("Y-m-d H:i:s", strtotime( "+12 hours", strtotime( date("Y-m-d H:i:s") ) ) );
      $dados_webservice["api_key"] = hash( "sha256", $dados_webservice["validade"].$usuario["email"] );
      $usuario_webservice_id = $this->webservice->insert( $dados_webservice, TRUE );
      $webservice = $this->webservice->getByUsuarioID( $usuario["usuario_id"] );
    }
    
    die( json_encode( array( "status" => true, "api_key" => $webservice["api_key"], "validade" => $webservice["validade"], "parceiro_id" => $webservice["parceiro_id"] ) ) );
  }
}
?>





