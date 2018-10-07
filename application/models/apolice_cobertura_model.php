<?php
Class Apolice_Cobertura_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'apolice_cobertura';
    protected $primary_key = 'apolice_cobertura_id';

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

    public function deleteByCotacao($cotacao_id){

        $this->db->where('cotacao_id', $cotacao_id);
        $this->db->delete($this->_table);

    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

}
