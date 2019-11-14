<?php

Class Produto_Parceiro_Canal_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_canal';
    protected $primary_key = 'produto_parceiro_canal_id';

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
            'label' => 'Produto do Parceiro',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'canal_id',
            'label' => 'Canal',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'tipo',
            'label' => 'Emissão / Cancelamento',
            'rules' => 'required',
            'groups' => 'default',
        ),
    );

    function remove_produto_parceiro( $produto_parceiro_id ){
        $this->update_by( ['produto_parceiro_id' => $produto_parceiro_id], ['deletado' => 1]);
    }

    function filter_by_produto_parceiro_tipo($produto_parceiro_id, $tipo = 0){
        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        $this->_database->where('tipo', $tipo);
        return $this;
    }

    function with_canal()
    {
        $this->_database->select(" canal.nome ");
        $this->_database->join("canal", "canal.canal_id = {$this->_table}.canal_id", "join");
        return $this;
    }

}
