<?php

Class Canal_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'canal';
    protected $primary_key = 'canal_id';

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
            'groups' => 'default',
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default',
        ),
    );

    function filter_by_slug( $slug ){
        $this->_database->where($this->_table .".slug", $slug);
        return $this;
    }

    function with_produto_parceiro( $produto_parceiro_id, $tipo = 0 ){
        $this->_database->select(" IF(produto_parceiro_canal.canal_id IS NULL, 0, 1) AS checado ", FALSE);
        $this->_database->join("produto_parceiro_canal", "produto_parceiro_canal.canal_id = {$this->_table}.canal_id AND produto_parceiro_canal.produto_parceiro_id = {$produto_parceiro_id} AND produto_parceiro_canal.tipo = {$tipo} AND produto_parceiro_canal.deletado = 0", "left");
        return $this;
    }

}
