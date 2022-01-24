<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Produto
*/
class Produto extends CI_Controller {
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
			$this->parceiro_id = $webservice["parceiro_id"];
		} else {
			die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
		}

		$this->usuario_id = $webservice["usuario_id"];
		$this->load->database('default');
		$this->load->model( "produto_parceiro_model", "produto_parceiro" );
		$this->load->model( "produto_parceiro_plano_model", "produto_parceiro_plano" );
		$this->load->model( "cobertura_plano_model", "cobertura_plano" );
	}

	public function index() {
		if( $_SERVER["REQUEST_METHOD"] !== "GET" )
		{
			die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
		}

		$parceiro_id = $this->parceiro_id;		
		$result = $this->getProdutosByRequestInfo();

		if( empty($result) )
		{
			die( json_encode( array( "status" => false, "message" => "Não foram localizados produtos do parceiro $parceiro_id" ) ) );
		}

		die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
	}

	public function detalhado()
	{
		$this->load->model( 'produto_parceiro_regra_preco_model', 'regra_preco');
		$this->load->model( "cotacao_model", "cotacao" );

		$cotacao = null;
		if (!empty($_GET['cotacao_id'])){
			$cotacao = $this->cotacao->get_by_id($_GET['cotacao_id']);
		}

		$aProduto = $this->getProdutosByRequestInfo();		
		foreach($aProduto as $i => $produto){
			$produto_parceiro_id = $produto["produto_parceiro_id"];
			$calculo = [];
			$produto["planos"] = $this->produto_parceiro_plano->PlanosHabilitados($this->parceiro_id, $produto_parceiro_id, null);
			if(!empty($cotacao) && $cotacao["produto_parceiro_id"] == $produto_parceiro_id){
				$calculo = $this->regra_preco->calculo( true, ['cotacao_id' => $cotacao["cotacao_id"]], true );
			}

			foreach($produto["planos"] as $j => $plano){
				$produto["planos"][$j]["coberturas"] = $this->cobertura_plano
				->with_cobertura()
				->with_cobertura_tipo()
				->with_parceiro()
				->filter_by_produto_parceiro_plano($plano["produto_parceiro_plano_id"])
				->order_by('cobertura_plano.ordem')
				->get_all();

				if (!empty($calculo)) {
					$idxF = app_search( $calculo['planos'], $plano["produto_parceiro_plano_id"], 'produto_parceiro_plano_id' );
					if ( $idxF >= 0 ) {
						$produto["planos"][$j]["valores"] = $calculo['planos'][$idxF]["valores"];
					} else {
						$produto["planos"][$j]["valores"] = [];
					}
				} else {
					$produto["planos"][$j]["valores"] = [];
				}
			}

			$produto["campos"] = $this->produto_parceiro_plano->getCampos((object)["produto_parceiro_id" => $produto_parceiro_id, "slug" => null, "produto_parceiro_plano_id" => null]);
			$aProduto[$i] = $produto;
		}
		echo json_encode($aProduto);
	}

	private function getProdutosByRequestInfo(){
		$parceiro_id = $this->parceiro_id;
		$produto_id = ( isset( $GET["produto_id"] ) ) ? $GET["produto_id"] : null;
		$slug_produto = ( isset( $GET["slug"] ) ) ? $GET["slug"] : null;
		$result = $this->produto_parceiro->getProdutosByParceiro($parceiro_id, $produto_id, true, $slug_produto);
		return $result;
	}
}
