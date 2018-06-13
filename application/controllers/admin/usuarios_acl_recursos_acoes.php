<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Usuarios_Acl_Recursos_Acoes
 *
 * @property Usuario_Acl_Recurso_Model $current_model
 *
 */
class usuarios_acl_recursos_acoes extends Admin_Controller {


    public function __construct(){

        parent::__construct();




        $this->template->set('page_title', 'Recursos');
        $this->template->set_breadcrumb('Recursos', base_url("$this->controller_uri/index"));

        $this->load->model('usuario_acl_recurso_acao_model', 'current_model');

        $this->load->model('usuario_acl_recurso_model', 'usuario_acl_recurso');
        $this->load->model('usuario_acl_acao_model', 'usuario_acl_acao');


    }




    public function edit($recurso_id)
    {
        $this->load->library('form_validation');

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar');
        $this->template->set_breadcrumb('Edit', base_url("$this->controller_uri/index"));



        $data = array();
        $recurso = $this->usuario_acl_recurso->get($recurso_id);




        if(!$recurso){

            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index");

        }

        if($_POST){

            $acoes = $this->input->post("acoes");

            if(isset($acoes) && is_array($acoes)){

                $this->current_model->insert_form();

                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                redirect("admin/usuarios_acl_recursos/index");

            }
        }



        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$recurso_id}");

        $data['acoes'] = $this->usuario_acl_acao->get_all();
        $data['recurso'] = $recurso;
        $data['usuario_acl_recurso_id'] = $recurso['usuario_acl_recurso_id'];
        $data['current_acoes'] =  $this->current_model->get_acoes_by_recurso($recurso_id);


        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }



}
