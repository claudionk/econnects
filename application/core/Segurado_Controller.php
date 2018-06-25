<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Segurado_Controller extends MY_Controller
{
    protected $noLogin = false;
    protected $_theme = 'theme-1';
    protected $_theme_logo = '';
    protected $_theme_nome = 'Connects Insurance';
    protected $layout = "base";

    protected $controller_name;
    protected $controller_uri;

    // const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
    // const FORMA_PAGAMENTO_FATURADO = 3;
    // const FORMA_PAGAMENTO_CARTAO_DEBITO = 6;
    // const FORMA_PAGAMENTO_BOLETO = 5;

    const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
    const FORMA_PAGAMENTO_FATURADO = 9;
    const FORMA_PAGAMENTO_CARTAO_DEBITO = 7;
    const FORMA_PAGAMENTO_BOLETO = 8;
    const FORMA_PAGAMENTO_TRANSF_BRADESCO = 5;
    const FORMA_PAGAMENTO_TRANSF_BB = 6;

    function __construct()
    {
        parent::__construct();

        $this->load->model("cliente_model", "cliente");

        $this->output->set_header('Expires: Sat, 01 Jan 2000 00:00:01 GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0, max-age=0');
        $this->output->set_header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        $this->output->set_header('Pragma: no-cache');

        $this->_theme_logo = app_assets_url('template/img/logo-connects.png', 'segurado');
        $this->controller_name = strtolower(get_class($this) );
        $this->controller_uri = "segurado/{$this->controller_name}";

        $userdata = $this->session->all_userdata();

        if(isset($userdata['parceiro_id']))
        {
            $this->_setTheme($userdata['parceiro_id']);
        }

        $this->template->set('theme', $this->_theme);
        $this->template->set('theme_logo', $this->_theme_logo);
        $this->template->set('title', $this->_theme_nome);
        $this->template->set_breadcrumb('Home', base_url('segurado/home'));
        $this->template->set('current_controller_name', $this->controller_name );
        $this->template->set('current_controller_uri', $this->controller_uri);
        $this->template->set('userdata', $userdata);

        //Seta layout
        $layout = ($this->session->userdata("layout")) ? $this->session->userdata("layout") : 'base';
        if($this->input->get('layout'))
        {
            $this->session->set_userdata("layout", $this->input->get('layout'));
            $layout = $this->input->get('layout');
        }
        //Seta layout
        if($this->input->get('context'))
        {
            $this->session->set_userdata("context", $this->input->get('context'));
        }

        $this->template->set('context', $this->session->userdata("context"));

        $this->template->set('layout', $layout);
        $urls_pode_acessar = $this->session->userdata("urls_pode_acessar");



        if(($this->router->fetch_class() !== 'login') && (!$this->auth->is_segurado())  &&  ($this->noLogin === false) ){
            $url_redirect = '';
            $this->load->library('user_agent');
            if ($this->agent->is_referral()){
               $url_redirect = urlencode($this->agent->referrer());
            }

            $redirect = "segurado/login/index/?redirect={$url_redirect}";
            $this->load->helper('cookie');
            $login_parceiro_id = get_cookie('login_parceiro_id');
/*
            if($login_parceiro_id){
                $this->load->model('parceiro_model', 'parceiro');
                $parceiro = $this->parceiro->get($login_parceiro_id);
                if($parceiro) {
                    $redirect = "parceiro/{$parceiro['slug']}?redirect={$url_redirect}";
                }
            }
*/

            redirect($redirect);
        }else if ($this->router->fetch_class() !== 'login'){
/*
           if(isset($userdata['termo_aceite']) && $userdata['termo_aceite'] == 0)
           {
               $this->template->js(app_assets_url('core/js/termo.js', 'segurado'));
           }
           if(isset($userdata['termo_aceite']) && $userdata['termo_aceite'] == 0){
               $this->template->js(app_assets_url('core/js/termo.js', 'segurado'));
           }
           */

           if(($urls_pode_acessar) && (!empty($urls_pode_acessar)) && (is_array($urls_pode_acessar)) &&  ($this->noLogin === false)){
                $pode_acessar = false;
                foreach($urls_pode_acessar as $url)
                {
                    $url_relativa = explode("#", $url);
                    $url_relativa = $url_relativa[0];

                    if(strpos(current_url(), $url_relativa) !== false)
                    {

                        $pode_acessar = true;
                    }
                }

                if(!$pode_acessar && !in_array(current_url(), $urls_pode_acessar))
                {
                    echo "Você não possui autorização para ver esta página.";
                    exit;
                }
            }
/*
            if(isset($userdata['termo_aceite']) && $userdata['termo_aceite'] == 0)
            {
                $this->template->js(app_assets_url('core/js/termo.js', 'admin'));
            }*/
        }



    }

    public function _setTheme($parceiro_id){

        $this->load->model('parceiro_model', 'parceiro');
        $parceiro = $this->parceiro->get($parceiro_id);
        $this->_theme = (!empty($parceiro['theme'])) ? $parceiro['theme'] : 'theme-1';
        $this->_theme_logo =  (!empty($parceiro['logo'])) ? app_assets_url("upload/parceiros/{$parceiro['logo']}", 'admin')  :  app_assets_url('template/img/logo-connects.png', 'admin');
        $this->_theme_nome =  (!empty($parceiro['apelido'])) ? $parceiro['apelido']  :  $this->_theme_nome;
        $this->template->set('theme', $this->_theme);
        $this->template->set('theme_logo', $this->_theme_logo);
        $this->template->set('title', $this->_theme_nome);
    }


}
