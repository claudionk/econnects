<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Log_Eventos
 *
 * @property Log_Evento_Model $current_model
 *
 */
class Log_Eventos extends Admin_Controller {


    public function __construct(){

        parent::__construct();




        $this->template->set('page_title', 'Log de Eventos');
        $this->template->set_breadcrumb('Log de Eventos', base_url("$this->controller_uri/index"));

        $this->load->model('log_evento_model', 'current_model');


    }



    public function index($offset = 0)
    {



        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'List');



        $this->template->set_breadcrumb('List', base_url("$this->controller_uri/index"));

        $this->load->library('pagination');
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 10;
        $this->pagination->initialize($config);


        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();



        $data['rows'] = $this->current_model->limit($config['per_page'], $offset )->get_all();



        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }



    public function view($id)
    {
        $this->load->library('form_validation');

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar');
        $this->template->set_breadcrumb('Edit', base_url("$this->controller_uri/index"));



        $data = array();
        $data['row'] = $this->current_model->with_usuario()->with_colaborador()->set_select()->get($id);



        if(!$data['row']){

            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index");

        }



        $this->template->css(app_assets_url('modulos/log_eventos/css/base.css',  'admin'));
        $this->template->js(app_assets_url('modulos/log_eventos/js/base.js',  'admin'));




        $this->template->load("admin/layouts/base", "$this->controller_uri/view", $data );
    }
    public  function delete($id){

        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }


}
