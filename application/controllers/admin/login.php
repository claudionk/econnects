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
            'login_form_url' => base_url("admin/login/proccess/{$parceiro}?redirect={$redirect}")
        );

        $this->template->load('admin/layouts/login', 'admin/login/form', $data);
    }

    public function proccess($parceiro = null)
    {
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

        }else {

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

                $parceiro_id = $this->session->userdata('parceiro_id');

                $usuario = $this->usuario->get($usuario_id);
                $colaborador = $this->colaborador->get($usuario['colaborador_id']);

                if($parceiro_id)
                {
                    $this->input->cookie('login_parceiro_id', $parceiro_id);


                    $parceiro = $this->parceiro->get($parceiro_id);

                    $this->session->set_userdata("parceiro_termo", $parceiro['termo_aceite_usuario']);

                    //Busca relacionamento
                    $parceiro_relacionamento = $this->parceiro_relacionamento
                        ->with_parceiro()
                        ->with_produto_parceiro()
                        ->get_many_by(
                        array("produto_parceiro.parceiro_id" => $parceiro_id)
                    );


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

            } else {
                $this->session->set_flashdata('loginerro', 'E-mail ou Senha incorretos.');
                $redirect = urlencode($redirect);
                if($parceiro) {
                    redirect("parceiro/{$parceiro}?redirect={$redirect}");
                }else{
                    redirect("admin/login?redirect={$redirect}");
                }

            }
        }

    }

    public function aceite_termo()
    {
        $usuario_id = $this->session->userdata('usuario_id');

        $this->usuario->update_termo($usuario_id);
        
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

}
