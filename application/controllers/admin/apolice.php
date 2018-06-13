<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Apolice extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Apólices");
        $this->template->set_breadcrumb("Apólices", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('apolice_model', 'current_model');
    }
    
    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Apólices");
        $this->template->set_breadcrumb("Apólices", base_url("$this->controller_uri/index"));

        

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 10;

        $this->pagination->initialize($config);
        
        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->getApoliceAll($config['per_page'], $offset); //limit()->get_all();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function certificado($apolice_id, $export = '' ){
        $result = $this->current_model->certificado($apolice_id, $export);
        if($result !== FALSE){
            exit($result);
        }
    }


}
