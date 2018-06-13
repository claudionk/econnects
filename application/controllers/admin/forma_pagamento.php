<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Colaborador $current_model
 *
 */
class Forma_Pagamento extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Forma Pagamento');
        $this->template->set_breadcrumb('Forma Pagamento', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('forma_pagamento_model', 'current_model');
    }
    
    public function index($offset = 0)
    {
        //Carrega models necessários
        $this->load->model('forma_pagamento_tipo_model', 'tipo');

        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Forma Pagamento');
        $this->template->set_breadcrumb('Forma Pagamento', base_url("$this->controller_uri/index"));
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        
        //Carrega dados
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->with_forma_pagamento_tipo()->get_all();


        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add()
    {
        //Carrega models necessários
        $this->load->model('forma_pagamento_tipo_model', 'tipo');

        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Forma Pagamento');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
       

        //Carrega dados para a página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['tipo'] = $this->tipo->get_all();

        
        //Caso post
        if($_POST)
        {

            if ($this->current_model->validate_form())
            {
                $this->current_model->insert_form();
                redirect("$this->controller_uri/index");
            }

        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id)
    {
        //Carrega models necessários
        $this->load->model('forma_pagamento_tipo_model', 'tipo');
        
        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Forma Pagametno');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['row'] =  $this->current_model->get($id); //Carrega Cobertura
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        //Carrega dados do model
        $data['tipo'] = $this->tipo->get_all();

        //Caso não exista registros
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index");
        }
        //Caso post
        if($_POST)
        {
                if ($this->current_model->validate_form())
                {
                    $this->current_model->update_form();

                    $this->session->set_flashdata('succ_msg', 'Os dados cadastrais foram salvos com sucesso.');
                    redirect("$this->controller_uri/index");
                }
        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }

}
