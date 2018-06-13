<?php

class Comunicacao_evento_model extends MY_Model
{

//Dados da tabela e chave primária
    protected $_table = "comunicacao_evento";
    protected $primary_key = "comunicacao_evento_id";

//Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

//Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'descricao');

    public $validate = array(

        array(
            'field' => 'comunicacao_tipo_id',
            'label' => 'Comunicacao tipo id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'comunicacao_tipo'
        ),

        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'descricao',
            'label' => 'Descricao',
            'rules' => 'required',
            'groups' => 'default',
        ),

    );
}