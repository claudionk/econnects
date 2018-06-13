<?php
Class Comissao_Item_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'comissao_item';
    protected $primary_key = 'comissao_item_id';

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
            'field' => 'comissao_id',
            'label' => 'Comissão',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'comissao',
        ),
        array(
            'field' => 'tipo',
            'label' => 'Tipo',
            'rules' => 'required|enum[PORCENTAGEM,VALOR]',
            'groups' => 'default'
        ),
        array(
            'field' => 'inicial',
            'label' => 'Valor inicial',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'final',
            'label' => 'Valor Final',
            'rules' => 'required',
            'groups' => 'default'
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
        $data =  parent::get_form_data($just_check);
        $data['inicial'] = (isset($data['inicial'])) ? app_unformat_currency($data['inicial']) : 0;
        $data['final'] = (isset($data['final'])) ? app_unformat_currency($data['final']) : 0;
        $data['valor'] = (isset($data['valor'])) ? app_unformat_currency($data['valor']) : 0;
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }


    function  filter_by_comissao($comissao_id){

        $this->_database->where("{$this->_table}.comissao_id", $comissao_id);

        return $this;
    }


    function  filter_by_intevalo_valor($valor){

        $this->_database->where("$valor >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$valor <=", "{$this->_table}.final", FALSE);
        return $this;
    }


}
