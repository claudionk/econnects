<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Cidades
 *
 * @property Localidade_Cidades $current_model
 *
 */
class Localidade_Cidades extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Cidades");
        $this->template->set_breadcrumb("Cidades", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('localidade_cidade_model', 'current_model');
        $this->load->model('localidade_estado_model', 'estados');
    }
    
    public function buscaCidadesPorEstado()
    {   
        //Passa parametros por JSON
        $retorno = array(
            'status' => false,
            'message' => 'Não foi possível resgatar os valores!'
        );
        //Resgata todas as cidades do estado selecionado
        $data = $this->current_model->getCidadesPorEstado($this->input->post("idEstado"));
        
        //Caso retorne dados
        if($data)
        {
            $retorno['status'] = true;
            $retorno['rows'] = $data;
        }
        //Retorna JSON à tela
        echo json_encode($retorno);
    }
    
    public function index($offset =0)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Cidades");
        $this->template->set_breadcrumb("Cidades", base_url("$this->controller_uri/index"));

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->get_all();
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }
    
    public function add()
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Cidade");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        //Caso post
        if($_POST)
        {
            //Valida formulário
            if($this->current_model->validate_form())
            {
                //Insere dados
                $insert_id = $this->current_model->insert_form();
                
                //Caso consiga inserir os dados
                if($insert_id) 
                {
                    //Mensagem de sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                }
                else 
                {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }
                //Redireciona para index
                redirect("$this->controller_uri/index");
            }
        }
        
        //Carrega dados da página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['estados'] = $this->estados->get_all();
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id)
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Cidade");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));
        
        //Caso post
        if($_POST)
        {
            //Valida form
            if($this->current_model->validate_form()) 
            {
                //Realiza update
                $this->current_model->update_form(); 
                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                //Redireciona para index
                redirect("$this->controller_uri/index");
            }
        }
        
        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        $data['estados'] = $this->estados->get_all(); //Resgata todas os estados
        
        //Caso não retorne dados
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
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
