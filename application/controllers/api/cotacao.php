<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Apolice
*/
class Cotacao extends CI_Controller {
    public $api_key;
    public $usuario_id;
    public $parceiro_id;

    const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
    const FORMA_PAGAMENTO_TRANSF_BRADESCO = 2;
    const FORMA_PAGAMENTO_TRANSF_BB = 7;
    const FORMA_PAGAMENTO_CARTAO_DEBITO = 8;
    const FORMA_PAGAMENTO_BOLETO = 9;
    const FORMA_PAGAMENTO_FATURADO = 10;
    const FORMA_PAGAMENTO_CHECKOUT_PAGMAX = 11;


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

        $campos = $this->produto_parceiro_campo->with_campo()->with_campo_tipo()->filter_by_produto_parceiro( $produto_parceiro_id )->filter_by_campo_tipo_slug( "cotacao" )->order_by( "ordem", "ASC" )->get_all();

        $erros = array();

        $validacao = array();
        foreach( $campos as $campo ) {
            $validacao[] = array(
            "field" => $campo["campo_nome_banco"],
            "label" => $campo["campo_nome"],
            "rules" => $campo["validacoes"],
            "groups" => "cotacao",
            "value" => isset($POST[$campo["campo_nome_banco"]]) ? $POST[$campo["campo_nome_banco"]] : ""
            );
        }

        $validacao_ok = true;
        foreach( $validacao as $check ) {
            if( strpos( $check["rules"], "required" ) !== false && $check["value"] == "" ) {
                $validacao_ok = false;
                $erros[] = $check;
            }
              // Validando dados dos campos - ALR
            switch ($check["field"]) {
                
                case 'cnpj_cpf':
                    # code...
                    if(strlen($check["value"]) == 11)
                    {
                        if(!(app_validate_cpf($check["value"])))
                        {
                            $validacao_ok = false;
                            $erros[] = $check;
                            break;
                        } 
                    }
                    else if(strlen($check["value"]) == 14)
                    {
                        if(!(app_validate_cnpj($check["value"])))
                        {
                            $validacao_ok = false;
                            $erros[] = $check;
                            break;
                        }
                    }
                default:
                    # code...
                    break;
            }
        }

        if (!empty($POST['produto_parceiro_plano_id'])) {
            $valid = $this->produto_parceiro_plano->verifica_tempo_limite_de_uso($POST['produto_parceiro_id'], $POST['produto_parceiro_plano_id'], $POST['nota_fiscal_data']);

            if (!empty($valid)) {
                die( json_encode( array( "status" => false, "message" => $valid ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
            }
        }

        if( $validacao_ok ) {
            $this->session->set_userdata( "cotacao_{$produto_parceiro_id}", $POST );

            $cotacao_id = (int)$cotacao_id;

            if( $produto["produto_slug"] == "equipamento" ) {
                $cotacao_id = $this->cotacao_equipamento->insert_update( $produto_parceiro_id, $cotacao_id );
                $cotacao_itens = $this->cotacao_equipamento->get_by( array( "cotacao_id" => $cotacao_id ) );
            } else if( $produto["produto_slug"] == "generico" || $produto["produto_slug"] == "seguro_saude" ) {
                $cotacao_id = $this->cotacao_generico->insert_update( $produto_parceiro_id, $cotacao_id );
                $cotacao_itens = $this->cotacao_generico->get_by( array( "cotacao_id" => $cotacao_id ) );
            }
            $result["cotacao_id"] = $cotacao_id;

            $cotacao = $this->cotacao->get_by_id($cotacao_id);
            $cotacao["detalhes"] = $cotacao_itens;
            $cotacao["status"] = true;
            $cotacao["message"] = "Validação OK"; 
            ob_clean();
            die( json_encode( $cotacao, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        } else {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Erro de validação", "erros" => $erros ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        }
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

        $cotacao_id = null;
        if( !isset( $GET["cotacao_id"] ) ) {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Campo cotacao_id é obrigatório" ) ) );
        }
        $cotacao_id = $GET["cotacao_id"];

        $produto_parceiro_id = issetor( $GET["produto_parceiro_id"], null );
        $equipamento_marca_id = issetor( $GET["equipamento_marca_id"] , null);
        $equipamento_categoria_id = issetor( $GET["equipamento_categoria_id"] , null);
        $quantidade = issetor( $GET["quantidade"] , null);
        $coberturas = issetor( $GET["coberturas"] , null);
        $repasse_comissao = issetor( $GET["repasse_comissao"] , 0);
        $desconto_condicional = issetor( $GET["desconto_condicional"] , 0);
        $result = array();

        if( is_null( $produto_parceiro_id ) ) {
            $cotacao = $this->cotacao->get_by_id( $cotacao_id );
            $produto_parceiro_id = $cotacao["produto_parceiro_id"];
        }
        $produto = $this->current_model->with_produto()->get($produto_parceiro_id);

        $params["cotacao_id"] = $cotacao_id;
        $params["produto_parceiro_id"] = $produto_parceiro_id;
        $params["parceiro_id"] = $this->parceiro_id;

        $params["equipamento_marca_id"] = $equipamento_marca_id;
        $params["equipamento_categoria_id"] = $equipamento_categoria_id;
        $params["quantidade"] = $quantidade;
        $params["coberturas"] = $coberturas;
        $params["repasse_comissao"] = $repasse_comissao;
        $params["desconto_condicional"] = $desconto_condicional;

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

        $produto_parceiro_plano_id = $cotacao_itens["produto_parceiro_plano_id"];
        $equipamento_marca_id = issetor( $POST["equipamento_marca_id"] , null);
        $equipamento_categoria_id = issetor( $POST["equipamento_categoria_id"] , null);
        $quantidade = issetor( $POST["quantidade"] , 1);
        $repasse_comissao = issetor( $POST["repasse_comissao"] , 0);
        $desconto_condicional = issetor( $POST["desconto_condicional"] , 0);
        $data_inicio_vigencia = issetor( $POST["data_inicio_vigencia"] , '');

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

        $result = $this->contratar_cotacao( $params, $produto["produto_slug"] );

        ob_clean();
        die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function contratar_cotacao( $params, $produto_slug ) {
        $this->load->model( "produto_parceiro_campo_model", "campo" );
        $this->load->model( "cotacao_equipamento_model", "cotacao_equipamento" );
        $this->load->model( "cliente_model", "cliente" );
        $this->load->model( "cotacao_model", "cotacao" );
        $this->load->model( "localidade_estado_model", "localidade_estado" );

        $produto_parceiro_id = issetor($params['produto_parceiro_id'], 0);
        $produto_parceiro_plano_id = issetor($params['produto_parceiro_plano_id'], 0);
        //$parceiro_id = issetor($params['parceiro_id'], 0);

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
                    "mensagem" => "Cotação inválida (001)",
                    "errors" => array(),
                );
                return $result;
            }
            $this->session->set_userdata( "cotacao_{$produto_parceiro_id}", $params );

            if ($produto_slug == 'equipamento') {
                $this->cotacao_equipamento->insert_update( $produto_parceiro_id, $cotacao_id, 3 );
            } else {
                $this->cotacao_generico->insert_update( $produto_parceiro_id, $cotacao_id, 3 );
            }

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

        $validacao = $this->campo->setValidacoesCamposPlano( $produto_parceiro_id, "dados_segurado", $produto_parceiro_plano_id );
        $campos = $this->produto_parceiro_campo->with_campo()->with_campo_tipo()->filter_by_produto_parceiro( $produto_parceiro_id )->filter_by_campo_tipo_slug( "dados_segurado" )->order_by( "ordem", "ASC" )->get_all();

        $erros = array();
        $validacao_ok = true;
        foreach( $campos as $campo ) {
            if( strpos( $campo["validacoes"], "required" ) !== false ) {
                if( !isset( $cotacao_salva[$campo["campo_nome_banco_equipamento"]] ) ) {
                    $erros[] = "O campo " . $campo["campo_nome_banco_equipamento"] . " não foi informado";
                    $validacao_ok = false;
                }
            }
        }

        if( !$validacao_ok || sizeof( $erros ) > 0 ) {
            $result = array(
                "status" => false,
                "mensagem" => $erros,
                "errors" => $erros,
            );
            return $result;
        }

        $validacao = array();
        foreach( $campos as $campo ) {
            $rule_check = "OK";
            if( strpos( $campo["validacoes"], "required" ) !== false && ( $cotacao_salva[$campo["campo_nome_banco_equipamento"]] == "" || is_null( $cotacao_salva[$campo["campo_nome_banco_equipamento"]] ) ) ) {
                $rule_check = "O preenchimento do campo " . $campo["campo_nome_banco_equipamento"] . " é obrigatório";
                $erros[] = $rule_check;
            } else {
                if( $cotacao_salva[$campo["campo_nome_banco_equipamento"]] != "" && $cotacao_salva[$campo["campo_nome_banco_equipamento"]] != "0000-00-00" && !is_null( $cotacao_salva[$campo["campo_nome_banco_equipamento"]] )  ) {
                    if( strpos( $campo["validacoes"], "validate_data" ) !== false ) {
                        $valida_data = date_parse_from_format("Y-m-d", $cotacao_salva[$campo["campo_nome_banco_equipamento"]]);
                        if( !checkdate( $valida_data["month"], $valida_data["day"], $valida_data["year"] ) ) {
                            $rule_check = "Data inválida (" . $campo["campo_nome_banco_equipamento"] . ")";
                            $erros[] = $rule_check;
                        }
                    }
                    if( strpos( $campo["validacoes"], "validate_email" ) !== false ) {
                        $valida_email = filter_var( $cotacao_salva[$campo["campo_nome_banco_equipamento"]], FILTER_VALIDATE_EMAIL );
                        if( !$valida_email ) {
                            $rule_check = "E-mail inválido (" . $campo["campo_nome_banco_equipamento"] . ")";
                            $erros[] = $rule_check;
                        }
                    }
                    if( strpos( $campo["validacoes"], "validate_celular" ) !== false ) {
                        $valida_celular = preg_match( "#^\(\d{2}\) 9?[6789]\d{3}-\d{4}$#", $this->celular( $cotacao_salva[$campo["campo_nome_banco_equipamento"]] ) );
                        if( $valida_celular ) {
                            $rule_check = "Número de telefone celular inválido (" . $campo["campo_nome_banco_equipamento"] . ")";
                            $erros[] = $rule_check;
                        }
                    }
                }
            }

            $validacao[] = array(
                "field" => $campo["campo_nome_banco_equipamento"],
                "label" => $campo["campo_nome"],
                "value" => $cotacao_salva[$campo["campo_nome_banco_equipamento"]],
                "rules" => $campo["validacoes"],
                "rule_check" => $rule_check,
                "groups" => "dados_segurado"
            );
        }
        if( !$validacao_ok || sizeof( $erros ) > 0 ) {
            $result  = array(
                "status" => false,
                "mensagem" => "Cotação inválida (003)",
                "errors" => $erros,
            );
        } else {
            $dados_cotacao["step"] = 4;
            $this->campo->setDadosCampos( $produto_parceiro_id, "equipamento", "dados_segurado", $produto_parceiro_plano_id,  $dados_cotacao );
            $dados_cotacao["produto_parceiro_plano_id"] = $produto_parceiro_plano_id;

            $this->cotacao_equipamento->update( $cotacao_salva["cotacao_equipamento_id"], $dados_cotacao, true );
            //$this->cliente->atualizar( $cotacao_salva["cliente_id"], $dados_cotacao );

            $result  = array(
                "status" => true,
                "mensagem" => "Cotação finalizada. Efetuar pagamento.",
                "cotacao_id" => $cotacao_salva["cotacao_id"],
                "produto_parceiro_id" => $cotacao_salva["produto_parceiro_id"],
                "validacao" => $validacao
            );
        }
        return $result;
    }

    function celular( $number ){
        $number = preg_replace( "/[^0-9]/", "", $number );
        $number = "(" . substr( $number, 0, 2 ) . ") " . substr( $number, 2, -4) . " - " . substr( $number, -4 );
        return $number;
    }

}
