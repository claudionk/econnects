<?php
Class Colaborador_Cargo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'colaborador_cargo';
    protected $primary_key = 'colaborador_cargo_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('descricao');

    
    //Dados
    public $validate = array(
        array(
            'field' => 'colaborador_departamento_id',
            'label' => 'Departamento',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'descricao' => $this->input->post('descricao'),
            'colaborador_departamento_id' => $this->input->post('colaborador_departamento_id'),
        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    function with_colaborador_departamento($fields = array('descricao'))
    {
        $this->with_simple_relation('colaborador_departamento', 'colaborador_departamento_', 'colaborador_departamento_id', $fields );
        return $this;
    }
}
