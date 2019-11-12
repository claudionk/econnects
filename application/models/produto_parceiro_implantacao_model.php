<?php

Class Produto_Parceiro_Implantacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_implantacao';
    protected $primary_key = 'produto_parceiro_implantacao_id';

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
            'field' => 'produto_parceiro_id',
            'label' => 'Produto Parceiro',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'implantacao_status_id',
            'label' => 'Status de Implantação',
            'rules' => 'required',
            'groups' => 'default',
        ),
    );

    function filter_by_produto_parceiro_id( $produto_parceiro_id ){
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        return $this;
    }

    function filter_by_implantacao_status_id( $implantacao_status_id ){
        $this->_database->where("{$this->_table}.implantacao_status_id", $implantacao_status_id);
        return $this;
    }

    function filter_by_implantacao_slug( $implantacao_slug ){
        $this->_database->select('parceiro.nome as user');
        $this->_database->join("implantacao_status", "implantacao_status.implantacao_status_id = {$this->_table}.implantacao_status_id", "inner");
        $this->_database->join("parceiro", "parceiro.parceiro_id = {$this->_table}.alteracao_usuario_id", "inner");
        $this->_database->where("implantacao_status.slug", $implantacao_slug);
        return $this;
    }

    function filter_by_last(){
        $this->_database->order_by("{$this->_table}.criacao", "DESC");
        $this->_database->limit(1);
        return $this;
    }

    function with_implantacao()
    {
        $this->_database->select('implantacao_status.nome, implantacao_status.slug');
        $this->_database->join("implantacao_status", "implantacao_status.implantacao_status_id = {$this->_table}.implantacao_status_id", "inner");
        return $this;
    }

}
