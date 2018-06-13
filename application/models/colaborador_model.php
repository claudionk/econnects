<?php

Class Colaborador_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'colaborador';
    protected $primary_key = 'colaborador_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;
    
    protected  $salt = '174mJuR18mS0lhgKL2J0CETRlN252x';

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
            'field' => 'colaborador_cargo_id',
            'label' => 'Cargo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'cpf',
            'label' => 'CPF',
            'rules' => 'trim|required|min_length[11]|max_lenght[11]|validate_cnpj_cpf',
            'groups' => 'default'
        ),
        array(
            'field' => 'agencia',
            'label' => 'Agência',
            'rules' => 'numeric',
            'groups' => 'default'
        ),
        array(
            'field' => 'conta',
            'label' => 'Conta do banco',
            'rules' => 'numeric',
            'groups' => 'default'
        ),
        array(
            'field' => 'banco_id',
            'label' => 'Banco',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_nascimento',
            'label' => 'Data de nascimento',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'telefone',
            'label' => 'Telefone',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'celular',
            'label' => 'Celular',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'foto',
            'label' => 'Foto',
            'rules' => '',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($limit = 0, $offset = 0)
    {
        //Dados
        $data =  array(
            'colaborador_cargo_id' => $this->input->post('colaborador_cargo_id'),
            'nome' => $this->input->post('nome'),
            'agencia' => (int)app_retorna_numeros($this->input->post('agencia')),
            'banco_id' => (int)$this->input->post('banco_id'),
            'conta' => $this->input->post('conta'),
            'data_nascimento' => app_dateonly_mask_mysql_null($this->input->post('data_nascimento')),
            'cpf' => app_retorna_numeros($this->input->post('cpf')),
            'telefone' => app_retorna_numeros($this->input->post('telefone')),
            'celular' => app_retorna_numeros($this->input->post('celular')),
            'foto' => $this->input->post('foto'),
            'email' => $this->input->post('email'),
        );

        return $data;
    }
    //Agrega colaborador
    function with_colaborador_cargo($fields = array('descricao'))
    {
        $this->with_simple_relation('colaborador_cargo', 'colaborador_cargo_', 'colaborador_cargo_id', $fields );
        return $this;
    }

    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
