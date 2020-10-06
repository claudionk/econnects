<?php
Class Produto_parceiro_solicitacao_desistencia_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_solicitacao_desistencia';
    protected $primary_key = 'produto_parceiro_solicitacao_desistencia_id';

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
            'field' => 'solicitacao_desistencia',
            'label' => 'Solicitação de Desistencia',
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
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'solicitacao_desistencia' => $this->input->post('solicitacao_desistencia'),

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


    function  filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);

        return $this;
    }


}
