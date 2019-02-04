<?php
Class Apolice_Generico_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'apolice_generico';
    protected $primary_key = 'apolice_generico_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome','endereco_logradouro', 'endereco_complemento',  'endereco_bairro', 'endereco_cidade', 'endereco_estado');

    //Dados
    public $validate = array(

    );

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_apolice($apolice_id){
        $this->_database->where('apolice_id', $apolice_id);
        return $this;
    }
}
