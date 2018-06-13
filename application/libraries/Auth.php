<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth {

    protected $default_config = array();
    protected $CI;
    protected $config = array();


    public function __construct( $config = array() )
    {
        $this->config = array_merge($this->default_config, $config);
        $this->CI = &get_instance();

        $this->CI->load->library('encrypt');
    }

    public function get_venda_online_token()
    {
        $this->CI->load->model("usuario_model", "usuario");

        $parceiro_id = $this->CI->session->userdata("parceiro_id");

        $usuario = $this->CI->usuario
            ->with_foreign()
            ->get_by(array(
                'usuario_acl_tipo.slug' => 'acesso_token',
                'parceiro_id' => $parceiro_id,
            ));

        if($usuario)
        {
            return $usuario['token'];
        }
        else
        {
            return false;
        }
    }

    public function generate_page_token($token = "", $urls_can_access = "", $layout = "", $context = "")
    {
        if(empty($url))
            $url = current_url();

        if(empty($token))
        {
            $token = $this->get_venda_online_token();

            if(!$token)
                return false;
        }

        $url = rawurlencode($this->CI->encrypt->encode($url));
        $token = rawurlencode($this->CI->encrypt->encode($token));
        $obj = json_encode(array(
            'urls_pode_acessar' => $urls_can_access,
            'layout' => $layout,
            'context' => $context,
        ));

        $obj = rawurlencode($this->CI->encrypt->encode($obj));

        return base_url("redirecionamento/carrega?url={$url}&token={$token}&obj={$obj}");
    }

    /**
     * Verifica as permissões da página em que o usuário se encontra no momento
     * @return mixed
     */
    public function checar_permissoes_pagina_atual()
    {
        $class = $this->CI->router->fetch_class();
        $method = $this->CI->router->fetch_method();

        return $this->check_permission($method, $class);
    }

    public function is_logged($type = 'admin'){

        $name_session = 'is_logged';

        if($type == 'agente'){
            $name_session = 'agente_is_logged';
        }elseif($type == 'segurado'){
            $name_session = 'cliente_is_logged';
        }elseif($type == 'anunciante'){
            $name_session = 'anunciante_is_logged';
        }

        return  (bool) $this->CI->session->userdata($name_session);

    }

    public function check_permission($action = 'view', $resource = null, $redirect = false, $message = 'Você não tem permissão para acessar esse recurso.')
    {

        return true;
        $this->CI->load->model('usuario_acl_permissao_model', 'usuario_acl_permissao');
        if(is_null($resource))
        {
            $resource =  $this->CI->router->fetch_class();
        }

        $result =  $this->CI->usuario_acl_permissao->verify_permission($this->CI->session->userdata('usuario_acl_tipo_id'),  $action, $resource );

        if((!$result) && $redirect !== false){
            $this->CI->session->set_flashdata('fail_msg', $message);
            redirect($redirect);
        }

        return $result;
    }

    public  function is_admin(){


        if($this->is_logged('admin')){

            return (bool) $this->CI->session->userdata('usuario_id');

        }else {

            return false;

        }


    }


    public  function is_segurado(){


        if($this->is_logged('segurado')){

            return (bool) $this->CI->session->userdata('cliente_id');

        }else {

            return false;

        }


    }



}