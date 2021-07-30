<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Emissao - ALR
*/

require_once APPPATH . 'controllers/admin/api.php';

class Emissao extends CI_Controller {
    public $api_key;
    public $http_method;
    public $usuario_id;

    public $parceiro_id;
    public $produto_parceiro_id;
    public $parceiro_id_pai; 
    public $produto_parceiro_plano_id;
    public $cotacao_id;
    public $equipamento_nome;
    public $categoria;
    public $ean;

    public $campos_estrutura;
    public $valor_premio_bruto;
    public $premio_liquido;
    public $forma_pagamento_id;
    public $produto_parceiro_pagamento_id;

    public $meio_pagto_slug;
    public $campos_meios_pagto;
    public $numero_sorte;
    public $numero_serie;
    public $comissao_premio;
    public $coberturas_opcionais;
    public $parcelas;

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

        // Aqui guardo em sessão para fazer acompanhamento
        $this->session->set_userdata("tokenAPI", $this->api_key);
        $this->session->set_userdata("tokenAPIvalid",$webservice["validade"]);

        $this->load->model( "apolice_model", "apolice" );
    }

    public function index() {

        $POST = json_decode( file_get_contents( "php://input" ), true );

        // Validação dos dados
        if (empty($POST))
        {
            die(json_encode(array("status"=>false,"message"=>"Parametros não informados"),JSON_UNESCAPED_UNICODE));

        } else
        {
            // if(empty($POST['produto_slug'])){
            //     die(json_encode(array("status"=>false,"message"=>"Atributo 'produto_slug' não informado"),JSON_UNESCAPED_UNICODE));
            // }
            if(empty($POST['plano_slug'])){
                die(json_encode(array("status"=>false,"message"=>"Atributo 'plano_slug' não informado"),JSON_UNESCAPED_UNICODE));
            }
            if(empty($POST['campos'])){
                die(json_encode(array("status"=>false,"message"=>"Atributo 'campos' não informado"),JSON_UNESCAPED_UNICODE));
            }
            if(!is_array($POST['campos'])){
                die(json_encode(array("status"=>false,"message"=>"Atributo 'campos' com formatação inválida"),JSON_UNESCAPED_UNICODE));
            }
            if(isset($POST['valor_premio_bruto']) && empty($POST['valor_premio_bruto'])){
                die(json_encode(array("status"=>false,"message"=>"Atributo 'valor_premio_bruto' deve ser maior que zero"),JSON_UNESCAPED_UNICODE));
            }
            /*if(empty($POST['meio_pagto_slug'])){
                die(json_encode(array("status"=>false,"message"=>"meio_pagto_slug não informado"),JSON_UNESCAPED_UNICODE));
            }*/
        }

        $this->equipamento_nome     = '';
        $this->categoria            = '';
        $this->ean                  = '';
        $this->num_apolice          = (!isset($POST['num_apolice'])) ? false : $POST['num_apolice'];
        $this->valor_premio_bruto   = (!isset($POST['valor_premio_bruto'])) ? 0 : $POST['valor_premio_bruto'];
        $this->premio_liquido       = (!isset($POST['valor_premio_liquido'])) ? 0 : $POST['valor_premio_liquido'];
        $this->comissao_premio      = (empty($POST['comissao'])) ? 0 : $POST['comissao'];
        $this->coberturas_opcionais = (!isset($POST['coberturas_opcionais'])) ? '' : $POST['coberturas_opcionais'];
        $this->meio_pagto_slug      = (!isset($POST['meiopagamento']['meio_pagto_slug'])) ? '' : $POST['meiopagamento']['meio_pagto_slug'];
        $this->campos_meios_pagto   = (!isset($POST['meiopagamento']['campos'])) ? [] : $POST['meiopagamento']['campos'];
        $this->parcelas             = (!isset($POST['meiopagamento']['parcelas'])) ? null : $POST['meiopagamento']['parcelas'];
        $this->numero_sorte         = (!isset($POST['numero_sorte'])) ? null : $POST['numero_sorte'];
        $this->numero_serie         = (!isset($POST['numero_serie'])) ? null : $POST['numero_serie'];

        $this->etapas('cotacao', $POST);
    }

    public function etapas($etapa = null, $parametros = []){

        switch ($etapa) {

            /* ---------inicio processo --------------- */
            case 'cotacao':

                $parceiro_id =  $this->parceiro_id;

                $this->load->model( "produto_parceiro_model", "produto_parceiro" );
                $this->load->model( "produto_parceiro_plano_model", "produto_parceiro_plano" );

                $produtos = $this->produto_parceiro->get_produtos_venda_admin_parceiros( $parceiro_id, $parametros['produto_slug'] );
                if(!empty($produtos))
                {
                    foreach ($produtos as $prod) {

                        // Separando o produto do parceiro
                        $r = $this->produto_parceiro_plano->coreSelectPlanosProdutoParceiro($prod['produto_parceiro_id'])->filter_by_slug($parametros["plano_slug"])->get_all();

                        if(!empty($r)){
                            $this->produto_parceiro_id = $prod['produto_parceiro_id'];
                            $this->parceiro_id_pai     = $prod['parceiro_id'];
                            $this->produto_parceiro_plano_id = $r[0]['produto_parceiro_plano_id'];
                        }

                    }

                    if (empty($this->produto_parceiro_id)) {
                        die(json_encode(array("status"=>false,"message"=>"Não foi possível identificar o Plano"),JSON_UNESCAPED_UNICODE));
                    }
                } 
                else 
                {
                    die(json_encode(array("status"=>false,"message"=>"Não foi possível identificar o Produto"),JSON_UNESCAPED_UNICODE));
                }

                // Campos da cotação
                $arrOptions = [
                    "produto_parceiro_id"       => $this->produto_parceiro_id,
                    "produto_parceiro_plano_id" => $this->produto_parceiro_plano_id,
                    "comissao_premio"           => $this->comissao_premio,
                    "coberturas_opcionais"      => $this->coberturas_opcionais,
                ];

                // número da sorte
                if ( $this->numero_sorte )
                {
                    $arrOptions['numero_sorte'] =  $this->numero_sorte;
                    $arrOptions['num_proposta_capitalizacao'] =  $this->numero_serie;
                }

                if(count($parametros['campos'][0]) > 0)
                {
                    foreach ($parametros['campos'][0] as $key => $vl) {
                        $arrOptions[$key] =  $vl;
                    }
                    $this->campos_estrutura = $arrOptions;
                }

                // Validação do número da apólice
                if( !empty($parametros['num_apolice']) && $this->apolice->search_apolice_produto_parceiro_plano_id( $parametros['num_apolice'] , $this->produto_parceiro_plano_id ) ){
                    die(json_encode(array("status"=>false,"message"=>"Já existe um certificado com o número {$parametros['num_apolice']} em nossa base"),JSON_UNESCAPED_UNICODE));
                }

                $validaModelo = false;
                $carregaModelo = false;
                $obj = new Api();

                // Consulta se existe a necessidade de consultar o modelo do equipamento
                $url = base_url() ."api/campos?produto_parceiro_id=". $this->produto_parceiro_id;
                $r = $obj->execute($url, 'GET');
                if(empty($r))
                    die(json_encode(array("status"=>false,"message"=>"Falha na consulta de campos habilitados"),JSON_UNESCAPED_UNICODE));

                $retorno = convert_objeto_to_array($r);
                if (is_array($r)) {
                    $err = empty($retorno->status);
                } else {
                    $err = empty($r);
                }

                if( $err ) {
                    $msg = ( !empty($retorno->{"mensagem"}) ) ? $retorno->{"mensagem"} : $r;
                    die(json_encode(array("status"=>false,"message"=>$msg),JSON_UNESCAPED_UNICODE));
                }

                foreach ($retorno as $ret) {
                    if ($validaModelo) break;

                    foreach ($ret->campos as $campo) {
                        if ( in_array($campo->nome_banco, ['ean', 'equipamento_id', 'equipamento_nome', 'equipamento_marca_id', 'equipamento_sub_categoria_id', 'equipamento_categoria_id']) ) {
                            $carregaModelo = true;
                            $pos = strpos($campo->validacoes, "required");
                            if ( !($pos === false) ) {
                                $validaModelo = true;
                                break;
                            }
                        }
                    }
                }

                if ($carregaModelo) {
                    // Caso não tenha sido informado os ID dos equipamentos
                    if ( empty($this->campos_estrutura["equipamento_id"]) || empty($this->campos_estrutura["equipamento_marca_id"]) || empty($this->campos_estrutura["equipamento_sub_categoria_id"]) || empty($this->campos_estrutura["equipamento_categoria_id"]) || empty($this->campos_estrutura["ean"]) ) {

                        //Caso tenha sido passado o EAN, faz a busca do equipamento consumindo o WS com base no EAN
                        if( ! empty($parametros['ean']) ){
                            $url = base_url() . "api/equipamento?ean=". $parametros['ean'] . "&ret=yes" ;
                            $r = $obj->execute($url);

                            // Ajuste ALR
                            if(!empty($r)) {
                                $retorno = json_decode($r,true);
                                $arrOptions["ean"]                          = $retorno["ean"];  
                                $arrOptions["equipamento_id"]               = $retorno["equipamento_id"];
                                $arrOptions["equipamento_nome"]             = isempty($parametros['modelo'], $retorno['nome']);
                                $arrOptions["equipamento_marca_id"]         = $retorno["equipamento_marca_id"];
                                $arrOptions["equipamento_sub_categoria_id"] = $retorno["equipamento_sub_categoria_id"];
                                $arrOptions["equipamento_categoria_id"]     = $retorno["equipamento_categoria_id"];
                                $carregaModelo                              = false ;

                                $this->equipamento_nome                                 = $arrOptions["equipamento_nome"];
                                $this->campos_estrutura["equipamento_id"]               = $retorno["equipamento_id"];
                                $this->campos_estrutura["equipamento_marca_id"]         = $retorno["equipamento_marca_id"];
                                $this->campos_estrutura["equipamento_sub_categoria_id"] = $retorno["equipamento_sub_categoria_id"];
                                $this->campos_estrutura["equipamento_categoria_id"]     = $retorno["equipamento_categoria_id"];
                            }
                        }
                        else{
                            if( $validaModelo && empty($parametros['marca']) && empty($parametros['modelo']) ){
                                die(json_encode(array("status"=>false,"message"=>"Os atributos '[ean] ou [marca e modelo]' não foram informados"),JSON_UNESCAPED_UNICODE));
                            }
                        }
                    } else {
                        $carregaModelo = false;
                    }

                    if ($carregaModelo)
                    {
                        if($validaModelo && empty($parametros['marca']))
                        {
                            if (!empty($msgBuscaEqip))
                                die(json_encode(array("status"=>false,"message"=>$msgBuscaEqip .". Informe o atributo `marca` para realizar a pesquisa alternativa."),JSON_UNESCAPED_UNICODE));
                            else
                                die(json_encode(array("status"=>false,"message"=>"Atributo 'marca' não informado"),JSON_UNESCAPED_UNICODE));
                        }
                        if(empty($parametros['modelo']))
                        {
                            if (!empty($msgBuscaEqip))
                                die(json_encode(array("status"=>false,"message"=>$msgBuscaEqip .". Informe o atributo `modelo` para realizar a pesquisa alternativa."),JSON_UNESCAPED_UNICODE));
                            else
                                die(json_encode(array("status"=>false,"message"=>"Atributo 'modelo' não informado"),JSON_UNESCAPED_UNICODE));
                        }

                        // pesquisa por marca e modelo
                        $fields = [
                            "modelo"     => $parametros['modelo'],
                            "marca"      => $parametros['marca'],
                            "categoria"  => isempty($parametros['categoria'], null),
                            "quantidade" => 1,
                        ];

                        $url = base_url() ."api/equipamento/modelo";
                        $r = $obj->execute($url, 'POST', json_encode($fields));

                        if($validaModelo && empty($r)) {
                            die(json_encode(array("status"=>false,"message"=>"Não foi possível realizar a consulta do equipamento por Marca/Modelo"),JSON_UNESCAPED_UNICODE));
                        }
                        else{
                            $retorno = json_decode($r,true);
                            if( $validaModelo && empty($retorno["status"]) ) {
                                $msg = ( !empty($retorno["mensagem"]) ) ? $retorno["mensagem"] : $r;
                                die(json_encode(array("status"=>false,"message"=>$msg),JSON_UNESCAPED_UNICODE));
                            }
                            else{
                                $arrOptions["ean"] = $retorno["dados"][0]["ean"];                            
                                $arrOptions["equipamento_id"] = $retorno["dados"][0]["equipamento_id"];
                                $arrOptions["equipamento_nome"] = $parametros['modelo'];
                                $arrOptions["equipamento_marca_id"] = $retorno["dados"][0]["equipamento_marca_id"];
                                $arrOptions["equipamento_sub_categoria_id"] = $retorno["dados"][0]["equipamento_sub_categoria_id"];
                                $arrOptions["equipamento_categoria_id"] = $retorno["dados"][0]["equipamento_categoria_id"];

                                $this->equipamento_nome = $arrOptions["equipamento_nome"] ;
                                $this->campos_estrutura["equipamento_id"] = $retorno["dados"][0]["equipamento_id"] ;
                                $this->campos_estrutura["equipamento_marca_id"] = $retorno["dados"][0]["equipamento_marca_id"] ;
                                $this->campos_estrutura["equipamento_sub_categoria_id"] = $retorno["dados"][0]["equipamento_sub_categoria_id"] ;
                                $this->campos_estrutura["equipamento_categoria_id"] = $retorno["dados"][0]["equipamento_categoria_id"] ;
                            }
                        }

                        $this->campos_estrutura = $arrOptions;
                    }
                }

                $url = base_url() ."api/cotacao";
                $r = $obj->execute($url, 'POST', json_encode($arrOptions));

                if(!empty($r))
                {
                    // pegando o ID da cotação
                    $retorno = convert_objeto_to_array($r);
                    if( !empty($retorno->{"status"}) )
                    {
                        $this->cotacao_id = $retorno->{"cotacao_id"};
                        $this->campos_estrutura['cotacao_id'] = $this->cotacao_id;
                        $this->produto_parceiro_id = $retorno->{"produto_parceiro_id"};

                        // Chamando o Calculo da cotação
                        $this->etapas('calculocotacao');
                    } 
                    else 
                    {
                        $msg = ( !empty($retorno->{"mensagem"}) ) ? $retorno->{"mensagem"} : $r;
                        die(json_encode(array("status"=>false,"message"=>$msg,"error"=>isset($r['erros']) ? $r['erros'] : null ),JSON_UNESCAPED_UNICODE));
                    }
                }
                else
                {
                    die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar os dados da cotação"),JSON_UNESCAPED_UNICODE));
                }

                break;

            case 'calculocotacao':

                // Validar o valor passado se diferente alertar e abortar
                $url = base_url() ."api/cotacao/calculo";
                $fields = [
                    'cotacao_id' => $this->cotacao_id,
                    'valor_fixo' => emptyor($this->premio_liquido, NULL),
                    'coberturas' => emptyor($this->campos_estrutura['coberturas'], []),
                ];

                $obj = new Api();
                $r = $obj->execute($url, 'POST', json_encode($fields));

                if(!empty($r))
                {
                    $retorno = convert_objeto_to_array($r);
                    if( !empty($retorno->{"status"}) )
                    {
                        // Validação valores  
                        if(!empty($this->valor_premio_bruto) && $this->valor_premio_bruto != $retorno->{"premio_liquido_total"}){
                            die(json_encode(array("status"=>false,"message"=>"O valor do prêmio {$this->valor_premio_bruto} informado diferente do valor calculado ".$retorno->{"premio_liquido_total"}, "cotacao_id" => $this->cotacao_id),JSON_UNESCAPED_UNICODE));
                        }

                        $retorno->{"cotacao_id"} = $this->cotacao_id;
                        $this->etapas('contratarcotacao',$retorno);
                    }
                    else
                    {
                        $msg = ( !empty($retorno->{"mensagem"}) ) ? $retorno->{"mensagem"} : $r;
                        die(json_encode(array("status"=>false, "message"=>$msg, "cotacao_id" => $this->cotacao_id),JSON_UNESCAPED_UNICODE));
                    }
                }
                else
                {
                    die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar os dados do cálculo da cotação"),JSON_UNESCAPED_UNICODE));
                }
                break;

            case 'contratarcotacao':

                if($parametros->{"status"})
                {
                    // Montando os dados - $this->campos_estrutura
                    $url = base_url() ."api/cotacao/contratar";
                    $obj = new Api();
                    $r = $obj->execute($url, 'POST', json_encode($this->campos_estrutura));

                    if(!empty($r))
                    {
                        $retorno = convert_objeto_to_array($r);
                        if( !empty($retorno->{"status"}) )
                        {
                            $this->etapas('formapagamento', $retorno);
                        }
                        else
                        {
                            $msg = ( !empty($retorno->{"mensagem"}) ) ? $retorno->{"mensagem"} : $r;
                            die(json_encode(array("status"=>false,"message"=>$msg,"error"=>isset($r['erros']) ? $r['erros'] : null ),JSON_UNESCAPED_UNICODE));
                        }
                    }
                    else
                    {
                        die(json_encode(array("status"=>false,"message"=>"Não foi possível efetuar a contratação"),JSON_UNESCAPED_UNICODE));
                    }
                }
                else
                {
                    die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar os dados do cálculo da cotação"),JSON_UNESCAPED_UNICODE));
                }
                break;

            case 'formapagamento':  

                if($parametros->{"status"})
                {

                    $url = base_url() ."api/pagamento/forma_pagamento_cotacao?cotacao_id={$this->cotacao_id}";
                    $obj = new Api();
                    $r = $obj->execute($url, 'GET');
                    if(!empty($r))
                    {
                        $retorno = convert_objeto_to_array($r);
                        $flag = false;
                        if (!empty($retorno) && is_array($retorno))
                        {
                            foreach ($retorno as $vl) {
                                if($vl->tipo->slug == $this->meio_pagto_slug) {
                                    $this->produto_parceiro_pagamento_id = $vl->pagamento[0]->produto_parceiro_pagamento_id;
                                    $this->forma_pagamento_id = $vl->pagamento[0]->forma_pagamento_id;
                                    $flag = true;
                                }
                            }
                        }
                        if(!$flag) {
                            die(json_encode(array("status"=>false,"message"=>"Não foi possível encontrar o meio_pagto_slug"),JSON_UNESCAPED_UNICODE));
                        }

                        $arrOptions = [
                            'status'=>true,
                            'cotacao_id'=>$this->cotacao_id,
                            'produto_parceiro_id'=>$this->produto_parceiro_id,
                            'produto_parceiro_pagamento_id'=>$this->produto_parceiro_pagamento_id,
                            'forma_pagamento_id'=>$this->forma_pagamento_id,
                            'campos'=>$this->campos_meios_pagto
                        ];

                        $this->etapas('efetuarpagamento',$arrOptions);
                    }
                    else
                    {
                        die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar a forma de pagamento"),JSON_UNESCAPED_UNICODE));
                    }
                }
                else{
                    die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar a contratação da cotação"),JSON_UNESCAPED_UNICODE));
                }

                break;

            case 'efetuarpagamento':

                if($parametros["status"])
                {
                    $arrOptions = [
                        "cotacao_id"                    => $parametros["cotacao_id"], 
                        "produto_parceiro_id"           => $parametros["produto_parceiro_id"], 
                        "forma_pagamento_id"            => $parametros["forma_pagamento_id"], 
                        "produto_parceiro_pagamento_id" => $parametros["produto_parceiro_pagamento_id"], 
                        "campos"                        => $parametros["campos"]  
                    ]; 

                    if ( !empty($this->parcelas) ) {
                        $arrOptions["parcelas"] = $this->parcelas;
                    }

                    $url = base_url() ."api/pagamento/pagar";
                    $obj = new Api();
                    $r = $obj->execute($url, 'POST', json_encode($arrOptions));
                    if(!empty($r))
                    {
                        $retorno = convert_objeto_to_array($r);
                        if( !empty($retorno->{"status"}) )
                        {
                            if (!empty($this->equipamento_nome)) $retorno->modelo = $this->equipamento_nome;
                            if (!empty($this->ean)) $retorno->ean = $this->ean;

                            $this->etapas('exibeapolice', $retorno);
                        }
                        else
                        {
                            $msg = ( !empty($retorno->{"mensagem"}) ) ? $retorno->{"mensagem"} : $r;
                            die(json_encode(array("status"=>false,"message"=>$msg, "cotacao_id" => $parametros["cotacao_id"]),JSON_UNESCAPED_UNICODE));
                        }           
                    }
                    else
                    {
                        die(json_encode(array("status"=>false,"message"=>"Não foi possível efetuar o pagamento"),JSON_UNESCAPED_UNICODE));
                    }
                }
                else
                {
                    die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar os dados da contratação"),JSON_UNESCAPED_UNICODE));
                }

                break;

            case 'exibeapolice':
                // Aqui verifico a apolice passada.
                if($parametros->status)
                {                 
                  	// Verificando se veio apólice 
                    if(!empty($this->num_apolice))
                    {
                        $upApolice = $this->apolice->updateBilhete($parametros->dados->apolice_id, $this->num_apolice);
                        if ( empty($upApolice['status']) )
                        {
                            die(json_encode(array("status"=>false, "message"=>$upApolice["message"], "pedido_id"=>$parametros->dados->pedido_id),JSON_UNESCAPED_UNICODE));
                        }
                    }

                    $url = base_url() ."api/apolice?pedido_id={$parametros->dados->pedido_id}" ;
                    $obj = new Api();
                    $r = $obj->execute($url, 'GET');

                    if(!empty($r))
                    {
                        $retorno = convert_objeto_to_array($r);
                        if( !empty($retorno->{"status"}) )
                        {
                            die(json_encode($retorno,JSON_PRETTY_PRINT));
                        }
                        else
                        {
                            $msg = ( !empty($retorno->{"mensagem"}) ) ? $retorno->{"mensagem"} : $r;
                            die(json_encode(array("status"=>false,"message"=>$msg, "pedido_id" => $parametros->dados->pedido_id),JSON_UNESCAPED_UNICODE));
                        }           
                    }
                    else
                    {
                        die(json_encode(array("status"=>true,"message"=>"O pedido foi confirmado, mas não foi possível exibir os dados da apólice", 'dados' => $parametros->dados),JSON_UNESCAPED_UNICODE));
                    }
                }
                else
                {
                    die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar os dados da efetivação do pagamento"),JSON_UNESCAPED_UNICODE));
                }

                break;  
                /* ---------fim processo --------------- */

            default:
                die(json_encode("Opção inválida $etapa", JSON_UNESCAPED_UNICODE));
                break;
        }
    }

}

