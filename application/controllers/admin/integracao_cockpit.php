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

        $this->load->library('pagination');

        //Carrega modelos
        $this->load->model('integracao_model', 'current_model');
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


    /*
    Metódo que lista as vendas da lasa
    */
    public function lista_vendas_lasa(){
        
        $data = $this->current_model->processamento_vendas_lasa();

        $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode($data));


        /*
        $rowperpage = 5;
 
        if($rowno != 0){
          $rowno = ($rowno-1) * $rowperpage;
        }
  
        $allcount = $this->db->count_all('integracao_log');
 
        $this->db->limit($rowperpage, $rowno);
        // $users_record = $this->db->get('posts')->result_array();
        $users_record = $this->current_model->processamento_vendas_lasa();
  
        $config['base_url'] = base_url().'post/loadRecord';
        $config['use_page_numbers'] = TRUE;
        $config['total_rows'] = $allcount;
        $config['per_page'] = $rowperpage;
 
        $config['full_tag_open']    = '<div class="pagging text-center"><nav><ul class="pagination">';
        $config['full_tag_close']   = '</ul></nav></div>';
        $config['num_tag_open']     = '<li class="page-item"><span class="page-link">';
        $config['num_tag_close']    = '</span></li>';
        $config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close']    = '<span class="sr-only">(current)</span></span></li>';

 
        $config['next_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['next_tag_close']  = '<span aria-hidden="true"></span></span></li>';
        $config['prev_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['prev_tag_close']  = '</span></li>';
        $config['first_tag_open']   = '<li class="page-item"><span class="page-link">';
        $config['first_tag_close'] = '</span></li>';
        $config['last_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['last_tag_close']  = '</span></li>';
 
        $this->pagination->initialize($config);
 
        $data['pagination'] = $this->pagination->create_links();
        $data['result'] = $users_record;
        $data['row'] = $rowno;
 
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($data));
        */
    }

}
