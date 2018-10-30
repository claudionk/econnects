<?php
Class Parceiro_Produto_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'parceiro_produto';
    protected $primary_key = 'parceiro_produto_id';

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
        array(
            'field' => 'produto_parceiro_id',
            'label' => 'Produto',
            'rules' => 'required',
            'groups' => 'default'
        ),
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
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
            'parceiro_id' => $this->input->post('parceiro_id'),
        );
        return $data;
    }

    public function removeAll($parceiro_id)
    {
        $this->delete_by(array('parceiro_id' => $parceiro_id));
    }

    function get_by_parceiro($parceiro_id)
    {
        $this->_database->where("{$this->_table}.parceiro_id", $parceiro_id);
        return $this;
    }

}

