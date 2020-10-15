<?php
Class Integracao_Log_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao_log';
    protected $primary_key = 'integracao_log_id';

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
            'field' => 'integracao_log_status_id',
            'label' => 'Status',
            'rules' => '',
            'groups' => 'default',
            'foreign' => 'integracao_log_status'
        )
    );


    public function insLog($integracao_id, $qnt_registros = 0, $file = ''){

        $dados_log = array();

        $dados_log['integracao_log_status_id'] = 1;
        $dados_log['integracao_id'] = $integracao_id;
        $dados_log['sequencia'] = $this->get_next_sequencia($integracao_id);
        $dados_log['processamento_inicio'] = date('Y-m-d H:i:s');
        $dados_log['quantidade_registros'] = $qnt_registros;
        $dados_log['nome_arquivo'] = $file;

        $integracao_log_id = $this->insert($dados_log, TRUE);


        $result = $this->get($integracao_log_id);

        return $result;
    }

    function get_next_sequencia($integracao_id)
    {
        $result = $this->_database->query("select ifnull(max(integracao_log.sequencia),0) as seq
                                             from integracao_log
                                            where integracao_log.integracao_id = $integracao_id
                                              and integracao_log.integracao_log_id in (select max(integracao_log_id) from integracao_log where integracao_log.integracao_id = $integracao_id and integracao_log.deletado = 0)
                                ")->result_array();
        $result = (int)$result[0]['seq'] + 1;
        return $result;
    }

    function filter_by_integracao($integracao_id)
    {
        $this->_database->where("integracao_log.integracao_id", $integracao_id);
        return $this;
    }

    function get_file_by_apolice_sequencia($num_apolice, $sequencia)
    {
        $result = $this->_database->query("SELECT l.* 
            FROM integracao i 
            JOIN integracao_log l ON i.integracao_id = l.integracao_id AND l.deletado = 0 
            WHERE i.cod_tpa IN(
                SELECT produto_parceiro.cod_tpa 
                FROM pedido
                INNER JOIN apolice ON apolice.pedido_id = pedido.pedido_id AND apolice.deletado = 0 AND apolice.num_apolice = '$num_apolice'
                INNER JOIN cotacao ON cotacao.cotacao_id = pedido.cotacao_id AND cotacao.deletado = 0
                INNER JOIN produto_parceiro ON produto_parceiro.produto_parceiro_id = cotacao.produto_parceiro_id
                WHERE pedido.deletado = 0
            ) 
            AND i.tipo = 'S'
            AND l.sequencia = $sequencia
        ")->result_array();
        return emptyor($result[0], []);
    }

    function filter_by_file($file)
    {
        $this->_database->where("integracao_log.nome_arquivo", $file);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    function filter_ret_CTA_custom($integracao_id)
    {
        $q = $this->_database->query("SELECT * FROM _CTA_reprocess_retorno WHERE integracao_id = $integracao_id")->result_array();
        return $q;
    }

    function get_sequencia_by_cod_prod($integracao_id, $cod_produto)
    {
        $this->_database->select("COUNT(`integracao_log`.`integracao_id`) AS seq");
        $this->_database->join("integracao_log_status", "{$this->_table}.integracao_log_status_id = integracao_log_status.integracao_log_status_id");
        $this->_database->where("{$this->_table}.integracao_id", $integracao_id);
        $this->_database->where("{$this->_table}.retorno", $cod_produto);
        $this->_database->where("integracao_log_status.slug", "S");
        $result = $this->get_all();
        $seq = emptyor($result[0]['seq'], 0);
        $result = (int)$seq + 1;
        return $result;
    }

}
