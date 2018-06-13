<?php
class Comunicacao_engine_configuracao_model extends MY_Model
{

//Dados da tabela e chave primária
    protected $_table = "comunicacao_engine_configuracao";
    protected $primary_key = "comunicacao_engine_configuracao_id";

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


    public $validate = array(
        array(
            'field' => 'comunicacao_engine_id',
            'label' => 'Comunicacao engine id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'comunicacao_engine'
        ),

        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'servidor',
            'label' => 'Servidor',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'porta',
            'label' => 'Porta',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'usuario',
            'label' => 'Usuario',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'senha',
            'label' => 'Senha',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'parametros',
            'label' => 'Parametros',
            'rules' => '',
            'groups' => 'default',
        ),

    );
}