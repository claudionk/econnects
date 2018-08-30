<?php
Class Parceiro_Relacionamento_Produto_Model extends MY_Model
{
  //Dados da tabela e chave primária
  protected $_table = 'parceiro_relacionamento_produto';
  protected $primary_key = 'parceiro_relacionamento_produto_id';

  //Configurações
  protected $return_type = 'array';
  protected $soft_delete = TRUE;

  //Chaves
  protected $soft_delete_key = 'deletado';
  protected $update_at_key = 'alteracao';
  protected $create_at_key = 'criacao';

  //campos para transformação em maiusculo e minusculo
  protected $fields_lowercase = array();
  protected $fields_uppercase = array();

  //Dados
  public $validate = array(
    array(
      'field' => 'produto_parceiro_id',
      'label' => 'Produto',
      'rules' => 'required',
      'groups' => 'default'
    ),
    array(
      'field' => 'parceiro_id',
      'label' => 'Parceiro',
      'rules' => 'required',
      'groups' => 'default'
    ),
    array(
      'field' => 'pai_id',
      'label' => 'Hierarquia',
      'rules' => '',
      'groups' => 'default'
    ),
    array(
      'field' => 'repasse_comissao',
      'label' => 'Repasse Comissão',
      'rules' => 'required',
      'groups' => 'default'
    ),
    array(
      'field' => 'repasse_maximo',
      'label' => 'Repasse Máximo',
      'rules' => 'required|callback_check_repasse_maximo',
      'groups' => 'default'
    ),
    array(
      'field' => 'comissao',
      'label' => 'Comissão',
      'rules' => 'required|callback_check_markup_relacionamento',
      'groups' => 'default'
    ),
    array(
      'field' => 'comissao_indicacao',
      'label' => 'Comissão indicação',
      'rules' => 'required',
      'groups' => 'default'
    ),
    array(
      'field' => 'desconto_data_ini',
      'label' => 'Data de início',
      'rules' => '',
      'groups' => 'default'
    ),
    array(
      'field' => 'desconto_data_fim',
      'label' => 'Data final',
      'rules' => '',
      'groups' => 'default'
    ),
    array(
      'field' => 'desconto_valor',
      'label' => 'Desconto Valor',
      'rules' => '',
      'groups' => 'default'
    ),
    array(
      'field' => 'desconto_habilitado',
      'label' => 'Desconto Habilitado',
      'rules' => 'callback_check_desconto_habilitado',
      'groups' => 'default'
    )
  );

  //Get dados
  public function get_form_data($just_check = false)
  {
    //Dados
    $data =  array(
      'produto_parceiro_id' => app_clear_number($this->input->post('produto_parceiro_id')),
      'parceiro_id' => app_clear_number($this->input->post('parceiro_id')),
      'pai_id' =>  (!empty($this->input->post('pai_id'))) ? app_clear_number($this->input->post('pai_id')) : 0,
      'repasse_comissao' => $this->input->post('repasse_comissao'),
      'repasse_maximo' => app_unformat_currency($this->input->post('repasse_maximo')),
      'comissao' => app_unformat_currency($this->input->post('comissao')),
      'comissao_indicacao' => app_unformat_currency($this->input->post('comissao_indicacao')),
      'desconto_data_ini' => app_dateonly_mask_to_mysql($this->input->post('desconto_data_ini')),
      'desconto_data_fim' => app_dateonly_mask_to_mysql($this->input->post('desconto_data_fim')),
      'desconto_valor' => app_unformat_currency($this->input->post('desconto_valor')),
      'desconto_habilitado' => $this->input->post('desconto_habilitado'),
    );
    return $data;
  }
  function get_by_id($id)
  {
    return $this->get($id);
  }


  public function with_produto_parceiro(){

    return $this->with_simple_relation_foreign('produto_parceiro', 'produto_parceiro_', 'produto_parceiro_id', 'produto_parceiro_id', array('nome'), 'inner');
  }

  public function with_parceiro(){

    return $this->with_simple_relation_foreign('parceiro', 'parceiro_', 'parceiro_id', 'parceiro_id', array('nome'), 'inner');
  }

  public function get_comissao($produto_parceiro_id, $parceiro_id){

    $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
    $this->_database->where('parceiro_id', $parceiro_id);

    $rows = $this->get_all();

    if($rows){
      return $rows[0];
    }else{
      return array();
    }


  }


  public function get_desconto($produto_parceiro_id, $parceiro_id){

    $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
    $this->_database->where('parceiro_id', $parceiro_id);
    $this->_database->where('desconto_habilitado', 1);
    $this->_database->where('desconto_data_ini <', date('Y-m-d H-i-s'));
    $this->_database->where('desconto_data_fim >', date('Y-m-d H-i-s'));

    $rows = $this->get_all();

    if($rows){
      return $rows[0];
    }else{
      return array();
    }

  }

  public function is_desconto_produto_habilitado($produto_parceiro_id){

    $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
    $this->_database->where('desconto_habilitado', 1);

    $rows = $this->get_all();

    if($rows){
      return TRUE;
    }else{
      return FALSE;
    }


  }


  public function get_comissao_markup($produto_parceiro_id, $parceiro_id){

    //$this->_database->select( "SUM(comissao) as comissao" );
    //$this->_database->where( "produto_parceiro_id", $produto_parceiro_id);
    //$this->_database->where( "produto_parceiro_id", $produto_parceiro_id);
    //$this->_database->where( "pai_id <> 0" );
    //$rows = $this->get_all( "parceiro_relacionamento_produto" );
    //if( $rows ) {
    //  $comissao = $rows[0]["comissao"];
    //} else {
    //  $comissao = 0;
    //}
    //return $comissao;

    $this->_database->where("produto_parceiro_id", $produto_parceiro_id);
    $this->_database->where("parceiro_id", $parceiro_id);
    $rows = $this->get_all();

    if($rows){
      $row = $rows[0];
      $soma = 0;
      while ((int)$row['pai_id'] != 0){
        $rows = $this->get($row['pai_id']);
        print_r( $rows );
        if($rows){
          $row = $rows;
          $soma += $row['comissao'];
        }else{
          $row['pai_id'] = 0;
        }
      }
      return $soma;
      die( print_r( $soma, true ) );

    }else{
      return 0;
    }

  }



  public function get_todas_comissoes($produto_parceiro_id, $parceiro_relacionamento_produto_id = 0){


    $this->_database->where('produto_parceiro_id', $produto_parceiro_id);

    if((int)$parceiro_relacionamento_produto_id > 0){
      $this->_database->where('parceiro_relacionamento_produto_id <>', $parceiro_relacionamento_produto_id);
    }

    $rows = $this->get_all();

    $soma = 0;
    if($rows){

      foreach ($rows as $row) {
        $soma += $row['comissao'];
      }

    }
    return $soma;

  }


  function  filter_by_pai($pai_id){

    $this->_database->where('pai_id', $pai_id);

    return $this;
  }

  function  filter_by_produto_parceiro($produto_parceiro_id){

    $this->_database->where('produto_parceiro_id', $produto_parceiro_id);

    return $this;
  }


  function  filter_by_parceiro($parceiro_id){

    $this->_database->where('parceiro_id', $parceiro_id);

    return $this;
  }

  public function getRelacionamentoProduto($produto_parceiro_id = 0, $pai_id = 0, &$arr){

    $this->load->model('parceiro_model', 'parceiro');

    $relacionamentos = $this->filter_by_pai($pai_id)->filter_by_produto_parceiro($produto_parceiro_id)->get_all();


    if($relacionamentos){

      foreach ($relacionamentos as $relacionamento) {
        $relacionamento['itens'] = array();
        $relacionamento['parceiro'] = $this->parceiro->get($relacionamento['parceiro_id']);
        $this->getRelacionamentoProduto($produto_parceiro_id, $relacionamento['parceiro_relacionamento_produto_id'], $relacionamento['itens']);
        $arr[] = $relacionamento;
      }
    }


  }

  public function get_all($limit = 0, $offset = 0, $processa = true) {
    if($processa) {
      $parceiro_id = $this->session->userdata('parceiro_id');

      $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
    }
    return parent::get_all($limit, $offset);
  }


  public function get_total($processa = true) {
    if($processa) {
      //Efetua join com cotação
      //$this->_database->join("parceiro as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

      $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
    }
    return parent::get_total(); // TODO: Change the autogenerated stub
  }

}



