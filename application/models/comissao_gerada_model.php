<?php
Class Comissao_Gerada_Model extends MY_Model {
  //Dados da tabela e chave primária
  protected $_table = 'comissao_gerada';
  protected $primary_key = 'comissao_gerada_id';

  //Configurações
  protected $return_type = 'array';
  protected $soft_delete = TRUE;

  //Chaves
  protected $soft_delete_key = 'deletado';
  protected $update_at_key = 'alteracao';
  protected $create_at_key = 'criacao';

  //campos para transformação em maiusculo e minusculo
  protected $fields_lowercase = array();
  protected $fields_uppercase = array('nome');

  //Dados
  public $validate = array(

  );

  function get_by_id($id)
  {
    return $this->get($id);
  }

  public function gerar_comissao_parceiro(){

    //busca vendas que não foram contabilziadas ainda
    $this->load->model('parceiro_relacionamento_produto_model', 'parceiro_relacionamento_produto');
    $this->load->model('comissao_classe_model', 'comissao_classe');

    $sql = "SELECT 
                  cotacao.cotacao_id,
                  cotacao.parceiro_id,
                  pedido.pedido_id,
                  pedido.codigo,
                  cotacao_equipamento.produto_parceiro_id,
                  cotacao_equipamento.premio_liquido,
                  cotacao_equipamento.iof, 
                  cotacao_equipamento.premio_liquido_total,
                  cotacao_equipamento.comissao_corretor 
              FROM pedido
              INNER JOIN cotacao ON pedido.cotacao_id = cotacao.cotacao_id
              INNER JOIN cotacao_equipamento ON cotacao_equipamento.cotacao_id = cotacao.cotacao_id
              WHERE 
                 pedido.pedido_status_id IN (3,8) 
              AND cotacao_equipamento.deletado = 0
              AND cotacao.deletado = 0
              AND pedido.deletado = 0		
              AND pedido.pedido_id NOT IN (SELECT comissao_gerada.pedido_id FROM comissao_gerada WHERE comissao_gerada.deletado = 0 AND comissao_gerada.comissao_classe_id = 1 )";

    $result = $this->_database->query($sql)->result_array();

    $comissao_classe = $this->comissao_classe->get(1);

    foreach ($result as $item) {

      //retira o IOF da compra
      $premio_liquido_total = $item["premio_liquido"];
      $comissao_venda =  ($item['comissao_corretor']/100) * $premio_liquido_total;

      $data_comissao = array();
      $data_comissao['comissao_classe_id'] = 1;
      $data_comissao['pedido_id'] = $item['pedido_id'];
      $data_comissao['valor'] = $comissao_venda;
      $data_comissao['parceiro_id'] = $item['parceiro_id'];
      $data_comissao['premio_liquido_total'] = $premio_liquido_total;
      $data_comissao['comissao'] = $item['comissao_corretor'];
      $data_comissao['descricao'] = "COMISSÃO {$comissao_classe['nome']} (". app_format_currency($item['comissao_corretor'], false, 3) ."%) REFERENTE AO PEDIDO {$item['codigo']}";
      $data_comissao['faturado'] = 0;

      $this->insert($data_comissao, TRUE);


    }

  }




  public function gerar_comissao_parceiro_relacionamento(){

    //busca vendas que não foram contabilziadas ainda
    $this->load->model('parceiro_relacionamento_produto_model', 'parceiro_relacionamento_produto');
    $this->load->model('comissao_classe_model', 'comissao_classe');

    $sql = "SELECT 
                  cotacao.cotacao_id,
                  cotacao.parceiro_id,
                  pedido.pedido_id,
                  pedido.codigo,
                  cotacao_equipamento.produto_parceiro_id,
                  cotacao_equipamento.premio_liquido,
                  cotacao_equipamento.iof, 
                  cotacao_equipamento.premio_liquido_total,
                  cotacao_equipamento.comissao_corretor 
              FROM
                  pedido
              INNER JOIN cotacao ON pedido.cotacao_id = cotacao.cotacao_id
              INNER JOIN cotacao_equipamento ON cotacao_equipamento.cotacao_id = cotacao.cotacao_id
              WHERE 
                 pedido.pedido_status_id IN (3,8) 
              AND cotacao_equipamento.deletado = 0
              AND cotacao.deletado = 0
              AND pedido.deletado = 0		
              AND pedido.pedido_id NOT IN 
              (SELECT comissao_gerada.pedido_id FROM comissao_gerada WHERE comissao_gerada.deletado = 0 AND comissao_gerada.comissao_classe_id = 4 )";

    $result = $this->_database->query($sql)->result_array();

    $comissao_classe = $this->comissao_classe->get(4);


    foreach ($result as $item) {

      //retira o IOF da compra
      $premio_liquido_total = $item["premio_liquido"];
      $relacionamento = array();

      $parceiro_relacionamento = $this->parceiro_relacionamento_produto
        ->filter_by_produto_parceiro($item['produto_parceiro_id'])
        //->filter_by_parceiro($item['parceiro_id'])
        ->get_all();

      foreach( $parceiro_relacionamento as $parceiro ) {
        if( $parceiro["pai_id"] != 0 ) {
          //$relacionamentos = $this->parceiro_relacionamento_produto->get($pai_id);
          $comissao_venda =  ($parceiro['comissao']/100) * $premio_liquido_total;

          $data_comissao = array();
          $data_comissao['comissao_classe_id'] = 4;
          $data_comissao['pedido_id'] = $item['pedido_id'];
          $data_comissao['valor'] = $comissao_venda;
          $data_comissao['parceiro_id'] = $parceiro['parceiro_id'];
          $data_comissao['premio_liquido_total'] = $premio_liquido_total;
          $data_comissao['comissao'] = $parceiro['comissao'];
          $data_comissao['descricao'] = "COMISSÃO {$comissao_classe['nome']} (". app_format_currency($parceiro['comissao'], false, 3) ."%) REFERENTE AO PEDIDO {$item['codigo']}";
          $data_comissao['faturado'] = 0;
          $this->insert($data_comissao, TRUE);
        }
      }
    }
  }

  public function filterFromPesquisa(){


    if($this->input->get('pedido_codigo')){
      $this->_database->like("pedido.codigo", $this->input->get('pedido_codigo') );
    }


    if($this->input->get('parceiro_id')){
      $this->_database->where("{$this->_table}.parceiro_id", $this->input->get('parceiro_id') );
    }


    if($this->input->get('comissao_classe_id')){
      $this->_database->where("{$this->_table}.comissao_classe_id", $this->input->get('comissao_classe_id') );
    }

    if($this->input->get('data_inicio') && $this->input->get('data_fim')){
      $this->_database->where("{$this->_table}.criacao >= ", app_dateonly_mask_to_mysql($this->input->get('data_inicio') ) );
      $this->_database->where("{$this->_table}.criacao <= ", app_dateonly_mask_to_mysql($this->input->get('data_fim') ) );
    }



    return $this;
  }

  function with_comissao_classe($fields = array('nome'))
  {
    $this->with_simple_relation_foreign('comissao_classe', 'comissao_classe_', 'comissao_classe_id', 'comissao_classe_id', $fields );
    return $this;
  }

  function with_pedido($fields = array('codigo'))
  {
    $this->with_simple_relation_foreign('pedido', 'pedido_', 'pedido_id', 'pedido_id', $fields );
    return $this;
  }
  function with_parceiro($fields = array('nome_fantasia'))
  {
    $this->with_simple_relation_foreign('parceiro', 'parceiro_', 'parceiro_id', 'parceiro_id', $fields );
    return $this;
  }


}


