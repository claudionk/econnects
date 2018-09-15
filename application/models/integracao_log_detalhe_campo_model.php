<?php
Class Integracao_Log_Detalhe_Campo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao_log_detalhe_campo';
    protected $primary_key = 'integracao_log_detalhe_campo_id';

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

    public function insLogDetalheCampo($integracao_log_detalhe_id, $integracao_erros_id, $msg = '', $slug = ''){

        $dados_log = array();

        $dados_log['integracao_log_detalhe_id'] = $integracao_log_detalhe_id;
        $dados_log['integracao_erros_id'] = $integracao_erros_id;
        $dados_log['msg'] = $msg;
        $dados_log['slug'] = $slug;

        return $this->insert($dados_log, TRUE);
    }

    function filterByLogID($integracao_log_id){
        $this->_database->where("integracao_log_detalhe_campo.integracao_log_detalhe_id", $integracao_log_id);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
