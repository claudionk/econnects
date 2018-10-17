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

  public $campos_estrutura;
  public $valor_premio_bruto;
  public $forma_pagamento_id;
  public $produto_parceiro_pagamento_id;

  public $meio_pagto_slug;
  public $campos_meios_pagto;


  
  public function __construct() {
    parent::__construct();

    header( "Access-Control-Allow-Origin: *" );
    header( "Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS" );
    header( "Access-Control-Allow-Headers: Cache-Control, APIKEY, apikey, Content-Type" );
    //header( "Content-Type: application/json");

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
  }

  public function valida_retorno()
  {
      if (!empty($r["response"])){
        $retorno = json_decode($r["response"]);
        if (empty($retorno)){
            $ret['response'] = $r["response"];
        } else {
            // sucesso
            //$ret = ['status' => true, 'response' => $response];
        }
      }
  }
  
  public function index() { /*$this->etapas('cotacao');*/  }

  public function inicio()
  {   
    

    $POST = json_decode( file_get_contents( "php://input" ), true );
    
    if(!empty($POST))
    {
      // Validação dos dados
      if(empty($POST['produto_slug'])){
        die(json_encode(array("status"=>false,"message"=>"produto_slug não informado"),JSON_UNESCAPED_UNICODE));
      }
      if(empty($POST['plano_slug'])){
        die(json_encode(array("status"=>false,"message"=>"plano_slug não informado"),JSON_UNESCAPED_UNICODE));
      }
      if(empty($POST['campos'])){
        die(json_encode(array("status"=>false,"message"=>"campos não informado"),JSON_UNESCAPED_UNICODE));
      }
      if(empty($POST['valor_premio_bruto'])){
        die(json_encode(array("status"=>false,"message"=>"valor_premio não informado"),JSON_UNESCAPED_UNICODE));
      }
      /*if(empty($POST['meio_pagto_slug'])){
        die(json_encode(array("status"=>false,"message"=>"meio_pagto_slug não informado"),JSON_UNESCAPED_UNICODE));
      }*/
    }
    else
    {
      die(json_encode(array("status"=>false,"message"=>"Parametros não informados"),JSON_UNESCAPED_UNICODE));
    }  

    $this->valor_premio_bruto =  $POST['valor_premio_bruto']; 
    $this->meio_pagto_slug    =  $POST['meiopagamento']['meio_pagto_slug']; 
    $this->campos_meios_pagto =  $POST['meiopagamento']['campos'];
    
    $this->etapas('cotacao',$POST);
  }

  public function etapas($etapa = null, $parametros = []){

    switch ($etapa) {

      /* ---------inicio processo --------------- */
      case 'cotacao':

        $parceiro_id =  $this->parceiro_id;

        $this->load->model( "produto_parceiro_model", "produto_parceiro" );
        $produtos = $this->produto_parceiro->get_produtos_venda_admin_parceiros( $parceiro_id, $parametros['produto_slug'] );

        if(!empty($produtos))
        {
          $this->produto_parceiro_id = $produtos[0]['produto_parceiro_id'];
          $this->parceiro_id_pai     = $produtos[0]['parceiro_id'];
        } 
        else 
        {
          die(json_encode(array("status"=>false,"message"=>"Não foi possível identificar o produto"),JSON_UNESCAPED_UNICODE));
        }

        // Separando o produto do parceiro
        $r = $this->produto_parceiro->filter_by_produto_parceiro($produto_parceiro_id)->filter_by_slug($parametros["plano_slug"])->get_all();
        if(!empty($r)){
          $this->produto_parceiro_plano_id = $r[0]['produto_parceiro_plano_id'];
        }
        else
        {
          die(json_encode(array("status"=>false,"message"=>"Não foi possível identificar o produto_parceiro_plano_id"),JSON_UNESCAPED_UNICODE));
        }

        // Campos da cotação
        $arrOptions = [
          "produto_parceiro_id" => $this->produto_parceiro_id,
          "produto_parceiro_plano_id" => $this->produto_parceiro_plano_id
        ];
        if(count($parametros['campos'][0]) > 0)
        {
          foreach ($parametros['campos'][0] as $key => $vl) {
            $arrOptions[$key] =  $vl;
          }
          $this->campos_estrutura = $arrOptions;
        }
        
        $url = $this->config->item("URL_sisconnects") ."api/cotacao";
                
        $obj = new Api();
        $r = $obj->execute($url, 'POST', json_encode($arrOptions), null, null);

        if(!empty($r))
        {
          // pegando o ID da cotação
          $retorno = json_decode($r);
          if($retorno->{"status"})
          {
            $this->cotacao_id = $retorno->{"cotacao_id"};
            $this->campos_estrutura['cotacao_id'] = $this->cotacao_id;
            $this->produto_parceiro_id = $retorno->{"produto_parceiro_id"};
            // Chamando o Calculo da cotação
            $this->etapas('calculocotacao');
          } 
          else 
          {
            die(json_encode(array("status"=>false,"message"=>"A cotação não foi gerada"),JSON_UNESCAPED_UNICODE));
          }
        }
        else
        {
          die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar os dados da cotação"),JSON_UNESCAPED_UNICODE));
        }
        
        
        break;

      case 'calculocotacao':

        // Validar o valor passado se diferente alertar e abortar
        $url = $this->config->item("URL_sisconnects") ."api/cotacao/calculo?cotacao_id=".$this->cotacao_id;

        $obj = new Api();
        $r = $obj->execute($url, 'GET', null, null, null);

        if(!empty($r))
        {
          $retorno = json_decode($r);
    
          // Validação valores  
          if($this->valor_premio_bruto != $retorno->{"premio_liquido_total"}){
             die(json_encode(array("status"=>false,"message"=>"O valor do prêmio {$this->valor_premio_bruto} informado diferente do valor cálculado ".$retorno->{"premio_liquido_total"}),JSON_UNESCAPED_UNICODE));
          }
          if($retorno->{"status"})
          {
            $retorno->{"cotacao_id"} = $this->cotacao_id;
            $this->etapas('contratarcotacao',$retorno);
          }
          else
          {
            die(json_encode(array("status"=>false,"message"=>"O cálculo da cotação não realizado"),JSON_UNESCAPED_UNICODE));
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
      
          $url = $this->config->item("URL_sisconnects") ."api/cotacao/contratar";
          $obj = new Api();
          $r = $obj->execute($url, 'POST', json_encode($this->campos_estrutura), null, null);

          if(!empty($r))
          {
            $retorno = json_decode($r);
            if($retorno->{"status"})
            {
              $this->etapas('formapagamento', $retorno);
            }
            else
            {
              die(json_encode(array("status"=>false,"message"=>"Contratação não realizada"),JSON_UNESCAPED_UNICODE));
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
          
          $url = $this->config->item("URL_sisconnects") ."api/pagamento/forma_pagamento_cotacao?cotacao_id={$this->cotacao_id}";
          

          $obj = new Api();
          $r = $obj->execute($url, 'GET', null, null, null);
          if(!empty($r))
          {
            $retorno = json_decode($r);
            

            $flag = false;
            foreach ($retorno as $vl) {
                if($vl->tipo->slug == $this->meio_pagto_slug) {
                  $this->produto_parceiro_pagamento_id = $vl->pagamento[0]->produto_parceiro_pagamento_id;
                  $this->forma_pagamento_id = $vl->pagamento[0]->forma_pagamento_id;
                  $flag = true;
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
        else
        {
          die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar a contratação da cotação"),JSON_UNESCAPED_UNICODE));
        }
        
        break;

      case 'efetuarpagamento':

        if($parametros["status"])
        {
          $arrOptions = [
            "cotacao_id" => $parametros["cotacao_id"], 
            "produto_parceiro_id" => $parametros["produto_parceiro_id"], 
            "forma_pagamento_id" => $parametros["forma_pagamento_id"], 
            "produto_parceiro_pagamento_id" => $parametros["produto_parceiro_pagamento_id"], 
            "campos" => $parametros["campos"]  
          ]; 
          $url = $this->config->item("URL_sisconnects") ."api/pagamento/pagar";
          $obj = new Api();
          $r = $obj->execute($url, 'POST', json_encode($arrOptions), null, null);

          if(!empty($r))
          {
            $retorno = json_decode($r); 
            if($retorno->{"status"})
            {
              $pedido_id = $retorno->{"dados"}->{"pedido_id"};
              die(json_encode(array($retorno),JSON_PRETTY_PRINT));
            }
            else
            {
              die(json_encode(array("status"=>false,"message"=>"Não foi realizar o pagamento"),JSON_UNESCAPED_UNICODE));
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
      /* ---------fim processo --------------- */

      default:
         die(json_encode("Opção inválida $etapa", JSON_UNESCAPED_UNICODE));
        break;
    }
  }
}
?>