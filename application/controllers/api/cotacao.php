<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Apolice
*/
class Cotacao extends CI_Controller {
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

        $this->load->model( "campo_model", "campos" );
        $this->load->model( "cotacao_model", "cotacao" );
        $this->load->model( "cotacao_equipamento_model", "cotacao_equipamento" );
        $this->load->model( "cotacao_generico_model", "cotacao_generico" );
        $this->load->model( "produto_parceiro_model", "produto_parceiro" );
        $this->load->model( "produto_parceiro_campo_model", "produto_parceiro_campo" );
        $this->load->model( "produto_parceiro_model", "current_model" );
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

    private function post( $POST ) {

        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('cobertura_plano_model', 'cobertura_plano');
        $this->load->model( "produto_parceiro_campo_model", "campo" );

        if( !isset( $POST["produto_parceiro_id"] ) ) {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Campo produto_parceiro_id é obrigatório" ) ) );
        }

        $result = array();
        $produto_parceiro_id = $POST["produto_parceiro_id"];
        $cotacao_id = null;

        if( $_SERVER["REQUEST_METHOD"] === "PUT" ) {
            if( !isset( $POST["cotacao_id"] ) ) {
                ob_clean();
                die( json_encode( array( "status" => false, "message" => "Campo cotacao_id é obrigatório no método PUT" ) ) );
            }
            $cotacao_id = $POST["cotacao_id"];
            } else {
                if( isset( $POST["cotacao_id"] ) ) {
                unset( $POST["cotacao_id"] );
            }
        }

        $produto = $this->produto_parceiro->with_produto()->get( $produto_parceiro_id );
        $POST["parceiro_id"] = $this->parceiro_id;
        $POST["usuario_cotacao_id"] = $this->usuario_id;

        $result = $this->campo->validate_campos( $produto_parceiro_id, "cotacao", $POST );
        if ( empty($result['status']) ) {
            ob_clean();
            die( json_encode( $result , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        }

        $coberturas_adicionais = null;
        if (!empty($POST['produto_parceiro_plano_id'])) {
            $POST['nota_fiscal_data'] = issetor($POST['nota_fiscal_data'], null);
            $valid = $this->produto_parceiro_plano->verifica_tempo_limite_de_uso($POST['produto_parceiro_id'], $POST['produto_parceiro_plano_id'], $POST['nota_fiscal_data']);

            if (!empty($valid)) {
                die( json_encode( array( "status" => false, "message" => $valid ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
            }

            // Gera o registro de coberturas adicionais
            if ( !empty($POST["coberturas_opcionais"]) && is_array($POST["coberturas_opcionais"]))
            {
                $coberturas = $this->cobertura_plano->filter_adicional_by_cobertura_slug($cotacao_id, $POST["coberturas_opcionais"], $POST['produto_parceiro_plano_id'])->get_all();
                foreach ($coberturas as $cobAdd) {
                    $coberturas_adicionais[] = $cobAdd['cobertura_plano_id'] .";". $cobAdd['preco'];
                }
            }
        }
        unset( $POST["coberturas_opcionais"] );

        $this->session->set_userdata( "cotacao_{$produto_parceiro_id}", $POST );
        $cotacao_id = (int)$cotacao_id;

        if( $produto["produto_slug"] == "equipamento" ) {
            $cotacao_id = $this->cotacao_equipamento->insert_update( $produto_parceiro_id, $cotacao_id, 1, $coberturas_adicionais );
            $cotacao_itens = $this->cotacao_equipamento->get_by( array( "cotacao_id" => $cotacao_id ) );
        } else {
            $cotacao_id = $this->cotacao_generico->insert_update( $produto_parceiro_id, $cotacao_id, 1, $coberturas_adicionais );
            $cotacao_itens = $this->cotacao_generico->get_by( array( "cotacao_id" => $cotacao_id ) );
        }
        $result["cotacao_id"] = $cotacao_id;

        $cotacao = $this->cotacao->get_by_id($cotacao_id);
        $cotacao["detalhes"] = $cotacao_itens;
        $cotacao["status"] = true;
        $cotacao["message"] = "Validação OK"; 
        ob_clean();
        die( json_encode( $cotacao, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

    }

    private function get( $GET ) {
        $cotacao_id = null;
        if( !isset( $GET["cotacao_id"] ) ) {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Campo produto_parceiro_id é obrigatório" ) ) );
        } else {
            $cotacao_id = $GET["cotacao_id"];
            $cotacao = $this->cotacao->get_by_id( $cotacao_id );
            $produto = $this->produto_parceiro->with_produto()->get( $cotacao["produto_parceiro_id"] );
            if( $produto ) {
                $produto_slug = $produto["produto_slug"];
                switch( $produto_slug ) {
                    case "equipamento":
                        $cotacao_itens = $this->cotacao_equipamento->get_by( array( "cotacao_id" => $cotacao_id ) );
                        break;
                    case "generico":
                    case "seguro_saude":
                        $cotacao_itens = $this->cotacao_generico->get_by( array( "cotacao_id" => $cotacao_id ) );
                        break;
                }
                ob_clean();
                die( json_encode( $cotacao_itens, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
            }
            ob_clean();
            die( json_encode( $cotacao, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        }
    }

    private function put() {

    }

    public function calculo() {
        if( $_SERVER["REQUEST_METHOD"] !== "GET" ) {
        ob_clean();
        die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        $GET = $_GET;
        $this->load->model( "cotacao_model", "cotacao" );
        $this->load->model( "produto_parceiro_campo_model", "produto_parceiro_campo" );
        $this->load->model( 'produto_parceiro_regra_preco_model', 'regra_preco');
        $this->load->model( 'cobertura_plano_model', 'cobertura_plano');
        $this->load->model( 'cotacao_generico_cobertura_model', 'cotacao_generico_cobertura');

        $cotacao_id = null;
        if( !isset( $GET["cotacao_id"] ) ) {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Campo cotacao_id é obrigatório" ) ) );
        }

        $result = array();
        $cotacao_id                     = $GET["cotacao_id"];
        $produto_parceiro_id            = issetor( $GET["produto_parceiro_id"], null );
        $equipamento_id                 = issetor( $GET["equipamento_id"] , null);
        $equipamento_marca_id           = issetor( $GET["equipamento_marca_id"] , null);
        $equipamento_categoria_id       = issetor( $GET["equipamento_categoria_id"] , null);
        $equipamento_de_para            = issetor( $GET["equipamento_de_para"] , null);
        $equipamento_sub_categoria_id   = issetor( $GET["equipamento_sub_categoria_id"] , null);
        $quantidade                     = issetor( $GET["quantidade"] , 1);
        $coberturas                     = issetor( $GET["coberturas_opcionais"] , null);
        $repasse_comissao               = issetor( $GET["repasse_comissao"] , 0);
        $desconto_condicional           = issetor( $GET["desconto_condicional"] , 0);
        $comissao_premio                = issetor( $GET["comissao_premio"] , 0);

        if( is_null( $produto_parceiro_id ) ) {
            $cotacao = $this->cotacao->get_by_id( $cotacao_id );
            $produto_parceiro_id = $cotacao["produto_parceiro_id"];
        }
        $produto = $this->current_model->with_produto()->get($produto_parceiro_id);
        if ( $produto['produto_slug'] == 'equipamento') {
            $cotacao_aux = $this->cotacao_equipamento->get_by(['cotacao_id' => $cotacao_id]);
        } else {
            $cotacao_aux = $this->cotacao_generico->get_by(['cotacao_id' => $cotacao_id]);
        }

        $params["cotacao_id"]                   = $cotacao_id;
        $params["produto_parceiro_id"]          = $produto_parceiro_id;
        $params["parceiro_id"]                  = $this->parceiro_id;
        $params["equipamento_id"]               = emptyor($equipamento_id, $cotacao_aux['equipamento_id']);
        $params["equipamento_marca_id"]         = emptyor($equipamento_marca_id, $cotacao_aux['equipamento_marca_id']);
        $params["equipamento_categoria_id"]     = emptyor($equipamento_categoria_id, $cotacao_aux['equipamento_categoria_id']);
        $params["equipamento_sub_categoria_id"] = emptyor($equipamento_sub_categoria_id, $cotacao_aux['equipamento_sub_categoria_id']);
        $params["equipamento_de_para"]          = emptyor($$equipamento_de_para, $cotacao_aux['equipamento_de_para']);
        $params["quantidade"]                   = $quantidade;
        $params["repasse_comissao"]             = emptyor($repasse_comissao, $cotacao_aux['repasse_comissao']);
        $params["desconto_condicional"]         = emptyor($desconto_condicional, $cotacao_aux['desconto_condicional']);
        $params["comissao_premio"]              = emptyor($comissao_premio, $cotacao_aux['comissao_premio']);

        if ( !empty($params["coberturas_opcionais"]) && is_array($params["coberturas_opcionais"]))
        {
            $coberturas = $this->cobertura_plano->filter_adicional_by_cobertura_slug($cotacao_id, $params["coberturas_opcionais"])->get_all();
            foreach ($coberturas as $cobAdd) {
                $params["coberturas"][] = $cobAdd['cobertura_plano_id'] .";". $cobAdd['preco'];
            }
        } else {
            $coberturas = $this->cotacao_generico_cobertura->with_cotacao($cotacao_id)->get_all();
            foreach ($coberturas as $cobAdd) {
                $params["coberturas"][] = $cobAdd['produto_parceiro_plano_id'] .";". $cobAdd['cobertura_plano_id'];
            }
        }

        $result = $this->regra_preco->calculo_plano( $params, true );

        ob_clean();
        die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    private function getValorCoberturaAdicionalGenerico($produto_parceiro_plano_id, $cobertura_plano_id, $qntDias){
        $this->load->model('cobertura_plano_model', 'cobertura_plano');

        $cobertura = $this->cobertura_plano->get_by(array(
            'produto_parceiro_plano_id' => $produto_parceiro_plano_id,
            'cobertura_plano_id' => $cobertura_plano_id,
        ));

        if($cobertura){
            return (app_calculo_porcentagem($cobertura['porcentagem'],$cobertura['preco'])*$qntDias);
        }else{
            return 0;
        }

    }

    public function contratar() {

        if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
            $POST = json_decode( file_get_contents( "php://input" ), true );
        } else {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        if( !isset( $POST["cotacao_id"] ) ) {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Campo cotacao_id é obrigatório" ) ) );
        }
        $cotacao_id = $POST["cotacao_id"];

        $cotacao = $this->cotacao->get_by_id( $cotacao_id );
        $produto_parceiro_id = $cotacao["produto_parceiro_id"];
        $produto = $this->current_model->with_produto()->get($produto_parceiro_id);

        if( $produto["produto_slug"] == "equipamento" ) {
            $cotacao_itens = $this->cotacao_equipamento->get_by( array( "cotacao_id" => $cotacao_id ) );
        } else {
            $cotacao_itens = $this->cotacao_generico->get_by( array( "cotacao_id" => $cotacao_id ) );
        }

        $params = $POST;

        $produto_parceiro_plano_id = issetor( issetor( $POST["produto_parceiro_plano_id"], $cotacao_itens["produto_parceiro_plano_id"]), 0);
        $equipamento_marca_id = issetor(issetor( $POST["equipamento_marca_id"], $cotacao_itens["equipamento_marca_id"]) , null);
        $equipamento_categoria_id = issetor(issetor( $POST["equipamento_categoria_id"], $cotacao_itens["equipamento_categoria_id"]) , null);
        $quantidade = issetor(issetor( $POST["quantidade"], $cotacao_itens["quantidade"]) , 1);
        $repasse_comissao = issetor(issetor( $POST["repasse_comissao"], $cotacao_itens["repasse_comissao"]) , 0);
        $desconto_condicional = issetor(issetor( $POST["desconto_condicional"], $cotacao_itens["desconto_condicional"]) , 0);
        $data_inicio_vigencia = issetor(issetor( $POST["data_inicio_vigencia"], $cotacao_itens["data_inicio_vigencia"]) , null);

        $params["cotacao_id"] = $cotacao_id;
        $params["produto_parceiro_id"] = $produto_parceiro_id;
        $params["produto_parceiro_plano_id"] = $produto_parceiro_plano_id;
        $params["parceiro_id"] = $this->parceiro_id;
        $params["usuario_cotacao_id"] = $this->usuario_id;
        $params["quantidade"] = $quantidade;
        $params["repasse_comissao"] = $repasse_comissao;
        $params["desconto_condicional"] = $desconto_condicional;
        $params["data_inicio_vigencia"] = $data_inicio_vigencia;

        if( $produto["produto_slug"] == "equipamento" ) {
            $params["equipamento_marca_id"] = $equipamento_marca_id;
            $params["equipamento_categoria_id"] = $equipamento_categoria_id;
        }

        if ( isset($params['coberturas_opcionais']) ) {
            unset($params['coberturas_opcionais']);
        }

        $result = $this->contratar_cotacao( $params, $produto["produto_slug"] );

        if(ob_get_length() > 0) {
            ob_clean();
        }
        die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function contratar_cotacao( $params, $produto_slug ) {
        $this->load->model( "produto_parceiro_campo_model", "campo" );
        $this->load->model( "cotacao_equipamento_model", "cotacao_equipamento" );
        $this->load->model( "cotacao_generico_model", "cotacao_generico" );
        $this->load->model( "cotacao_seguro_viagem_model", "cotacao_seguro_viagem" );
        $this->load->model( "cliente_model", "cliente" );
        $this->load->model( "cotacao_model", "cotacao" );
        $this->load->model( "pedido_model", "pedido" );
        $this->load->model( "apolice_model", "apolice" );
        $this->load->model( "localidade_estado_model", "localidade_estado" );
        $this->load->model( "capitalizacao_model", "capitalizacao" );

        //$parceiro_id = issetor($params['parceiro_id'], 0);
        $produto_parceiro_id = issetor($params['produto_parceiro_id'], 0);
        $produto_parceiro_plano_id = issetor($params['produto_parceiro_plano_id'], 0);
        $repasse_comissao = $params["repasse_comissao"];
        $desconto_condicional= $params["desconto_condicional"];
        $cotacao_id = issetor($params['cotacao_id'], 0);

        $result  = array(
            'status' => false,
            'mensagem' => 'Contratação não finalizada',
        );

        if( $cotacao_id > 0 ) {

            if( $this->cotacao->isCotacaoValida( $cotacao_id ) == FALSE ) {
                $result  = array(
                    "status" => false,
                    "mensagem" => "O status da Cotação não permite esta operação",
                    "errors" => array(),
                );
                return $result;
            }

            // Define qual o auxiliar
            if ( $produto_slug == "equipamento" ) {
                $cotacao_aux = $this->cotacao_equipamento;
                $cotacao_aux_id = "cotacao_equipamento_id";
            } elseif ( $produto_slug == "seguro_viagem" ) {
                $cotacao_aux = $this->cotacao_seguro_viagem;
                $cotacao_aux_id = "cotacao_seguro_viagem_id";
            } else {
                $cotacao_aux = $this->cotacao_generico;
                $cotacao_aux_id = "cotacao_generico_id";
            }

            $this->session->set_userdata( "cotacao_{$produto_parceiro_id}", $params );
            $cotacao_aux->insert_update( $produto_parceiro_id, $cotacao_id, 3 );

            unset( $params["parceiro_id"] );
        } else {
            $result = array(
                "status" => false,
                "mensagem" => "Cotacao_id não informado",
                "errors" => array(),
            );
            return $result;
        }

        $cotacao_salva = null;
        if( $produto_slug == "equipamento" ) {
            $cotacao_salva = $this->cotacao->with_cotacao_equipamento();
        } else {
            $cotacao_salva = $this->cotacao->with_cotacao_generico();
        }  
        $cotacao_salva = $cotacao_salva
            ->filterByID($cotacao_id)
            ->get_all(0,0,false);

        $cotacao_salva = $cotacao_salva[0];

        $result = $this->campo->validate_campos( $produto_parceiro_id, ["cotacao", "dados_segurado"], $cotacao_salva );
        if ( empty($result['status']) ) {
            return $result;
        }

        // Salva o plano na cotação
        $dados_cotacao["produto_parceiro_plano_id"] = $produto_parceiro_plano_id;
        $cotacao_aux->update( $cotacao_salva[$cotacao_aux_id], $dados_cotacao, true );

        // valida Capitalização
        $capitalizacao = $this->capitalizacao->validaNumeroSorte($cotacao_salva["cotacao_id"]);
        if ( empty($capitalizacao['status']) ) {

            $result = $capitalizacao;

        } else {

            $dados_cotacao["step"] = 4;
            $this->campo->setDadosCampos( $produto_parceiro_id, "equipamento", "dados_segurado", $produto_parceiro_plano_id,  $dados_cotacao );
            $cotacao_aux->update( $cotacao_salva[$cotacao_aux_id], $dados_cotacao, true );

            $result  = array(
                "status" => true,
                "mensagem" => "Cotação finalizada com Sucesso.",
                "produto_parceiro_id" => $cotacao_salva["produto_parceiro_id"],
                "cotacao_id" => $cotacao_salva["cotacao_id"],
                "validacao" => $result['validacao'],
            );

            // Valida config do produto para definir se gera apólice ao contratar ou não
            $pedido = $this->pedido->filter_by_cotacao($cotacao_id)->get_all();
            if (!empty($pedido)) {
                $pedido_id = $pedido[0]['pedido_id'];
                $apolice_id = $this->apolice->insertApolice($pedido_id, 'contratar');
                $result['apolice_id'] = $apolice_id;
                $result['pedido_id'] = $pedido_id;
            }

        }

        return $result;
    }

    public function listPendentes()
    {
        $POST = json_decode( file_get_contents( "php://input" ), true );

        if( empty($POST["documento"]) && empty($POST["cotacao_id"]) ) {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Informe o Documento ou o ID da Cotação" ) ) );
        }

        $cotacao_id = issetor($POST["cotacao_id"], '');
        $documento = issetor($POST["documento"], '');
        $documento = preg_replace( "/[^0-9]/", "", $documento );

        $cotacao = $this->cotacao->getCotacaoByDoc( $documento, $cotacao_id );

        ob_clean();
        die( json_encode( ['status' => true, 'itens' => $cotacao], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    function celular( $number ){
        $number = preg_replace( "/[^0-9]/", "", $number );
        $number = "(" . substr( $number, 0, 2 ) . ") " . substr( $number, 2, -4) . " - " . substr( $number, -4 );
        return $number;
    }

}
