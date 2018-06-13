<?php
Class Equipamento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'equipamento';
    protected $primary_key = 'equipamento_id';

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
            'field' => 'equipamento_marca_id',
            'label' => 'Marca',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'equipamento_marca'
        ),
        array(
            'field' => 'equipamento_categoria_id',
            'label' => 'Categoria',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'equipamento_categoria'
        ), /*
        array(
            'field' => 'equipamento_sub_categoria_id',
            'label' => 'Sub Categoria',
            'rules' => '',
            'groups' => 'default',
            'foreign' => 'equipamento_categoria',
            'foreign_key' => 'equipamento_categoria_id',
            'foreign_join' => 'left'
        ), */
        array(
            'field' => 'ean',
            'label' => 'EAN',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'tags',
            'label' => 'TAGS',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'marca',
            'label' => 'Marca',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'skus',
            'label' => 'SKUS',
            'rules' => '',
            'groups' => 'default'
        )
    );
}
