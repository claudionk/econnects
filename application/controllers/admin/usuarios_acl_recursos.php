<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases usuarios_acl_recursos
 *
 * @property Usuario_Acl_Recurso_Model $current_model
 *
 */
class Usuarios_Acl_Recursos extends Admin_Controller {


    public function __construct(){

        parent::__construct();




        $this->template->set('page_title', 'Recursos');
        $this->template->set_breadcrumb('Recursos', base_url("$this->controller_uri/index"));

        $this->load->model('usuario_acl_recurso_model', 'current_model');


    }


    public function set_ordem()
    {
        if(isset($_POST['itens']))
        {
            $i = 1;
            foreach ($_POST['itens'] as $item)
            {
                $data_ordem = array();
                $data_ordem['ordem'] = $i;
                $this->current_model->update($item[0], $data_ordem, TRUE);
                $i++;
            }
            $this->session->set_flashdata('succ_msg', 'A ordem foi salva corretamente.');
        }
        else
        {
            $this->session->set_flashdata('fail_msg', 'Não possuem registros para salvar a ordem.');
            exit('0');
        }
        exit('1');
    }


	public function index()
	{


        $pai_id = (int)$this->input->post('pai_id');

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'List');

        $this->template->js(app_assets_url('core/js/jquery.tablednd.js', 'admin'));
        $this->template->js(app_assets_url('modulos/usuarios_acl_recursos/base.js', 'admin'));


        $this->template->set_breadcrumb('List', base_url("$this->controller_uri/index"));


        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();



        $data['rows'] = $this->current_model->filter_by_pai($pai_id)->order_by('ordem')->get_all();

        $arrSelectPai = array();
        $this->current_model->getRecursosSelect(0, 0, $arrSelectPai);
        $data['list_pai'] =  $arrSelectPai;
        $data['pai_id'] =  $pai_id;

/*
        $arrSelectPai = array();
        $this->current_model->getRecursosUsuario(0, 0, $arrSelectPai);
        print_r($arrSelectPai);exit; */

        $arrMenu = array();
        $user = $this->session->all_userdata();
        app_montar_menu($user['recursos'],  $arrMenu);

        //exit(implode("\n", $arrMenu));
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
	}

    public function add()
    {

        $this->load->library('form_validation');

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Recurso');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        if($_POST){

            if($this->current_model->validate_form()){

                $insert_id = $this->current_model->insert_form();

                if($insert_id){

                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                }else {

                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }

                redirect("$this->controller_uri/index");


            }
        }

        $data = array();

        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $arrSelectPai = array();
        $this->current_model->getRecursosSelect(0, 0, $arrSelectPai);
        $data['list_pai'] =  $arrSelectPai;

        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id)
    {
        $this->load->library('form_validation');

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar');
        $this->template->set_breadcrumb('Edit', base_url("$this->controller_uri/index"));



        $data = array();
        $data['row'] = $this->current_model->get($id);



        if(!$data['row']){

            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index");

        }

        if($_POST){

            if($this->current_model->validate_form()){

                $this->current_model->update_form();

                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                redirect("$this->controller_uri/index");

            }
        }



        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        $arrSelectPai = array();
        $this->current_model->getRecursosSelect(0, 0, $arrSelectPai);
        $data['list_pai'] =  $arrSelectPai;

        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id){

        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }


}
