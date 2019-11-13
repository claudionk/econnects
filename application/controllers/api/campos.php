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
      $produto_parceiro_id = $GET["produto_parceiro_id"];
      $produto_parceiro_plano_id = null;

      if( !empty( $GET["produto_parceiro_plano_id"] ) ) {
        $produto_parceiro_plano_id = $GET["produto_parceiro_plano_id"];
      }

      $slug = null;
      if( isset( $GET["slug"] ) ) {
        $slug = $GET["slug"];
      }

      $planos = $this->produto_parceiro_plano->coreSelectPlanosProdutoParceiro( $produto_parceiro_id, $produto_parceiro_plano_id )->get_all_select();
      $result = array();
      
      if( $planos ) {
        //$produto_parceiro_id = $planos[0]["produto_parceiro_id"];
        $produtos = $this->produto_parceiro->get_produtos_venda_admin( null, null, $produto_parceiro_id );
        $produto_slug = $produtos[0]["slug"];

        $campos_tipo = $this->campo_tipo->coreSelecCampoTipo( $slug )->get_all_select();
        $i = 0;
        foreach ($campos_tipo as $index => $item) {
          $campos = $this->produto_parceiro_campo->coreSelecCampoProdutoParceiro( $produto_parceiro_id, $item["campo_tipo_id"] )->get_all_select();
          if( $campos ) {
            if( $item["slug"] == "dados_passageiro" && $produto_slug != "seguro_viagem" ) {
              $pula = null;
            } else {
              $result[$i] = $item;
              $result[$i]["campos"] = $campos;
              $i++;
            }
          }
        }
      }
      if( $result ) {
        die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
      } else {
        die( json_encode( array( "status" => false, "message" => "Não foram localizados os campos do produto_parceiro_id $produto_parceiro_id" ) ) );
      }
      
    }
  }
}



