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

  public function countByDay($day = 'd', $month = "m", $year = "Y")
  {
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

  public function countByDays()
  {
    $arr = array();
    for ($i = 1; $i < 32; $i++) {
      $arr[] = array(
        'dia' => $i,
        'enviados' => $this->countByDay($i)
      );
    }
    return $arr;
  }

  public function countByParceiros()
  {
    $parceiro_id = $this->session->userdata('parceiro_id');
    $sql = "
            select p.nome, count(*) as total_enviados from comunicacao c
            inner join produto_parceiro_comunicacao ppc on ppc.produto_parceiro_comunicacao_id = c.produto_parceiro_comunicacao_id
            inner join produto_parceiro pp on pp.produto_parceiro_id = ppc.produto_parceiro_id
            inner join parceiro p on p.parceiro_id = pp.parceiro_id
            where pp.parceiro_id=$parceiro_id
            group by p.parceiro_id";

    $query = $this->_database->query($sql);

    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return array();
  }

  public function countByEngines()
  {
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

    $query = $this->_database->query($sql);

    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return array();
  }

  public function get_all($limit = 0, $offset = 0, $processa = true)
  {
    if ($processa) {
      $parceiro_id = $this->session->userdata('parceiro_id');

      // $this->_database->join("produto_parceiro_comunicacao","produto_parceiro_comunicacao.produto_parceiro_comunicacao_id=comunicacao.produto_parceiro_comunicacao_id");
      $this->_database->join("produto_parceiro", "produto_parceiro.produto_parceiro_id=produto_parceiro_comunicacao.produto_parceiro_id");
      if (!empty($parceiro_id)) {
        $this->_database->where("(produto_parceiro.parceiro_id=$parceiro_id)");
      }
    }
    return parent::get_all($limit, $offset);
  }


  public function get_total($processa = true)
  {
    if ($processa) {
      $parceiro_id = $this->session->userdata('parceiro_id');
      //Efetua join com cotação
      //$this->_database->join("parceiro as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

      //$this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
      $this->_database->join("produto_parceiro_comunicacao", "produto_parceiro_comunicacao.produto_parceiro_comunicacao_id=comunicacao.produto_parceiro_comunicacao_id");
      $this->_database->join("produto_parceiro", "produto_parceiro.produto_parceiro_id=produto_parceiro_comunicacao.produto_parceiro_id");
      $this->_database->where("(produto_parceiro.parceiro_id=$parceiro_id)");
    }
    return parent::get_total(); // TODO: Change the autogenerated stub
  }

  public function getDataReport($reportName, $parameters, $justRead = false)
  {
    $sql = $this->$reportName($parameters);

    if ($justRead) {
      $sql .= " LIMIT 1";
    }
    $query = $this->_database->query($sql);

    if ($query->num_rows() > 0) {
      if ($justRead) {
        return array_keys($query->result_array()[0]);
      }
      return $query->result_array();
    }
    return array();
  }

  public function slaCapitalizacao($parameters)
  {
    $where = ' ';
    if (isset($parameters['periodo']) && $parameters['periodo']) {
      $where .= " and DATE_FORMAT(DATE_ADD(l.processamento_fim, interval -1 MONTH), '%m/%Y') = '" . $parameters['periodo'] . "'";
    }
    return
      "SELECT l.integracao_log_id ID, DATE_FORMAT(DATE_ADD(l.processamento_fim, interval -1 MONTH), '%m/%Y') PERIODO, i.nome OPERACAO, CONCAT('<a href=\'/var/www/webroot/ROOT/assets/uploads/integracao/',  i.integracao_id,'/', i.tipo,'/', l.nome_arquivo, '\' download>', l.nome_arquivo, '</a>')  ARQUIVO, l.quantidade_registros `QUANTIDADE DE REGISTROS`, DATE_FORMAT(l.processamento_fim, '%d/%m/%Y') `DATA DO PROCESSAMENTO`
    FROM integracao i
    INNER JOIN integracao_log l ON i.integracao_id = l.integracao_id AND l.deletado = 0 AND l.quantidade_registros > 0 AND l.processamento_fim IS NOT NULL
    WHERE i.tipo = 'S' AND i.slug_group = 'sulacap-ativacao' AND i.deletado = 0 " . $where . " ";
  }

  public function slaEmissaoCancelamento($parameters)
  {
    $where = ' ';
    if (isset($parameters['operacao']) && $parameters['operacao']) {
      $where .= " and operacao = '" . $parameters['operacao'] . "'";
    }
    if (isset($parameters['periodo']) && $parameters['periodo']) {
      $where .= " and periodo = '" . $parameters['periodo'] . "'";
    }
    return
      "SELECT l.integracao_log_id ID, SUBSTRING_INDEX(SUBSTRING_INDEX(l.nome_arquivo, '.', 2), '.', -1) OPERACAO, CONCAT('<a href=\'/var/www/webroot/ROOT/assets/uploads/integracao/',  i.integracao_id,'/', i.tipo,'/', l.nome_arquivo, '\' download>', l.nome_arquivo, '</a>')  ARQUIVO, l.quantidade_registros `QUANTIDADE DE REGISTROS`, DATE_FORMAT(l.processamento_fim, '%d/%m/%Y') `DATA DO PROCESSAMENTO`
      FROM integracao i
      INNER JOIN integracao_log l ON i.integracao_id = l.integracao_id AND l.deletado = 0 AND l.processamento_fim IS NOT NULL AND l.nome_arquivo NOT LIKE '%SINISTRO%' AND l.nome_arquivo NOT LIKE '%COBRANCA%'
      WHERE i.tipo = 'R' AND i.slug_group = 'retorno-seguradora' AND i.deletado = 0 " . $where . " ";
  }

  public function slaEmissaoCancelamentoRejeicao($parameters)
  {
    $where = ' ';
    if (isset($parameters['operacao']) && $parameters['operacao']) {
      $where .= " and operacao = '" . $parameters['operacao'] . "'";
    }
    if (isset($parameters['periodo']) && $parameters['periodo']) {
      $where .= " and periodo = '" . $parameters['periodo'] . "'";
    }
    return
      "SELECT d.integracao_log_detalhe_id ID, SUBSTRING_INDEX(SUBSTRING_INDEX(l.nome_arquivo, '.', 2), '.', -1) OPERACAO, a.num_apolice_cliente BILHETE, x.movimento MOVIMENTO, d.criacao `DATA DA CRÍTICA`, cta.CTA_Retorno_ok `DATA DA RESOLUÇÃO`
      FROM (
      SELECT substring_index(d.chave, '|', 1) num_apolice, substring_index(d.chave, '|', -1) apolice_movimentacao_tipo_id, IF(substring_index(d.chave, '|', -1) = '1', 'EMISSAO', 'CANCELAMENTO') movimento, MIN(d.integracao_log_detalhe_id) integracao_log_detalhe_id
      FROM integracao i
      INNER JOIN integracao_log l ON i.integracao_id = l.integracao_id AND l.deletado = 0
      INNER JOIN integracao_log_detalhe d ON l.integracao_log_id = d.integracao_log_id AND d.deletado = 0
          INNER JOIN integracao_log_detalhe_campo c ON d.integracao_log_detalhe_id = c.integracao_log_detalhe_id AND c.deletado = 0
      #and c.msg not like '<html%'
              #and c.msg not like 'Falha%'
      WHERE i.tipo = 'R' AND i.slug_group = 'retorno-seguradora' and i.deletado = 0
      AND d.integracao_log_status_id = 5
          #and d.integracao_log_detalhe_id = 7637484
          GROUP BY d.chave, IF(substring_index(d.chave, '|', -1) = '1', 'EMISSAO', 'CANCELAMENTO')
      ) x
      INNER JOIN integracao_log_detalhe d ON x.integracao_log_detalhe_id = d.integracao_log_detalhe_id
      INNER JOIN integracao_log_detalhe_campo c ON d.integracao_log_detalhe_id = c.integracao_log_detalhe_id
      INNER JOIN integracao_log l ON d.integracao_log_id = l.integracao_log_id
      INNER JOIN apolice a ON x.num_apolice = a.num_apolice AND a.deletado = 0
      LEFT JOIN cta_movimentacao cta ON a.apolice_id = cta.apolice_id AND cta.apolice_movimentacao_tipo_id = x.apolice_movimentacao_tipo_id " . $where . " ";
  }

  public function slaEmissaoCancelamentoRejeicaoBilhete($parameters)
  {
    $where = ' ';
    if (isset($parameters['operacao']) && $parameters['operacao']) {
      $where .= " and operacao = '" . $parameters['operacao'] . "'";
    }
    if (isset($parameters['periodo']) && $parameters['periodo']) {
      $where .= " and periodo = '" . $parameters['periodo'] . "'";
    }
    return
      "SELECT l.integracao_log_id ID, SUBSTRING_INDEX(SUBSTRING_INDEX(l.nome_arquivo, '.', 2), '.', -1) OPERACAO, CONCAT('<a href=\'/var/www/webroot/ROOT/assets/uploads/integracao/',  i.integracao_id,'/', i.tipo,'/', l.nome_arquivo, '\' download>', l.nome_arquivo, '</a>')  ARQUIVO, l.quantidade_registros `QUANTIDADE DE REGISTROS`, DATE_FORMAT(l.processamento_fim, '%d/%m/%Y') `DATA DO PROCESSAMENTO`
      FROM integracao i
      INNER JOIN integracao_log l ON i.integracao_id = l.integracao_id AND l.deletado = 0 AND l.processamento_fim IS NOT NULL AND l.nome_arquivo LIKE '%COBRANCA%'
      WHERE i.tipo = 'R' AND i.slug_group = 'retorno-seguradora' AND i.deletado = 0 " . $where . " ";
  }

  public function slaBaixaComissao($parameters)
  {
    $where = ' ';
    if (isset($parameters['operacao']) && $parameters['operacao']) {
      $where .= " and operacao = '" . $parameters['operacao'] . "'";
    }
    if (isset($parameters['periodo']) && $parameters['periodo']) {
      $where .= " and periodo = '" . $parameters['periodo'] . "'";
    }
    return
      "SELECT l.integracao_log_id ID, DATE_FORMAT(DATE_ADD(l.processamento_fim, interval -1 MONTH), '%m/%Y') PERIODO, i.nome OPERACAO, CONCAT('<a href=\'/var/www/webroot/ROOT/assets/uploads/integracao/',  i.integracao_id,'/', i.tipo,'/', l.nome_arquivo, '\' download>', l.nome_arquivo, '</a>')  ARQUIVO, l.quantidade_registros `QUANTIDADE DE REGISTROS`, DATE_FORMAT(l.processamento_fim, '%d/%m/%Y') `DATA DO PROCESSAMENTO`
    FROM integracao i
    INNER JOIN integracao_log l ON i.integracao_id = l.integracao_id AND l.deletado = 0 AND l.quantidade_registros > 0 AND l.processamento_fim IS NOT NULL
    WHERE i.tipo = 'S' AND i.slug_group = 'sulacap-ativacao' AND i.deletado = 0 " . $where . " ";
  }
}
