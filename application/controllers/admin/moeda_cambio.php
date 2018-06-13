<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Contato $current_model
 *
 */
class Moeda_Cambio extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Contato');
        $this->template->set_breadcrumb('Contato', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('moeda_cambio_model', 'current_model');
    }
    
    public function index($moeda_id,   $offset = 0)
    {
        //Carrega models necessários
        $this->load->model('cliente_model', 'cliente');
        
        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Câmbio');
        $this->template->set_breadcrumb('Câmbio', base_url("$this->controller_uri/index"));
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index/{$moeda_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_moeda($moeda_id)->get_total();
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        

        //Carrega dados

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model->filter_by_moeda($moeda_id)->order_by('data_cambio', 'DESC')->limit($config['per_page'], $offset)->get_all();
        $data['moeda_id'] = $moeda_id;
        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($moeda_id)
    {   

        //Carrega models necessários
        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Cãmbio');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
        
        //Caso post
        if($_POST)
        {
            //Valida formulário
            if($this->current_model->validate_form())
            {
                //Tenta inserir form
                $insert_id = $this->current_model->insert_form();

                //Caso inserção ocorra com sucesso
                if($insert_id)
                {
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                }
                else 
                {
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }

                //Redireciona para index
                redirect("$this->controller_uri/index/$moeda_id");
            }
        }

        //Carrega dados para a página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['moeda_id'] = $moeda_id;
		

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id)
    {

        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Câmbio');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");



        
        //Caso não exista registros
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/moeda/index/");
        }


        $data['moeda_id'] =  $data['row']['moeda_id'];
        //Caso post
        if($_POST)
        {
            //Valida formulário
            if($this->current_model->validate_form())
            {
                //Realiza update
                $this->current_model->update_form();
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                redirect("$this->controller_uri/index/" . $data['row']['moeda_id']);
            }
        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {

        $row = $this->current_model->get($id);

        if(!$row)
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/moeda/index/");
        }
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index/{$row['moeda_id']}");
    }

}
