<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Equipamento extends CI_Controller {
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
  }
  
  public function index() {
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    if( !isset( $GET["ean"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo EAN é obrigatório" ) ) );
    }
    
    $ean = $GET["ean"];
    
    $Equipamento = $this->db->query( "SELECT * FROM equipamento WHERE ean='$ean'" )->result_array();
    if( sizeof( $Equipamento ) ) {
      die( json_encode( array( 
        "equipamento_id" => $Equipamento[0]["equipamento_id"],
        "ean" => $Equipamento[0]["ean"],
        "nome" => $Equipamento[0]["nome"],
        "equipamento_marca_id" => $Equipamento[0]["equipamento_marca_id"],
        "equipamento_categoria_id" => $Equipamento[0]["equipamento_categoria_id"],
        "equipamento_sub_categoria_id" => $Equipamento[0]["equipamento_sub_categoria_id"]
      ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    } else {
      die( json_encode( array( "status" => false, "message" => "Não foram localizados equipamentos com esse EAN ($ean)" ) ) );
    }    
  }
  
  public function categorias() {
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    $Categorias = $this->db->query( "SELECT * FROM equipamento_categoria WHERE deletado=0 AND equipamento_categoria_nivel=1 ORDER BY nome" )->result_array();
    die( json_encode( $Categorias, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
  }
  
  public function marcas() {
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }

    $Marcas = $this->db->query( "SELECT * FROM equipamento_marca WHERE deletado=0 ORDER BY nome" )->result_array();
    die( json_encode( $Marcas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
  }

}





