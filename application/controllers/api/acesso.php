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

    public function index($GET = null) {

        if (empty($GET)){
            $GET = $_GET;
        }

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

    public function chave() {

        $this->load->model( "parceiro_model", "parceiro" );
        $this->load->model( "usuario_model", "usuario" );
        $this->load->model( "usuario_webservice_model", "webservice" );

        if( !isset( $_POST["chaveacesso"] ) ) {
            die( json_encode( array( "status" => false, "message" => "Campo chaveacesso é obrigatório" ) ) );
        } else {
            $chaveacesso = $_POST["chaveacesso"];
        }

        if( !isset( $_POST["senhaacesso"] ) ) {
            die( json_encode( array( "status" => false, "message" => "Campo senhaacesso é obrigatório" ) ) );
        } else {
            $senhaacesso = $_POST["senhaacesso"];
        }

        $parceiro[0] = $this->parceiro->get_by( array( "api_key" => $chaveacesso, "api_senha" => $senhaacesso ) );
        if( empty($parceiro) ) {
            die( json_encode( array( "status" => false, "message" => "Parceiro não encontrado" ) ) );
        }

        if( count($parceiro) > 1) {
            die( json_encode( array( 'x' => count($parceiro), "status" => false, "message" => "Falha na configuração do parceiro. Consulte o administrador", 'sql' => $this->db->last_query() ) ) );
        }

        $parceiro_id = $parceiro[0]['parceiro_id'];

        $usuario = $this->usuario->get_user_externo( $parceiro_id );
        if( empty($usuario) ) {
            die( json_encode( array( "status" => false, "message" => "Usuário não cadastrado" ) ) );
        }

        $email = $usuario[0]['email'];
        $senha = $usuario[0]['senha'];

        $this->index(['email' => $email, 'senha' => $senha]);
    }

}
