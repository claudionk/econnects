<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property mixed template
 */
class Login extends Admin_Controller 
{
    protected $noLogin = true;

    function __construct()
    {
        parent::__construct();

        $this->load->model('usuario_model', 'usuario');
        $this->load->model('parceiro_model', 'parceiro');
    }

    public function index($parceiro = null) 
    {
        $redirect = urlencode($this->input->get('redirect'));

        if($parceiro)
        {
            $row = $this->parceiro->get_by(
                array('slug' => $parceiro)
            );
            if($row)
            {
                $this->_setTheme($row['parceiro_id']);
            }
        }
        $data = array(
            'login_form_url'    => base_url("admin/login/proccess/{$parceiro}?redirect={$redirect}"),
            'esqueceu_form_url' => base_url("admin/login/esqueci_senha"),
        );

        $this->template->load('admin/layouts/login', 'admin/login/form', $data);
    }

    public function proccess($parceiro = null) 
    {
        //Carrega models necessários
        $this->load->model('colaborador_model', 'colaboradores');
        $redirect = urldecode($this->input->get('redirect'));
        $redirect = urlencode($redirect);

        $this->load->helper('cookie');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('login', 'E-mail', 'valid_email|trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Senha', 'trim|required|xss_clean');

        if($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('loginerro', 'E-mail ou Senha incorretos.');

            if($parceiro)
                redirect("parceiro/{$parceiro}?redirect={$redirect}");
            else
                redirect("admin/login?redirect={$redirect}");
        }
        else 
        {
            //processa os dados de login
            $this->load->model("Parceiro_relacionamento_produto_model", 'parceiro_relacionamento');
            $this->load->model("Parceiro_model", 'parceiro');
            $this->load->model("Colaborador_parceiro_model", 'colaborador_parceiro');
            $this->load->model("Colaborador_model", 'colaborador');

            //Se logar
            if($usuario_id = $this->usuario->login($this->input->post('login'), $this->input->post('password')) )
            {
                //Seta parceiros permitidos
                $parceiros_permitidos = array();
                $parceiro_id          = $this->session->userdata('parceiro_id');
                $usuario              = $this->usuario->get($usuario_id);
                $colaborador          = $this->colaborador->get($usuario['colaborador_id']);
                
                if ($usuario['bloqueado'])
                {
                    $this->session->set_flashdata('loginerro', 'Usuario bloqueado.');

                    if ($parceiro)
                        redirect("parceiro/{$parceiro}?redirect={$redirect}");
                    else
                        redirect("admin/login?redirect={$redirect}");
                }

                if($parceiro_id) 
                {
                    $this->input->cookie('login_parceiro_id', $parceiro_id);

                    $parceiro = $this->parceiro->get($parceiro_id);

                    $this->session->set_userdata("parceiro_termo", $parceiro["termo_aceite_usuario"]);
                    $this->session->set_userdata("parceiro_pai_id", $parceiro["parceiro_pai_id"]);

                    //Busca relacionamento
                    $parceiro_relacionamento = $this->parceiro_relacionamento
                                                    ->with_parceiro()
                                                    ->with_produto_parceiro()
                                                    ->get_many_by(
                                                        array("produto_parceiro.parceiro_id" => $parceiro_id)
                                                    );
                  
                    $parceiro_relacionamento = $this->db->query( "SELECT * FROM parceiro WHERE parceiro_pai_id=$parceiro_id AND deletado=0" )->result_array();
                    $parceiros_permitidos[]  = $parceiro_id;

                    foreach($parceiro_relacionamento as $parceiro_filho)
                    {
                        $parceiros_permitidos[] = $parceiro_filho['parceiro_id'];
                    }

                    //Seta sessão
                    $this->session->set_userdata("parceiros_permitidos", $parceiros_permitidos);

                    $cookie = array(
                        'name'   => 'login_parceiro_id',
                        'value'  => $parceiro_id,
                        'expire' => 7*24*60*60
                    );
                    $this->input->set_cookie($cookie);
                }
                elseif($colaborador)
                {
                    $colaborador_parceiro = $this->colaborador_parceiro->get_many_by(array('colaborador_id' => $colaborador['colaborador_id']));

                    if($colaborador_parceiro)
                    {
                        $this->session->set_userdata('parceiro_id', $colaborador_parceiro[0]['parceiro_id']);

                        $parceiros_permitidos[] = $colaborador_parceiro[0]['parceiro_id'];

                        //para cada Parceiro associado ao colaborador
                        foreach($colaborador_parceiro as $parceiro)
                        {
                            $parceiros_permitidos[]  = $parceiro['parceiro_id'];

                            //Busca relacionamento
                            $parceiro_relacionamento = $this->parceiro_relacionamento
                                                            ->with_parceiro()
                                                            ->with_produto_parceiro()
                                                            ->get_many_by(
                                                                array("produto_parceiro.parceiro_id" => $parceiro['parceiro_id'])
                                                            );

                            foreach($parceiro_relacionamento as $parceiro_filho)
                            {
                                $parceiros_permitidos[] = $parceiro_filho['parceiro_id'];
                            }
                        }

                        if(is_array($parceiros_permitidos))
                        {
                            $this->session->set_userdata("parceiro_selecionado", $parceiros_permitidos[0]);
                            $this->session->set_userdata("parceiros_permitidos", $parceiros_permitidos);
                        }

                        $cookie = array(
                            'name'   => 'login_parceiro_id',
                            'value'  => $parceiro_id,
                            'expire' => 7*24*60*60
                        );

                        $this->input->set_cookie($cookie);
                        $this->session->set_userdata("is_colaborador", true);
                    }
                }

                $parceiros = array();
                foreach($parceiros_permitidos as $parceiro_id)
                {
                    $parceiro = $this->parceiro->get($parceiro_id);

                    if(!in_array($parceiro, $parceiros))
                        $parceiros[] = $parceiro;
                }

                $this->session->set_userdata("parceiros", $parceiros);

                if ($redirect) 
                    redirect($redirect);
                else
                    redirect('admin/home');
            } 
            else 
            {
                $falhas = $this->usuario->get_falhas_login($this->input->post('login'));
                $msg    = "E-mail ou Senha incorretos. <br>";

                if (!$falhas['bloqueado'])
                    $msg .= "Voce tem " . (3 - $falhas['falhas']) . " tentativa(s) restante(s)";
                else
                    $msg = "Usuario bloqueado, entre em contato com o Suporte";

                $this->session->set_flashdata('loginerro', $msg);

                if ($parceiro)
                    redirect("parceiro/{$parceiro}?redirect={$redirect}");
                else
                    redirect("admin/login?redirect={$redirect}");

            }
        }
    }

    public function aceite_termo() {

        $login      = $this->session->userdata("email");
        $usuario_id = $this->session->userdata('usuario_id');

        $ajax       = $this->input->post("ajax");
        $senhaAtual = $this->input->post("senha_atual");
        $senhaNova  = $this->input->post("senha_nova");
        
        if ($ajax) {

            $output = array();

            try {
                
                $usuario = $this->usuario->find_login($login, $senhaAtual);

                if (empty($usuario)) {
                    throw new Exception("Esta não é a sua senha");                
                }
    
                if ($senhaAtual == $senhaNova) {
                    throw new Exception("A nova senha não pode ser igual a antiga");
                }

                $output["status"]   = $this->usuario->update_termo($usuario_id, $senhaNova, $ajax);        
                $output["message"]  = "Termo aceito com sucesso";
                
            } catch (Exception $ex) {

                $output["status"]   = false;
                $output["message"]  = $ex->getMessage();

            }            

            echo json_encode($output);

        } else {

            $this->usuario->update_termo($usuario_id);        

        }
        
    }

    public function logout($parceiro_id = null)
    {

        $redirect = 'admin/login';
        if($parceiro_id){
            $row = $this->parceiro->get($parceiro_id);
            if($row){
                $redirect = "parceiro/{$row['slug']}";
            }
        }else{

        }

       $this->usuario->logout();
       redirect($redirect);

    }

    public function solicitar_a2f($usuarioAuthData, $email, $nome_usuario)
    {
        $dh_vencimento = $this->gerar_dh_vencimento_a2f();
        $tokenGerado   = $this->gerar_token_a2f();
        $codigoGerado  = $this->gerar_codigo_a2f();
        
        $now  = date("Y-m-d H:i:s");
        $data = [
            'usuario_id'      => $usuarioAuthData["id_usuario"],
            'dh_registro'     => $now,
            'dh_solicitacao'  => $now,
            'dh_vencimento'   => $dh_vencimento,
            'dh_confirmacao'  => NULL,
            'ip_solicitacao'  => $usuarioAuthData["ip_solicitacao"],
            'token'           => $tokenGerado,
            'codigo'          => $codigoGerado,
            'ativo'           => 1,
            'id_sessao'       => $usuarioAuthData["id_sessao"],
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
        ];

        $this->db->where("usuario_auth.usuario_id", $data['usuario_id']);
        $this->db->update("usuario_auth", $data); 

        $bodyMessage = "
            <body leftmargin=\"0\" topmargin=\"0\" onLoad=\"MM_preloadImages('imagens/drop_quemsomosb.gif','imagens/drop_knowhowb.gif','imagens/drop_qualidadeb.gif')\">
            <table width=\"780\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top: 30px;border-bottom: 1px solid #255A8E;margin-bottom: 5px;padding-bottom: 15px;\">
                <tbody>
                    <tr>
                        <td align=\"middle\" width=\"50%\">
                            <a href=\"https://sgs-h.jelastic.saveincloud.net/\">
                                <img width='300' height='150' src='http://econnects-h.jelastic.saveincloud.net/assets/admin/template/img/image-login.png' alt='Acesse sua conta'>    
                            </a>
                        </td>
                        <td align=\"middle\" width=\"50%\">
                            <h3 style=\"margin:0; font-size: 30px; line-height: 0.3em; color: #255A8E; font-family: Montserrat, 'Open Sans', Helvetica, Arial, sans-serif; font-weight: 400; letter-spacing: -2px;\">
                                ACESSE SUA CONTA
                            </h3>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style='text-align: center;'>
                Insira o seguinte codigo de verificacao para acessar o Neo Connect
            </p>
            <h2 style='text-align: center; color: #255A8E'>
                <b>$codigoGerado</b>
            </h2>
        ";
        
        // private function envia_email($mensagem, $engine)
        // /var/www/webroot/ROOT/econnects/application/libraries/Comunicacao.php
        // TODO: IMPLEMENTAR O ENVIO DO EMAIL

        // $this->funcoes->disparaEmail(null, "SIS - Seguranca", "Codigo de Verificacao - ".$nome_usuario, $bodyMessage, null, null, $email);

        // $this->load->library('email');
        // $this->email->from('your@example.com', 'Your Name');
        // $this->email->to($email);
        // $this->email->cc('another@another-example.com');
        // $this->email->bcc('them@their-example.com');
        // $this->email->subject('Email Test');
        // $this->email->message($bodyMessage);
        // $this->email->send();

        return $tokenGerado;
    }


    public static function gerar_token_a2f(){
        return self::gerar_aleatorio(12);
    }

    public static function gerar_codigo_a2f(){
        return self::gerar_aleatorio(8);
    }

    public static function gerar_dh_vencimento_a2f($dh_solicitacao = null){
        $dh_format = "Y-m-d H:i:s";
        $horas = 6;

        if($dh_solicitacao == null){
            $dh_solicitacao = date($dh_format);
        }
        
        $oDhVencimento = date_add(date_create($dh_solicitacao), date_interval_create_from_date_string($horas.' hours'));
        $dh_vencimento = date_format($oDhVencimento, $dh_format);
        return $dh_vencimento;
    }    

    public static function gerar_aleatorio($length){        
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;        
    }


    public function esqueci_senha($parceiro = null)
    {
        $redirect = urlencode($this->input->get('redirect'));

        if ($parceiro)
        {
            $row = $this->parceiro->get_by(
                array('slug' => $parceiro)
            );

            if ($row)
            {
                $this->_setTheme($row['parceiro_id']);
            }
        }

        $data = array(
            'envia_codigo_url' => base_url("admin/login/envia_codigo/{$parceiro}?redirect={$redirect}"),
        );

        $this->template->load('admin/layouts/esqueci_senha', 'admin/login/form', $data);
    }

    public function envia_codigo($parceiro = null)
    {
        $email = $this->input->post('email');

		$this->db->select('usuario.*');
        $this->db->from('usuario');
        $this->db->where('usuario.email', $email);

        $query = $this->db->get();
        
        if ($query->num_rows() == 1)
        {
            $usuario = $query->result_array()[0];

            extract($usuario);

            if ($bloqueado)
            {
                $this->session->set_flashdata('loginerro', 'Usuario bloqueado.');

                $redirect = urlencode($redirect);

                if ($parceiro)
                    redirect("parceiro/{$parceiro}?redirect={$redirect}");
                else
                    redirect("admin/login?redirect={$redirect}");
            }

            $authData                   = array();      
            $authData["id_usuario"]     = $usuario_id; //$this->usuario->usuario_id;      
            $authData["id_sessao"]      = $this->session->userdata('session_id');
            $authData["ip_solicitacao"] = $this->input->ip_address();

            $tokenGerado = $this->solicitar_a2f($authData, $email, $nome);
            $redirect    = urlencode($redirect);

            $this->session->set_flashdata('token', $tokenGerado);

            redirect("admin/login/valida_token/?redirect={$redirect}");
        }
        else
        {
            $this->session->set_flashdata('email_erro', 'E-mail não encontrado');

            redirect("admin/login/esqueci_senha");
        }
    }


    public function valida_token($parceiro = null) 
    {
        $redirect = urlencode($this->input->get('redirect'));

        if ($parceiro)
        {
            $row = $this->parceiro->get_by(
                array('slug' => $parceiro)
            );

            if ($row)
            {
                $this->_setTheme($row['parceiro_id']);
            }
        }

        $data = [
            'valida_token_url' => base_url("admin/login/proccess_token/{$parceiro}?redirect={$redirect}")
        ];

        $this->template->load('admin/layouts/valida_token', 'admin/login/form', $data);
    }

    public function proccess_token($parceiro = null) 
    {
        $redirect   = urldecode($this->input->get('redirect'));
        $code       = $this->input->post('code');
        $token      = $this->session->flashdata('token');
        $session_id = $this->session->userdata('session_id');
        $ip         = $this->input->ip_address();

		$this->db->select('usuario_auth.*');
        $this->db->from('usuario_auth');
        $this->db->where('usuario_auth.codigo',         $code);
        $this->db->where('usuario_auth.token',          $token);
        $this->db->where('usuario_auth.id_sessao',      $session_id);
        $this->db->where('usuario_auth.ip_solicitacao', $ip);

        $query = $this->db->get();
        
        if ($query->num_rows() == 1)
        {
            $result = $query->result_array()[0];
            extract($result);
            
            $now = date("Y-m-d H:i:s");
            
            if ($now > $dh_vencimento)
            {
                $redirect = urlencode($redirect);
    
                $this->session->set_flashdata('token_erro', 'Codigo expirado!');
    
                if ($parceiro)
                    redirect("parceiro/{$parceiro}?redirect={$redirect}");
                else
                    redirect("admin/login/valida_token/?redirect={$redirect}");
            }

			$this->db->where('usuario_auth.usuario_id', $usuario_id);
			$this->db->update('usuario_auth', [
				'dh_confirmacao' => $now
			]);

            redirect("admin/login/reseta_senha/?redirect={$redirect}");
        }
        else
		{
            $redirect = urlencode($redirect);

            $this->session->set_flashdata('token_erro', 'Codigo invalido!');

            if ($parceiro)
            {
                redirect("parceiro/{$parceiro}?redirect={$redirect}");
            }
            else
            {
                redirect("admin/login/valida_token/?redirect={$redirect}");
            }
        }
    }
    

    public function reseta_senha($parceiro = null)
    {
        $redirect = urlencode($this->input->get('redirect'));

        if ($parceiro)
        {
            $row = $this->parceiro->get_by(
                array('slug' => $parceiro)
            );

            if ($row)
            {
                $this->_setTheme($row['parceiro_id']);
            }
        }

        $data = array(
            'reseta_senha_url' => base_url("admin/login/proccess_senha/{$parceiro}?redirect={$redirect}"),
        );

        $this->template->load('admin/layouts/reseta_senha', 'admin/login/form', $data);
    }

    public function proccess_senha($parceiro = null)
    {
        $session_id = $this->session->userdata('session_id');
        $ip         = $this->input->ip_address();

        print_r ('<pre>');
        print_r ($this->input->post('password'));
        print_r ('<br>');
        print_r ($this->input->post('confirm_pass'));
        print_r ('<br>');
        print_r ($session_id);
        print_r ('<br>');
        print_r ($ip);
        print_r ('<br>');
        print_r ('</pre>');

        exit();
    }

    // function ipCheck() {
    //     if (getenv('HTTP_X_FORWARDED_FOR')) {
    //         $ip = getenv('HTTP_X_FORWARDED_FOR');
    //     }
    //     elseif (getenv('HTTP_X_REAL_IP')) {
    //         $ip = getenv('HTTP_X_REAL_IP');
    //     }
    //     else {
    //         $ip = $_SERVER['REMOTE_ADDR'];
    //     }
    //     return $ip;
    // }


    /*

    public function confirmar_a2f($usuarioAuthData){
        $updateValidateUsuariosAuthSQL = "UPDATE
            sis_usuarios_auth 
        SET
            dh_confirmacao = '".date("Y-m-d H:i:s")."'
        WHERE 1 = 1
            AND token = '".$usuarioAuthData["token"]."'
            AND codigo = '".$usuarioAuthData["codigo"]."'
            AND id_usuario = '".$usuarioAuthData["id_usuario"]."'
            AND id_sessao = '".$usuarioAuthData["id_sessao"]."'
            AND ip_solicitacao = '".$usuarioAuthData["ip_solicitacao"]."'";               
        $this->db->query($updateValidateUsuariosAuthSQL);        
        return $this->db->affected_rows > 0;
    }





    public static function valdiarEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }    
    */
}
