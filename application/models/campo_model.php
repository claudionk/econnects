<?php

Class Campo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'campo';
    protected $primary_key = 'campo_id';

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
            'field' => 'campo_classe_id',
            'label' => 'Classe',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'opcoes',
            'label' => 'Opções',
            'rules' => 'trim',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome_banco',
            'label' => 'Nome Banco',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome_banco_equipamento',
            'label' => 'Nome Banco Equipamento',
            'rules' => 'trim',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome_banco_generico',
            'label' => 'Nome Banco Generico',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome_banco_viagem',
            'label' => 'Nome Banco Viagem',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'classes',
            'label' => 'Classes CSS',
            'rules' => 'trim',
            'groups' => 'default'
        )
    );


    //Agrega tipo de campo
    function with_campo_tipo($fields = array('nome'))
    {
        $this->with_simple_relation('campo_tipo', 'campo_tipo_', 'campo_tipo_id', $fields );
        return $this;
    }

    //Agrega tipo de campo
    function with_campo_classe($fields = array('nome'))
    {
        $this->with_simple_relation('campo_classe', 'campo_classe_', 'campo_classe_id', $fields );
        return $this;
    }


    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
