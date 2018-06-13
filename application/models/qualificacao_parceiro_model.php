<?php
Class Qualificacao_Parceiro_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'qualificacao_parceiro';
    protected $primary_key = 'qualificacao_parceiro_id';

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
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'parceiro_id' => $this->input->post('parceiro_id'),
            'qualificacao_id' => $this->input->post('qualificacao_id')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_qualificacao($qualificacao_id){

        $this->_database->where('qualificacao_id', $qualificacao_id );

        return $this;
    }

    function with_parceiro(){

       $this->with_simple_relation('parceiro', 'parceiro_', 'parceiro_id', array('nome'));

        return $this;
    }


}
