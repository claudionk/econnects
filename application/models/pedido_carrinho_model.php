<?php
class Pedido_Carrinho_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'pedido_carrinho';
    protected $primary_key = 'pedido_carrinho_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = FALSE;

    //Chaves
    protected $update_at_key = 'update_at';
    protected $create_at_key = 'create_at';

    //Dados
    public $validate = array();

    //Retorna por Id
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
