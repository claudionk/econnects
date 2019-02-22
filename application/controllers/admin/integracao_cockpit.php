<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Cockpit $current_model
 *
 */
class Integracao_cockpit extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Integrações");
        $this->template->set_breadcrumb("Integrações", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('integracao_model', 'current_model');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('integracao_comunicacao_model', 'integracao_comunicacao');
    }
    
    // public function index($offset = 0) //Função padrão (load)
    // {
    //     // Carrega bibliotecas
    //     $this->load->library('pagination');
        
    //     // Carrega variáveis de informação para a página
    //     $this->template->set('page_title_info', '');
    //     $this->template->set('page_subtitle', "Integração");
    //     $this->template->set_breadcrumb("Integração", base_url("$this->controller_uri/index"));
        
        

    //     // Inicializa tabela
    //     $config['base_url'] = base_url("$this->controller_uri/index");
    //     $config['uri_segment'] = 4;
    //     $config['total_rows'] =  $this->current_model->get_total();
    //     $config['per_page'] = 10;

    //     $this->pagination->initialize($config);
        
    //     // Carrega dados para a página
    //     $data = array();
    //     $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->get_all();
    //     $data['primary_key'] = $this->current_model->primary_key();
    //     $data["pagination_links"] = $this->pagination->create_links();
        
    //     // Carrega template
    //     $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    // }

    public function index(){
        // Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Integração");
        $this->template->set_breadcrumb("Integração", base_url("$this->controller_uri/index"));

        $data = [];
        $data['retorno'] = [];

        // Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/index", $data );

    }

    public function cockpit(){
        // Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Integração");
        $this->template->set_breadcrumb("Integração", base_url("$this->controller_uri/index"));

        $data = [];

        // Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/index", $data );

    }

}
