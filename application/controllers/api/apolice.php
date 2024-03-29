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
        $this->load->model('apolice_cobertura_model', 'apolice_cobertura');

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

            if ( !empty($GET["limit"]) )
            {
                $limit = (int)$GET["limit"];
                if($limit > 1000){
                    return array( "status" => false, "message" => "Limite máximo de paginação: 1000" );
                }

                $offset = $limit * (int)$GET['page'];
                $pedidos = $pedidos['pedidos']->limit($limit, $offset)->get_all();
            }
            else{
                $pedidos = $pedidos['pedidos']->get_all();
            }

            if($pedidos) {
                $resposta = [];
                foreach ($pedidos as $pedido) {

                    //Monta resposta da apólice
                    $apolice = $this->apolice->getApolicePedido($pedido["pedido_id"]);
                    $apolice[0]["inadimplente"] = ($this->pedido->isInadimplente( $pedido["pedido_id"] ) === false ) ? 0 : 1;
                    $apolice[0]["coberturas"] = $this->apolice_cobertura->OnlyCoberturas()->filterByTipo("A")->getByApoliceID($apolice[0]["apolice_id"])->get_all();

                    $this->load->model('Comissao_gerada_model', 'comissao_gerada');

                    $aComissaoGerada = $this->comissao_gerada->getByParceiroId($pedido["pedido_id"], null, array('3','2'));

                    if(sizeof($aComissaoGerada))
                    {
                        foreach ($aComissaoGerada as $iComissaoGerada => $vComissaoGerada)
                        {
                            $data_template[$iComissaoGerada]["tipo_id"] = $vComissaoGerada["parceiro_tipo_id"];
                            $data_template[$iComissaoGerada]["nome"] = strtoupper($vComissaoGerada["tipo_parceiro"]);
                            $data_template[$iComissaoGerada]["slug"] = $vComissaoGerada["tipo_parceiro_slug"];
                            $data_template[$iComissaoGerada]["percentual"] = number_format((float)$vComissaoGerada["comissao"], 4, '.' , '');
                            $data_template[$iComissaoGerada]["valor"] = number_format((float)$vComissaoGerada["valor"], 2, '.' , '');
                        }

                        $pedido["comissao"] = $data_template;
                    }

                    $faturas = $this->fatura->filterByPedido($pedido["pedido_id"])
                    ->with_fatura_status()
                    ->with_pedido()
                    ->order_by("data_processamento")
                    ->get_all();

                    foreach ($faturas as $index => $fatura)
                    {
                        $faturas[$index]["parcelas"] = $this->fatura_parcela->with_fatura_status()
                            ->filterByFatura($fatura["fatura_id"])
                            ->order_by("num_parcela")
                            ->get_all();
                    }

                    $resposta[] = array(
                        "apolice" => api_retira_timestamps($apolice),
                        "faturas" => api_retira_timestamps($faturas),
                        "pedido" => api_retira_timestamps($pedido)
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

            $days_ago = null;
            if( isset( $GET["dias_atras"] ) ) {
                $days_ago = $GET["dias_atras"];
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
          
            $data_inicio_proc = null;
            if( isset( $GET["data_inicio_proc"] ) ) {
                $data_inicio_proc = $GET["data_inicio_proc"];
            }

            $data_fim_proc = null;
            if( isset( $GET["data_fim_proc"] ) ) {
                $data_fim_proc = $GET["data_fim_proc"];
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
            $params["days_ago"] = $days_ago;
            $params["data_inicio_proc"] = $data_inicio_proc;
            $params["data_fim_proc"] = $data_fim_proc;
    
            if($apolice_id || $num_apolice || $documento || $pedido_id || $apolice_status || $days_ago || $data_inicio || $data_fim || $data_inicio_proc || $data_fim_proc) {
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
                $num_apolice_cliente = !empty($PUT["num_apolice_cliente"])? $PUT["num_apolice_cliente"]: null;

                // atualiza o numero do bilhete
                $ret = $this->apolice->updateBilhete( $apolice_id, $num_apolice, $num_apolice_cliente);
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

    public function validarDadosEntrada($POST = [])
    {

        if (empty($POST)) {
            $POST = $this->getDados();
        }

        $this->load->model("pedido_model", "pedido");
        $this->load->model("apolice_model", "apolice");

        try {

            $apolice_id = null;

            if (isset($POST["apolice_id"])) {
                $apolice_id = $POST["apolice_id"];
            } else if (isset($POST["num_apolice"])) {

                $apolice = $this->apolice->get_by(array("num_apolice" => $POST["num_apolice"], "deletado" => 0));
                
                if (!empty($apolice)) {
                    $apolice_id = $apolice["apolice_id"];
                } else {
                    throw new Exception("Apólice não encontrada para o num_apolice: ".$POST["num_apolice"]);
                }
                
            } else {
                throw new Exception("É obrigatório um dos campos: apolice_id ou num_apolice");
            }

            $pedido = $this->pedido->with_apolice()->filter_by_apolice($apolice_id)->get_all();
            if(!$pedido) {
                throw new Exception("Apólice não encontrada");                
            }

            return [ 'dados' => $POST, 'pedido_id' => $pedido[0]["pedido_id"] ];

        } catch (Exception $ex) {
            die( json_encode( array( "status" => false, "message" => $ex->getMessage() ) ) );
        }

        
    }

    private function __validarDadosEntrada($POST = []){
        return $this->validarDadosEntrada($POST);
    }

    public function cancelar() {
        
        $this->checkKey();

        $validacao = $this->__validarDadosEntrada();
        $pedido_id = $validacao['pedido_id'];
        $dados_bancarios = !empty($validacao['dados']['dados_bancarios']) ? $validacao['dados']['dados_bancarios'] : [];
        $define_date = !empty($validacao['dados']["define_date"]) ? $validacao['dados']["define_date"] : date("Y-m-d H:i:s") ;
        $tipo_usuario = !empty($validacao['dados']['tipo_usuario']) ? $validacao['dados']['tipo_usuario'] : [];
        $dados_apolice = $this->apolice->getApolicePedido($pedido_id);
        if (($tipo_usuario == 'INT' || $tipo_usuario == 'CLI') && $dados_apolice[0]["parceiro_id"] == 30){ //Tratamento exclusivo para LASA e Softbox
            $tipo_motivo = emptyor($validacao['dados']["tipo_motivo"], 'R');
        }else{
            $tipo_motivo = emptyor($validacao['dados']["tipo_motivo"], 'C');
        }
        //pega as configurações de cancelamento do pedido
        $produto_parceiro_cancelamento = $this->pedido->cancelamento( $pedido_id, $dados_bancarios, $define_date, 'C', $tipo_motivo);

        if( isset( $produto_parceiro_cancelamento["result"] ) && $produto_parceiro_cancelamento["result"] == false ) {
            die( json_encode( array( "status" => false, "message" => $produto_parceiro_cancelamento["mensagem"] ) ) );
        } else {
            die( json_encode( array( "status" => true, "message" => "Apólice cancelada com sucesso" ) ) );
        }

    }

    public function calculoCancelar() {
        $this->checkKey();

        $validacao = $this->__validarDadosEntrada();

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

        // validação de dados
        $POST = $this->getDados();

        if( empty( $POST["apolices"] ) ) {
            die( json_encode( array( "status" => false, "message" => "Campo apolices é obrigatório" ) ) );
        }

        $this->load->model("apolice_endosso_model", "apolice_endosso");
        $this->load->model('apolice_movimentacao_tipo_model', 'tipo');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('fatura_model', 'fatura');

        $apolices = [];
        $result = [];
        $errosResult = [];

        // Realiza as validações antes de executar
        foreach ($POST["apolices"] as $key => $value) {

            $erros = [];

            // Validar apolice_id
            $validacao = $this->validarDadosEntrada($value);

            $apolice_id = $validacao['dados']['apolice_id'];
            $pedido_id  = $validacao['pedido_id'];

            if ( $this->apolice->getApoliceStatus($apolice_id, 'cancelada') ) {
                $errosResult[] = $this->parcelaReturn($apolice_id,"A apólice já está cancelada" );
                continue;
            }

            // TODO: validar se o valor da parcela está correto para não aceitar qualquer valor

            // Validar outros parâmetros
            (array_key_exists('parcela', $validacao['dados'])) ? $parcela = $validacao['dados']['parcela'] : $erros[] = $this->parcelaReturn($apolice_id,"Campo parcela é obrigatório");
            (array_key_exists('total_parcelas', $validacao['dados'])) ? $total_parcelas = $validacao['dados']['total_parcelas'] : $erros[] = $this->parcelaReturn($apolice_id,"Campo total_parcelas é obrigatório");
            (array_key_exists('valor', $validacao['dados'])) ? $valor = trim(str_replace(",", ".", $validacao['dados']['valor'])) : $erros[] = $this->parcelaReturn($apolice_id,"Campo valor é obrigatório");
            (array_key_exists('valor_pago', $validacao['dados'])) ? $valor_pago = trim(str_replace(",", ".", $validacao['dados']['valor_pago'])) : $erros[] = $this->parcelaReturn($apolice_id,"Campo valor_pago é obrigatório");
            (array_key_exists('data_vencimento', $validacao['dados'])) ? $data_vencimento = trim($validacao['dados']['data_vencimento']) : $erros[] = $this->parcelaReturn($apolice_id,"Campo data_vencimento é obrigatório");
            (array_key_exists('data_pagamento', $validacao['dados'])) ? $data_pagamento = trim($validacao['dados']['data_pagamento']) : $erros[] = $this->parcelaReturn($apolice_id,"Campo data_pagamento é obrigatório");

            if ( !empty($erros) ){
                $errosResult[] = $erros;
                continue;
            }

            // Validar campos de data
            if (!app_validate_data_americana($data_vencimento)) $erros[] = $this->parcelaReturn($apolice_id,"Campo data_vencimento deve ser uma data válida (YYYY-MM-DD)");
            if (!app_validate_data_americana($data_pagamento)) $erros[] = $this->parcelaReturn($apolice_id,"Campo data_pagamento deve ser uma data válida (YYYY-MM-DD)");

            // Validar campos numéricos
            if (! is_numeric($valor)) $erros[] = $this->parcelaReturn($apolice_id,"Campo valor deve ser um campo númerico");
            if (! is_numeric($total_parcelas)) $erros[] = $this->parcelaReturn($apolice_id,"Campo total_parcelas deve ser um campo númerico");
            if ( empty($valor) ) $erros[] = $this->parcelaReturn($apolice_id,"Campo valor deve ser maior que zero");
            if ( empty($valor_pago) ) $erros[] = $this->parcelaReturn($apolice_id,"Campo valor_pago deve ser maior que zero");

            // Carregar pedido
            $pedido = $this->pedido->getPedidosByID($pedido_id);
            $pedido = $pedido[0];

            // Validar total de parcelas
            if ((int) $pedido['num_parcela'] != $total_parcelas)
                $erros[] = $this->parcelaReturn($apolice_id,"O total de parcelas informado é inválido" );

            $produto_parceiro_pagamento_id = $pedido['produto_parceiro_pagamento_id'];
            $cotacao_id = $pedido['cotacao_id'];
            $produto_parceiro_id = $pedido['produto_parceiro_id'];

            // Selecionar parcela atual
            $ultima_parcela = $this->apolice_endosso->lastSequencial($apolice_id);
            $parcela_atual = (int) $ultima_parcela['parcela'] + 1;

            // Validar parcela
            if ($parcela <= 0) $erros[] = $this->parcelaReturn($apolice_id,"Parcela " . $parcela . " não é uma parcela válida");
            if ($parcela > $total_parcelas) $erros[] = $this->parcelaReturn($apolice_id,"Parcela atual é maior que o total de parcelas");
            if ($parcela > $parcela_atual) $erros[] = $this->parcelaReturn($apolice_id,"Parcela " . $parcela_atual . " ainda não foi efetuada");
            if ($parcela < $parcela_atual) $erros[] = $this->parcelaReturn($apolice_id,"Parcela " . $parcela . " já foi efetuada");

            $apolices[] = [
                'apolice_id'                    => $apolice_id,
                'pedido_id'                     => $pedido_id,
                'cotacao_id'                    => $cotacao_id,
                'parcela'                       => $parcela,
                'total_parcelas'                => $total_parcelas,
                'valor'                         => $valor,
                'valor_pago'                    => $valor_pago,
                'produto_parceiro_id'           => $produto_parceiro_id,
                'produto_parceiro_pagamento_id' => $produto_parceiro_pagamento_id,
                'data_vencimento'               => $data_vencimento,
                'data_pagamento'                => $data_pagamento,
            ];

            if ( !empty($erros) ){
                $errosResult[] = $erros;
                continue;
            }

        }

        if ( !empty($errosResult) )
        {
            die( json_encode([
                "status" => false,
                "apolices" => $errosResult,
            ]) );
        }

        // executa após nenhuma inconsistência
        foreach ($apolices as $ap) {

            $apolice_id                     = $ap['apolice_id'];
            $pedido_id                      = $ap['pedido_id'];
            $cotacao_id                     = $ap['cotacao_id'];
            $produto_parceiro_id            = $ap['produto_parceiro_id'];
            $produto_parceiro_pagamento_id  = $ap['produto_parceiro_pagamento_id'];
            $parcela                        = $ap['parcela'];
            $valor                          = $ap['valor'];
            $data_vencimento                = $ap['data_vencimento'];
            $data_pagamento                 = $ap['data_pagamento'];

            // Carregar tipo
            $tipo = $this->tipo->filter_by_slug('A')->get_all();
            $tipo = $tipo[0];

            $this->apolice_endosso->insEndosso($tipo["slug"], $tipo["apolice_movimentacao_tipo_id"], $pedido_id, $apolice_id, $produto_parceiro_pagamento_id, $parcela, $valor);

            $fatura = $this->fatura->filterByPedido($pedido_id)->get_all();
            if ( !empty($fatura) )
            {
                $fatura = $fatura[0];
                $this->fatura->insFaturaParcelamentoDet($fatura['fatura_id'], $cotacao_id, $produto_parceiro_id, $parcela, $valor, $data_vencimento, $data_pagamento);
            }

            $result[] = [
                'status' => true,
                'apolice_id' => $apolice_id,
            ];

        }

        die(json_encode(array("status" => true, "message" => "Inserido com sucesso", "apolices" => $result)));
    }

    private function parcelaReturn ($apolice_id, $msg) {
        return array(
            "status" => false,
            "message" => $msg,
            "apolice_id" => $apolice_id
        );
    }

    public function onlyCancelados(){

    }

}

