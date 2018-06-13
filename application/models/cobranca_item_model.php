<?php
Class Cobranca_Item_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cobranca_item';
    protected $primary_key = 'cobranca_item_id';

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
        ),
        array(
            'field' => 'descricao',
            'label' => 'descrição',
            'rules' => '',
            'groups' => 'default'
        )
    );

    function get_by_id($id)
    {
        return $this->get($id);
    }


}
