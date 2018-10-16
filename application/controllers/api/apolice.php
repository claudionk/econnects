<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apolice extends CI_Controller {

    public function __construct() {
        parent::__construct();

        header( "Access-Control-Allow-Origin: *" );
        header( "Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS" );
        header( "Access-Control-Allow-Headers: Cache-Control, APIKEY, apikey, Content-type" );
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

        $this->load->model('apolice_model', 'apolice');
        $this->load->model('apolice_status_model', 'apolice_status');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model("fatura_model", "fatura");
        $this->load->model("fatura_parcela_model", "fatura_parcela");

        $this->load->helper("api_helper");
    }

    private function update( $apolice_id, $num_apolice ) {
        $this->db->query("UPDATE apolice SET num_apolice='$num_apolice' WHERE apolice_id=$apolice_id" );
        $result = $this->db->query("SELECT * FROM apolice WHERE apolice_id=$apolice_id" )->result_array();
        die( json_encode( array( "status" => (bool)sizeof($result), "apolice" => $result ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function index() {
        if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
            $GET = $_GET;
        } else {
            if ($_SERVER["REQUEST_METHOD"] === "POST" ){
                $GET = $_POST;
            } elseif( $_SERVER["REQUEST_METHOD"] === "PUT" ) {
                $PUT = json_decode( file_get_contents( "php://input" ), true );
                if( !isset( $PUT["apolice_id"] ) ) {
                    die( json_encode( array( "status" => false, "message" => "Campo apolice_id é obrigatório" ) ) );
                }
                if( !isset( $PUT["num_apolice"] ) ) {
                    die( json_encode( array( "status" => false, "message" => "Campo num_apolice é obrigatório" ) ) );
                }
                $apolice_id = $PUT["apolice_id"];
                $num_apolice = $PUT["num_apolice"];

                $this->update( $apolice_id, $num_apolice );
            } else {
                die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
            }
        }

        $apolice_id = null;
        if( isset( $GET["apolice_id"] ) ) {
            $apolice_id = $GET["apolice_id"];
        } 


        $num_apolice = null;
        if( isset( $GET["num_apolice"] ) ) {
            $num_apolice = $GET["num_apolice"];
        }

        $documento = null;
        if( isset( $GET["documento"] ) ) {
            $documento = $GET["documento"];
        }

        $pedido_id = null;
        if( isset( $GET["pedido_id"] ) ) {
            $pedido_id = $GET["pedido_id"];
        }

        $params = array();

        $params["apolice_id"] = $apolice_id;
        $params["num_apolice"] = $num_apolice;
        $params["documento"] = $documento;
        $params["pedido_id"] = $pedido_id;

        if($apolice_id || $num_apolice || $documento || $pedido_id ) {
            $pedidos = $this->pedido
            ->with_pedido_status()
            ->with_cotacao_cliente_contato()
            ->with_apolice()
            ->with_fatura()
            ->filterNotCarrinho()
            ->filterAPI($params)
            ->get_all();
            //   print_r($this->db->last_query());exit;
            // echo "<pre>";print_r($pedidos);echo "</pre>";

            if($pedidos) {

                foreach ($pedidos as $pedido) {
                    //Monta resposta da apólice
                    $apolice = $this->apolice->getApolicePedido( $pedido["pedido_id"] );
                    $apolice[0]["inadimplente"] = ($this->pedido->isInadimplente( $pedido["pedido_id"] ) === false ) ? 0 : 1;


                    $faturas = $this->fatura->filterByPedido($pedido["pedido_id"])
                    ->with_fatura_status()
                    ->with_pedido()
                    ->order_by("data_processamento")
                    ->get_all();

                    foreach ($faturas as $index => $fatura) {
                        $faturas[$index]["parcelas"] = $this->fatura_parcela->with_fatura_status()
                            ->filterByFatura($fatura["fatura_id"])
                            ->order_by("num_parcela")
                            ->get_all();
                    }

                    $resposta[] = array(
                        "apolice" => api_retira_timestamps($apolice),
                        "faturas" => api_retira_timestamps($faturas),
                        "pedido" => api_retira_timestamps($pedido),
                    );
                }

                die( json_encode( $resposta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
            } else {
                die( json_encode( array( "status" => false, "message" => "Não foi possível localizar a apólice com os parâmetros informados" ) ) );
                $response->setStatus(false);
            }

        } else {
            die( json_encode( array( "status" => false, "message" => "Parâmetros inválidos" ) ) );
        }
    }

    function cancelar() {
        if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
            $POST = json_decode( file_get_contents( "php://input" ), true );
        } else {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        $apolice_id = null;
        if( isset( $POST["apolice_id"] ) ) {
            $apolice_id = $POST["apolice_id"];
            $params["apolice_id"] = $apolice_id;
        } else {
            die( json_encode( array( "status" => false, "message" => "Campo apolice_id é obrigatório" ) ) );
        }

        $this->load->model("pedido_model", "pedido");

        $pedido = $this->pedido->with_apolice()->filter_by_apolice($apolice_id)->get_all();
        if(!$pedido) {
            die( json_encode( array( "status" => false, "message" => "Apólice não encontrada" ) ) );
        }
        $pedido_id = $pedido[0]["pedido_id"];

        //pega as configurações de cancelamento do pedido
        $produto_parceiro_cancelamento = $this->pedido->cancelamento( $pedido_id );

        if( isset( $produto_parceiro_cancelamento["result"] ) && $produto_parceiro_cancelamento["result"] == false ) {
            die( json_encode( array( "status" => false, "message" => $produto_parceiro_cancelamento["mensagem"] ) ) );
        } else {
            die( json_encode( array( "status" => true, "message" => "Apólice cancelada com sucesso" ) ) );
        }

    }

    function calculoCancelar() {
        if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
            $POST = json_decode( file_get_contents( "php://input" ), true );
        } else {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        $apolice_id = null;
        if( isset( $POST["apolice_id"] ) ) {
            $apolice_id = $POST["apolice_id"];
            $params["apolice_id"] = $apolice_id;
        } else {
            die( json_encode( array( "status" => false, "message" => "Campo apolice_id é obrigatório" ) ) );
        }

        $this->load->model("pedido_model", "pedido");

        $pedido = $this->pedido->with_apolice()->filter_by_apolice($apolice_id)->get_all();
        if(!$pedido) {
            die( json_encode( array( "status" => false, "message" => "Apólice não encontrada" ) ) );
        }
        $pedido_id = $pedido[0]["pedido_id"];

        //pega as configurações de cancelamento do pedido
        $produto_parceiro_cancelamento = $this->pedido->cancelamento_calculo( $pedido_id );

        die( json_encode( $produto_parceiro_cancelamento ) );

    }

}

