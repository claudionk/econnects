<?php
Class Contato_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'contato';
    protected $primary_key = 'contato_id';

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
            'field' => 'contato_tipo_id',
            'label' => 'Tipod e Contato',
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
            'field' => 'contato',
            'label' => 'Contato',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados

    //Agrega relação simples com tipo de contato
    function with_contato_tipo($fields = array('nome'))
    {
        $this->with_simple_relation_foreign('contato_tipo', 'contato_tipo_', 'contato_tipo_id', 'contato_tipo_id', $fields );
        return $this;
    }

}
