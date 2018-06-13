<?php
Class Apolice_Movimentacao_Tipo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'apolice_movimentacao_tipo';
    protected $primary_key = 'apolice_movimentacao_tipo_id';

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
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    function filter_by_slug($slug)
    {
        $this->_database->where('apolice_movimentacao_tipo.slug', $slug);
        return $this;
    }


    function get_by_id($id)
    {
        return $this->get($id);
    }


}
