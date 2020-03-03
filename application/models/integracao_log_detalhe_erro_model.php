<?php
Class Integracao_Log_Detalhe_Erro_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao_log_detalhe_erro';
    protected $primary_key = 'integracao_log_detalhe_erro_id';

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
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => '',
            'groups' => 'default',
        ),
        array(
            'field' => 'tipo',
            'label' => 'Tipo de Crítica',
            'rules' => '',
            'groups' => 'default',
        ),
    );

    function filterByCodErroParceiro($cod_erro, $slug = null)
    {
        $this->_database->join("integracao_log_detalhe_erro_parceiro p", "{$this->_table}.integracao_log_detalhe_erro_id = p.integracao_log_detalhe_erro_id", 'inner');
        $this->_database->where("p.cod_erro", $cod_erro);

        if ( !empty($slug) )
        {
            $this->_database->where("p.slug_group", $slug);
        }

        return $this;
    }

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

}
