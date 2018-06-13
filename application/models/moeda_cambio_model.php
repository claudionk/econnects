<?php
Class Moeda_Cambio_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'moeda_cambio';
    protected $primary_key = 'moeda_cambio_id';

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
            'field' => 'data_cambio',
            'label' => 'Data do Câmbio',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor_real',
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
            'moeda_id' => $this->input->post('moeda_id'),
            'data_cambio' => app_dateonly_mask_to_mysql($this->input->post('data_cambio')),
            'valor_real' => app_unformat_currency($this->input->post('valor_real')),
        );
        return $data;
    }

    //Retorna por Id
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    function  filter_by_moeda($moeda_id){

        $this->_database->where("{$this->_table}.moeda_id", $moeda_id);

        return $this;
    }

    function  filter_by_data($data){

        $this->_database->where("{$this->_table}.data_cambio", $data);

        return $this;
    }

    //Agrega colaborador
    function with_moeda($fields = array('nome'))
    {
        $this->with_simple_relation('moeda', 'moeda_', 'moeda_id', $fields );
        return $this;
    }

    function getValor($moeda_id, $valor){

        $cotacao_dia = $this->filter_by_moeda($moeda_id)->order_by('data_cambio', 'DESC')->limit(1)->get_all();
        $cotacao_dia = $cotacao_dia[0];
        return $valor*$cotacao_dia['valor_real'];

    }

    function getCotacaoDia($moeda_id){

        $cotacao_dia = $this->filter_by_moeda($moeda_id)->order_by('data_cambio', 'DESC')->limit(1)->get_all();
        $cotacao_dia = $cotacao_dia[0];
        return $cotacao_dia;

    }

}
