<?php
Class Produto_Parceiro_Cliente_Status_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_cliente_status';
    protected $primary_key = 'produto_parceiro_cliente_status_id';

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
            'field'   => 'produto_parceiro_id',
            'label'   => 'Produto',
            'rules'   => 'required',
            'groups'  => 'default'
        ),

        array(
            'field'   => 'cliente_evolucao_status_id',
            'label'   => 'Satus',
            'rules'   => 'required',
            'groups'  => 'default'
        ),

        array(
            'field'   => 'cliente_evolucao_status_grupo_id',
            'label'   => 'Grupo',
            'rules'   => 'required',
            'groups'  => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'cliente_evolucao_status_id' => $this->input->post('cliente_evolucao_status_id'),
            'cliente_evolucao_status_grupo_id' => $this->input->post('cliente_evolucao_status_grupo_id'),
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id')
        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->select("{$this->_table}.*, cliente_evolucao_status_grupo.descricao_grupo, cliente_evolucao_status.descricao");
        $this->_database->join("cliente_evolucao_status","cliente_evolucao_status.cliente_evolucao_status_id = {$this->_table}.cliente_evolucao_status_id", "left");
        $this->_database->join("cliente_evolucao_status_grupo","cliente_evolucao_status_grupo.cliente_evolucao_status_grupo_id = {$this->_table}.cliente_evolucao_status_grupo_id", "left");
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->where("{$this->_table}.deletado", 0);

        return $this;
    }

}
