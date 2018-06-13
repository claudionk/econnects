<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Colaborador $current_model
 *
 */
class Comissao_item extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Itens Comissão');
        $this->template->set_breadcrumb('Itens Comissão', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('comissao_item_model', 'current_model');

        $this->load->model('comissao_model', 'comissao');


    }
    
    public function index($comissao_id = 0,  $offset = 0)
    {
        $comissao = $this->comissao->get($comissao_id);

        $this->load->library('form_validation');

        //Verifica se registro existe
        if(!$comissao)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/comissao/index");
        }

        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Comissão');
        $this->template->set_breadcrumb('Comissão', base_url("$this->controller_uri/index/{$comissao_id}"));
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index/{$comissao_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_comissao($comissao_id)->get_total();
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        
        //Carrega dados
        $data = array();
        $data['comissao'] = $comissao;
        $data['comissao_id'] = $comissao_id;
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->filter_by_comissao($comissao_id)->get_all();


        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($comissao_id = 0)
    {

        $comissao = $this->comissao->get($comissao_id);

        $this->load->library('form_validation');

        //Verifica se registro existe
        if(!$comissao)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/comissao/index");
        }

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Comissão');
        $this->template->set_breadcrumb('Comissão', base_url("$this->controller_uri/index/{$comissao_id}"));
       

        //Carrega dados para a página
        $data = array();

        $data['comissao_id'] = $comissao_id;
        $data['comissao'] = $comissao;
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/add");

        //Caso post
        if($_POST)
        {

            if ($this->current_model->validate_form())
            {
                $this->current_model->insert_form();
                redirect("$this->controller_uri/index/{$comissao_id}");
            }

        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($comissao_id = 0, $id)
    {
        $comissao = $this->comissao->get($comissao_id);

        $this->load->library('form_validation');

        //Verifica se registro existe
        if(!$comissao)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/comissao/index");
        }

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Comissão');
        $this->template->set_breadcrumb('Comissão', base_url("$this->controller_uri/index/{$comissao_id}"));


        //Carrega dados para a página
        $data = array();

        $data['comissao_id'] = $comissao_id;
        $data['comissao'] = $comissao;

        $data['row'] =  $this->current_model->get($id); //Carrega Cobertura
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");


        //Caso não exista registros
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index/{$comissao_id}");
        }
        //Caso post
        if($_POST)
        {
                if ($this->current_model->validate_form())
                {
                    $this->current_model->update_form();

                    $this->session->set_flashdata('succ_msg', 'Os dados cadastrais foram salvos com sucesso.');
                    redirect("$this->controller_uri/index/{$comissao_id}");
                }
        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($comissao_id, $id)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("$this->controller_uri/index/{$comissao_id}");
    }


 }
