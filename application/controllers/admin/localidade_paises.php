<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_PaÃ­ses
 *
 * @property Localidade_PaÃ­ses $current_model
 *
 */
class Localidade_Paises extends Admin_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informaÃ§Ãµes da pÃ¡gina
        $this->template->set('page_title', "Países");
        $this->template->set_breadcrumb("Países", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('localidade_pais_model', 'current_model');
        $this->load->model("localidade_continente_model", "localidade_continente");
    }
    
    public function index($offset = 0) //FunÃ§Ã£o padrÃ£o (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Carrega variÃ¡veis de informaÃ§Ã£o para a pÃ¡gina
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Países");
        $this->template->set_breadcrumb("Países", base_url("$this->controller_uri/index"));
        
        

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 20;
        $config['use_page_numbers'] = TRUE;
        $this->pagination->initialize($config);
        
        //Carrega dados para a pÃ¡gina
        $data = array();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->get_all();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );

    }
    
    public function add() //FunÃ§Ã£o que adiciona registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');



        //Setar variÃ¡veis de informaÃ§Ã£o da pÃ¡gina
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Países");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
        
        //Carrega dados da pÃ¡gina
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['continentes'] = $this->localidade_continente->get_all();
        //Caso post
        if($_POST)
        {
            //Valida formulÃ¡rio
            if($this->current_model->validate_form())
            {
                //Insere form
                $insert_id = $this->current_model->insert_form();
                if($insert_id) 
                {
                    //Caso inserido com sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
                }
                else 
                {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'NÃ£o foi possÃ­vel salvar o Registro.'); 
                }
                //Redireciona para index
                redirect("$this->controller_uri/index");
            }
        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public function edit($id) //FunÃ§Ã£o que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variÃ¡veis de informaÃ§Ã£o da pÃ¡gina
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Países");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        
        //Carrega dados para a pÃ¡gina
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        $data['continentes'] = $this->localidade_continente->get_all();
        
        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro nÃ£o exista
            $this->session->set_flashdata('fail_msg', 'NÃ£o foi possÃ­vel encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }
        
        //Caso post
        if($_POST)
        {
            if($this->current_model->validate_form()) //Valida form
            {
                //Realiza update
                $this->current_model->update_form(); 
                
                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                
                //Redireciona para index
                redirect("$this->controller_uri/index");
            }
        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }


}
