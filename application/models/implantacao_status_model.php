<?php

Class Implantacao_Status_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'implantacao_status';
    protected $primary_key = 'implantacao_status_id';

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
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default',
        ),
    );

    function filter_by_produto_parceiro_id( $produto_parceiro_id ){
        $this->_database->where("produto_parceiro_plano.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        return $this->get_all();
    }

}
