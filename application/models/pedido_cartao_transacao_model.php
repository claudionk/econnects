<?php

Class Pedido_Cartao_Transacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'pedido_cartao_transacao';
    protected $primary_key = 'pedido_cartao_transacao_id';

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
    );

    function filter_by_cartao($pedido_cartao_id)
    {
        $this->_database->where('pedido_cartao_transacao.pedido_cartao_id', $pedido_cartao_id);
        $this->_database->where('pedido_cartao_transacao.processado', 0);
        $this->_database->where('pedido_cartao_transacao.deletado', 0);
        return $this;
    }

    function filter_by_cartao_erro($pedido_cartao_id)
    {
        $this->_database->where('pedido_cartao_transacao.pedido_cartao_id', $pedido_cartao_id);
        $this->_database->where('pedido_cartao_transacao.processado', 1);
        $this->_database->where('pedido_cartao_transacao.result', 'ERRO');
        $this->_database->where('pedido_cartao_transacao.deletado', 0);
        return $this;
    }

    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
