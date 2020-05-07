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

        $this->load->database('default');

        $this->load->model('apolice_model', 'apolice');
        $this->load->model('apolice_status_model', 'apolice_status');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model("fatura_model", "fatura");
        $this->load->model("fatura_parcela_model", "fatura_parcela");

        $this->load->helper("api_helper");
    }

    private function checkKey() {
        if( !isset( $_SERVER["HTTP_APIKEY"] ) ) {
            die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
        }

        $this->api_key = $_SERVER["HTTP_APIKEY"];
        $this->load->model( "usuario_webservice_model", "webservice" );

        $webservice = $this->webservice->checkKeyExpiration( $this->api_key );
        if( !sizeof( $webservice ) ) {
            die( json_encode( array( "status" => false, "message" => "APIKEY is invalid" ) ) );
        }

        $this->usuario_id = $webservice["usuario_id"];
        $this->parceiro_id = $webservice["parceiro_id"];
        return $webservice;
    }

    public function consulta(){             
        $this->checkKey();        
        $filtroApolice = array();        

        $inputData = $_POST;      

        try{
            if(empty($inputData["apolice_status"])){
                throw new Exception('O campo "apolice_status" é obrigatório');         
            }else{            
                if(!in_array($inputData["apolice_status"],["1", "2", "3"])){
                    throw new Exception('O campo "apolice_status" deve ser "1", "2" ou "3" [1: ativa; 2: cancelada; 3: ativa ou cancelada]');
                }
            }
    
            if(empty($inputData["data_inicio"])){            
                throw new Exception('O campo "data_inicio" é obrigatório');
            }else{
                if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $inputData["data_inicio"])) {
                    throw new Exception('O campo "data_inicio" deve conter o formato "yyyy-mm-dd"');
                }        
            } 
    
            if(empty($inputData["data_fim"])){            
                throw new Exception('O campo "data_fim" é obrigatório');
            }else{
                if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $inputData["data_fim"])) {
                    throw new Exception('O campo "data_fim" deve conter o formato "yyyy-mm-dd"');
                }        
            }
    
            $data_inicio = new DateTime($inputData["data_inicio"]);
            $data_fim = new DateTime($inputData["data_fim"]);
            $dias = $data_fim->diff($data_inicio)->format("%a");
            if($dias > 31){
                throw new Exception("O intervalo de datas deve ter no máximo 31 dias");
            }        
    
            $outputData = $this->retornaApolices($inputData);
        }catch(Exception $ex){
            $outputData = array("status" => false, "message" => $ex->getMessage());
        }
        
        die( json_encode( $outputData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }
    

    public function consultaBase() {
        $outputData =  $this->retornaApolices($_POST);
        die( json_encode($outputData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function consultaBaseProduto() {
        die( json_encode( $this->retornaProdutoApolices($_POST), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function retornaApolices($GET = []) {                
        $pedidos = $this->filtraPedidos($GET);                        
        
        if (!empty($pedidos['status'])) {
      

            $pedidos = $pedidos['pedidos']->get_all();                    
            
            if($pedidos) {
                $resposta = [];
                foreach ($pedidos as $pedido) {
                    //Monta resposta da apólice
                    $apolice = $this->apolice->getApolicePedido($pedido["pedido_id"]);
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

                return array("status" => true, "dados" => $resposta);
            } else {
                return array( "status" => false, "message" => "Não foi possível localizar a apólice com os parâmetros informados" );
            }

        } else {
            return $pedidos;
        }
    }

    public function retornaProdutoApolices($GET = []) {
        $pedidos = $this->filtraPedidos($GET);
        if (!empty($pedidos['status'])) {
            $pedidos = $pedidos['pedidos']->group_by('produto.produto_id, produto.nome')->get_all();
            return array("status" => true, "dados" => api_retira_timestamps($pedidos));
        } else {
            return $pedidos;
        }
    }

    private function filtraPedidos($GET = []) {
        try{
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
    
            $parceiro_id = null;
            if( isset( $GET["parceiro_id"] ) ) {
                $parceiro_id = $GET["parceiro_id"];
            }
    
            $produto_id = null;
            if( isset( $GET["produto_id"] ) ) {
                $produto_id = $GET["produto_id"];
            }

            $apolice_status = null;
            if( isset( $GET["apolice_status"] ) ) {
                $apolice_status = $GET["apolice_status"];                
            }
    
            $data_fim = null;
            if( isset( $GET["data_fim"] ) ) {
                $data_fim = $GET["data_fim"];
            }
    
            $data_inicio = null;
            if( isset( $GET["data_inicio"] ) ) {
                $data_inicio = $GET["data_inicio"];
            }
    
            $retorno = null;
            $params = array();
            $params["apolice_id"] = $apolice_id;
            $params["num_apolice"] = $num_apolice;
            $params["documento"] = $documento;
            $params["pedido_id"] = $pedido_id;
            $params["parceiro_id"] = $parceiro_id;
            $params["produto_id"] = $produto_id;
            $params["apolice_status"] = $apolice_status;
            $params["data_inicio"] = $data_inicio;
            $params["data_fim"] = $data_fim;
    
            if($apolice_id || $num_apolice || $documento || $pedido_id || $apolice_status) {            
                $pedidos = $this->pedido
                ->with_pedido_status()
                // ->with_apolice()
                ->with_cotacao_cliente_contato()
                ->with_produto_parceiro()
                // ->with_fatura()            
                ->filterNotCarrinho()            
                ->filterAPI($params);
                $retorno = array("status" => true, "pedidos" => $pedidos);
            } else {
                throw new Exception("Parametros inválidos");
            }        
            
        }catch(Exception $ex){
            $retorno = array( "status" => false, "message" => $ex->getMessage());
        }
        return $retorno;
    }

    public function index() {

        if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
            $GET = $_GET;
            die( json_encode( $this->retornaApolices($GET), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        } else {
            if ($_SERVER["REQUEST_METHOD"] === "POST" || $_SERVER["REQUEST_METHOD"] === "PUT" ) {
                $PUT = json_decode( file_get_contents( "php://input" ), true );
                if( !isset( $PUT["apolice_id"] ) ) {
                    die( json_encode( array( "status" => false, "message" => "Campo apolice_id é obrigatório" ) ) );
                }
                if( !isset( $PUT["num_apolice"] ) ) {
                    die( json_encode( array( "status" => false, "message" => "Campo num_apolice é obrigatório" ) ) );
                }
                $apolice_id = $PUT["apolice_id"];
                $num_apolice = $PUT["num_apolice"];

                // atualiza o numero do bilhete
                $ret = $this->apolice->updateBilhete( $apolice_id, $num_apolice );
                die( json_encode( $ret, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
            } else {
                die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
            }
        }

    }

    public function getDados ()
    {
        if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
            $POST = json_decode( file_get_contents( "php://input" ), true );

            if (empty($POST)) {
                die(json_encode(array("status" => false, "message" => "Dados não recebidos")));
            }
        } else {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        return $POST;
    }

    public function cancelar() {
        
        $this->checkKey();

        $validacao = $this->apolice->validarDadosEntrada($this->getDados());
        if ( empty($validacao['status']) ) {
            die( json_encode( $validacao ) );
        }

        $pedido_id = $validacao['pedido_id'];
        $dados_bancarios = !empty($validacao['dados']['dados_bancarios']) ? $validacao['dados']['dados_bancarios'] : [];
        $define_date = !empty($validacao['dados']["define_date"]) ? $validacao['dados']["define_date"] : date("Y-m-d H:i:s") ;

        //pega as configurações de cancelamento do pedido
        $produto_parceiro_cancelamento = $this->pedido->cancelamento( $pedido_id, $dados_bancarios, $define_date);

        if( isset( $produto_parceiro_cancelamento["result"] ) && $produto_parceiro_cancelamento["result"] == false ) {
            die( json_encode( array( "status" => false, "message" => $produto_parceiro_cancelamento["mensagem"] ) ) );
        } else {
            die( json_encode( array( "status" => true, "message" => "Apólice cancelada com sucesso" ) ) );
        }

    }

    public function calculoCancelar() {
        $this->checkKey();

        $validacao = $this->apolice->validarDadosEntrada($this->getDados());
        if ( empty($validacao['status']) ) {
            die( json_encode( $validacao ) );
        }

        $pedido_id = $validacao['pedido_id'];
        $define_date = !empty($validacao['dados']["define_date"]) ? $validacao['dados']["define_date"] : date("Y-m-d H:i:s") ;

        //pega as configurações de cancelamento do pedido
        $produto_parceiro_cancelamento = $this->pedido->cancelamento_calculo( $pedido_id , $define_date );

        die( json_encode( $produto_parceiro_cancelamento ) );
    }

    public function getDocumentos()
    {
        $this->checkKey();

        if( empty( $_GET["plano_slug"] ) ) {
            die( json_encode( array( "status" => false, "message" => "O Atributo plano_slug é obrigatório" ) ) );
        }

        $this->load->model( "produto_parceiro_plano_model", "produto_parceiro_plano" );
        $this->load->model( "produto_parceiro_plano_tipo_documento_model", "produto_parceiro_plano_tipo_documento" );

        $planos = $this->produto_parceiro_plano
            ->wtih_plano_habilitado($this->parceiro_id)
            ->filter_by_slug($_GET["plano_slug"])
            ->get_all_select();
        if( empty( $planos ) ) {
            die( json_encode( array( "status" => false, "message" => "Nenhum plano encontrado com o slug informado" ) ) );
        }

        $produto_parceiro_plano_id = $planos[0]['produto_parceiro_plano_id'];

        $docs = $this->produto_parceiro_plano_tipo_documento
            ->filter_by_plano_slug($_GET["plano_slug"])
            ->with_tipo_documento()
            ->get_all_select();
        if( empty( $docs ) ) {
            die( json_encode( array( "status" => false, "message" => "Nenhum documento encontrado" ) ) );
        }

        die( json_encode( array( "status" => true, "message" => "OK" , "documents" => $docs) ) );
    }

    public function sendDocumentos(){
        $this->checkKey();

        $payload = json_decode( file_get_contents( "php://input" ), false );

        if( empty( $payload->{"apolice_id"} ) ) {
            die( json_encode( array( "status" => false, "message" => "O atributo 'apolice_id' é um campo obrigatório" ) ) );
        } else {
            $apolice_id = $payload->{"apolice_id"};
        }

        if( empty( $payload->{"itens"} ) ) {
            die( json_encode( array( "status" => false, "message" => "O atributo 'itens' é um campo obrigatório" ) ) );
        }

        // Models
        $this->load->model( "apolice_model", "apolice" );
        $this->load->model( "apolice_documento_model", "docs" );
        $this->load->model( "produto_parceiro_plano_tipo_documento_model", "prod_parc_plano_doc" );

        $apolice = $this->apolice->get($apolice_id);
        if( empty( $apolice ) ) {
            die( json_encode( array( "status" => false, "message" => "Apólice não encontrada (#$apolice_id)" ) ) );
        }

        $produto_parceiro_plano_id = $apolice['produto_parceiro_plano_id'];

        $itens = [];
        $cont=0;

        foreach ($payload->{"itens"} as $item) {

            if( empty( $item->{"tipo_documento_id"} ) ) {
                die( json_encode( array( "status" => false, "message" => "O atributo 'tipo_documento_id' é um campo obrigatório" ) ) );
            }

            if( empty( $item->{"extension"} ) ) {
                die( json_encode( array( "status" => false, "message" => "O atributo 'extension' é um campo obrigatório" ) ) );
            }

            if( empty( $item->{"file"} ) ) {
                die( json_encode( array( "status" => false, "message" => "O atributo 'file' é um campo obrigatório (base64)" ) ) );
            }

            $produto_parceiro_plano_tipo_documento = $this->prod_parc_plano_doc->filter_by_plano_id($produto_parceiro_plano_id)->filter_by_tipo_doc_id($item->{"tipo_documento_id"})->get_all();
            if( empty( $produto_parceiro_plano_tipo_documento ) ) {
                die( json_encode( array( "status" => false, "message" => "Tipo de Documento não habilitado para este Plano" ) ) );
            }

            $produto_parceiro_plano_tipo_documento_id = $produto_parceiro_plano_tipo_documento[0]['produto_parceiro_plano_tipo_documento_id'];

            $itens[$cont] = (array) $item;
            $itens[$cont]['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
            $itens[$cont]['produto_parceiro_plano_tipo_documento_id'] = $produto_parceiro_plano_tipo_documento_id;

            $cont++;
        }

        // inativa todos os documentos inseridos
        $this->docs->disableDoc($apolice_id);

        $cont = 0;
        foreach ($itens as $item) {

            #$tipo_arquivo = pathinfo($arquivo, PATHINFO_EXTENSION);
            $produto_parceiro_plano_tipo_documento_id = $item["produto_parceiro_plano_tipo_documento_id"];
            $name = "{$apolice_id}_{$produto_parceiro_plano_tipo_documento_id}_". date('Ymd_His') ."_". rand(0,100) .".". $item["extension"];

            $apolice_documento_id = $this->docs->uploadFile($name, $apolice_id, $produto_parceiro_plano_tipo_documento_id, $item["file"]);
            $itens[$cont]['apolice_documento_id'] = $apolice_documento_id;
            unset($itens[$cont]['file']);
        }

        die( json_encode( array( "status" => true, "message" => "OK" , "documents" => $itens) ) );
    }

    /**
     * Função criada para clientes que controlam o faturamento manualmente por fora do sistema
     *
     * @param integer apolice_id
     * @param integer parcela
     * @param integer total_parcelas
     * @param double valor
     * @return json
     *
     * @author Davi Souto
     * @since  08/04/2019 
     */
    public function parcela() {
        $this->checkKey();

        $result = $this->apolice->emissaoParcela( $this->getDados() );
        die( json_encode($result) );
    }

    public function onlyCancelados(){

    }

}

