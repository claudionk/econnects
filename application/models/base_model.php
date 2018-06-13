<?php
Class Base_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'base';
    protected $primary_key = 'base_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'estado_civil', 'sexo', 'cep', 'tipo_logradouro', 'logradouro', 'complemento', 'bairro', 'municipio', 'uf','nome_mae');


    //Dados
    public $validate = array(

    );

    //Get dados

    function get_by_id($id)
    {
        return $this->get($id);
    }


}
