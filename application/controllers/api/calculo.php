<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Calculo extends CI_Controller {
  const PRECO_TIPO_TABELA = 1;
  const PRECO_TIPO_COBERTURA = 2;
  const PRECO_TIPO_VALOR_SEGURADO = 3;
  const PRECO_POR_EQUIPAMENTO = 5;
  const PRECO_POR_LINHA = 6;

  const TIPO_CALCULO_NET = 1;
  const TIPO_CALCULO_BRUTO = 2;

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
          $x = $this->put( $PUT );
        } else {
          die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }
      }
    }
  }
  
  private function get( $GET ) {
    
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
    $this->load->model('produto_parceiro_model', 'produto_parceiro');


    $cotacao_id = $GET["cotacao_id"];
    
    $cotacao = $this->cotacao->get($cotacao_id);
    $parceiro_id = $cotacao["parceiro_id"];
    $produto_parceiro_id = $cotacao["produto_parceiro_id"];
    
    $sucess = TRUE;
    $messagem = '';

    $coberturas_adicionais = ($this->input->post('coberturas')) ? $this->input->post('coberturas') : array();

    $desconto = $this->desconto->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

    $desconto_upgrade = 0;

    if($cotacao_id) {
      $cotacao = $this->cotacao->get($cotacao_id);
      if(($cotacao) && ((int)$cotacao['cotacao_upgrade_id']) > 0){

        $pedido_antigo = $this->pedido->get_by(array('cotacao_id' => $cotacao['cotacao_upgrade_id']));
        $desconto_upgrade = $pedido_antigo['valor_total'];

      }
    }


    $cotacao = $this->cotacao->with_cotacao_equipamento()->filterByID($cotacao_id)->get_all();
    $cotacao = $cotacao[0];
    
    $repasse_comissao = $cotacao["repasse_comissao"];
    $desconto_condicional= $cotacao["desconto_condicional"];

    $quantidade = $cotacao["quantidade"];


    $row =  $this->produto_parceiro->get_by_id($produto_parceiro_id);


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

    print_r( "Produto: $produto_parceiro_id - Parceiro: $parceiro_id\n");
    print_r( "Configuração: " . print_r( $configuracao, true ) );
    print_r( "Cotação: " . print_r( $cotacao, true ) );


    if($repasse_comissao > $configuracao['repasse_maximo'] ){
      $repasse_comissao = $configuracao['repasse_maximo'];
    }

    $repasse_comissao = str_pad(number_format((double)$repasse_comissao, 2, '.', ''), 5, "0", STR_PAD_LEFT);

    $comissao_corretor = ( $configuracao["comissao"] - $repasse_comissao );


    $servico_produto_id = $cotacao["servico_produto_id"] ? $cotacao["servico_produto_id"] : 0;
    $servico_produto = $this->servico_produto->get( $servico_produto_id );

    if($servico_produto && $quantidade < $servico_produto["quantidade_minima"] ) {
      $quantidade = $servico_produto["quantidade_minima"];
    }

    $importancia_segurada = $cotacao["nota_fiscal_valor"];
    $data_nascimento = $cotacao["data_nascimento"];
    $servico_produto_id = $cotacao["servico_produto_id"];
    $equipamento_marca_id = $cotacao["equipamento_marca_id"];
    $equipamento_categoria_id = $cotacao["equipamento_categoria_id"];
    
    $valores_bruto = $this->getValoresPlano( $produto_parceiro_id, $importancia_segurada, $quantidade, $servico_produto_id, $equipamento_marca_id, $equipamento_categoria_id, $data_nascimento );

    $valores_cobertura_adicional_total = array();
    $valores_cobertura_adicional = array();
    if($coberturas_adicionais) {
      foreach ($coberturas_adicionais as $coberturas_adicional) {
        $cobertura = explode(';', $coberturas_adicional);
        $vigencia = $this->plano->getInicioFimVigencia($cobertura[0], date('Y-m-d'));

        $valor = $this->getValorCoberturaAdicional($cobertura[0], $cobertura[1], $quantidade );
        $valores_cobertura_adicional_total[$cobertura[0]] = (isset($valores_cobertura_adicional_total[$cobertura[0]])) ? ($valores_cobertura_adicional_total[$cobertura[0]] + $valor) : $valor;
        $valores_cobertura_adicional[$cobertura[0]][] = $valor;
        error_log( print_r( $cobertura, true ), 3, "/var/log/httpd/econnects.log" );
      }
    }

    if(!$valores_bruto) {
      $result = array(
        'sucess' => FALSE,
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
        'mensagem' => 'PLANO NÃO DISPONÍVEL PARA ESSAS CONFIGURAÇÕES',
        'quantidade' => $quantidade,
      );
      die( json_encode( $result ) );
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

    die( print_r( $valores_bruto, true ) );

    $desconto_condicional_valor = 0;
    foreach ($arrPlanos as $plano){
      //precificacao_tipo_id

      switch ((int)$configuracao["calculo_tipo_id"]) {
        case self::TIPO_CALCULO_NET:
          $valor = $valores_bruto[$plano["produto_parceiro_plano_id"]];
          $valor += (isset($valores_cobertura_adicional_total[$plano["produto_parceiro_plano_id"]])) ? $valores_cobertura_adicional_total[$plano["produto_parceiro_plano_id"]] : 0;
          $valor = ($valor/(1-(($markup + $comissao_corretor)/100)));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano["produto_parceiro_plano_id"]] = $valor;
          break;
        case self::TIPO_CALCULO_BRUTO:
          $valor = $valores_bruto[$plano["produto_parceiro_plano_id"]];
          $valor += (isset($valores_cobertura_adicional_total[$plano["produto_parceiro_plano_id"]])) ? $valores_cobertura_adicional_total[$plano["produto_parceiro_plano_id"]] : 0;
          $valor = ($valor) - (($valor) * (($markup + $comissao_corretor)/100));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano["produto_parceiro_plano_id"]] = $valor;
          break;
        default:
          break;
      }
    }
    
    die( json_encode( $result ) );
    //print_r( $cotacao );
    
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
      'sucess' => $sucess,
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
      'mensagem' => $messagem,
      'quantidade' => $quantidade,
    );

    error_log( "Cálculo#2: " . print_r( $valores_liquido, true ) . "\n", 3, "/var/log/httpd/php_errors.log" );
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

    //Output
    die( json_encode( $result ) );


    die( json_encode( array( "status" => true, "message" => "GET", "api_key" => $this->api_key, "parameters" => $GET ) ) );
  }

  private function post( $POST ) {
    die( json_encode( array( "status" => true, "message" => "POST", "api_key" => $this->api_key, "parameters" => $POST ) ) );
  }

  private function put( $PUT ) {
    die( json_encode( array( "status" => true, "message" => "PUT", "api_key" => $this->api_key, "parameters" => $PUT ) ) );
  }

  private function getValoresPlano( $produto_parceiro_id, $importancia_segurada, $quantidade = 1, $servico_produto_id = 0, $equipamento_marca_id = 0, $equipamento_categoria_id = 0, $data_nascimento = "" ) {

    $this->load->model('produto_parceiro_plano_model', 'plano');
    $this->load->model('moeda_model', 'moeda');
    $this->load->model('moeda_cambio_model', 'moeda_cambio');

    $moeda_padrao = $this->moeda->filter_by_moeda_padrao()->get_all();
    $moeda_padrao = $moeda_padrao[0];

    $quantidade = ((int)$quantidade <=0) ? 1 : (int)$quantidade;

    $produto_parceiro =  $this->produto_parceiro->get_by_id($produto_parceiro_id);
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
      switch ((int)$plano['precificacao_tipo_id']) {
        case self::PRECO_TIPO_TABELA:

          $calculo = $this->getValorTabelaFixa($plano["produto_parceiro_plano_id"], $equipamento_categoria_id, $equipamento_marca_id, $importancia_segurada) *$quantidade;

          if($calculo)
            $valores[$plano["produto_parceiro_plano_id"]] = $calculo;
          else
            return null;

          if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
            $valores[$plano["produto_parceiro_plano_id"]] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$plano["produto_parceiro_plano_id"]]);
          }
          break;
        case self::PRECO_TIPO_COBERTURA:
          break;
        case self::PRECO_TIPO_VALOR_SEGURADO:
          $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
          $this->load->model('equipamento_model', 'equipamento');
          $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
          if( $produto_parceiro_plano_id > 0 ) {
            $valor = $this->produto_parceiro_plano_precificacao_itens
              ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
              ->filter_by_tipo_equipamento("TODOS")
              ->get_all();
            if( $valor ) {
              $valores[$produto_parceiro_plano_id] = floatval( $importancia_segurada ) * ( floatval( $valor[0]["valor"] ) / 100 );
            } else {
              $valores[$produto_parceiro_plano_id] = 0;
            }
          } else {
            $valores[$produto_parceiro_plano_id] = 0;
          }
          break;
        case self::PRECO_POR_LINHA;
          $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
          $this->load->model('equipamento_model', 'equipamento');
          $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
          if( $produto_parceiro_plano_id > 0 ) {
            $valor = $this->produto_parceiro_plano_precificacao_itens
              ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
              ->filter_by_faixa( $importancia_segurada )
              ->filter_by_tipo_equipamento("CATEGORIA")
              ->filter_by_equipamento($equipamento_categoria_id)
              ->get_all();
            if( $valor ) {
              $valores[$produto_parceiro_plano_id] = floatval( $importancia_segurada ) * ( floatval( $valor[0]["valor"] ) / 100 );
            } else {
              $valores[$produto_parceiro_plano_id] = 0;
            }
          } else {
            $valores[$produto_parceiro_plano_id] = 0;
          }

          break;
        default:
          break;
      }
    }
    return $valores;
  }

  private function getValorCoberturaAdicional($produto_parceiro_plano_id, $cobertura_plano_id, $quantidade ){
    $this->load->model('cobertura_plano_model', 'cobertura_plano');

    $cobertura = $this->cobertura_plano->get_by(array(
      "produto_parceiro_plano_id" => $produto_parceiro_plano_id,
      'cobertura_plano_id' => $cobertura_plano_id,
    ));


    if($cobertura){
      return (app_calculo_porcentagem($cobertura['porcentagem'],$cobertura['preco'])*$qntDias);
    }else{
      return 0;
    }

  }

  private function getValorTabelaFixa($produto_parceiro_plano_id, $equipamento_categoria_id, $equipamento_marca_id, $valor_nota){
    $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
    $this->load->model('equipamento_model', 'equipamento');


    $valor = $this->produto_parceiro_plano_precificacao_itens
      ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
      ->filter_by_tipo_equipamento('TODOS')
      ->get_all();

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
        ->filter_by_tipo_equipamento('CATEGORIA')
        ->filter_by_equipamento($equipamento_categoria_id)
        ->get_all();

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
  
}
?>




