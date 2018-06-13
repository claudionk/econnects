<?php
Class Cotacao_Seguro_Viagem_Pessoa_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cotacao_seguro_viagem_pessoa';
    protected $primary_key = 'cotacao_seguro_viagem_pessoa_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'endereco', 'endereco_complemento', 'endereco_bairro', 'endereco_cidade', 'endereco_uf');
    
    //Dados
    public $validate = array(

    );



    function filter_by_seguro_viagem($cotacao_seguro_viagem_id){
        $this->_database->where('cotacao_seguro_viagem_id', $cotacao_seguro_viagem_id);

        return $this;

    }
    
    
    
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
