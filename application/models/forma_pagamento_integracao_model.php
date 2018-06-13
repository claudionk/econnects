<?php
Class Forma_Pagamento_Integracao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'forma_pagamento_integracao';
    protected $primary_key = 'forma_pagamento_integracao_id';

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
            'field' => 'chave_acesso',
            'label' => 'Chave de Acesso (produção)',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'chave_acesso_homolog',
            'label' => 'Chave de Acesso (homologação)',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'url',
            'label' => 'URL de Acesso',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'producao',
            'label' => 'Produção',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'qnt_erros',
            'label' => 'Quantidade de Erros',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'chave_acesso' => $this->input->post('chave_acesso'),
            'chave_acesso_homolog' => $this->input->post('chave_acesso_homolog'),
            'url' => $this->input->post('url'),
            'producao' => $this->input->post('producao'),
            'qnt_erros' => $this->input->post('qnt_erros'),

        );
        return $data;
    }

    function get_by_slug($slug)
    {
        return $this->get_by('slug', $slug);
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }


}
