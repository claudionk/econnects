<?php
Class Parceiro_Contato_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'parceiro_contato';
    protected $primary_key = 'parceiro_contato_id';

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
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_contato_cargo_id',
            'label' => 'Cargo',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_contato_departamento_id',
            'label' => 'Departamento',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'contato_tipo_id',
            'label' => 'Tipo de Contato',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'contato',
            'label' => 'Contato',
            'rules' => 'required|validate_contato',
            'groups' => 'default'
        )
    );


    function insert_contato($data){

        $this->load->model('contato_model', 'contato');

        $data_contato = array();
        $data_contato['contato_tipo_id'] = $data['contato_tipo_id'];
        $data_contato['nome'] = $data['nome'];
        $data_contato['contato'] = ($data['contato_tipo_id'] != 1) ? app_retorna_numeros($data['contato']) : $data['contato'];


        $contato_id = $this->contato->insert($data_contato, TRUE);

        $data_parceiro_contato = array();
        $data_parceiro_contato['contato_id'] = $contato_id;
        $data_parceiro_contato['parceiro_id'] = $data['parceiro_id'];
        $data_parceiro_contato['parceiro_contato_cargo_id'] = (int)$data['parceiro_contato_cargo_id'];
        $data_parceiro_contato['parceiro_contato_departamento_id'] = (int)$data['parceiro_contato_departamento_id'];
        return $this->insert($data_parceiro_contato, TRUE);



    }

    function update_contato($data){

        $this->load->model('contato_model', 'contato');

        $data_contato = array();
        $data_contato['contato_tipo_id'] = $data['contato_tipo_id'];
        $data_contato['nome'] = $data['nome'];
        $data_contato['contato'] = ($data['contato_tipo_id'] != 1) ? app_retorna_numeros($data['contato']) : $data['contato'];


        $this->contato->update( $data['contato_id'], $data_contato, TRUE);

        $data_parceiro_contato = array();
        $data_parceiro_contato['parceiro_contato_cargo_id'] = (int)$data['parceiro_contato_cargo_id'];
        $data_parceiro_contato['parceiro_contato_departamento_id'] = (int)$data['parceiro_contato_departamento_id'];
        return $this->update($data['parceiro_contato_id'], $data_parceiro_contato, TRUE);



    }


    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_parceiro($parceiro_id){

        $this->_database->where('parceiro_id', $parceiro_id);

        return $this;
    }

    //Agrega contato
    function with_contato($fields = array('contato.nome', 'contato.contato', 'contato.contato_tipo_id', 'contato_tipo.nome as contato_tipo'))
    {

        $this->_database->select($fields);
        $this->_database->join('contato', 'contato.contato_id = parceiro_contato.contato_id');
        $this->_database->join('contato_tipo', 'contato_tipo.contato_tipo_id = contato.contato_tipo_id');
        return $this;
    }

    //Agrega departamento e cargo
    function with_departamento_cargo($fields = array('parceiro_contato_cargo.nome as cargo', 'parceiro_contato_departamento.nome as departamento'))
    {

        $this->_database->select($fields);
        $this->_database->join('parceiro_contato_cargo', 'parceiro_contato_cargo.parceiro_contato_cargo_id = parceiro_contato.parceiro_contato_cargo_id', 'LEFT');
        $this->_database->join('parceiro_contato_departamento', 'parceiro_contato_departamento.parceiro_contato_departamento_id = parceiro_contato.parceiro_contato_departamento_id', 'LEFT');
        return $this;
    }

}
