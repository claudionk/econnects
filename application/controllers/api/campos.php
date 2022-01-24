<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Campos extends CI_Controller {
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
    $this->load->model( "produto_parceiro_plano_model", "produto_parceiro_plano" );
    $this->load->model( "produto_parceiro_campo_model", "produto_parceiro_campo" );
    $this->load->model( "campo_tipo_model", "campo_tipo" );
  }
  
  public function index() {
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    if( !isset( $GET["produto_parceiro_id"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo produto_parceiro_id é obrigatório" ) ) );
    } else {
      $this->load->model( "cotacao_model", "cotacao" );

      $produto_parceiro_id = $GET["produto_parceiro_id"];
      $produto_parceiro_plano_id = null;

      if( !empty( $GET["produto_parceiro_plano_id"] ) ) {
        $produto_parceiro_plano_id = $GET["produto_parceiro_plano_id"];
      }

      $slug = null;
      if( isset( $GET["slug"] ) ) {
        $slug = $GET["slug"];
      }

      $onlyInvalids = false;
      $cotacao_id = null;
      $cotacao = null;
      if (!empty($_GET['cotacao_id'])){
        $cotacao = $this->cotacao->get_by_id($_GET['cotacao_id']);

        if(!empty($cotacao) && $cotacao["produto_parceiro_id"] == $produto_parceiro_id){
          $cotacao_id = $cotacao["cotacao_id"];
          $onlyInvalids = !empty($_GET['invalidos']);
        }
      }

      $filter = new stdClass();
      $filter->slug = $slug;
      $filter->produto_parceiro_plano_id = $produto_parceiro_plano_id;
      $filter->produto_parceiro_id = $produto_parceiro_id;

      $result = $this->produto_parceiro_plano->getCampos($filter, $onlyInvalids, $cotacao_id);

      if( $result ) {
        die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
      } else {
        die( json_encode( array( "status" => false, "message" => "Não foram localizados os campos do produto_parceiro_id $produto_parceiro_id" ) ) );
      }
      
    }
  }
}



