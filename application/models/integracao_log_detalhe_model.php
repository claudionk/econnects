<?php
Class Integracao_Log_Detalhe_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao_log_detalhe';
    protected $primary_key = 'integracao_log_detalhe_id';

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
        ),
    
    );

    public function insLogDetalhe($integracao_log_id, $num_linha = 0, $valor_chave = '', $retorno = ''){

        $dados_log = array();

        $dados_log['integracao_log_status_id'] = 2;
        $dados_log['integracao_log_id'] = $integracao_log_id;
        $dados_log['num_linha'] = $num_linha;
        $dados_log['chave'] = $valor_chave;
        $dados_log['retorno'] = $retorno;
        $dados_log['retorno_codigo'] = '';

        $integracao_log_detalhe_id = $this->insert($dados_log, TRUE);


        // $result = $this->get($integracao_log_detalhe_id);

        return $integracao_log_detalhe_id;
    }

    function filterByLogID($integracao_log_id){
        $this->_database->where("integracao_log_detalhe.integracao_log_id", $integracao_log_id);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    public function getProcessSucess($chave, $tipo_transacao)
    {
        $this->_database
        ->join("integracao_log", "{$this->_table}.integracao_log_id = integracao_log.integracao_log_id", 'inner')
        ->join("integracao_log_detalhe_dados", "{$this->_table}.integracao_log_detalhe_id = integracao_log_detalhe_dados.integracao_log_detalhe_id", 'inner')
        ->where_in("integracao_log.deletado", 0)
        ->where_in("integracao_log.integracao_id", 15)
        ->where_in("{$this->_table}.deletado", 0)
        ->where_in("{$this->_table}.chave", $chave)
        ->where_in("{$this->_table}.integracao_log_status_id", 4)
        ->where_in("integracao_log_detalhe_dados.tipo_transacao", $tipo_transacao);

        $process = $this->get_all();

        return ($process) ? $process : array();
    }

}
