<?php

Class Servico_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'servico';
    protected $primary_key = 'servico_id';

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
            'field' => 'servico_tipo_id',
            'label' => 'Tipo',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'servico_tipo'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'usuario',
            'label' => 'Usuário',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'senha',
            'label' => 'Senha',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parametros',
            'label' => 'Parametros',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'token',
            'label' => 'Token',
            'rules' => '',
            'groups' => 'default'
        )
    );

    function with_prod_parc_serv ($produto_parceiro_id)
    {
        $this->_database->select('pps.param');
        $this->_database->join('produto_parceiro_servico pps', "{$this->_table}.servico_id = pps.servico_id AND pps.produto_parceiro_id = $produto_parceiro_id AND pps.deletado = 0", "left");
        return $this;
    }

}
