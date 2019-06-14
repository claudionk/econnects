<?php
class Comunicacao_model extends MY_Model
{

  //Dados da tabela e chave primária
  protected $_table = "comunicacao";
  protected $primary_key = "comunicacao_id";

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

  public $validate = array(

    array(
      'field' => 'produto_parceiro_comunicacao_id',
      'label' => 'Produto parceiro comunicação',
      'rules' => 'required|numeric',
      'groups' => 'default',
      'foreign' => 'produto_parceiro_comunicacao'
    ),

    array(
      'field' => 'comunicacao_status_id',
      'label' => 'Comunicacao status id',
      'rules' => 'required|numeric',
      'groups' => 'default',
      'foreign' => 'comunicacao_status'
    ),

    array(
      'field' => 'mensagem_from',
      'label' => 'Mensagem from',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'mensagem_from_name',
      'label' => 'Mensagem from name',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'mensagem_to',
      'label' => 'Mensagem to',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'mensagem_to_name',
      'label' => 'Mensagem to name',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'mensagem',
      'label' => 'Mensagem',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'data_enviar',
      'label' => 'Data enviar',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'data_envio',
      'label' => 'Data envio',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'retorno',
      'label' => 'Retorno',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'retorno_codigo',
      'label' => 'Retorno codigo',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'tabela',
      'label' => 'Tabela',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'campo',
      'label' => 'Campo',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'chave',
      'label' => 'Chave',
      'rules' => '',
      'groups' => 'default',
    ),
    array(
      'field' => 'cotacao_id',
      'label' => 'cotacao_id',
      'rules' => '',
      'groups' => 'default',
    ),

  );

  public function countByDay($day = 'd', $month = "m", $year = "Y") {
    $parceiro_id = $this->session->userdata('parceiro_id');
    $data_i = date("{$year}-{$month}-{$day} 00:00:00");
    $data_f = date("{$year}-{$month}-{$day} 23:59:59");

    //$this->_database->join("produto_parceiro_comunicacao","produto_parceiro_comunicacao.produto_parceiro_comunicacao_id=comunicacao.produto_parceiro_comunicacao_id");
    //$this->_database->join("produto_parceiro","produto_parceiro.produto_parceiro_id=produto_parceiro_comunicacao.produto_parceiro_id");
    $this->_database->where("(produto_parceiro.parceiro_id=$parceiro_id)");

    return $this
      ->where("data_envio", ">=", $data_i)
      ->where("data_envio", "<=", $data_f)
      ->get_total();
  }

  public function countByDays() {
    $arr = array();
    for($i = 1; $i < 32; $i++)
    {
      $arr[] = array(
        'dia' => $i,
        'enviados' => $this->countByDay($i)
      );
    }
    return $arr;
  }

  public function countByParceiros() {
    $parceiro_id = $this->session->userdata('parceiro_id');
    $sql = "
            select p.nome, count(*) as total_enviados from comunicacao c
            inner join produto_parceiro_comunicacao ppc on ppc.produto_parceiro_comunicacao_id = c.produto_parceiro_comunicacao_id
            inner join produto_parceiro pp on pp.produto_parceiro_id = ppc.produto_parceiro_id
            inner join parceiro p on p.parceiro_id = pp.parceiro_id
            where pp.parceiro_id=$parceiro_id
            group by p.parceiro_id";

    $query =$this->_database->query($sql);

    if($query->num_rows() > 0)
    {
      return $query->result_array();
    }
    return array();
  }

  public function countByEngines() {
    $parceiro_id = $this->session->userdata('parceiro_id');
    $sql = "
        select ce.nome, count(*) as total_enviados from comunicacao c
            inner join produto_parceiro_comunicacao ppc on ppc.produto_parceiro_comunicacao_id = c.produto_parceiro_comunicacao_id
            inner join produto_parceiro pp on pp.produto_parceiro_id = ppc.produto_parceiro_id
            inner join comunicacao_template ct on ct.comunicacao_template_id = ppc.comunicacao_template_id
            inner join comunicacao_engine_configuracao cec on cec.comunicacao_engine_configuracao_id = ct.comunicacao_engine_configuracao_id
            inner join comunicacao_engine ce on ce.comunicacao_engine_id = cec.comunicacao_engine_id
            where pp.parceiro_id=$parceiro_id
        group by ce.comunicacao_engine_id";

    $query =$this->_database->query($sql);

    if($query->num_rows() > 0)
    {
      return $query->result_array();
    }
    return array();
  }

  public function get_all($limit = 0, $offset = 0, $processa = true) {
    if($processa) {
      $parceiro_id = $this->session->userdata('parceiro_id');
      
      // $this->_database->join("produto_parceiro_comunicacao","produto_parceiro_comunicacao.produto_parceiro_comunicacao_id=comunicacao.produto_parceiro_comunicacao_id");
      $this->_database->join("produto_parceiro","produto_parceiro.produto_parceiro_id=produto_parceiro_comunicacao.produto_parceiro_id");
      if (!empty($parceiro_id)) {
        $this->_database->where("(produto_parceiro.parceiro_id=$parceiro_id)");
      }
        
    }
    return parent::get_all($limit, $offset);
  }


  public function get_total($processa = true) {
    if($processa) {
      $parceiro_id = $this->session->userdata('parceiro_id');
      //Efetua join com cotação
      //$this->_database->join("parceiro as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

      //$this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
      $this->_database->join("produto_parceiro_comunicacao","produto_parceiro_comunicacao.produto_parceiro_comunicacao_id=comunicacao.produto_parceiro_comunicacao_id");
      $this->_database->join("produto_parceiro","produto_parceiro.produto_parceiro_id=produto_parceiro_comunicacao.produto_parceiro_id");
      $this->_database->where("(produto_parceiro.parceiro_id=$parceiro_id)");
    }
    return parent::get_total(); // TODO: Change the autogenerated stub
  }

}
