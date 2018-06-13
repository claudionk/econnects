<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Contato $current_model
 *
 */
class Integracao_Log extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Logs');
        $this->template->set_breadcrumb('logs', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('integracao_log_model', 'current_model');
        $this->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');
        $this->load->model('integracao_model', 'integracao');
    }
    
    public function index($integracao_id = 0, $offset = 0)
    {

        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Logs');
        $this->template->set_breadcrumb('Logs', base_url("$this->controller_uri/index"));
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index/{$integracao_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_integracao($integracao_id)->get_total();
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        
        //Seta session para botão voltar

        //Carrega dados

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model->with_foreign()->filter_by_integracao($integracao_id)->limit($config['per_page'], $offset)->order_by('criacao', 'DESC')->get_all();
        $data['integracao_id'] = $integracao_id;
        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function view($integracao_id = 0, $integracao_log_id = 0, $offset = 0)
    {
        //Carrega JS necessário

        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Detalhes Log');
        $this->template->set_breadcrumb('Detalhes', base_url("$this->controller_uri/index"));

        //Carrega bibliotecas
        $this->load->library('pagination');

        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/view/{$integracao_id}/{$integracao_log_id}");
        $config['uri_segment'] = 6;
        $config['total_rows'] =  $this->integracao_log_detalhe->filterByLogID($integracao_log_id)->get_total();
        $config['per_page'] = 10;
        $this->pagination->initialize($config);

        //Seta session para botão voltar

        //Carrega dados

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->integracao_log_detalhe->with_foreign()->filterByLogID($integracao_log_id)->limit($config['per_page'], $offset)->get_all();
        $data['integracao_id'] = $integracao_id;
        //Carrega dados do template

        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/view", $data );
    }
    public  function delete($id)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index/".$this->session->userdata('cliente_id'));
    }

}
