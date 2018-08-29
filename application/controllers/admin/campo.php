<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Colaborador $current_model
 *
 */
class Campo extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Campo');
        $this->template->set_breadcrumb('Campo', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        
        $this->load->model('campo_model', 'current_model');
    }
    
    public function index($offset = 0)
    {
        //Carrega models necessários
        $this->load->model('campo_tipo_model', 'tipo');

        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Campo');
        $this->template->set_breadcrumb('Campo', base_url("$this->controller_uri/index"));
        
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
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)

            ->get_all();


        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add()
    {
        //Carrega models necessários
        $this->load->model('campo_tipo_model', 'tipo');
        $this->load->model('campo_classe_model', 'classe');

        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Campo');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        $this->template->js(app_assets_url('coral/plugins/forms_elements_select2/js/select2.js?v=v2.0.0-rc1&amp;sv=v0.0.1.2', 'admin'));
        $this->template->js(app_assets_url('modulos/campo/base.js', 'admin'));


        //Carrega dados para a página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['tipo'] = $this->tipo->get_all();
        $data['classe'] = $this->classe->get_all();

        
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
        $this->load->model('campo_tipo_model', 'tipo');
        $this->load->model('campo_classe_model', 'classe');

        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Campo');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        $this->template->js(app_assets_url('coral/plugins/forms_elements_select2/js/select2.js?v=v2.0.0-rc1&amp;sv=v0.0.1.2', 'admin'));
        $this->template->js(app_assets_url('modulos/campo/base.js', 'admin'));

        //Carrega dados para a página
        $data = array();
        $data['row'] =  $this->current_model->get($id); //Carrega Cobertura
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        //Carrega dados do model
        $data['tipo'] = $this->tipo->get_all();
        $data['classe'] = $this->classe->get_all();

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
        $this->load->model('produto_parceiro_campo_model', 'produto_parceiro_campo');
        $campoUsado = $this->produto_parceiro_campo->buscaCampoUsado($id)->get_all();
        // echo "<pre>";print_r($campoUsado);echo "</pre>";
        if (!empty($campoUsado)) {
            $this->session->set_flashdata('fail_msg', 'Existem '. sizeof($campoUsado) .' configurações que utilizam este campo e por isso não pode ser excluído.');
            redirect("{$this->controller_uri}/index");
            exit();
        }

        // validar se o campo está sendo usado em alguma configuração
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }

}
