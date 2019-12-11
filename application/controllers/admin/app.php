<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Characterize_Phrases
*
* @property Produto_Parceiro_Plano_Model $current_model
*
*/
class App extends Admin_Controller{

    protected $layout = "base";
    protected $color  = 'default';
    protected $token;
    protected $getUrl = '';
    public $name;

    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Venda');
        $this->template->set_breadcrumb('Venda', base_url("$this->controller_uri/index"));

        //Carrega modelos necessários
        $this->load->model('produto_parceiro_model', 'current_model');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('cliente_model', 'cliente');

        //Seta layout
        $layout = $this->session->userdata("layout");
        $this->layout = isset($layout) && !empty($layout) ? $layout : 'base';

        if(! empty($this->input->get("token"))){
            $this->token = $this->input->get("token");
            $this->getUrl = '?token='.$this->token;
        }
        if(! empty($this->input->get("layout"))){
            $this->layout = $this->input->get("layout");
            $this->getUrl .= '&layout='.$this->layout;
        }
        if(! empty($this->input->get("color"))){
            $this->color  = $this->input->get("color");
            $this->getUrl .= '&color='.$this->color;
        }

        $this->template->js(app_assets_url("template/js/libs/cycle2/cycle2.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/cycle2/jquery.cycle2.carousel.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/bootstrap-swiper/jquery.touchSwipe.min.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/toastr/toastr.js", "admin"));
        $this->template->js(app_assets_url("core/js/app.js", "admin"));

        $this->template->css(app_assets_url("template/css/{$this->_theme}/libs/toastr/toastr.css", "admin"));
        $this->template->css(app_assets_url("template/css/{$this->_theme}/libs/wizard/wizard.css", "admin"));
        $this->template->css(app_assets_url("core/css/app.css", "admin"));

        if(! empty($this->input->get("color"))){
            $this->template->css(app_assets_url('modulos/venda/equipamento/css/'.$this->input->get("color").'.css', 'admin'));
        }

        //echo '<pre>', print_r($this->session); exit;
    }

    /**
    * Página Inicial
    */

    public function index() {
        $view = "admin/venda/equipamento/front/app/compra";
        $data['logado'] = false;
        $data['current_uri'] = $this->controller_uri;


        $this->template->load("admin/layouts/{$this->layout}", $view, $data );
    }

    public function comprar() {
        $view = "admin/venda/equipamento/front/app/comprar";
        $data['logado'] = false;
        $data['current_uri'] = $this->controller_uri;


        $this->template->load("admin/layouts/{$this->layout}", $view, $data );
    }

    public function cadastrar() {
        $view = "admin/venda/equipamento/front/app/cadastrar";
        $data['logado'] = true;
        $data['current_uri'] = $this->controller_uri;

        $this->template->load("admin/layouts/{$this->layout}", $view, $data );
    }

    public function login() {
        $view = "admin/venda/equipamento/front/app/login";
        $data['logado'] = false;
        $data['current_uri'] = $this->controller_uri;


        $this->template->load("admin/layouts/{$this->layout}", $view, $data );
    }

    public function home() {
        $view = "admin/venda/equipamento/front/app/home";
        $data['logado'] = true;
        $data['current_uri'] = $this->controller_uri;


        $this->template->load("admin/layouts/{$this->layout}", $view, $data );
    }

}
