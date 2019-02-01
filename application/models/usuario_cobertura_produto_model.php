<?php
Class Usuario_Cobertura_Produto_Model extends MY_Model {
  protected $_table = 'usuario_cobertura_produto';
  protected $primary_key = 'usuario_cobertura_produto_id';

  protected $return_type = 'array';

  protected $soft_delete = TRUE;

  protected $soft_delete_key = 'deletado';

  protected  $salt = '174mJuR18mS0lhgKL2J0CETRlN252x';

  protected $update_at_key = 'alteracao';
  protected $create_at_key = 'criacao';

  //campos para transformação em maiusculo e minusculo
  protected $fields_lowercase = array();
  protected $fields_uppercase = array('nome');


  public $validate = array(
    // array(
    //   'field' => 'nome',
    //   'label' => 'Nome',
    //   'rules' => 'required',
    //   'groups' => 'add_parceiro, edit_parceiro, config, add_colaborador, edit_colaborador'
    // ),
    // array(
    //   'field' => 'colaborador_id',
    //   'label' => 'Colaborador',
    //   'rules' => 'required',
    //   'groups' => 'add, edit'
    // ),
    // array(
    //   'field' => 'email',
    //   'label' => 'E-mail',
    //   'rules' => 'required|valid_email',
    //   'groups' => 'add, edit, add_parceiro, edit_parceiro, config, add_colaborador, edit_colaborador'
    // ),
    // array(
    //   'field' => 'ativo',
    //   'label' => 'Ativo',
    //   'groups' => 'add, edit, add_parceiro, edit_parceiro, add_colaborador, edit_colaborador'
    // ),
    // array(
    //   'field' => 'usuario_acl_tipo_id',
    //   'label' => 'Nível do usuário',
    //   'rules' => 'required',
    //   'groups' => 'add, edit, add_parceiro, edit_parceiro, add_colaborador, edit_colaborador',
    //   'foreign' => 'usuario_acl_tipo',
    // ),
    // array(
    //   //regra apenas para adicionar
    //   'field' => 'email',
    //   'label' => 'E-mail',
    //   'rules' => 'required|valid_email|check_email_usuario_exists',
    //   'groups' => 'add, add_parceiro, add_colaborador'
    // ),
    // array(
    //   //regra apenas para editar check_email_usuario_owner
    //   'field' => 'email',
    //   'label' => 'E-mail',
    //   'rules' => 'required|valid_email|check_email_usuario_owner',
    //   'groups' => 'edit, edit_parceiro, edit_colaborador'
    // ),
    // array(
    //   //regra apenas para adicionar
    //   'field' => 'cpf',
    //   'label' => 'CPF',
    //   'rules' => 'validate_cpf|check_cpf_usuario_exists',
    //   'groups' => 'add, add_parceiro, add_colaborador'
    // ),
    // array(
    //   //regra apenas para editar check_email_usuario_owner
    //   'field' => 'cpf',
    //   'label' => 'CPF',
    //   'rules' => 'validate_cpf|check_cpf_usuario_owner',
    //   'groups' => 'edit, edit_parceiro, config, edit_colaborador'
    // ),
    // array(
    //   //Precisa inserir uma senha ao adicionar um novo usuario
    //   'field' => 'senha',
    //   'label' => 'Senha',
    //   'rules' => 'required',
    //   'groups' => 'add, add_parceiro, add_colaborador'
    // ),
    // array(
    //   //Precisa inserir uma senha ao adicionar um novo usuario
    //   'field' => 'token',
    //   'label' => 'Token',
    //   'groups' => 'add, add_parceiro, add_colaborador'
    // ),
    // array(
    //   //Precisa inserir uma senha ao adicionar um novo usuario
    //   'field' => 'parceiro_id',
    //   'label' => 'parceiro',
    //   'rules' => '',
    //   'groups' => 'add, edit, edit_parceiro, add_colaborador, edit_colaborador'
    // )

  );
  
}