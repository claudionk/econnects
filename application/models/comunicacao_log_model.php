<?php

class Comunicacao_log_model extends MY_Model
{

//Dados da tabela e chave primária
    protected $_table = "comunicacao_log";
    protected $primary_key = "comunicacao_log_id";

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

    public $validate = array(

        array(
            'field' => 'comunicacao_id',
            'label' => 'Comunicacao id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'comunicacao'
        ),

        array(
            'field' => 'descricao',
            'label' => 'Descricao',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'dados',
            'label' => 'Dados',
            'rules' => 'required',
            'groups' => 'default',
        ),

    );
}