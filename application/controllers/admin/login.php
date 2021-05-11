<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property mixed template
 */
class Login extends Admin_Controller {
    protected $noLogin = true;

    function __construct()
    {
        parent::__construct();

        $this->load->model('usuario_model', 'usuario');
        $this->load->model('parceiro_model', 'parceiro');
    }

    public function index($parceiro = null) {
        
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
            'login_form_url' => base_url("admin/login/proccess/{$parceiro}?redirect={$redirect}")
        );

        $this->template->load('admin/layouts/login', 'admin/login/form', $data);
    }

    public function proccess($parceiro = null) {
        //Carrega models necessários
        $this->load->model('colaborador_model', 'colaboradores');
        $redirect = urldecode($this->input->get('redirect'));

        $this->load->helper('cookie');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('login', 'E-mail', 'valid_email|trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Senha', 'trim|required|xss_clean');


        if($this->form_validation->run() == FALSE)
        {

            $this->session->set_flashdata('loginerro', 'E-mail ou Senha incorretos.');
            $redirect = urlencode($redirect);
            if($parceiro)
            {
                redirect("parceiro/{$parceiro}?redirect={$redirect}");
            }
            else
            {
                redirect("admin/login?redirect={$redirect}");
            }

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

                    $redirect = urlencode($redirect);

                    if ($parceiro)
                    {
                        redirect("parceiro/{$parceiro}?redirect={$redirect}");
                    }
                    else
                    {
                        redirect("admin/login?redirect={$redirect}");
                    }
                }

                
                print   ('<pre>');
                print_r ($usuario);
                print   ('</pre>');
                exit();

                // //TODO INICIO
                // exit('<br>Valida Senha<br>');

                // 
                

                // mostraLogin("Insira o c&oacute;digo enviado no e-mail <b>".$email_usuario."</b> para o usu&aacute;rio <b>".$nome_usuario."</b>", "a2f", [
                //   "id_sessao" => $_SESSION['id_session'],
                //   "token" => $tokenGerado,
                //   "success" => true
                // ]);
                // exit();
                // //TODO FINAL

                print ('<pre>');
                print('<br> Usuario 1: <br>');
                extract($usuario);
                $usuarioAuthData = array();      
                $usuarioAuthData["id_usuario"] = $usuario_id; //$this->usuario->usuario_id;      
                $usuarioAuthData["id_sessao"] =  $this->session->userdata('session_id');
                $usuarioAuthData["ip_solicitacao"] = $this->input->ip_address();
                $email_usuario = $email; //$this->$usuario->email;
                $nome_usuario = $nome; //$this->$usuario->nome;
                // var_dump ($usuarioAuthData);
                $tokenGerado = $this->solicitar_a2f($usuarioAuthData, $email_usuario, $nome_usuario);
                // print('<br> Email: <br>');
                // print($email_usuario);
                // print('<br> Nome: <br>');
                // print($nome_usuario);
                // print_r($colaborador);
                // print('<br> ****************************** Colaborador ******************************<br>');
                // var_dump($this->colaborador);
                // print('<br> ****************************** Usuario ******************************<br>');
                // var_dump($this->usuario);
                print ('</pre>');
                exit('<br>Colaborador<br>');

                if($parceiro_id) {
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

                    $parceiros_permitidos[] = $parceiro_id;

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
                }elseif($colaborador){
                    $colaborador_parceiro = $this->colaborador_parceiro->get_many_by(array('colaborador_id' => $colaborador['colaborador_id']));

                    if($colaborador_parceiro)
                    {
                        $this->session->set_userdata('parceiro_id', $colaborador_parceiro[0]['parceiro_id']);

                        $parceiros_permitidos[] = $colaborador_parceiro[0]['parceiro_id'];

                        //para cada Parceiro associado ao colaborador
                        foreach($colaborador_parceiro as $parceiro)
                        {
                            $parceiros_permitidos[] = $parceiro['parceiro_id'];

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


                if($redirect) {
                    redirect($redirect);
                }else{
                    redirect('admin/home');
                }

            }
            else
            {
                $redirect = urlencode($redirect);
                
                $falhas   = $this->usuario->get_falhas_login($this->input->post('login'));

                $msg      = ($falhas <= 2)
                                ? "Voce tem " . (3 - $falhas) . " tentativa(s) restante(s)"
                                : "Usuario bloqueado, entre em contato com o Suporte";

                $this->session->set_flashdata('loginerro', "E-mail ou Senha incorretos. <br> $msg");

                if ($parceiro) 
                {
                    redirect("parceiro/{$parceiro}?redirect={$redirect}");
                }
                else
                {
                    redirect("admin/login?redirect={$redirect}");
                }

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

    public function lost_pass()
    {



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


    public function solicitar_a2f($usuarioAuthData, $email, $nome_usuario){
        $dh_vencimento = $this->gerar_dh_vencimento_a2f();
        print('dh_vencimento: '.$dh_vencimento . '<br>');
        $tokenGerado = $this->gerar_token_a2f();
        print('tokenGerado: '.$tokenGerado . '<br>');
        $codigoGerado = $this->gerar_codigo_a2f();
        print('codigoGerado: '.$codigoGerado . '<br>');
        
        $insertUsuarioAuthSQL = "INSERT INTO usuario_auth
                                            (usuario_id_auth,
                                            usuario_id,
                                            dh_registro,
                                            dh_solicitacao,
                                            dh_vencimento,
                                            dh_confirmacao,
                                            ip_solicitacao,
                                            token,
                                            codigo,
                                            ativo,
                                            id_sessao,
                                            HTTP_USER_AGENT)
                                            VALUES
                                            (NULL,
                                            ". $usuarioAuthData["id_usuario"] . ",
                                            '".date("Y-m-d H:i:s")."',
                                            '".date("Y-m-d H:i:s")."',
                                            '$dh_vencimento',
                                            NULL,
                                            '".$usuarioAuthData["ip_solicitacao"]."',
                                            '".$tokenGerado."',
                                            '".$codigoGerado."',
                                            1,
                                            '".$usuarioAuthData["id_sessao"]."',
                                            NULL);        
                                ";

        print('<br>SQL Insert: ' . $insertUsuarioAuthSQL);
        $bodyMessage = "
            <body leftmargin=\"0\" topmargin=\"0\" onLoad=\"MM_preloadImages('imagens/drop_quemsomosb.gif','imagens/drop_knowhowb.gif','imagens/drop_qualidadeb.gif')\">
                <a href='http://econnects-h.jelastic.saveincloud.net/admin/login/index/?redirect=&token=$tokenGerado'>
                    <img width='600' height='300' src='http://econnects-h.jelastic.saveincloud.net/assets/admin/template/img/image-login.png' alt='Acesse sua conta'>
                </a>    
                <a href='http://econnects-h.jelastic.saveincloud.net/admin/login/index/?redirect=&token=$tokenGerado'>
                    <h2 style='text-align: left; color: #255A8E'><b>ACESSE SUA CONTA:</b></h2>
                </a>
                <p style='text-align: left;'>Insira o seguinte codigo de verificacao para acessar o Neo Connect</p>
                <h2 style='text-align: left; color: #255A8E'><b>$codigoGerado</b></h2>        
            </body>   
        ";
        
       // private function envia_email($mensagem, $engine)
       // /var/www/webroot/ROOT/econnects/application/libraries/Comunicacao.php
       // TODO: IMPLEMENTAR O ENVIO DO EMAIL
        print ('<br> Mensage: ');
        print ($bodyMessage);
        
        exit('<br>Fim solicitar_a2f');
/*

        $updateUsuariosAuthSQL = "UPDATE usuario_auth SET 
          dh_confirmacao = NULL, 
          dh_solicitacao = '".date("Y-m-d H:i:s")."', 
          dh_vencimento = '$dh_vencimento', 
          ip_solicitacao = '".$usuarioAuthData["ip_solicitacao"]."', 
          id_sessao = '".$usuarioAuthData["id_sessao"]."',
          token = '".$tokenGerado."', 
          codigo = '".$codigoGerado."'
        WHERE
          usuarioid = '".$usuarioAuthData["id_usuario"]."'";

        print('<br>SQL: ' . $updateUsuariosAuthSQL);

        $this->db->query($updateUsuariosAuthSQL);

        $bodyMessage = "   <body leftmargin=\"0\" topmargin=\"0\" onLoad=\"MM_preloadImages('imagens/drop_quemsomosb.gif','imagens/drop_knowhowb.gif','imagens/drop_qualidadeb.gif')\">
        <table width=\"780\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top: 30px;border-bottom: 1px solid #255A8E;margin-bottom: 5px;padding-bottom: 30px;\"><tbody><tr><td align=\"middle\" width=\"50%\"><a href=\"https://sgs-h.jelastic.saveincloud.net/\"><img src=\"https://sgs-h.jelastic.saveincloud.net/wp-content/uploads/2018/12/logo_1.png\" alt=\"SIS serviços\"></a></td><td align=\"middle\" width=\"50%\"><h3 style=\" font-size: 30px; line-height: 0.3em; color: #255A8E; font-family: Montserrat, 'Open Sans', Helvetica, Arial, sans-serif; font-weight: 400; letter-spacing: -2px;\">ACESSE SUA CONTA</h3></td></tr></tbody></table> <p style='text-align: center;'>Insira o seguinte codigo de verificacao para acessar o SGS</p> <h2 style='text-align: center; color: #255A8E'><b>$codigoGerado</b></h2>";

        $this->funcoes->disparaEmail(null, "SIS - Seguranca", "Codigo de Verificacao - ".$nome_usuario, $bodyMessage, null, null, $email);
*/
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
