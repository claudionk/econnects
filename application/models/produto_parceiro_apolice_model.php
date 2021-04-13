<?php
Class Produto_Parceiro_Apolice_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_apolice';
    protected $primary_key = 'produto_parceiro_apolice_id';

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
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'template',
            'label' => 'Template',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'template_coberturas',
            'label' => 'Template das Coberturas',
            'rules' => '',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'template' => $this->input->post('template'),
            'template_coberturas' => $this->input->post('template_coberturas'),
        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function  filter_by_produto_parceiro($produto_parceiro_id)
    {
        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        return $this;
    }

}
