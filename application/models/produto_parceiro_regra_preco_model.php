<?php
Class Produto_Parceiro_Regra_Preco_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_regra_preco';
    protected $primary_key = 'produto_parceiro_regra_preco_id';

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
            'field' => 'regra_preco_id',
            'label' => 'Regra Preço',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'regra_preco_id' => $this->input->post('regra_preco_id'),
            'parametros' => $this->input->post('parametros'),
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_regra_preco(){
        $this->with_simple_relation('regra_preco', 'regra_preco_', 'regra_preco_id', array('nome'));
        return $this;
    }

    function  filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);

        return $this;
    }

}
