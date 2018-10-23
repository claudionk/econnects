<?php

Class Pedido_Dados_Bancarios_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'pedido_dados_bancarios';
    protected $primary_key = 'pedido_dados_bancarios_id';

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
            'label' => 'nome',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'segurado',
            'label' => 'segurado',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'tipofavorecido',
            'label' => 'tipofavorecido',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'tipoconta',
            'label' => 'tipoconta',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'banco',
            'label' => 'banco',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'agencia',
            'label' => 'agencia',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'conta',
            'label' => 'conta',
            'rules' => 'required',
            'groups' => 'default',
        ),      
    );

    //Retorna por slug
    function get_by_slug($slug)
    {
        return $this->get_by('slug', $slug);
    }


    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

}
