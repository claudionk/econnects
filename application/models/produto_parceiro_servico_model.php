<?php
Class Produto_Parceiro_Servico_Model extends MY_Model
{
  //Dados da tabela e chave primária
  protected $_table = 'produto_parceiro_servico';
  protected $primary_key = 'produto_parceiro_servico_id';

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
      'field' => 'campo_id',
      'label' => 'Campo',
      'rules' => 'required',
      'groups' => 'default'
    )
  );

  function get_by_id($id)
  {
    return $this->get($id);
  }

  function with_servico(){
    $this->with_simple_relation('servico', 'servico_', 'servico_id', array('nome', 'descricao', 'usuario', 'senha', 'parametros', 'token', 'token_validade'));
    return $this;
  }

  function with_servico_tipo(){
    $this->_database->select('servico_tipo.servico_tipo_id, servico_tipo.slug', 'servico_tipo.nome');
    $this->_database->join('servico_tipo', 'servico.servico_tipo_id = servico_tipo.servico_tipo_id', 'inner');
    return $this;
  }


  function  filter_by_servico_tipo($slug){

    $this->_database->where("servico_tipo.slug", $slug);

    return $this;
  }
  function  filter_by_produto_parceiro($produto_parceiro_id){

    $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);

    return $this;
  }


  public function unitfour_getCliente( $documento, $produto_parceiro_id, $info_service = "unitfour_pf" ) {
    $this->load->model('base_pessoa_model', 'base_pessoa');
    $cliente = $this->base_pessoa->getByDoc( $documento, $produto_parceiro_id, $info_service );
    return $cliente;
  }

  public function Ifaro_getCliente($documento, $produto_parceiro_id, $info_service  = "ifaro_pf" ) {
    $this->load->model('base_pessoa_model', 'base_pessoa');
    $cliente = $this->base_pessoa->getByDoc( $documento, $produto_parceiro_id, $info_service );
    return $cliente;
  }


  public function isUnitfour_getCliente($produto_parceiro_id){
    $config = $this
      ->with_servico()
      ->with_servico_tipo()
      ->filter_by_servico_tipo('unitfour_pf')
      ->filter_by_produto_parceiro($produto_parceiro_id)
      ->get_all();
    
    if($config){
      return true;
    }else{
      return false;
    }
  }

  public function isIfaro_getCliente( $produto_parceiro_id ) {
    $config = $this
      ->with_servico()
      ->with_servico_tipo()
      ->filter_by_servico_tipo('ifaro_pf')
      ->filter_by_produto_parceiro( $produto_parceiro_id )
      ->get_all();

    if($config){
      return true;
    }else{
      return false;
    }
  }


}

