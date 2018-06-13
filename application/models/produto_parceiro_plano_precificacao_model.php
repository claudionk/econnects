<?php
Class Produto_Parceiro_Plano_Precificacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_plano_precificacao';
    protected $primary_key = 'produto_parceiro_plano_precificacao_id';

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
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => 'required',
            'groups' => 'default'
        )/*,
        array(
            'field' => 'comissao',
            'label' => 'Comissão',
            'rules' => 'required',
            'groups' => 'default'
        )*/
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $valor_post = $this->input->post('valor');
        if(empty($valor_post)){
            $valor = 0;
        }else {
            $valor = app_unformat_currency($valor_post);
        }
        $data =  array(
            'produto_parceiro_plano_id' => $this->input->post('produto_parceiro_plano_id'),
            'descricao' => $this->input->post('descricao'),
            'valor' => $valor

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_campo(){
        $this->with_simple_relation('campo', 'campo_', 'campo_id', array('nome'));
        return $this;
    }

    function  filter_by_produto_parceiro_plano($produto_parceiro_plano_id){

        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);

        return $this;
    }

}
