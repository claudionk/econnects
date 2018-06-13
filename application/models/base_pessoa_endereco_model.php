<?php
Class Base_Pessoa_Endereco_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'base_pessoa_endereco';
    protected $primary_key = 'base_pessoa_endereco_id';

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
   
    );


    function filter_by_base_pessoa($base_pessoa_id){
        $this->_database->where("{$this->_table}.base_pessoa_id", $base_pessoa_id );
        return $this;

    }


}
