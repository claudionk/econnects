<?php
Class Usuario_Model extends MY_Model {
  protected $_table = 'usuario';
  protected $primary_key = 'usuario_id';

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
    array(
      'field' => 'nome',
      'label' => 'Nome',
      'rules' => 'required',
      'groups' => 'add_parceiro, edit_parceiro, config, add_colaborador, edit_colaborador'
    ),
    array(
      'field' => 'colaborador_id',
      'label' => 'Colaborador',
      'rules' => 'required',
      'groups' => 'add, edit'
    ),
    array(
      'field' => 'email',
      'label' => 'E-mail',
      'rules' => 'required|valid_email',
      'groups' => 'add, edit, add_parceiro, edit_parceiro, config, add_colaborador, edit_colaborador'
    ),
    array(
      'field' => 'ativo',
      'label' => 'Ativo',
      'groups' => 'add, edit, add_parceiro, edit_parceiro, add_colaborador, edit_colaborador'
    ),
    array(
      'field' => 'usuario_acl_tipo_id',
      'label' => 'Nível do usuário',
      'rules' => 'required',
      'groups' => 'add, edit, add_parceiro, edit_parceiro, add_colaborador, edit_colaborador',
      'foreign' => 'usuario_acl_tipo',
    ),
    array(
      //regra apenas para adicionar
      'field' => 'email',
      'label' => 'E-mail',
      'rules' => 'required|valid_email|check_email_usuario_exists',
      'groups' => 'add, add_parceiro, add_colaborador'
    ),
    array(
      //regra apenas para editar check_email_usuario_owner
      'field' => 'email',
      'label' => 'E-mail',
      'rules' => 'required|valid_email|check_email_usuario_owner',
      'groups' => 'edit, edit_parceiro, edit_colaborador'
    ),
    array(
      //regra apenas para adicionar
      'field' => 'cpf',
      'label' => 'CPF',
      'rules' => 'validate_cpf|check_cpf_usuario_exists',
      'groups' => 'add, add_parceiro, add_colaborador'
    ),
    array(
      //regra apenas para editar check_email_usuario_owner
      'field' => 'cpf',
      'label' => 'CPF',
      'rules' => 'validate_cpf|check_cpf_usuario_owner',
      'groups' => 'edit, edit_parceiro, config, edit_colaborador'
    ),
    array(
      //Precisa inserir uma senha ao adicionar um novo usuario
      'field' => 'senha',
      'label' => 'Senha',
      'rules' => 'required',
      'groups' => 'add, add_parceiro, add_colaborador'
    ),
    array(
      //Precisa inserir uma senha ao adicionar um novo usuario
      'field' => 'token',
      'label' => 'Token',
      'groups' => 'add, add_parceiro, add_colaborador'
    ),
    array(
      //Precisa inserir uma senha ao adicionar um novo usuario
      'field' => 'parceiro_id',
      'label' => 'parceiro',
      'rules' => '',
      'groups' => 'add, edit, edit_parceiro, add_colaborador, edit_colaborador'
    )

  );
  function get_form_data( $limit = 0, $offset = 0 ) {
    $ativo = $this->input->post('ativo');
    $usuario_acl_tipo_id = $this->input->post('usuario_acl_tipo_id');
    $parceiro_id = $this->input->post('parceiro_id');
    $data =  array(
      'colaborador_id' => (int)$this->input->post('colaborador_id'),
      'parceiro_id' => $this->input->post('parceiro_id'),
      'nome' => $this->input->post('nome'),
      'email' => $this->input->post('email'),
      'cpf' => $this->input->post('cpf'),
      'usuario_acl_tipo_id' => $usuario_acl_tipo_id,
      'ativo' => ($ativo=="" ? "1" : $ativo),
      'colaborador_cargo_id' => app_retorna_numeros($this->input->post('colaborador_cargo_id')),
      'banco_id' => app_retorna_numeros($this->input->post('banco_id')),
      'telefone' => app_retorna_numeros($this->input->post('telefone')),
      'celular' => app_retorna_numeros($this->input->post('celular')),
      'data_nascimento' => app_dateonly_mask_mysql_null($this->input->post('data_nascimento')),
      'agencia' => $this->input->post('agencia'),
      'conta' => $this->input->post('conta'),
    );

    $data['parceiro_id'] = (int) $this->input->post("parceiro_id");

    if($this->input->post('senha') && $this->input->post('senha') !== ''){
      $data['senha'] = MD5($this->salt.$this->input->post('senha'));
    }

    if(!isset($data['token']) || empty($data['token']))
    {
      $data['token'] = md5($this->salt . date("YmdHis") . $this->salt);
    }

    return $data;

  }

  function get_form_data_colaborador() {
    $data =  array(
      'colaborador_id' => (int)$this->input->post('colaborador_id'),
      'parceiro_id' => 0,
      'nome' => '',
      'email' => $this->input->post('email'),
      'cpf' => '',
      'usuario_acl_tipo_id' => $this->input->post('usuario_acl_tipo_id'),
      'ativo' => $this->input->post('ativo'),
    );

    if($this->input->post('senha') && $this->input->post('senha') !== ''){
      $data['senha'] = MD5($this->salt.$this->input->post('senha'));
    }

    return $data;

  }

  function insere_usuario( $data, $id_colaborador ) {
    $data['colaborador_id'] = $id_colaborador;
    $this->_database->insert($this->_table, $data);
  }
  
  //Realiza update no usuário
  function update_config($id) {
    $data = $this->get_form_data();
    $dados_usuario = array(
      'nome' => $data['nome'],
      'cpf' => $data['cpf'],
    );

    if(isset($data['senha'])){
      $dados_usuario['senha'] = $data['senha'];
    }
    $this->_database->where($this->_table . '.usuario_id', $id);
    $this->_database->update($this->_table, $dados_usuario);
  }    //Realiza update no usuário
  
  function update_usuario ($id, $data) {
    $this->_database->where($this->_table . '.colaborador_id', $id);
    $this->_database->update($this->_table, $data);
  }
  //Retorna colaborador id de usuário
  function get_colaborador_by_id($id)
  {
    $query = $this->_database->get_where($this->_table, array($this->primary_key => $id));
    if($query->num_rows() > 0) {
      $row = $query->result_array();
      return $row[0];

    }
    return array();
  }

  //Retorna usuario pelo colaborador
  function get_by_colaborador_id($id) {
    $query = $this->_database->select('usuario.usuario_id, usuario.usuario_acl_tipo_id, usuario.colaborador_id, usuario.senha, usuario.email, usuario.ativo')->get_where($this->_table, array('colaborador_id' => $id));

    if ($query->num_rows() > 0) {
      $row = $query->result_array();
      return $row[0];
    }

    return array();
  }
  //Retorna usuário
  function get_usuario($id) {
    return $this->getRow($id);
  }

  function validateEditForm() {

    $this->load->library('form_validation');
    $this->form_validation->set_message('required', 'O campo %s é obrigatório.');
    $this->form_validation->set_message('valid_email', 'O campo %s precisa ter um e-mail válido.');

    $this->form_validation->set_rules('colaborador_id', 'Colaborador', 'required');
    $this->form_validation->set_rules('nome', 'Nome', 'required');
    $this->form_validation->set_rules('usuario_acl_tipo_id', 'Tipo', 'required');

    /**
         * Se for um novo registro verifica se o e-mail ja esta sendo usando
         */
    if($this->input->post('new_record')) {
      $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|check_email_usuario_exists');
      $this->form_validation->set_rules('senha', 'Senha', 'required');
      /**
         * Se for uma alteração verifica se o e-mail ja esta sendo usado,
         * caso o endereço de e-mail tenha mudado
         *
         */
    } else {
      //$this->form_validation->set_rules('ativo', 'Ativo', 'required');
      $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|check_email_usuario_owner');
    }
    return $this->form_validation->run();
  }

  function check_acl3( $parceiro_id ) {
    $this->_database->select($this->_table. '.usuario_acl_tipo_id');
    $this->_database->from( $this->_table);
    $this->_database->where( $this->_table. '.usuario_acl_tipo_id', 3);
    $this->_database->where( $this->_table. '.parceiro_id', $parceiro_id);
    $this->_database->where( $this->_table. '.deletado', 0);

    $query = $this->_database->get();

    if ($query->num_rows() > 0) {
      return true;
    } else {
      return false;
    }

  }

  function get_user_externo( $parceiro_id ) {
    $this->_database->select($this->_table.'.email, '.$this->_table.'.senha');
    $this->_database->from( $this->_table);
    $this->_database->where( $this->_table. '.usuario_acl_tipo_id', 3);
    $this->_database->where( $this->_table. '.parceiro_id', $parceiro_id);
    $this->_database->where( $this->_table. '.deletado', 0);

    $query = $this->_database->get();

    if ($query->num_rows() > 0) {
      return $query->result_array();
    } else {
      return null;
    }

  }

  /**
     * Verifica se tem algum usuario ativo com esse endereço de mail
     *
     * @param $email
     * @return bool
     */
  function check_email_exists($email) {
    log_message('debug', 'check_email_user'. $email);
    $this->_database->select($this->_table. '.email');
    $this->_database->from($this->_table);
    $this->_database->where($this->_table. '.email', $email);
    $this->_database->where($this->_table. '.deletado', 0);

    $query = $this->_database->get();


    if ($query->num_rows() > 0) {

      return true;

    } else {
      return false;
    }

  }

  /**
     * Verifica se tem algum usuario ativo com esse endereço de mail
     *
     * @param $email
     * @return bool
     */
  function check_cpf_exists($cpf){

    $this->_database->select($this->_table. '.cpf');
    $this->_database->from($this->_table);
    $this->_database->where($this->_table. '.cpf', $cpf);
    $this->_database->where($this->_table. '.deletado', 0);

    $query = $this->_database->get();

    if ($query->num_rows() > 0) {

      return true;

    } else {
      return false;
    }

  }
  /**
     * Verifica se o usuario é dono do e-mail
     *
     * @param $email
     * @param $usuario_id
     * @return bool
     */
  function check_email_owner($email, $usuario_id){

    log_message('debug', 'check_email_owner'. $email . ' - ' . $usuario_id);
    $this->_database->select($this->_table. '.email');
    $this->_database->from($this->_table);
    $this->_database->where($this->_table. '.email', $email);

    if($usuario_id)
      $this->_database->where($this->_table. '.usuario_id', $usuario_id);
    else
      $this->_database->where($this->_table. '.colaborador_id', $this->input->post("colaborador_id"));


    $this->_database->where($this->_table. '.deletado', 0);

    $query = $this->_database->get();

    if ($query->num_rows() > 0) {

      return true;

    } else {
      return false;
    }

  }
  /**
     * Verifica se o usuario é dono do cpf
     *
     * @param $cpf
     * @param $usuario_id
     * @return bool
     */
  function check_cpf_owner($cpf, $usuario_id){

    $this->_database->select($this->_table. '.cpf');
    $this->_database->from($this->_table);
    $this->_database->where($this->_table. '.cpf', $cpf);
    $this->_database->where($this->_table. '.usuario_id', $usuario_id);
    $this->_database->where($this->_table. '.deletado', 0);

    $query = $this->_database->get();

    if ($query->num_rows() > 0) {

      return true;

    } else {
      return false;
    }

  }

  /**
     * Efetua login no sistema
     * @param $login
     * @param $password
     * @return bool
     */
  function login($login, $password) {
    $this->load->model('usuario_acl_recurso_model', 'recursos');

    $this->_database->select($this->_table. '.*');
    $this->_database->select('colaborador.colaborador_cargo_id');
    $this->_database->select('colaborador.banco_id');
    $this->_database->select('colaborador.nome as nome_colaborador');
    $this->_database->select('colaborador.telefone');
    $this->_database->select('colaborador.celular');
    $this->_database->select('colaborador.email as email_colaborador');
    $this->_database->select('colaborador.data_nascimento');
    $this->_database->select('colaborador.cpf');
    $this->_database->select('colaborador.agencia');
    $this->_database->select('colaborador.conta');
    $this->_database->select('colaborador.foto');
    $this->_database->from($this->_table);
    $this->_database->where($this->_table. '.email', $login);
    $this->_database->where($this->_table. '.senha', MD5($this->salt.$password));
    $this->_database->where($this->_table. '.ativo', 1);
    $this->_database->where($this->_table. '.deletado', 0);
    $this->_database->join('colaborador', 'colaborador.colaborador_id = '.$this->_table.'.colaborador_id', 'left');
    $this->_database->limit(1);

    $query = $this->_database->get();

    if ($query->num_rows() == 1) {
      $result = $query->result_array();
      $usuario = $result[0];
      $usuario['nome'] = empty($usuario['nome_colaborador']) ? $usuario['nome'] : $usuario['nome_colaborador'];
      $usuario['email'] = empty($usuario['email_colaborador']) ? $usuario['email'] : $usuario['email_colaborador'];
      $usuario['is_logged'] = true;
      $usuario['upload_url'] = base_url('assets/uploads/media') . '/';
      $usuario['recursos'] = array();

      $this->recursos->getRecursosUsuario($usuario['usuario_id'], 0, $usuario['recursos'], 1);

      /*
             * deleta os dados da sessão antiga
             */
      $this->session->sess_destroy();
      $this->session->set_userdata($usuario);
      $this->log_evento->log($this , 'login', 'Login');

      return $usuario['usuario_id'];
    } else {
      return false;
    }
  }

  function login_token($token)
  {
    $this->load->model('usuario_acl_recurso_model', 'recursos');

    $this->_database->select($this->_table. '.*');
    $this->_database->select('colaborador.colaborador_cargo_id');
    $this->_database->select('colaborador.banco_id');
    $this->_database->select('colaborador.nome as nome_colaborador');
    $this->_database->select('colaborador.telefone');
    $this->_database->select('colaborador.celular');
    $this->_database->select('colaborador.email as email_colaborador');
    $this->_database->select('colaborador.data_nascimento');
    $this->_database->select('colaborador.cpf');
    $this->_database->select('colaborador.agencia');
    $this->_database->select('colaborador.conta');
    $this->_database->select('colaborador.foto');
    $this->_database->from($this->_table);
    $this->_database->where($this->_table. '.token', $token);
    $this->_database->where($this->_table. '.ativo', 1);
    $this->_database->where($this->_table. '.deletado', 0);
    $this->_database->join('colaborador', 'colaborador.colaborador_id = '.$this->_table.'.colaborador_id', 'left');
    $this->_database->limit(1);

    $query = $this->_database->get();

    if ($query->num_rows() == 1) {
      $result = $query->result_array();
      $usuario = $result[0];
      $usuario['nome'] = empty($usuario['nome_colaborador']) ? $usuario['nome'] : $usuario['nome_colaborador'];
      $usuario['email'] = empty($usuario['email_colaborador']) ? $usuario['email'] : $usuario['email_colaborador'];
      $usuario['is_logged'] = true;
      $usuario['upload_url'] = base_url('assets/uploads/media') . '/';
      $usuario['recursos'] = array();

      $this->recursos->getRecursosUsuario($usuario['usuario_id'], 0, $usuario['recursos'], 1);

      /*
             * deleta os dados da sessão antiga
             */
      $this->session->sess_destroy();
      $this->session->set_userdata($usuario);
      $this->log_evento->log($this , 'login', 'Login');

      return $usuario['usuario_id'];
    } else {
      return false;
    }
  }


  function update_termo($usuario_id){

    $dados_usuario = array();
    $dados_usuario['termo_aceite'] = 1;
    $dados_usuario['termo_aceite_data'] = date('Y-m-d H:i:s');
    $dados_usuario['termo_aceite_ip'] = $this->input->ip_address();
    $this->update($usuario_id, $dados_usuario, TRUE);
    $this->session->set_userdata('termo_aceite', 1);
    redirect('admin/home');

  }

  function logout(){

    $this->log_evento->log($this , 'logout', 'Logout');
    $this->session->sess_destroy();

  }

  function  filter_by_parceiro($parceiro_id){

    $this->_database->where('parceiro_id', $parceiro_id);

    return $this;
  }

  function  filter_by_cpf($cpf){

    $this->_database->where('cpf', $cpf);

    return $this;
  }

  function with_usuario_acl_tipo($fields = array('nome'))
  {
    $this->with_simple_relation('usuario_acl_tipo', 'usuario_tipo_', 'usuario_acl_tipo_id', $fields );
    return $this;
  }
  function with_colaborador($fields = array('nome'))
  {
    $this->with_simple_relation('colaborador', 'colaborador_', 'colaborador_id', $fields );
    return $this;
  }
}

