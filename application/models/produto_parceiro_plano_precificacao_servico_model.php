<?php
Class Produto_Parceiro_Plano_Precificacao_Servico_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_plano_precificacao_servico';
    protected $primary_key = 'produto_parceiro_plano_precificacao_servico_id';

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
            'field' => 'produto_parceiro_plano_id',
            'label' => 'Produto parceiro',
            'rules' => 'required|numeric',
            'groups' => 'default',
       //     'foreign' => 'produto_parceiro_plano',
        ),
        array(
            'field' => 'servico_produto_id',
            'label' => 'Serviço / Produto',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'servico_produto',
        ),
        array(
            'field' => 'valor',
            'label' => 'Valor',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_plano_id' => $this->input->post('produto_parceiro_plano_id'),
            'servico_produto_id' => $this->input->post('servico_produto_id'),
            'valor' => app_unformat_currency($this->input->post('valor')),
        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


    function  filter_by_produto_parceiro_plano($produto_parceiro_plano_id){

        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);

        return $this;
    }

}
