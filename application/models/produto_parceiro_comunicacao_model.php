<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Lazarini
 * Date: 09/06/2016
 * Time: 15:39
 */


class Produto_parceiro_comunicacao_model extends MY_Model
{

//Dados da tabela e chave primária
    protected $_table = "produto_parceiro_comunicacao";
    protected $primary_key = "produto_parceiro_comunicacao_id";

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

    public $validate = array(
        array(
            'field' => 'produto_parceiro_id',
            'label' => 'Produto parceiro id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'produto_parceiro'
        ),

        array(
            'field' => 'comunicacao_evento_id',
            'label' => 'Comunicacao evento id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'comunicacao_evento'
        ),

        array(
            'field' => 'comunicacao_template_id',
            'label' => 'Comunicacao template',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'comunicacao_template'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descricao',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'disparo',
            'label' => 'Disparo (Dias)',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'disparo_quantidade',
            'label' => 'Quantidade (Dias)',
            'rules' => 'required',
            'groups' => 'default',
        ),

    );


    function with_parceiro(){

        $this->_database->select("parceiro.nome_fantasia as parceiro_nome_fantasia")
            ->join("parceiro", "parceiro.parceiro_id = produto_parceiro.parceiro_id");

        return $this;
    }
}