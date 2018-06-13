<?php

class Comunicacao_template_model extends MY_Model
{

//Dados da tabela e chave primária
    protected $_table = "comunicacao_template";
    protected $primary_key = "comunicacao_template_id";

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
            'field' => 'comunicacao_tipo_id',
            'label' => 'Tipo de comunicação',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'comunicacao_tipo'
        ),
        array(
            'field' => 'comunicacao_engine_configuracao_id',
            'label' => 'Comunicacao engine configuracao id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'comunicacao_engine_configuracao'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descricao',
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
            'field' => 'mensagem',
            'label' => 'Mensagem',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'mensagem_de',
            'label' => 'De',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'mensagem_titulo',
            'label' => 'Título',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'mensagem_anexo',
            'label' => 'Anexo',
            'groups' => 'default',
            'type' => 'file',
        ),
    );
}