<?php
Class Contato_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'contato';
    protected $primary_key = 'contato_id';

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
            'field' => 'contato_tipo_id',
            'label' => 'Tipod e Contato',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_contato',
            'label' => 'Data do Contato',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'contato',
            'label' => 'Contato',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados

    //Agrega relação simples com tipo de contato
    function with_contato_tipo($fields = array('nome'))
    {
        $this->with_simple_relation_foreign('contato_tipo', 'contato_tipo_', 'contato_tipo_id', 'contato_tipo_id', $fields );
        return $this;
    }

    public function delete_by_cliente($id_cliente, $contato_tipo_id){
        $query = $this->_database->query("
            UPDATE cliente_contato
            JOIN contato ON contato.contato_id = cliente_contato.contato_id
            SET contato.deletado = 1, contato.alteracao = NOW(), cliente_contato.deletado = 1, cliente_contato.alteracao = NOW()
            WHERE cliente_contato.cliente_id =  $id_cliente 
            AND contato.contato_tipo_id = $contato_tipo_id
            AND cliente_contato.deletado =  0 AND contato.deletado =  0
        ");
    }

}
