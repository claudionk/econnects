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
        $this->_database->select('MAX(integracao_log.sequencia) as seq');
        $this->_database->where("integracao_log.integracao_id", $integracao_id);
        $result = $this->get_all();
        $result = (int)$result[0]['seq'] + 1;

        return $result;
    }

    function filter_by_integracao($integracao_id)
    {
        $this->_database->where("integracao_log.integracao_id", $integracao_id);
        return $this;
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
