<?php
Class Capitalizacao_Serie_Titulo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'capitalizacao_serie_titulo';
    protected $primary_key = 'capitalizacao_serie_titulo_id';

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
            'field' => 'ativo',
            'label' => 'ativo',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'ativo' => $this->input->post('ativo'),
            'numero' => $this->input->post('numero'),
            'capitalizacao_serie_id' => $this->input->post('capitalizacao_serie_id'),
        );
        if(!$data['numero']){
            unset($data['numero']);
        }
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function  filter_by_capitalizacao_serie($capitalizacao_serie_id){

        $this->_database->where("{$this->_table}.capitalizacao_serie_id", $capitalizacao_serie_id);

        return $this;
    }

    function  filter_by_pedido($pedido_id){

        $this->_database->where("{$this->_table}.pedido_id", $pedido_id);

        return $this;
    }

}
