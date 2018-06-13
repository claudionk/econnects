<?php
Class Banco_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'banco';
    protected $primary_key = 'banco_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');

    //Dados
    public $validate = array(
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo',
            'label' => 'Código',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
