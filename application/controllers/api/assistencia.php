<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Assistencia
 */
class Assistencia extends CI_Controller {
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
    $this->parceiro_id = $webservice["parceiro_id"];
    $this->load->database('default');

    // Aqui guardo em sessão para fazer acompanhamento
    $this->session->set_userdata("tokenAPI", $this->api_key);
    $this->session->set_userdata("tokenAPIvalid",$webservice["validade"]);
    
    // $this->load->model( "seguro_viagem_motivo_model", "motivo");
  }
  
  public function emissao()
  {
    if( $_SERVER["REQUEST_METHOD"] === "POST" ) 
    {
      $POST = json_decode( file_get_contents( "php://input" ), true );

      if(!empty($POST))
      {
          // Validação dos dados
          if(empty($POST['data_inicial'])){
            die(json_encode(array("status"=>false,"message"=>"Atributo 'data_inicial' não informado"),JSON_UNESCAPED_UNICODE));   
          } 
          else if(!$this->validar_data($POST['data_inicial']))
          {
            die(json_encode(array("status"=>false,"message"=>"Atributo 'data_inicial' está com a data inválida"),JSON_UNESCAPED_UNICODE));
          }
          if(empty($POST['data_final'])){
              die(json_encode(array("status"=>false,"message"=>"Atributo 'data_final' não informado"),JSON_UNESCAPED_UNICODE));
          }
          else if(!$this->validar_data($POST['data_final']))
          {
            die(json_encode(array("status"=>false,"message"=>"Atributo 'data_final' está com a data inválida"),JSON_UNESCAPED_UNICODE));
          } 
          if(strtotime($POST['data_inicial']) > strtotime($POST['data_final']))
          {
            die(json_encode(array("status"=>false,"message"=>"Data inicial não pode ser maior que a Data final"),JSON_UNESCAPED_UNICODE));
          }

          $arr [] = [
            "status"=>true,
            "message"=>"Validação concluída com sucesso",
            "usuario_id"=> $this->usuario_id,
            "parceiro_id"=> $this->parceiro_id
          ];

          // Agora eu faço a busca dos dados


          // Tipo de Saída
          switch ($POST['type_output']) {
            case 'xml':
            case 'XML':
                $this->saida_xml($arr);
              break;
            
            default:
                $this->saida_json($arr);
              break;
          }

      } else {
          die(json_encode(array("status"=>false,"message"=>"Parametros não informados"),JSON_UNESCAPED_UNICODE));
      }  
    } 
    else 
    {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
  }

  public function cancelamento()
  {
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
      die( json_encode( array('exemplo' => 'cancelamento' )) );
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
  }

  public function emissao_cancelamento()
  {
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
      die( json_encode( array('exemplo' => 'emissao_cancelamento' )) );
    } else {
      die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
    }
  }

  private function saida_json($dados = [])
  {
    die( json_encode($dados, JSON_UNESCAPED_UNICODE) );
  }

  private function saida_xml($dados = [])
  {

    # Instancia do objeto XMLWriter
    $xml = new XMLWriter;

    # Cria memoria para armazenar a saida
    $xml->openMemory();

    # Inicia o cabeçalho do documento XML
    $xml->startDocument( '1.0' , 'utf-8' );

    $tot = count($dados);

    for($i = 0; $i < $tot; $i++)
    {
      # Adiciona/Inicia um Elemento / Nó Pai <item>
      $xml->startElement("item");
      foreach ($dados[$i] as $campo => $d ) {
        # Adiciona um Nó Filho 
        $xml->writeElement($campo, $d);
      }
      # Finaliza o Nó Pai / Elemento <Item>
      $xml->endElement();
    }

    #  Configura a saida do conteúdo para o formato XML
    header( 'Content-type: text/xml' );

    # Imprime os dados armazenados
    print $xml->outputMemory(true);

    # Salvando o arquivo em disco
    # retorna erro se o header foi definido
    # retorna erro se outputMemory já foi chamado
    $file = fopen('saida.xml','w+');
    fwrite($file,$xml->outputMemory(true));
    fclose($file);
  }



  private function saida_xml2()
  {

    $arrDados = array();

    $arrDados[0]['id'] = '1';
    $arrDados[0]['title'] = 'Teste 1';
    $arrDados[0]['description'] = 'Desc 1';
    $arrDados[0]['image'] = 'Image 1';

    $arrDados[1]['id'] = '2';
    $arrDados[1]['title'] = 'Teste 2';
    $arrDados[1]['description'] = 'Desc 2';
    $arrDados[1]['image'] = 'Image 2';

    $arrDados[2]['id'] = '3';
    $arrDados[2]['title'] = 'Teste 3';
    $arrDados[2]['description'] = 'Desc 3';
    $arrDados[2]['image'] = 'Image 3';


    # Instancia do objeto XMLWriter
    $xml = new XMLWriter;

    # Cria memoria para armazenar a saida
    $xml->openMemory();

    # Inicia o cabeçalho do documento XML
    $xml->startDocument( '1.0' , 'iso-8859-1' );


    for ( $i = 0; $i < count( $arrDados ); $i++ ) {

      # Adiciona/Inicia um Elemento / Nó Pai <item>
      $xml->startElement("item");

      #  Adiciona um Nó Filho <quantidade> e valor 8
      $xml->writeElement("id", $arrDados[$i]['id']);
      $xml->writeElement("title", $arrDados[$i]['title']);
      $xml->writeElement("description", $arrDados[$i]['description']);
      $xml->writeElement("image", $arrDados[$i]['image']);

      #  Finaliza o Nó Pai / Elemento <Item>
      $xml->endElement();
    }

    #  Configura a saida do conteúdo para o formato XML
    header( 'Content-type: text/xml' );

    # Imprime os dados armazenados
    print $xml->outputMemory(true);

    # Salvando o arquivo em disco
    # retorna erro se o header foi definido
    # retorna erro se outputMemory já foi chamado
    $file = fopen('saida.xml','w+');
    fwrite($file,$xml->outputMemory(true));
    fclose($file);
  }

  private function validar_data($data = null)
  {    
    $dt_en = DateTime::createFromFormat('Y-m-d', $data);
    $dt_br = DateTime::createFromFormat('d-m-Y', $data);
    
    if($dt_en && $dt_en->format('Y-m-d') === $data){
      return true;
    }

    if($dt_br && $dt_br->format('d-m-Y') === $data){
      return true;
    }

    return false;
  }

}