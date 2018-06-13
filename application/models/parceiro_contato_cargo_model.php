<?php
Class Parceiro_Contato_Cargo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'parceiro_contato_cargo';
    protected $primary_key = 'parceiro_contato_cargo_id';

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
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }


}
