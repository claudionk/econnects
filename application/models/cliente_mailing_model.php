<?php
Class Cliente_Mailing_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cliente_mailing';
    protected $primary_key = 'cliente_mailing_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('descricao');

    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    function get_by_codigo($codigo)
    {
        return $this->where("{$this->_table}.codigo", $codigo);
    }
}
