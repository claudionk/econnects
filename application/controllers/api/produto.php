<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Produto extends CI_Controller {
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

    if( isset( $_SERVER["HTTP_APIKEY"] ) ) {
      $this->api_key = $_SERVER["HTTP_APIKEY"];
      $this->load->model( "usuario_webservice_model", "webservice" );
      
      $webservice = $this->webservice->checkKeyExpiration( $this->api_key );
      if( !sizeof( $webservice ) ) {
        die( json_encode( array( "status" => false, "message" => "APIKEY is invalid" ) ) );
      }
    } else {
      die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
    }
    $this->usuario_id = $webservice["usuario_id"];
    $this->load->database('default');
    
    $this->load->model( "produto_parceiro_model", "produto_parceiro" );
  }
  
  public function index() {
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    if( !isset( $GET["parceiro_id"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo parceiro_id é obrigatório" ) ) );
    }
    
    if( isset( $GET["produto_id"] ) ) {
      $produto_id = $GET["produto_id"];
      $produtos = $this->produto_parceiro->get_produtos_venda_admin( $parceiro_id, $produto_id );
    } else {
      $parceiro_id = $GET["parceiro_id"];
      $produtos = $this->produto_parceiro->get_produtos_venda_admin( $parceiro_id );
    }

    $relacionamento = $this->produto_parceiro->get_produtos_venda_admin_parceiros( $parceiro_id );

    if( $produtos || $relacionamento ) {
      $result = array_merge($produtos, $relacionamento);
      die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    } else {
      die( json_encode( array( "status" => false, "message" => "Não foram localizados produtos do parceiro $parceiro_id" ) ) );
    }
  }
}


