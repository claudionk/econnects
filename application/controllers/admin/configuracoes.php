<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Usuarios_Acl_Acoes
 *
 * @property Configuracao_Model $current_model
 *
 */
class Configuracoes extends Admin_Controller {


    public function __construct(){

        parent::__construct();




        $this->template->set('page_title', 'Configurações');
        $this->template->set_breadcrumb('Configurações', base_url("$this->controller_uri/index"));

        $this->load->model('configuracao_model', 'current_model');


    }

    public function index($offset = 0)
    {
        redirect('admin/configuracoes/set');


    }



    public function set()
    {
        $this->load->library('form_validation');

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar');
        $this->template->set_breadcrumb('Edit', base_url("$this->controller_uri/index"));



        $data = array();

        if($_POST){


                $this->current_model->update_config_from_input();
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                redirect("$this->controller_uri/set");
        }

        $data['config'] = $this->current_model->get_all_options();

        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/set");


        $this->template->load("admin/layouts/base", "$this->controller_uri/set", $data );
    }
    public  function delete($id){

        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }


}
