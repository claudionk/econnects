<?php
Class Produto_Parceiro_Plano_Precificacao_Itens_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_plano_precificacao_itens_config';
    protected $primary_key = 'produto_parceiro_plano_precificacao_itens_config_id';

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
            'field' => 'produto_parceiro_plano_precificacao_itens_id',
            'label' => 'Item do Plano',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'produto_parceiro_plano_precificacao_itens',
        ),
        array(
            'field' => 'unidade_tempo',
            'label' => 'Unidade',
            'rules' => 'required|enum[DIA,MES,ANO,VALOR,IDADE,COMISSAO,GARANTIA_FABRICANTE]',
            'groups' => 'default'
        ),
        array(
            'field' => 'inicial',
            'label' => 'Inicial',
            'rules' => 'required|numericOrDecimal',
            'groups' => 'default'
        ),
        array(
            'field' => 'final',
            'label' => 'Final',
            'rules' => 'required|numericOrDecimal',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_plano_precificacao_itens_id' => $this->input->post('produto_parceiro_plano_precificacao_itens_id'),
            'unidade_tempo' => $this->input->post('unidade_tempo'),
            'inicial' => app_unformat_currency($this->input->post('inicial')),
            'final' => app_unformat_currency($this->input->post('final')),
        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_produto_parceiro_plano_precificacao_itens($produto_parceiro_plano_precificacao_itens_id){

        $this->_database->where("{$this->_table}.produto_parceiro_plano_precificacao_itens", $produto_parceiro_plano_precificacao_itens);

        return $this;
    }

    function remove_itens_config($produto_parceiro_plano_precificacao_itens_id){
        $this->update_by( ['produto_parceiro_plano_precificacao_itens_id' => $produto_parceiro_plano_precificacao_itens_id], ['deletado' => 1]);
    }

}
