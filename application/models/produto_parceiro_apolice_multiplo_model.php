<?php
Class Produto_Parceiro_Apolice_Multiplo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_apolice_multiplo';
    protected $primary_key = 'produto_parceiro_apolice_multiplo_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

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
            'field' => 'produto_parceiro_apolice_multiplo_range_id',
            'label' => 'Range de Apólice',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'produto_parceiro_id',
            'label' => 'Produto',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_apolice_multiplo_range_id' => $this->input->post('produto_parceiro_id'),
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
        );
        return $data;
    }

    public function removeAll($produto_parceiro_apolice_multiplo_range_id)
    {
        $this->delete_by(array('produto_parceiro_apolice_multiplo_range_id' => $produto_parceiro_apolice_multiplo_range_id));
    }

    function get_by_produto_parceiro_apolice_multiplo_range_id($produto_parceiro_apolice_multiplo_range_id)
    {
        $this->_database->where("{$this->_table}.produto_parceiro_apolice_multiplo_range_id", $produto_parceiro_apolice_multiplo_range_id);
        return $this;
    }

    function get_by_produto_parceiro_id($produto_parceiro_id)
    {
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        return $this;
    }

}
