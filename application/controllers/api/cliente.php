<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Cliente
*/
class Cliente extends CI_Controller {
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
                ob_clean();
                die( json_encode( array( "status" => false, "message" => "APIKEY is invalid" ) ) );
            }
        } else {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
        }
        $this->usuario_id = $webservice["usuario_id"];
        $this->parceiro_id = $webservice["parceiro_id"];

        $this->load->database('default');
        $this->load->model( "cliente_model", "cliente" );
    }

    public function index() {
        if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
            $GET = $_GET;
            $x = $this->get( $GET );
        } else {
            if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
                $POST = json_decode( file_get_contents( "php://input" ), true );
                $x = $this->post( $POST );
            } else {
                if( $_SERVER["REQUEST_METHOD"] === "PUT" ) {
                    $PUT = json_decode( file_get_contents( "php://input" ), true );
                    $x = $this->post( $PUT );
                } else {
                    ob_clean();
                    die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
                }
            }
        }
    }

    private function post( $POST )
    {
        $this->load->model('cliente_evolucao_status_model', 'cliente_evolucao_status');
        $this->load->model('cliente_contato_model', 'cliente_contato');

        if( !isset( $POST["produto_parceiro_id"] ) )
        {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Campo produto_parceiro_id é obrigatório" ) ) );
        }

        if( empty($POST["documento"]) )
        {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Campo Documento (CPF / CNPJ) é obrigatório" ) ) );
        }

        if ( empty($POST['codigo']) )
        {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "O status do Mailing é obrigatório" ) ) );
        }

        // padrao esperado
        $data["produto_parceiro_id"] = $POST["produto_parceiro_id"];
        $data["parceiro_id"] = $this->parceiro_id;

        $data['DADOS_CADASTRAIS']['CPF'] = $POST['documento'];
        $data['DADOS_CADASTRAIS']['CODIGO'] = emptyor($POST['codigo'], NULL);
        $data['DADOS_CADASTRAIS']['NOME'] = emptyor($POST['nome'], '');
        $data['DADOS_CADASTRAIS']['SEXO'] = emptyor($POST['sexo'], '');
        $data['DADOS_CADASTRAIS']['DATANASC'] = emptyor($POST['data_nascimento'], NULL);

        if ( !empty($POST['cliente_evolucao_status_id']) )
        {
            $data['DADOS_CADASTRAIS']['STATUS'] = $POST['cliente_evolucao_status_id'];
        }
        elseif ( !empty($POST['status']) )
        {
            // pesquisar o dia do status pelo texto recebido
            $statusEvolucao = $this->cliente_evolucao_status->filter_by_descricao($POST['status'])->get_all();
            if ( !empty($statusEvolucao) )
            {
                $data['DADOS_CADASTRAIS']['STATUS'] = $statusEvolucao[0]['cliente_evolucao_status_id'];
            }
        }
        // $data_cliente['colaborador_id']             = 1;
        // $data_cliente['colaborador_comercial_id']   = 1;
        // $data_cliente['titular']                    = 1;
        // $data_cliente['grupo_empresarial_id']       = 0;

        if ( !empty($POST['enderecos']) )
        {
            foreach ($POST['enderecos'] as $endereco) {
                 $data['ENDERECOS'][] = [
                    'UF' => $endereco['uf'],
                    'CIDADE' => $endereco['cidade'],
                ];
            }
        }
        if ( !empty($POST['telefone']) )
        {
            // valida o melhor horario
            $melhor_horario = 'Q';
            if ( !empty($POST['melhor_horario']) )
            {
                $melhor_horario = $this->cliente_contato->melhorHorario( $POST['melhor_horario'], 'nome', 'slug' );
            }

            $data['TELEFONES'][] = [
                'RANKING' => 1,
                'MELHOR_HORARIO' => $melhor_horario,
                'TELEFONE' => $POST['telefone'],
            ];
        }

        if ( !empty($POST['telefone_2']) )
        {
            $data['TELEFONES'][] = [
                'RANKING' => 2,
                'TELEFONE' => $POST['telefone_2'],
            ];
        }
        // $data['TELEFONES'][0]['cliente_contato_nivel_relacionamento_id'] = 3;
        // $data['TELEFONES'][0]['decisor']                                 = 1;

        if ( !empty($POST['email']) )
        {
            $data['EMAILS'][]['EMAIL'] = $POST['email'];
        }

        $result = $this->cliente->cliente_insert_update($data, $data["produto_parceiro_id"]);
        if ( empty($result) ) {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "O cliente não pode ser cadastrado" ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        }

        $result = array();
        $result["status"] = true;
        $result["message"] = "Cliente Registrado com Sucesso"; 
        ob_clean();
        die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

    }

}
