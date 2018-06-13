<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class comunicacao_tipo
 */
class comunicacao_controller extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Comunicação");
        $this->template->set_breadcrumb("Integrações", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('comunicacao_model', 'current_model');
        $this->load->model('comunicacao_engine_model', 'comunicacao_engine');
        $this->load->model("comunicacao_tipo_model", "comunicacao_tipo");
    }
    
    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Charts");
        $this->template->set_breadcrumb("Engine de comunicação", base_url("$this->controller_uri/index"));
        
        //Carrega dados para a página
        $data = array();
        $data['comunicacao_por_dia'] = json_encode($this->current_model->countByDays());
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        $this->template->js(app_assets_url('template/js/libs/raphael/raphael-min.js', 'admin'));
        $this->template->js(app_assets_url('template/js/libs/morris.js/morris.min.js', 'admin'));
        $this->template->js(app_assets_url('modulos/comunicacao/base.js', 'admin'));


        //Carrega template
        $this->template->load("admin/layouts/base", "admin/comunicacao/charts", $data );
    }

    public function getComunicacao()
    {
        //Carrega dados para a página
        $data = array();
        $data['status'] = true;
        $data['data'] = array();
        $data['data']['comunicacao_por_dia'] = $this->current_model->countByDays();
        $data['data']['comunicacao_por_engine'] = $this->current_model->countByEngines();
        $data['data']['comunicacao_por_parceiro'] = $this->current_model->countByParceiros();

        echo json_encode($data);
    }
}
