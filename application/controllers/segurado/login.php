<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property mixed template
 */
class Login extends Segurado_Controller
{
    protected $noLogin = true;

    function __construct()
    {
        parent::__construct();

        $this->load->model('cliente_model', 'cliente');
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
            'login_form_url' => base_url("segurado/login/proccess/{$parceiro}?redirect={$redirect}")
        );

        $this->template->load('segurado/layouts/login', 'segurado/login/form', $data);
    }

    public function proccess($parceiro = null)
    {
        //Carrega models necessários
        $this->load->model('cliente_model', 'cliente');
        $redirect = urldecode($this->input->get('redirect'));

        $this->load->helper('cookie');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('login', 'CPF', 'validate_cnpj_cpf|trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Senha', 'trim|required|xss_clean');


        if($this->form_validation->run() == FALSE)
        {

            $this->session->set_flashdata('loginerro', 'CPF ou Senha incorretos.');
            $redirect = urlencode($redirect);
            if($parceiro)
            {
                redirect("/segurado/parceiro/{$parceiro}?redirect={$redirect}");
            }
            else
            {
                redirect("segurado/login?redirect={$redirect}");
            }

        }else {

            //Se logar

            if($usuario_id = $this->cliente->login($this->input->post('login'), $this->input->post('password')) )
            {

                //Seta parceiros permitidos
                $parceiros_permitidos = array();

                $parceiro_id = $this->session->userdata('parceiro_id');

                $usuario = $this->cliente->get($usuario_id);

                if($parceiro_id)
                {
                    $this->input->cookie('login_parceiro_id', $parceiro_id);


                    $parceiro = $this->parceiro->get($parceiro_id);

                    //Seta sessão

                    $cookie = array(
                        'name'   => 'login_parceiro_id',
                        'value'  => $parceiro_id,
                        'expire' => 7*24*60*60
                    );
                    $this->input->set_cookie($cookie);
                }
                $parceiros = array();

                $this->session->set_userdata("parceiros", $parceiros);


                if($redirect) {
                    redirect($redirect);
                }else{
                    redirect('segurado/home');
                }

            } else {
                $this->session->set_flashdata('loginerro', 'CPF ou Senha incorretos.');
                $redirect = urlencode($redirect);
                if($parceiro) {
                    redirect("segurado/parceiro/{$parceiro}?redirect={$redirect}");
                }else{
                    redirect("segurado/login?redirect={$redirect}");
                }

            }
        }

    }

    public function lost_pass()
    {



    }
    public function logout($parceiro_id = null)
    {

        $redirect = 'segurado/login';
        if($parceiro_id){
            $row = $this->parceiro->get($parceiro_id);
            if($row){
                $redirect = "segurado/parceiro/{$row['slug']}";
            }
        }else{

        }

       $this->cliente->logout();
       redirect($redirect);

    }

}
