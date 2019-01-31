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
        

          /*  
          $arr [] = [
              "status"=>true,
              "message"=>"Validação concluída com sucesso",
              "usuario_id"=> $this->usuario_id,
              "parceiro_id"=> $this->parceiro_id
            ];
          */
        
          // Agora eu faço a busca dos dados
    
          $arr = $this->db->query("
          SELECT
          ast.nome as status_apolice, 
          a.num_apolice as apolice, 
          ag.data_adesao as data_emissao,
          concat('BR2_C2_', c.cobertura_id) as produto,
          IF(ast.nome = 'ATIVA','IN','EX') as tipo_movimento_registro,
          ag.valor_premio_total as valor_premio,
          fp.nome as forma_pagto,
          IF(m.sigla = 'BRL',1,2) as moeda,
          a.num_apolice as chave,
          null as codigo_memorial,
          '-agencia' as agencia,
          'BR' as 'codigo_pais',
          'BR2_C2' as 'cartera',
          ag.nome as nome_segurado,
          concat(
          ag.endereco_logradouro,', ',
            ag.endereco_numero
          ) as endereco, 
          ag.endereco_bairro,
          ag.endereco_cidade,
          ag.contato_telefone as celular,
          ag.data_nascimento,
          null as 'serie_veiculo',
          ag.email,
          ag.data_ini_vigencia,
          ag.data_fim_vigencia,
          ag.data_cancelamento,
          CASE
            WHEN fs.nome = 'PENDENTE DE FATURAMENTO' THEN 'NP'
              WHEN fs.nome = 'PAGA' THEN 'PG'
              WHEN fs.nome = 'FATURADA' THEN 'PA'
              ELSE 'NP'
          END as tipo_movimiento_pago,
          ag.data_pagamento,
          ag.valor_premio_total as valor_pago,
          concat(ag.num_parcela,'/',f.num_parcela) as num_parcela,
          null as 'quantidade_beneficiario',
          null as 'nome_beneficiario_1',
          null as 'numero_identificacao_1',
          null as 'data_nasc_beneficiario_1',
          null as 'grau_parentesco_1',
          null as 'nome_beneficiario_2',
          null as 'numero_identificacao_2',
          null as 'data_nasc_beneficiario_2',
          null as 'grau_parentesco_2',
          null as 'nome_beneficiario_3',
          null as 'numero_identificacao_3',
          null as 'data_nasc_beneficiario_3',
          null as 'grau_parentesco_3',
          null as 'nome_beneficiario_4',
          null as 'numero_identificacao_4',
          null as 'data_nasc_beneficiario_4',
          null as 'grau_parentesco_4',

          -- a.apolice_id, 
          -- a.pedido_id, 
          -- a.produto_parceiro_plano_id, 
          -- a.parceiro_id,
          -- ast.nome as nome_status_apolice, 
          -- ppp.produto_parceiro_plano_id, 
          -- ppp.produto_parceiro_id,
          ppp.nome as nome_plano,
          -- ppp.descricao as descricao_plano,
          -- ppp.slug_plano,
          c.nome as nome_cobertura,
          -- pp.parceiro_id, 
          -- pp.produto_id, 
          -- pp.seguradora_id, 
          pp.nome 
          -- pp.cod_tpa, 
          -- pp.slug_produto
          from apolice a
          inner join apolice_status ast on ast.apolice_status_id = a.apolice_status_id
          inner join produto_parceiro_plano ppp on ppp.produto_parceiro_plano_id = a.produto_parceiro_plano_id
          inner join cobertura_plano cp on cp.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
          inner join cobertura c on c.cobertura_id = cp.cobertura_id
          inner join produto_parceiro pp on pp.produto_parceiro_id = ppp.produto_parceiro_id
          inner join produto p on p.produto_id = pp.produto_id
          inner join apolice_generico ag on ag.apolice_id = a.apolice_id
          left join apolice_cobertura ac on ac.apolice_id = a.apolice_id
          inner join pedido ped on ped.pedido_id = a.pedido_id
          inner join produto_parceiro_pagamento prpp on prpp.produto_parceiro_pagamento_id = ped.produto_parceiro_pagamento_id
          inner join forma_pagamento fp on fp.forma_pagamento_id = prpp.forma_pagamento_id
          inner join moeda m on m.moeda_id = ppp.moeda_id
          inner join fatura f on f.pedido_id = ped.pedido_id 
          inner join fatura_status fs on fs.fatura_status_id = f.fatura_status_id
          where a.parceiro_id = ".$this->parceiro_id." and cp.cobertura_plano_id in(
  select cobertura_plano_id from usuario_cobertura_produto 
    where usuario_id = ".$this->usuario_id." and deletado = 0
)
and ast.apolice_status_id = 1 
and a.criacao between '".date("Y-m-d", strtotime($POST['data_inicial']))." 00:00:00' and '".date("Y-m-d", strtotime($POST['data_final']))." 23:59:59' and a.deletado = 0
order by ag.data_adesao desc
")->result();
         
          // Tipo de Saída
          $tipo_saida = (isset($POST['type_output'])) ? $POST['type_output'] : '';
          switch ($tipo_saida) {
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

          /*  
          $arr [] = [
              "status"=>true,
              "message"=>"Validação concluída com sucesso",
              "usuario_id"=> $this->usuario_id,
              "parceiro_id"=> $this->parceiro_id
            ];
          */
        
          // Agora eu faço a busca dos dados
    
          $arr = $this->db->query("
          SELECT 
          ast.nome as status_apolice, 
          a.num_apolice as apolice, 
          ag.data_adesao as data_emissao,
          concat('BR2_C2_', c.cobertura_id) as produto,
          IF(ast.nome = 'ATIVA','IN','EX') as tipo_movimento_registro,
          ag.valor_premio_total as valor_premio,
          fp.nome as forma_pagto,
          IF(m.sigla = 'BRL',1,2) as moeda,
          a.num_apolice as chave,
          null as codigo_memorial,
          '-agencia' as agencia,
          'BR' as 'codigo_pais',
          'BR2_C2' as 'cartera',
          ag.nome as nome_segurado,
          concat(
          ag.endereco_logradouro,', ',
            ag.endereco_numero
          ) as endereco, 
          ag.endereco_bairro,
          ag.endereco_cidade,
          ag.contato_telefone as celular,
          ag.data_nascimento,
          null as 'serie_veiculo',
          ag.email,
          ag.data_ini_vigencia,
          ag.data_fim_vigencia,
          ag.data_cancelamento,
          CASE
            WHEN fs.nome = 'PENDENTE DE FATURAMENTO' THEN 'NP'
              WHEN fs.nome = 'PAGA' THEN 'PG'
              WHEN fs.nome = 'FATURADA' THEN 'PA'
              ELSE 'NP'
          END as tipo_movimiento_pago,
          ag.data_pagamento,
          ag.valor_premio_total as valor_pago,
          concat(ag.num_parcela,'/',f.num_parcela) as num_parcela,
          null as 'quantidade_beneficiario',
          null as 'nome_beneficiario_1',
          null as 'numero_identificacao_1',
          null as 'data_nasc_beneficiario_1',
          null as 'grau_parentesco_1',
          null as 'nome_beneficiario_2',
          null as 'numero_identificacao_2',
          null as 'data_nasc_beneficiario_2',
          null as 'grau_parentesco_2',
          null as 'nome_beneficiario_3',
          null as 'numero_identificacao_3',
          null as 'data_nasc_beneficiario_3',
          null as 'grau_parentesco_3',
          null as 'nome_beneficiario_4',
          null as 'numero_identificacao_4',
          null as 'data_nasc_beneficiario_4',
          null as 'grau_parentesco_4',

          -- a.apolice_id, 
          -- a.pedido_id, 
          -- a.produto_parceiro_plano_id, 
          -- a.parceiro_id,
          -- ast.nome as nome_status_apolice, 
          -- ppp.produto_parceiro_plano_id, 
          -- ppp.produto_parceiro_id,
          ppp.nome as nome_plano,
          -- ppp.descricao as descricao_plano,
          -- ppp.slug_plano,
          c.nome as nome_cobertura,
          -- pp.parceiro_id, 
          -- pp.produto_id, 
          -- pp.seguradora_id, 
          pp.nome 
          -- pp.cod_tpa, 
          -- pp.slug_produto
          from apolice a
          inner join apolice_status ast on ast.apolice_status_id = a.apolice_status_id
          inner join produto_parceiro_plano ppp on ppp.produto_parceiro_plano_id = a.produto_parceiro_plano_id
          inner join cobertura_plano cp on cp.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
          inner join cobertura c on c.cobertura_id = cp.cobertura_id
          inner join produto_parceiro pp on pp.produto_parceiro_id = ppp.produto_parceiro_id
          inner join produto p on p.produto_id = pp.produto_id
          inner join apolice_generico ag on ag.apolice_id = a.apolice_id
          left join apolice_cobertura ac on ac.apolice_id = a.apolice_id
          inner join pedido ped on ped.pedido_id = a.pedido_id
          inner join produto_parceiro_pagamento prpp on prpp.produto_parceiro_pagamento_id = ped.produto_parceiro_pagamento_id
          inner join forma_pagamento fp on fp.forma_pagamento_id = prpp.forma_pagamento_id
          inner join moeda m on m.moeda_id = ppp.moeda_id
          inner join fatura f on f.pedido_id = ped.pedido_id 
          inner join fatura_status fs on fs.fatura_status_id = f.fatura_status_id
          where a.parceiro_id = ".$this->parceiro_id." and cp.cobertura_plano_id in(
  select cobertura_plano_id from usuario_cobertura_produto 
    where usuario_id = ".$this->usuario_id." and deletado = 0
)
and ast.apolice_status_id <> 1 
and a.criacao between '".$POST['data_inicial']." 00:00:00' and '".$POST['data_final']." 23:59:59' and a.deletado = 0
order by ag.data_adesao desc")->result();
         
          // Tipo de Saída
          $tipo_saida = (isset($POST['type_output'])) ? $POST['type_output'] : '';
          switch ($tipo_saida) {
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
