<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Apolice
*/
class Enquete extends CI_Controller {
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

        if( !isset( $GET["apolice_id"] ) ) {
            die( json_encode( array( "status" => false, "message" => "Campo apolice_id é obrigatório" ) ) );
        }

        $this->load->model( "enquete_resposta_model", "enquete_resposta" );
        $this->load->model( "enquete_resposta_pergunta_model", "enquete_resposta_pergunta" );

        $apolice_id = $GET["apolice_id"];
        $resposta = $GET["resposta"];
        $mensagem = $GET["mensagem"];

        $sql = "
                
                select apolice.*, produto_parceiro_configuracao.enquete_id from
                apolice 
                inner join produto_parceiro_plano on apolice.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id and produto_parceiro_plano.deletado = 0
                inner join produto_parceiro on produto_parceiro.produto_parceiro_id = produto_parceiro_plano.produto_parceiro_id and produto_parceiro.deletado = 0
                inner join produto_parceiro_configuracao on produto_parceiro_configuracao.produto_parceiro_id = produto_parceiro.produto_parceiro_id and produto_parceiro_configuracao.deletado = 0 
                where 
                apolice.apolice_id = {$apolice_id}
                 and produto_parceiro_configuracao.enquete_id <> 0        
        ";
        $apolice = $this->db->query( $sql )->result_array();
        if( sizeof( $apolice ) ) {

            $apolice = $apolice[0];


            $pergunta_zero_a_dez = $this->db->query( "select * from enquete_pergunta where enquete_pergunta.deletado = 0 and enquete_pergunta.enquete_id = {$data['enquete_id']} and enquete_pergunta.tipo = 'zero_a_dez'" )->result_array();

            if(! sizeof( $pergunta_zero_a_dez ) ) {
                die( json_encode( array( "status" => false, "message" => "pergunta Zero a Dez da Enquete não encontrado" ) ) );
            }

            $pergunta_mensagem = $this->db->query( "select * from enquete_pergunta where enquete_pergunta.deletado = 0 and enquete_pergunta.enquete_id = {$data['enquete_id']} and enquete_pergunta.tipo = 'texto'" )->result_array();

            if(! sizeof( $pergunta_mensagem ) ) {
                die( json_encode( array( "status" => false, "message" => "pergunta Mensagem da Enquete não encontrado" ) ) );
            }


            $data = array();

            $data['enquete_id'] = $apolice['enquete_id'];
            $data['enquete_configuracao_id'] = 1;
            $data['apolice_id'] = $apolice_id;
            $data['tentativas_envio'] = 1;
            $data['respondido'] = 'total';
            $data['data_enviada'] = date('Y-m-d H:i:s');
            $data['data_respondido'] = date('Y-m-d H:i:s');
            $data['criacao'] = date('Y-m-d H:i:s');
            $data['alteracao'] = date('Y-m-d H:i:s');
            $data['deletado'] = 0;
            $data['locked'] = 0;
            $data['alteracao_usuario_id'] = 0;
            $data['ultimo_erro'] = '';

            $enquete_resposta_id = $this->enquete_resposta->insert($data, TRUE);

            if(!$enquete_resposta_id){
                die( json_encode( array( "status" => false, "message" => "Erro incluindo Resposta da Enquete" ) ) );
            }

            $data = array();
            $data['enquete_resposta_id'] = $enquete_resposta_id;
            $data['enquete_pergunta_id'] = $pergunta_zero_a_dez[0]['enquete_pergunta_id'];
            $data['resposta'] = $resposta;
            $data['respondida'] = 1;
            $data['criacao'] = date('Y-m-d H:i:s');
            $data['alteracao'] = date('Y-m-d H:i:s');
            $data['deletado'] = 0;
            $data['alteracao_usuario_id'] = 0;

            $this->enquete_resposta_pergunta->insert($data, TRUE);

            $data['enquete_pergunta_id'] = $pergunta_mensagem[0]['enquete_pergunta_id'];
            $data['resposta'] = $mensagem;

            $this->enquete_resposta_pergunta->insert($data, TRUE);

            die( json_encode( array( 
                "status" => true,
                "message" => "Enquete respondida com sucesso!"
            )));
        } else {          	
        	if( !isset( $GET["ret"] ) ) { /*rcarpi - gambiarra para nao gerar erro no header ao consumit o metodo*/
            	die( json_encode( array( "status" => false, "message" => "Apólice ou Enquete não encontrado" ) ) );
            }
        }    
    }


}


