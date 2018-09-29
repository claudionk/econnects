<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Cotacao extends CI_Controller {
  public $api_key;
  public $usuario_id;

  const PRECO_TIPO_TABELA = 1;
  const PRECO_TIPO_COBERTURA = 2;
  const PRECO_TIPO_VALOR_SEGURADO = 3;
  const PRECO_POR_EQUIPAMENTO = 5;

  const TIPO_CALCULO_NET = 1;
  const TIPO_CALCULO_BRUTO = 2;


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
        die( json_encode( array( "status" => false, "message" => "APIKEY is invalid" ) ) );
      }
    } else {
      die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
    }
    $this->usuario_id = $webservice["usuario_id"];
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
          die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }
      }
    }
  }

  private function post( $POST ) {

    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('cotacao_generico_model', 'cotacao_generico');
    $this->load->model('produto_parceiro_campo_model', 'produto_parceiro_campo');

    if( !isset( $POST["produto_parceiro_id"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo produto_parceiro_id é obrigatório" ) ) );
    }

    $result = array();
    $produto_parceiro_id = $POST["produto_parceiro_id"];
    $cotacao_id = null;
    
    if( $_SERVER["REQUEST_METHOD"] === "PUT" ) {
      if( !isset( $POST["cotacao_id"] ) ) {
        die( json_encode( array( "status" => false, "message" => "Campo cotacao_id é obrigatório no método PUT" ) ) );
      }
      $cotacao_id = $POST["cotacao_id"];
    } else {
      if( isset( $POST["cotacao_id"] ) ) {
        unset( $POST["cotacao_id"] );
      }
    }

    $produto = $this->produto_parceiro->with_produto()->get( $produto_parceiro_id );
    $POST["parceiro_id"] = $produto["parceiro_id"];
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
    }

    //$this->cotacao->setValidate( $validacao );
    //if( $this->cotacao->validate_form( "cotacao" ) )

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
      die( json_encode( $cotacao, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    } else {
      die( json_encode( array( "status" => false, "message" => "Erro de validação", "erros" => $erros ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }
  }

  private function get( $GET ) {
    $cotacao_id = null;
    if( !isset( $GET["cotacao_id"] ) ) {
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
        die( json_encode( $cotacao_itens, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
      }
      die( json_encode( $cotacao, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }
  }

  private function put() {

  }
  
  
  public function calculo() {
    if( $_SERVER["REQUEST_METHOD"] !== "GET" ) {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    $GET = $_GET;
    $this->load->model(" cotacao_model", "cotacao" );
    $this->load->model( "cotacao_equipamento_model", "cotacao_equipamento" );
    $this->load->model( "cotacao_generico_model", "cotacao_generico" );
    $this->load->model( "produto_parceiro_campo_model", "produto_parceiro_campo" );

    $cotacao_id = null;
    if( !isset( $GET["cotacao_id"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo cotacao_id é obrigatório" ) ) );
    }
    $cotacao_id = $GET["cotacao_id"];

    $produto_parceiro_id = null;
    if( isset( $GET["produto_parceiro_id"] ) )
      $produto_parceiro_id = $GET["produto_parceiro_id"];
    
    $equipamento_marca_id = null;
    if( isset( $GET["equipamento_marca_id"] ) )
    $equipamento_marca_id = $GET["equipamento_marca_id"];
      
    $equipamento_categoria_id = null;
    if( isset( $GET["equipamento_categoria_id"] ) )
    $equipamento_categoria_id = $GET["equipamento_categoria_id"];
      
    $quantidade = null;
    if( isset( $GET["quantidade"] ) )
    $quantidade = $GET["quantidade"];
      
    $coberturas = null;
    if( isset( $GET["coberturas"] ) )
    $coberturas = $GET["coberturas"];
      
    $repasse_comissao = 0;
    if( isset( $GET["repasse_comissao"] ) )
      $repasse_comissao = $GET["repasse_comissao"];
    
    $desconto_condicional = 0;
    if( isset( $GET["desconto_condicional"] ) )
      $desconto_condicional = $GET["desconto_condicional"];
    
    $result = array();

    if( is_null( $produto_parceiro_id ) ) {
      $cotacao = $this->cotacao->get_by_id( $cotacao_id );
      $produto_parceiro_id = $cotacao["produto_parceiro_id"];
    }
    $produto = $this->current_model->with_produto()->get($produto_parceiro_id);
    
    $params["cotacao_id"] = $cotacao_id;
    $params["produto_parceiro_id"] = $produto_parceiro_id;
    $params["parceiro_id"] = $produto['parceiro_id'];
    $params["equipamento_marca_id"] = $equipamento_marca_id;
    $params["equipamento_categoria_id"] = $equipamento_categoria_id;
    $params["quantidade"] = $quantidade;
    $params["coberturas"] = $coberturas;
    $params["repasse_comissao"] = $repasse_comissao;
    $params["desconto_condicional"] = $desconto_condicional;


    if( $produto["produto_slug"] == "equipamento" ){
      $result = $this->calculo_cotacao_equipamento( $params );
    }elseif( $produto["produto_slug"] == "generico" ){
      $result = $this->calculo_cotacao_generico( $params );
    }
    die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
  }

  public function calculo_cotacao_equipamento( $params = array() ) {
    $this->load->model('produto_parceiro_campo_model', 'campo');
    $this->load->model('pedido_model', 'pedido');
    $this->load->model('produto_parceiro_plano_model', 'plano');
    $this->load->model('cobertura_plano_model', 'plano_cobertura');
    $this->load->model('cobertura_model', 'cobertura');
    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('produto_parceiro_desconto_model', 'desconto');
    $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
    $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
    $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');

    $success = true;
    $mensagem = "";


    //print_r($_POST);

    $produto_parceiro_id = issetor($params['produto_parceiro_id'], 0);
    $parceiro_id = issetor($params['parceiro_id'], 0);
    $equipamento_marca_id = issetor($params['equipamento_marca_id'], 0);
    $equipamento_categoria_id = issetor($params['equipamento_categoria_id'], 0);
    $quantidade = issetor($params['quantidade'], 0);

    $coberturas_adicionais = issetor($params['coberturas'], array());

    $repasse_comissao = $params["repasse_comissao"];
    $desconto_condicional= $params["desconto_condicional"];
    
    $desconto = $this->desconto->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

    $desconto_upgrade = 0;
    $cotacao_id = issetor($params['cotacao_id'], 0);

    if($cotacao_id) {
      $cotacao = $this->cotacao->get($cotacao_id);

      if(($cotacao) && ((int)$cotacao['cotacao_upgrade_id']) > 0){

        $pedido_antigo = $this->pedido->get_by(array('cotacao_id' => $cotacao['cotacao_upgrade_id']));
        $desconto_upgrade = $pedido_antigo['valor_total'];

      }
    }


    $cotacao = $this->cotacao->with_cotacao_equipamento()->filterByID($cotacao_id)->get_all(0,0,false);
    $cotacao = $cotacao[0];


    $row =  $this->current_model->get_by_id($produto_parceiro_id);

    if(count($desconto) > 0){
      $desconto = $desconto[0];
    }else{
      $desconto = array('habilitado' => 0);
    }

    $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

    if(count($configuracao) > 0){
      $configuracao = $configuracao[0];
    }else{
      $configuracao = array();
    }


    $markup = 0;
    if($row['parceiro_id'] != $parceiro_id){


      $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $parceiro_id);

      $configuracao["repasse_comissao"] =  floatval($rel["repasse_comissao"]);
      $configuracao["repasse_maximo"] = $rel["repasse_maximo"];
      $configuracao["comissao"] = $rel["comissao"];


      //buscar o markup
      $markup = $this->relacionamento->get_comissao_markup($produto_parceiro_id, $parceiro_id);


      $rel_desconto = $this->relacionamento->get_desconto($produto_parceiro_id, $parceiro_id);
      if(count($rel_desconto) > 0){
        $desconto['data_ini'] = $rel_desconto['desconto_data_ini'];
        $desconto['data_fim'] = $rel_desconto['desconto_data_fim'];
        $desconto['habilitado'] = $rel_desconto['desconto_habilitado'];
      }else{
        $desconto = array('habilitado' => 0);
      }



    }

    if($repasse_comissao > $configuracao["repasse_maximo"] ){
      $repasse_comissao = $configuracao["repasse_maximo"];
    }

    //$repasse_comissao = str_pad(number_format((double)$repasse_comissao, 2, '.', ''), 5, "0", STR_PAD_LEFT);

    $comissao_corretor = ($configuracao['comissao'] - $repasse_comissao);

    $valores_bruto = $this->getValoresPlano($produto_parceiro_id, $equipamento_marca_id, $equipamento_categoria_id, $cotacao['nota_fiscal_valor'], $quantidade);

    $valores_cobertura_adicional_total = array();
    $valores_cobertura_adicional = array();
    
    if($coberturas_adicionais){
      foreach ($coberturas_adicionais as $coberturas_adicional) {
        $cobertura = explode(';', $coberturas_adicional);
        $vigencia = $this->plano->getInicioFimVigencia($cobertura[0], $cotacao['nota_fiscal_data']);

        $valor = $this->getValorCoberturaAdicional($cobertura[0], $cobertura[1], $vigencia['dias']);
        $valores_cobertura_adicional_total[$cobertura[0]] = (isset($valores_cobertura_adicional_total[$cobertura[0]])) ? ($valores_cobertura_adicional_total[$cobertura[0]] + $valor) : $valor;
        $valores_cobertura_adicional[$cobertura[0]][] = $valor;
      }
    }

    if(!$valores_bruto) {
      $result  = array(
        'status' => FALSE,
        'produto_parceiro_id' => $produto_parceiro_id,
        'repasse_comissao' => 0,
        'comissao' => 0,
        'desconto_upgrade' => 0,
        'desconto_condicional_valor' => 0,
        //'valores_bruto' => 0,
        //'valores_cobertura_adicional' => 0,
        //'valores_totais_cobertura_adicional' => 0,
        //'valores_liquido' => 0,
        //'valores_liquido_total' => 0,
        'mensagem' => 'TABELA DE PREÇO NÃO CONFIGURADA',
        'quantidade' => $quantidade,
      );
      return $result;
    }


    if($row['venda_agrupada']){
      $arrPlanos = $this->plano->distinct()
        ->with_produto_parceiro()
        ->with_produto();

      if((isset($cotacao['origem_id'])) && ($cotacao['origem_id'])){
        $arrPlanos->with_origem($cotacao['origem_id']);
      }
      if((isset($cotacao['destino_id'])) && ($cotacao['destino_id'])){
        $arrPlanos->with_destino($cotacao['destino_id']);
      }
      if((isset($cotacao['faixa_salarial_id'])) && ($cotacao['faixa_salarial_id'])){
        $arrPlanos->with_faixa_salarial($cotacao['faixa_salarial_id']);
      }

      $arrPlanos = $arrPlanos
        ->get_many_by(array(
          'produto_parceiro.venda_agrupada' => 1
        ));

    }else {
      $arrPlanos = $this->plano->filter_by_produto_parceiro($produto_parceiro_id);

      if((isset($cotacao['origem_id'])) && ($cotacao['origem_id'])){
        $arrPlanos->with_origem($cotacao['origem_id']);
      }
      if((isset($cotacao['destino_id'])) && ($cotacao['destino_id'])){
        $arrPlanos->with_destino($cotacao['destino_id']);
      }
      if((isset($cotacao['faixa_salarial_id'])) && ($cotacao['faixa_salarial_id'])){
        $arrPlanos->with_faixa_salarial($cotacao['faixa_salarial_id']);
      }

      $arrPlanos = $arrPlanos->get_all();
    }
    $valores_liquido = array();

    //verifica o limite da vigencia dos planos
    $fail_msg = '';

    /**
         * FAZ O CÁLCULO DO PLANO
         */
    $desconto_condicional_valor = 0;
    foreach ($arrPlanos as $plano){
      //precificacao_tipo_id

      switch ((int)$configuracao['calculo_tipo_id'])
      {
        case self::TIPO_CALCULO_NET:
          $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
          $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
          $valor = ($valor/(1-(($markup + $comissao_corretor)/100)));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
          break;
        case self::TIPO_CALCULO_BRUTO:
          $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
          $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
          $valor = ($valor) - (($valor) * (($markup + $comissao_corretor)/100));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
          break;
        default:
          break;


      }

    }


    $regra_preco = $this->regra_preco->with_regra_preco()
      ->filter_by_produto_parceiro($produto_parceiro_id)
      ->get_all();

    $iof = 0;
    $valores_liquido_total = array();
    foreach ($valores_liquido as $key => $value) {
      $valores_liquido_total[$key] = $value;
      foreach ($regra_preco as $regra) {
        $valores_liquido_total[$key] += (($regra['parametros']/100) * $value);
        $valores_liquido_total[$key] -= $desconto_upgrade;
        if( strtoupper($regra["regra_preco_nome"]) == "IOF" ) {
          $iof = $regra['parametros'];
        }
      }
    }

    //Resultado
    $result  = array(
      'status' => $success,
      'produto_parceiro_id' => $produto_parceiro_id,
      'repasse_comissao' => (float)$repasse_comissao,
      'comissao' => (float)$comissao_corretor,
      'desconto_upgrade' => (float)$desconto_upgrade,
      'desconto_condicional_valor' => (float)$desconto_condicional_valor,
      //'valores_bruto' => $valores_bruto,
      //'valores_cobertura_adicional' => $valores_cobertura_adicional,
      //'valores_totais_cobertura_adicional' => $valores_cobertura_adicional_total,
      //'valores_liquido' => $valores_liquido,
      //'valores_liquido_total' => $valores_liquido_total,
      'premio_liquido' => (float)$valores_liquido[$plano['produto_parceiro_plano_id']],
      'premio_liquido_total' => (float)$valores_liquido_total[$plano['produto_parceiro_plano_id']],
      'iof' => (float)$iof
    );

    //Salva cotação

    if($cotacao_id) {
      $cotacao_salva = $this->cotacao->with_cotacao_equipamento()
        ->filterByID($cotacao_id)
        ->get_all();

      $cotacao_salva = $cotacao_salva[0];
      $cotacao_eqp = array();
      $cotacao_eqp['repasse_comissao'] = $repasse_comissao;
      $cotacao_eqp['comissao_corretor'] = $comissao_corretor;
      $cotacao_eqp['desconto_condicional'] = $desconto_condicional;
      $cotacao_eqp['desconto_condicional_valor'] = $desconto_condicional_valor;
      $cotacao_eqp['premio_liquido'] = $valores_liquido[$plano['produto_parceiro_plano_id']];
      $cotacao_eqp['premio_liquido_total'] = $valores_liquido_total[$plano['produto_parceiro_plano_id']];
      $cotacao_eqp['iof'] = $iof;

      $coberturas = $this->db->query( "SELECT 
    								   * 
                                     FROM 
                                       cobertura_plano cp 
                                       INNER JOIN cobertura c ON (c.cobertura_id=cp.cobertura_id) 
                                     WHERE 
                                       produto_parceiro_plano_id IN 
                                       (SELECT produto_parceiro_plano_id FROM produto_parceiro_plano WHERE produto_parceiro_id=$produto_parceiro_id and deletado=0) 
                                       AND cobertura_tipo_id=1" )->result_array();


      $pedido = $this->db->query( "SELECT * FROM pedido WHERE cotacao_id=$cotacao_id AND deletado=0" )->result_array();
      if( $pedido ) {
        die( json_encode( array( "status" => false, "message" => "Não foi possível efetuar o calculo. Motivo: já existe um pedido para essa cotação." ) ) );
      } else {
        $this->cotacao_equipamento->update($cotacao_salva['cotacao_equipamento_id'], $cotacao_eqp, TRUE);
        $this->db->query( "DELETE FROM cotacao_cobertura WHERE cotacao_id=$cotacao_id" );
        for( $i = 0; $i < sizeof( $coberturas ); $i++ ) {
          $cobertura = $coberturas[$i];
          $cobertura_plano_id = $cobertura["cobertura_plano_id"];
          $importancia_segurada = floatval( $cotacao["nota_fiscal_valor"] );
          $percentagem = 0;
          $valor_cobertura = 0;
          if( $cobertura["mostrar"] == "importancia_segurada" ) {
            $percentagem = floatval($cobertura["porcentagem"]);
            $valor_cobertura = ( $importancia_segurada * $percentagem ) / 100;
          }elseif( $cobertura["mostrar"] == "preco" || $cobertura["mostrar"] == "descricao" ) {
            $percentagem = 0;
            $valor_cobertura = floatval($cobertura["preco"]);
          }
          $coberturas[$i]["valor_cobertura"] = $valor_cobertura;
          
          $this->db->query( "INSERT INTO cotacao_cobertura (cotacao_id, cobertura_plano_id, valor, criacao ) values( $cotacao_id, $cobertura_plano_id, $valor_cobertura, '" . date("Y-m-d H:i:s") . "')" );
        }
      }
      $result["importancia_segurada"] = $cotacao["nota_fiscal_valor"];
      $result["coberturas"] = $coberturas;
      $result["mensagem"] = $mensagem;
    }

    return $result;

  }

  private function getValoresPlano($produto_parceiro_id, $equipamento_marca_id, $equipamento_categora_id, $valor_nota, $quantidade = 1){

    $this->load->model('produto_parceiro_plano_model', 'plano');
    $this->load->model('moeda_model', 'moeda');
    $this->load->model('moeda_cambio_model', 'moeda_cambio');

    $moeda_padrao = $this->moeda->filter_by_moeda_padrao()->get_all();
    $moeda_padrao = $moeda_padrao[0];

    $quantidade = ((int)$quantidade <=0) ? 1 : (int)$quantidade;

    $produto_parceiro =  $this->current_model->get_by_id($produto_parceiro_id);
    if($produto_parceiro['venda_agrupada']) {
      $arrPlanos = $this->plano->distinct()
        ->with_produto_parceiro()
        ->with_produto()
        ->get_many_by(array(
          'produto_parceiro.venda_agrupada' => 1
        ));
    }else{
      $arrPlanos = $this->plano->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
    }


    $valores = array();
    foreach ($arrPlanos as $plano){
      $valor_cobertura_plano = 0;
      switch ((int)$plano['precificacao_tipo_id']) {
        case self::PRECO_TIPO_TABELA:

          $calculo = $this->getValorTabelaFixa($plano['produto_parceiro_plano_id'], $equipamento_categora_id, $equipamento_marca_id, $valor_nota) * $quantidade;

          if($calculo)
            $valores[$plano['produto_parceiro_plano_id']] = $calculo;
          else
            return null;

          if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
            $valores[$plano['produto_parceiro_plano_id']] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$plano['produto_parceiro_plano_id']]);
          }
          break;
        case self::PRECO_TIPO_COBERTURA:
          $this->load->model("produto_parceiro_plano_precificacao_itens_model", "produto_parceiro_plano_precificacao_itens");
          $this->load->model("produto_parceiro_plano_precificacao_model", "produto_parceiro_plano_precificacao");
          $this->load->model("equipamento_model", "equipamento");
          $this->load->model("cobertura_plano_model", "plano_cobertura");
          $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
          
          $arrCoberturas = $this->plano_cobertura->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)->get_all();
          foreach ($arrCoberturas as $idx => $cob) {
            if( $arrCoberturas[$idx]["mostrar"] == "importancia_segurada" ) {
              $valor_cobertura_plano = $valor_cobertura_plano + floatval( $valor_nota ) * ( floatval( $arrCoberturas[$idx]["porcentagem"] ) / 100 );
            }
            if( $arrCoberturas[$idx]["mostrar"] == "preco" ) {
              $valor_cobertura_plano = $valor_cobertura_plano + floatval( $arrCoberturas[$idx]["preco"] );
            }
          }
          
          $valores[$produto_parceiro_plano_id] = $valor_cobertura_plano;
          
          break;
        case self::PRECO_TIPO_VALOR_SEGURADO:
          $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
          $this->load->model('equipamento_model', 'equipamento');
          $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
          $valor = $this->produto_parceiro_plano_precificacao_itens
            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
            ->filter_by_tipo_equipamento("TODOS")
            ->get_all();
          $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );
          break;
        case self::PRECO_POR_EQUIPAMENTO;
          $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
          $this->load->model('equipamento_model', 'equipamento');
          $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
          $valor = $this->produto_parceiro_plano_precificacao_itens
            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
            ->filter_by_faixa( $valor_nota )
            ->filter_by_tipo_equipamento("CATEGORIA")
            ->filter_by_equipamento($equipamento_categora_id)
            ->get_all();

          $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );

          break;
          
        default:
          break;


      }
    }
    return $valores;

  }

  private function getValorCoberturaAdicional($produto_parceiro_plano_id, $cobertura_plano_id, $qntDias){
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

  /**
     * Busca o Valor da tabela FIXA
     * @param $produto_parceiro_plano_id
     * @param $equipamento_nome
     * @return mixed|null
     */

  private function getValorTabelaFixa($produto_parceiro_plano_id, $equipamento_categoria_id, $equipamento_marca_id, $valor_nota) {


    $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
    $this->load->model('equipamento_model', 'equipamento');


    $valor = $this->produto_parceiro_plano_precificacao_itens
      ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
      ->filter_by_tipo_equipamento('TODOS')
      ->get_all();

    //error_log( "Valor nota: $valor_nota\nTipo cobranca: " . print_r( $valor, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

    if(count($valor) > 0)
    {
      $valor = $valor[0];
      if($valor['cobranca'] == 'PORCENTAGEM')
      {
        return app_calculo_porcentagem($valor['valor'], $valor_nota);
      }
      else
      {
        return $valor['valor'];
      }

    }else{


      //BUSCA POR CATEGORIA / LINHA
      $valor = $this->produto_parceiro_plano_precificacao_itens
        ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
        ->filter_by_tipo_equipamento('TODOS')
        ->filter_by_equipamento($equipamento_categoria_id)
        ->get_all();

      //error_log( "Valor nota: $valor_nota\nTipo cobranca: " . print_r( $valor, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

      if(count($valor) > 0) {
        $valor = $valor[0];
        if($valor['cobranca'] == 'PORCENTAGEM'){
          return app_calculo_porcentagem($valor['valor'], $valor_nota);
        }else{
          return $valor['valor'];
        }
      }
      /*
                        //BUSCA POR SUB CATEGORIA CATEGORIA / LINHA
                        $valor = $this->produto_parceiro_plano_precificacao_itens
                            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                            ->filter_by_tipo_equipamento('POR CATEGORIA / LINHA')
                            ->filter_by_equipamento($equipamento['equipamento_sub_categoria_id'])
                            ->get_all();

                        if(count($valor) > 0) {
                            $valor = $valor[0];
                            if($valor['cobranca'] == 'PORCENTAGEM'){
                                return app_calculo_porcentagem($valor['valor'], $valor_nota);
                            }else{
                                return $valor['valor'];
                            }
                        }*/


      //BUSCA POR MARCA
      $valor = $this->produto_parceiro_plano_precificacao_itens
        ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
        ->filter_by_tipo_equipamento('MARCA')
        ->filter_by_equipamento($equipamento_marca_id)
        ->get_all();

      //error_log( "Valor nota: $valor_nota\nTipo cobranca: " . print_r( $valor, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

      if(count($valor) > 0) {
        $valor = $valor[0];
        if($valor['cobranca'] == 'PORCENTAGEM'){
          return app_calculo_porcentagem($valor['valor'], $valor_nota);
        }else{
          return $valor['valor'];
        }
      }

    }

    return null;

  }


  public function calculo_cotacao_generico( $params = array() ) {
    $this->load->model('produto_parceiro_campo_model', 'campo');
    $this->load->model('pedido_model', 'pedido');
    $this->load->model('produto_parceiro_plano_model', 'plano');
    $this->load->model('cobertura_plano_model', 'plano_cobertura');
    $this->load->model('cobertura_model', 'cobertura');
    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_generico_model', 'cotacao_generico');
    $this->load->model('produto_parceiro_desconto_model', 'desconto');
    $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
    $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
    $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
    $this->load->model('servico_produto_model', 'servico_produto');


    $success = true;
    $mensagem = "";


    //print_r($_POST);


    $produto_parceiro_id = issetor($params['produto_parceiro_id'], 0);
    $parceiro_id = issetor($params['parceiro_id'], 0);
    $equipamento_marca_id = issetor($params['equipamento_marca_id'], 0);
    $equipamento_categoria_id = issetor($params['equipamento_categoria_id'], 0);
    $quantidade = issetor($params['quantidade'], 0);

    $coberturas_adicionais = issetor($params['coberturas'], array());

    $repasse_comissao = $params["repasse_comissao"];
    $desconto_condicional= $params["desconto_condicional"];
    $desconto = $this->desconto->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

    $desconto_upgrade = 0;
    $cotacao_id = issetor($params['cotacao_id'], 0);




    if($cotacao_id) {
      $cotacao = $this->cotacao->get($cotacao_id);




      if(($cotacao) && ((int)$cotacao['cotacao_upgrade_id']) > 0){

        $pedido_antigo = $this->pedido->get_by(array('cotacao_id' => $cotacao['cotacao_upgrade_id']));
        $desconto_upgrade = $pedido_antigo['valor_total'];

      }
    }


    $cotacao = $this->cotacao->with_cotacao_generico()->filterByID($cotacao_id)->get_all();
    $cotacao = $cotacao[0];


    $row =  $this->current_model->get_by_id($produto_parceiro_id);

    if(count($desconto) > 0){
      $desconto = $desconto[0];
    }else{
      $desconto = array('habilitado' => 0);
    }

    $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

    if(count($configuracao) > 0){
      $configuracao = $configuracao[0];
    }else{
      $configuracao = array();
    }


    $markup = 0;
    if($row['parceiro_id'] != $parceiro_id){


      $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $parceiro_id);

      $configuracao['repasse_comissao'] =  $rel['repasse_comissao'];
      $configuracao['repasse_maximo'] = $rel['repasse_maximo'];
      $configuracao['comissao'] = $rel['comissao'];


      //buscar o markup
      $markup = $this->relacionamento->get_comissao_markup($produto_parceiro_id, $parceiro_id);



      $rel_desconto = $this->relacionamento->get_desconto($produto_parceiro_id, $parceiro_id);
      if(count($rel_desconto) > 0){
        $desconto['data_ini'] = $rel_desconto['desconto_data_ini'];
        $desconto['data_fim'] = $rel_desconto['desconto_data_fim'];
        $desconto['habilitado'] = $rel_desconto['desconto_habilitado'];
      }else{
        $desconto = array('habilitado' => 0);
      }



    }

    if($repasse_comissao > $configuracao['repasse_maximo'] ){
      $repasse_comissao = floatval( $configuracao["repasse_maximo"] );
    }

    //$repasse_comissao = str_pad(number_format((double)$repasse_comissao, 2, '.', ''), 5, "0", STR_PAD_LEFT);

    $comissao_corretor = ($configuracao['comissao'] - $repasse_comissao);


    $servico_produto_id = $cotacao['servico_produto_id'] ? $cotacao['servico_produto_id'] : 0;
    $servico_produto = $this->servico_produto->get($servico_produto_id);


    if($servico_produto && $quantidade < $servico_produto['quantidade_minima'])
    {
      $quantidade = $servico_produto['quantidade_minima'];
    }

    $valores_bruto = $this->getValoresPlanoGenerico($produto_parceiro_id, $quantidade, $servico_produto_id);

    $valores_cobertura_adicional_total = array();
    $valores_cobertura_adicional = array();
    if($coberturas_adicionais){

      foreach ($coberturas_adicionais as $coberturas_adicional) {
        $cobertura = explode(';', $coberturas_adicional);
        $vigencia = $this->plano->getInicioFimVigencia($cobertura[0], date('Y-m-d'));

        $valor = $this->getValorCoberturaAdicionalGenerico($cobertura[0], $cobertura[1], $vigencia['dias']);
        $valores_cobertura_adicional_total[$cobertura[0]] = (isset($valores_cobertura_adicional_total[$cobertura[0]])) ? ($valores_cobertura_adicional_total[$cobertura[0]] + $valor) : $valor;
        $valores_cobertura_adicional[$cobertura[0]][] = $valor;

      }

    }



    if(!$valores_bruto)
    {
      $result = array(
        'status' => false,
        'produto_parceiro_id' => $produto_parceiro_id,
        'repasse_comissao' => 0,
        'comissao' => 0,
        'desconto_upgrade' => 0,
        'desconto_condicional_valor' => 0,
        'valores_bruto' => 0,
        'valores_cobertura_adicional' => 0,
        'valores_totais_cobertura_adicional' => 0,
        'valores_liquido' => 0,
        'valores_liquido_total' => 0,
        'mensagem' => 'TABELA DE PREÇO NÃO CONFIGURADA',
        'quantidade' => $quantidade,
      );
      return $result;
    }


    if($row['venda_agrupada']){
      $arrPlanos = $this->plano->distinct()
        ->order_by('produto_parceiro_plano.ordem', 'asc')
        ->with_produto_parceiro()
        ->with_produto()
        ->get_many_by(array(
          'produto_parceiro.venda_agrupada' => 1
        ));

    }else {
      $arrPlanos = $this->plano->order_by('produto_parceiro_plano.ordem', 'asc')->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
    }
    $valores_liquido = array();

    //verifica o limite da vigencia dos planos
    $fail_msg = '';

    /**
         * FAZ O CÁLCULO DO PLANO
         */
    $desconto_condicional_valor = 0;
    foreach ($arrPlanos as $plano){
      //precificacao_tipo_id

      switch ((int)$configuracao['calculo_tipo_id'])
      {
        case self::TIPO_CALCULO_NET:
          $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
          $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
          $valor = ($valor/(1-(($markup + $comissao_corretor)/100)));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
          break;
        case self::TIPO_CALCULO_BRUTO:
          $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
          $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
          $valor = ($valor) - (($valor) * (($markup + $comissao_corretor)/100));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
          break;
        default:
          break;


      }

    }


    $regra_preco = $this->regra_preco->with_regra_preco()
      ->filter_by_produto_parceiro($produto_parceiro_id)
      ->get_all();

    $valores_liquido_total = array();
    foreach ($valores_liquido as $key => $value) {
      $valores_liquido_total[$key] = $value;
      foreach ($regra_preco as $regra) {
        $valores_liquido_total[$key] += (($regra['parametros']/100) * $value);
        $valores_liquido_total[$key] -= $desconto_upgrade;
      }
    }

    //Resultado
    $result  = array(
      'status' => $success,
      'produto_parceiro_id' => $produto_parceiro_id,
      'repasse_comissao' => $repasse_comissao,
      'comissao' => $comissao_corretor,
      'desconto_upgrade' => $desconto_upgrade,
      'desconto_condicional_valor' => $desconto_condicional_valor,
      'valores_bruto' => $valores_bruto,
      'valores_cobertura_adicional' => $valores_cobertura_adicional,
      'valores_totais_cobertura_adicional' => $valores_cobertura_adicional_total,
      'valores_liquido' => $valores_liquido,
      'valores_liquido_total' => $valores_liquido_total,
      'mensagem' => $mensagem,
      'quantidade' => $quantidade,
    );

    //Salva cotação

    if($cotacao_id) {
      $cotacao_salva = $this->cotacao->with_cotacao_generico()
        ->filterByID($cotacao_id)
        ->get_all();

      $cotacao_salva = $cotacao_salva[0];
      $data_cotacao = array();
      $data_cotacao['repasse_comissao'] = $repasse_comissao;
      $data_cotacao['comissao_corretor'] = $comissao_corretor;
      $data_cotacao['desconto_condicional'] = $desconto_condicional;
      $data_cotacao['desconto_condicional_valor'] = $desconto_condicional_valor;
      $this->cotacao_generico->update($cotacao_salva['cotacao_generico_id'], $data_cotacao, TRUE);
    }

    //Seta sessão
    // $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $cotacao);

    return $result;
  }

  private function getValoresPlanoGenerico($produto_parceiro_id, $quantidade = 1, $servico_produto_id = 0){

    $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
    $this->load->model('produto_parceiro_plano_precificacao_servico_model', 'produto_parceiro_plano_precificacao_servico');
    $this->load->model('moeda_model', 'moeda');
    $this->load->model('moeda_cambio_model', 'moeda_cambio');


    $quantidade = ((int)$quantidade <=0) ? 1 : (int)$quantidade;

    $moeda_padrao = $this->moeda->filter_by_moeda_padrao()->get_all();
    $moeda_padrao = $moeda_padrao[0];

    $produto_parceiro =  $this->current_model->get_by_id($produto_parceiro_id);
    if($produto_parceiro['venda_agrupada']) {
      $arrPlanos = $this->produto_parceiro_plano->distinct()
        ->with_produto_parceiro()
        ->order_by('produto_parceiro_plano.ordem', 'asc')
        ->with_produto()
        ->get_many_by(array(
          'produto_parceiro.venda_agrupada' => 1
        ));
    }else{
      $arrPlanos = $this->produto_parceiro_plano->order_by('produto_parceiro_plano.ordem', 'asc')->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
    }
    $valores = array();
    foreach ($arrPlanos as $plano){
      switch ((int)$plano['precificacao_tipo_id'])
      {
        case self::PRECO_TIPO_TABELA:

          $vigencia = $this->produto_parceiro_plano->getInicioFimVigencia($plano['produto_parceiro_plano_id'], date('Y-m-d'));

          $calculo = $this->getValorTabelaFixaGenerico($plano['produto_parceiro_plano_id'], $vigencia['dias'])*$quantidade;
          if($calculo)
            $valores[$plano['produto_parceiro_plano_id']] = $calculo;
          else
            return null;

          if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
            $valores[$plano['produto_parceiro_plano_id']] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$plano['produto_parceiro_plano_id']]);
          }
          break;
        case self::PRECO_TIPO_COBERTURA:
          break;
        case self::PRECO_TIPO_VALOR_SEGURADO:
          break;
        case self::PRECO_TIPO_FIXO_SERVICO:

          $preco = $this->produto_parceiro_plano_precificacao_servico
            ->get_by(array(
              'produto_parceiro_plano_id' => $plano['produto_parceiro_plano_id'],
              'servico_produto_id' => $servico_produto_id,
            ));

          if($preco)
          {
            $valores[$plano['produto_parceiro_plano_id']] = (float) $preco['valor'] * (int) $quantidade;
          }
          else
            return null;

          break;
        default:
          break;


      }
    }


    return $valores;


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

  private function getValorTabelaFixaGenerico($produto_parceiro_plano_id, $qntDias){
    $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'itens');

    $valor = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
      ->filter_by_intevalo_dias($qntDias)
      ->filter_by_tipo('RANGE')
      ->get_all();

    if(count($valor) > 0){
      return $valor[0]['valor'];
    }else{
      //não achou

      //Verifica se Busca por mês
      if(($qntDias >= 30) && ($qntDias % 30) == 0){
        $valor = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
          ->filter_by_intevalo_dias(floor($qntDias/30), 'MES')
          ->filter_by_tipo('RANGE')
          ->get_all();
        if(count($valor) > 0){
          return $valor[0]['valor'];
        }
      }


      //Verifica se Busca por ANO
      if(($qntDias >= 365) && ($qntDias % 365) == 0){
        $valor = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
          ->filter_by_intevalo_dias(floor($qntDias/365), 'ANO')
          ->filter_by_tipo('RANGE')
          ->get_all();
        if(count($valor) > 0){
          return $valor[0]['valor'];
        }
      }


      $ultimo = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
        ->filter_by_intevalo_menor($qntDias, 'DIA')
        ->order_by('produto_parceiro_plano_precificacao_itens.final', 'DESC')
        ->filter_by_tipo('RANGE')
        ->limit(1)
        ->get_all();

      if(is_array($ultimo) && sizeof($ultimo) > 0)
        $ultimo = $ultimo[0];


      $valor_adicional = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
        ->filter_by_tipo('ADICIONAL')
        ->limit(1)
        ->get_all();

      if(is_array($valor_adicional) && sizeof($valor_adicional) > 0)
      {
        $valor_adicional = $valor_adicional[0];

        $valor = $ultimo['valor'];
        for ($i = $ultimo['final']; $i < $qntDias; $i++){
          $valor += $valor_adicional['valor'];
        }
        return $valor;
      }
      return null;
    }
  }

  
  public function contratar() {
    
    if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
      $POST = json_decode( file_get_contents( "php://input" ), true );
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
    
    if( !isset( $POST["cotacao_id"] ) ) {
      die( json_encode( array( "status" => false, "message" => "Campo cotacao_id é obrigatório" ) ) );
    }
    $cotacao_id = $POST["cotacao_id"];

    $result = array();
    $cotacao = $this->cotacao->get_by_id( $cotacao_id );
    $produto_parceiro_id = $cotacao["produto_parceiro_id"];
    $produto = $this->current_model->with_produto()->get($produto_parceiro_id);

    if( $produto["produto_slug"] == "equipamento" ) {
      $cotacao_itens = $this->cotacao_equipamento->get_by( array( "cotacao_id" => $cotacao_id ) );
    } else if( $produto["produto_slug"] == "generico" || $produto["produto_slug"] == "seguro_saude" ) {
      $cotacao_itens = $this->cotacao_generico->get_by( array( "cotacao_id" => $cotacao_id ) );
    }
    
    $produto_parceiro_plano_id = $cotacao_itens["produto_parceiro_plano_id"];

    $equipamento_marca_id = null;
    $params = $POST;
    
    if( isset( $POST["equipamento_marca_id"] ) )
    $equipamento_marca_id = $POST["equipamento_marca_id"];
      
    $equipamento_categoria_id = null;
    if( isset( $POST["equipamento_categoria_id"] ) )
    $equipamento_categoria_id = $POST["equipamento_categoria_id"];
      
    $quantidade = 1;
    if( isset( $POST["quantidade"] ) )
    $quantidade = $POST["quantidade"];
      
    $coberturas = null;
    if( isset( $POST["coberturas"] ) )
    $coberturas = $POST["coberturas"];
      
    $repasse_comissao = 0;
    if( isset( $POST["repasse_comissao"] ) )
      $repasse_comissao = $POST["repasse_comissao"];
    
    $desconto_condicional = 0;
    if( isset( $POST["desconto_condicional"] ) )
      $desconto_condicional = $POST["desconto_condicional"];

    $data_inicio_vigencia = "";
    if( isset( $POST["data_inicio_vigencia"] ) )
      $data_inicio_vigencia = $POST["data_inicio_vigencia"];
    
    $result = array();

    $params["cotacao_id"] = $cotacao_id;
    $params["produto_parceiro_id"] = $produto_parceiro_id;
    $params["produto_parceiro_plano_id"] = $produto_parceiro_plano_id;
    $params["parceiro_id"] = $produto['parceiro_id'];
    $params["quantidade"] = $quantidade;
    $params["coberturas"] = $coberturas;
    $params["repasse_comissao"] = $repasse_comissao;
    $params["desconto_condicional"] = $desconto_condicional;
    $params["data_inicio_vigencia"] = $data_inicio_vigencia;

    if( $produto["produto_slug"] == "equipamento" ) {
      unset( $params["coberturas"] );
      $params["equipamento_marca_id"] = $equipamento_marca_id;
      $params["equipamento_categoria_id"] = $equipamento_categoria_id;
      $result = $this->contratar_equipamento( $params );
    } elseif( $produto["produto_slug"] == "generico" || $produto["produto_slug"] == "seguro_saude" ) {
      $result = $this->contratar_generico( $params );
    }
    die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
  }

  public function contratar_equipamento( $params ) {
    $this->load->model( "produto_parceiro_campo_model", "campo" );
    $this->load->model( "cotacao_equipamento_model", "cotacao_equipamento" );
    $this->load->model( "cliente_model", "cliente" );
    $this->load->model( "cotacao_model", "cotacao" );
    $this->load->model( "localidade_estado_model", "localidade_estado" );

    $produto_parceiro_id = issetor($params['produto_parceiro_id'], 0);
    $produto_parceiro_plano_id = issetor($params['produto_parceiro_plano_id'], 0);
    $parceiro_id = issetor($params['parceiro_id'], 0);

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
      $this->cotacao_equipamento->insert_update( $produto_parceiro_id, $cotacao_id, 3 );
      unset( $params["parceiro_id"] );


    } else {
      $result = array(
          "status" => false,
          "mensagem" => "Cotacao_id não informado",
          "errors" => array(),
      );
      return $result;
    }

    $cotacao_salva = $this->cotacao->with_cotacao_equipamento()
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
      return $erros;
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
        "validacao" => $validacao);
    }
    return $result;
  }
  
  function celular( $number ){
    $number = preg_replace( "/[^0-9]/", "", $number );
    $number = "(" . substr( $number, 0, 2 ) . ") " . substr( $number, 2, -4) . " - " . substr( $number, -4 );
    return $number;
  }

}











