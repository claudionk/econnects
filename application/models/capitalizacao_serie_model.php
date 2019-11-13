<?php
Class Capitalizacao_Serie_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'capitalizacao_serie';
    protected $primary_key = 'capitalizacao_serie_id';

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
            'field' => 'numero_inicio',
            'label' => 'Número de início',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'quantidade',
            'label' => 'Quantidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'ativo',
            'label' => 'ativo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'serie_aberta',
            'label' => 'Tipo de Série',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_inicio',
            'label' => 'Início Distribuição',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_fim',
            'label' => 'Fim Distribuição',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $len = strlen($this->input->post('numero_inicio'));
        $data =  array(
            'capitalizacao_id' => $this->input->post('capitalizacao_id'),
            'numero_inicio' => $this->input->post('numero_inicio'),
            'numero_fim' => str_pad((int)$this->input->post('numero_inicio') + (int)$this->input->post('quantidade'), $len, "0", STR_PAD_LEFT ) ,
            'quantidade' => app_retorna_numeros($this->input->post('quantidade')),
            'ativo' => $this->input->post('ativo'),
            'serie_aberta' => $this->input->post('serie_aberta'),
            'data_inicio' => app_dateonly_mask_to_mysql($this->input->post('data_inicio')),
            'data_fim' => app_dateonly_mask_to_mysql($this->input->post('data_fim')),
        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_capitalizacao($capitalizacao_id){
        $this->_database->where("{$this->_table}.capitalizacao_id", $capitalizacao_id);
        return $this;
    }

    function filter_by_ativo($ativo){
        $this->_database->where("{$this->_table}.ativo", $ativo);
        return $this;
    }

}
