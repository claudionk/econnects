<?php
Class Integracao_detalhe_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao_detalhe';
    protected $primary_key = 'integracao_detalhe_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'descricao');

    private $data_template_script = array();

    //Dados
    public $validate = array(
        array(
            'field' => 'integracao_id',
            'label' => 'Integração',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'tipo',
            'label' => 'Tipo de Dados',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'ordem',
            'label' => 'Ordem',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'script_sql',
            'label' => 'SQL',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'deletado',
            'label' => 'Deletado',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'habilitado',
            'label' => 'Habilitado',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'before_execute',
            'label' => 'Antes de Executar',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'after_execute',
            'label' => 'Depois de Executar',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'before_detail',
            'label' => 'Antes de Executar Detalhe',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'after_detail',
            'label' => 'Depois de Executar Detalhe',
            'rules' => '',
            'groups' => 'default'
        ),
    );

    public function __construct()
    {
        parent::__construct();

        $this->load->library('parser');
    }

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'integracao_id' => $this->input->post('integracao_id'),
            'tipo' => $this->input->post('tipo'),
            'ordem' => $this->input->post('ordem'),
            'script_sql' => $this->input->post('script_sql'),
            'deletado' => $this->input->post('deletado'),
            'criacao' => $this->input->post('criacao'),
            'alteracao_usuario_id' => $this->input->post('alteracao_usuario_id'),
            'alteracao' => $this->input->post('alteracao'),
            'before_execute' => $this->input->post('before_execute'),
            'after_execute' => $this->input->post('after_execute'),
            'before_detail' => $this->input->post('before_detail'),
            'after_detail' => $this->input->post('after_detail'),
        );
        return $data;
    }

    public function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

}
