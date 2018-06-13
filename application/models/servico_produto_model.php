<?php
Class Servico_Produto_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'servico_produto';
    protected $primary_key = 'servico_produto_id';

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
            'field' => 'nome',
            'label' => 'Serviço',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'unidade',
            'label' => 'Unidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'quantidade_minima',
            'label' => 'Quantidade Mínima',
            'rules' => 'required|numeric',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'descricao' => $this->input->post('descricao'),
            'unidade' => $this->input->post('unidade'),
            'quantidade_minima' => $this->input->post('quantidade_minima'),
        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

}
