<?php
Class Cotacao_Cobertura_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cotacao_cobertura';
    protected $primary_key = 'cotacao_cobertura_id';

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

    function filterByID($cotacao_id){
        $this->_database->where("cotacao_id", $cotacao_id);
        $this->_database->where("deletado", 0);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    /**
     * Retorna todos
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function get_all($limit = 0, $offset = 0, $processa = true)
    {
        return parent::get_all($limit, $offset);
    }

}
