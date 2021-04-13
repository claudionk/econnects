<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Plano
*/
class Plano extends CI_Controller {
	public $api_key;
	public $usuario_id;
	public $parceiro_id;

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
		$this->parceiro_id = $webservice["parceiro_id"];
		$this->load->database('default');

		$this->load->model( "cobertura_plano_model", "cobertura_plano" );
		$this->load->model( "produto_parceiro_model", "produto_parceiro" );
		$this->load->model( "produto_parceiro_plano_model", "produto_parceiro_plano" );
	}

	public function index() {
		if( $_SERVER["REQUEST_METHOD"] !== "GET" )
		{
			die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
		}
		
		$GET = $_GET;

		if( !isset( $GET["produto_parceiro_id"] ) )
		{
			die( json_encode( array( "status" => false, "message" => "Campo produto_parceiro_id é obrigatório" ) ) );
		}

		$produto_parceiro_id = $GET["produto_parceiro_id"];
		$slug_plano = ( isset( $GET["slug"] ) ) ? $GET["slug"] : null;
		$planos = $this->produto_parceiro_plano->PlanosHabilitados($this->parceiro_id, $produto_parceiro_id, $slug_plano);
		if( $planos )
		{
			die( json_encode( $planos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
		} else {
			die( json_encode( array( "status" => false, "message" => "Não foram localizados planos do produto_parceiro_id $produto_parceiro_id" ) ) );
		}
	}

	public function coberturas() {
		if( $_SERVER["REQUEST_METHOD"] !== "GET" )
		{
			die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
		}

		$GET = $_GET;

		if( !isset( $GET["produto_parceiro_plano_id"] ) ) {
			die( json_encode( array( "status" => false, "message" => "Campo produto_parceiro_plano_id é obrigatório" ) ) );
		}

		$produto_parceiro_plano_id = $GET["produto_parceiro_plano_id"];
		$coberturas = $this->cobertura_plano->with_cobertura()->filter_by_produto_parceiro_plano( $produto_parceiro_plano_id )->get_all();
		if( $coberturas ) {
			die( json_encode( $coberturas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
		} else {
			die( json_encode( array( "status" => false, "message" => "Não foram localizadas coberturas para o produto_parceiro_plano_id produto_parceiro_plano_id" ) ) );
		}
	}

}
