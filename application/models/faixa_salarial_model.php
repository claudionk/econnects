<?php
Class Faixa_Salarial_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'faixa_salarial';
    protected $primary_key = 'faixa_salarial_id';

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
            'label' => 'descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inicio',
            'label' => 'Início',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'fim',
            'label' => 'Fim',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'ordem',
            'label' => 'Ordem',
            'rules' => '',
            'groups' => 'default'
        ),
    );

    public function get_by_parceiro($parceiro_plano_id)
    {
        $this->_database->distinct();
        $this->_database->select("faixa_salarial.*");
        $this->_database->from("produto_parceiro_plano plano");
        $this->_database->join("produto_parceiro_plano_faixa_salarial", "produto_parceiro_plano_faixa_salarial.produto_parceiro_plano_id = plano.produto_parceiro_plano_id AND produto_parceiro_plano_faixa_salarial.deletado = 0");
        $this->_database->join("faixa_salarial", "faixa_salarial.faixa_salarial_id = produto_parceiro_plano_faixa_salarial.faixa_salarial_id");
        $this->_database->where("plano.produto_parceiro_id = {$parceiro_plano_id}");

        $query = $this->_database->get();
        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return array();
    }


    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'descricao' => $this->input->post('descricao'),
            'inicio' => app_unformat_currency($this->input->post('inicio')),
            'fim' => app_unformat_currency($this->input->post('fim')),

        );
        return $data;
    }

}
