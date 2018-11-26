<?php
Class Integracao_Log_Detalhe_Api_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao_log_detalhe_api';
    protected $primary_key = 'integracao_log_detalhe_api_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;
    protected $soft_insert = FALSE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();

    public function geraLogApiFail($dados = []){
        return $this->insert($dados, TRUE);
    }

    function filterByLogID($integracao_log_id){
        $this->_database->where("integracao_log_detalhe_api.integracao_log_detalhe_api_id", $integracao_log_id);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
