<?php

Class Pedido_Boleto_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'pedido_boleto';
    protected $primary_key = 'pedido_boleto_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;
    
    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();
    
    //Dados
    public $validate = array(
        array(
            'field' => 'sacado_nome',
            'label' => 'Nome do Sacado',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );


    //Retorna por slug
    function filter_by_pedido($pedido_id)
    {
        $this->_database->where('pedido_boleto.pedido_id', $pedido_id);
        //$this->_database->where('pedido_boleto.processado', 0);
        $this->_database->where('pedido_boleto.deletado', 0);
        $this->_database->where('pedido_boleto.ativo', 1);
        $this->_database->order_by('pedido_boleto.criacao', 'DESC');
        $this->_database->limit(1);
        return $this;
    }

    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}

