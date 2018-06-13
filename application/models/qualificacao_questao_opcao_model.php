<?php
Class Qualificacao_Questao_Opcao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'qualificacao_questao_opcao';
    protected $primary_key = 'qualificacao_questao_opcao_id';

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
            'field' => 'nome',
            'label' => 'Qualificação',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'qualificacao_questao_id',
            'label' => 'Questão',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'regua_valor',
            'label' => 'Regua',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'qualificacao_questao_id' => $this->input->post('qualificacao_questao_id'),
            'regua_valor' => $this->input->post('regua_valor')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_questao($qualificacao_questao_id){

        $this->_database->where('qualificacao_questao_id', $qualificacao_questao_id );

        return $this;
    }



}
