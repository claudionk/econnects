<?php
Class Moeda_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'moeda';
    protected $primary_key = 'moeda_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('sigla', 'nome');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo',
            'label' => 'Código do País',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo_pais',
            'label' => 'Código do País',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'sigla',
            'label' => 'Símbolo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'padrao',
            'label' => 'Padrão',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo_bc',
            'label' => 'Código Banco Central',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'atualziar_cambio',
            'label' => 'Atualizar Câmbio',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'codigo' => $this->input->post('codigo'),
            'codigo_pais' => $this->input->post('codigo_pais'),
            'sigla' => $this->input->post('sigla'),
            'padrao' => $this->input->post('padrao'),
            'codigo_bc' => $this->input->post('codigo_bc'),
            'atualziar_cambio' => $this->input->post('atualziar_cambio'),
        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    function  filter_by_atualizacao_cambio(){

        $this->_database->where("{$this->_table}.atualziar_cambio", 1);

        return $this;
    }

    function  filter_by_moeda_padrao(){

        $this->_database->where("{$this->_table}.padrao", 1);

        return $this;
    }
}
