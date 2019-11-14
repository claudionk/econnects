<?php
class comunicacao_agendamento_model extends MY_Model
{

  //Dados da tabela e chave primária
  protected $_table = "comunicacao_agendamento";
  protected $primary_key = "comunicacao_agendamento_id";
    protected $enable_log = FALSE;

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

  public $validate = array(

    array(
      'field' => 'produto_parceiro_comunicacao_id',
      'label' => 'Produto parceiro comunicação',
      'rules' => 'required|numeric',
      'groups' => 'default',
      'foreign' => 'produto_parceiro_comunicacao'
    ),

    array(
      'field' => 'comunicacao_status_id',
      'label' => 'Comunicacao status id',
      'rules' => 'required|numeric',
      'groups' => 'default',
      'foreign' => 'comunicacao_status'
    ),

    array(
      'field' => 'mensagem_from',
      'label' => 'Mensagem from',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'mensagem_from_name',
      'label' => 'Mensagem from name',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'mensagem_to',
      'label' => 'Mensagem to',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'mensagem_to_name',
      'label' => 'Mensagem to name',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'mensagem',
      'label' => 'Mensagem',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'data_enviar',
      'label' => 'Data enviar',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'data_envio',
      'label' => 'Data envio',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'retorno',
      'label' => 'Retorno',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'retorno_codigo',
      'label' => 'Retorno codigo',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'tabela',
      'label' => 'Tabela',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'campo',
      'label' => 'Campo',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'chave',
      'label' => 'Chave',
      'rules' => '',
      'groups' => 'default',
    ),
    array(
      'field' => 'cotacao_id',
      'label' => 'cotacao_id',
      'rules' => '',
      'groups' => 'default',
    ),



  );

}
