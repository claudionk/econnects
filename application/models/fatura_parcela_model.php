<?php
Class Fatura_Parcela_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'fatura_parcela';
    protected $primary_key = 'fatura_parcela_id';

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

    //Get dados
    public function get_form_data($just_check = false)
    {

    }

    function with_fatura_status($fields = array('nome'))
    {
        $this->with_simple_relation('fatura_status', 'fatura_status_', 'fatura_status_id', $fields );
        return $this;
    }

    function filterByFatura($fatura_id){
        $this->_database->where("fatura_parcela.fatura_id", $fatura_id);
        return $this;
    }


    function get_by_id($id)
    {
        return $this->get($id);
    }


}
