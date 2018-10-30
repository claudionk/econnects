<?php
Class Cobertura_Plano_Model extends MY_Model {
    //Dados da tabela e chave primária
    protected $_table = 'cobertura_plano';
    protected $primary_key = 'cobertura_plano_id';

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
            'field' => 'cobertura_id',
            'label' => 'Cobertura',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'porcentagem',
            'label' => 'Porcentagem (%)',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'preco',
            'label' => 'Valor',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'mostrar',
            'label' => 'Exibição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'custo',
            'label' => 'Custo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'cobertura_custo',
            'label' => 'Cobertura Tipo Custo',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false) {
        //Dados
        $data =  array(
            'cobertura_id' => $this->input->post('cobertura_id'),
            'produto_parceiro_plano_id' => $this->input->post('produto_parceiro_plano_id'),
            'parceiro_id' => $this->input->post('parceiro_id'),
            'porcentagem' => app_unformat_currency($this->input->post('porcentagem')),
            'preco' => app_unformat_currency($this->input->post('preco')),
            'custo' => app_unformat_currency($this->input->post('custo')),
            'mostrar' => $this->input->post('mostrar'),
            'descricao' => $this->input->post('descricao'),
            'cobertura_custo' => $this->input->post('cobertura_custo'),

        );
        return $data;
    }
  
    function get_by_id($id) {
        return $this->get($id);
    }


    function  filter_by_produto_parceiro_plano($produto_parceiro_plano_id) {

        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);

        return $this;
    }


    //Agrega Coberturas
    function with_cobertura($fields = array('nome')) {
        $this->with_simple_relation('cobertura', 'cobertura_', 'cobertura_id', $fields );
        return $this;
    }
  
    //Agrega Coberturas
    function with_cobertura_tipo($fields = array('cobertura_tipo.nome as cobertura_tipo')) {
        $this->_database->select($fields);
        $this->_database->join('cobertura_tipo', 'cobertura_tipo.cobertura_tipo_id = cobertura.cobertura_tipo_id');
        return $this;
    }

    //Agrega Coberturas
    function with_parceiro($fields = array('parceiro.nome as parceiro_nome')) {
        $this->_database->select($fields);
        $this->_database->join('parceiro', 'parceiro.parceiro_id = cobertura_plano.parceiro_id');
        return $this;
    }

    //Agrega Coberturas
    function select_total_porcentagem() {
        $this->_database->select_sum('porcentagem');
        return $this;
    }

    function with_prod_parc($produto_parceiro_id, $produto_parceiro_plano_id = null){

        $this->_database->select("{$this->_table}.*, cobertura.*, produto_parceiro_plano.*");
        $this->_database->join("cobertura", "cobertura.cobertura_id = {$this->_table}.cobertura_id");
        $this->_database->join("produto_parceiro_plano", "produto_parceiro_plano.produto_parceiro_plano_id = {$this->_table}.produto_parceiro_plano_id");

        $this->_database->where("produto_parceiro_plano.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->where("produto_parceiro_plano.deletado", 0);
        $this->_database->where("cobertura.cobertura_tipo_id", 1);
        if (!empty($produto_parceiro_plano_id)) {
            $this->_database->where("produto_parceiro_plano.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        }

        return $this;
    }

}

