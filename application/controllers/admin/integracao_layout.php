<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Contato $current_model
 *
 */
class Integracao_layout extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Integração Layout');
        $this->template->set_breadcrumb('Integração Layout', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('integracao_layout_model', 'current_model');
        $this->load->model('integracao_model', 'integracao');
    }
    
    public function index($integracao_id)
    {

        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Integração Layout');
        $this->template->set_breadcrumb('Integração Layout', base_url("$this->controller_uri/index"));
        


        //Carrega dados

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['rows'] = $this->current_model
                        ->filterFromInput()
                        ->filter_by_integracao($integracao_id)
                        ->get_all();
        $data['integracao_id'] = $integracao_id;
        $data['tipo'] =  array('H' => 'HEADER','D' => 'DETAIL','T' => 'Trailler');
        $data['campo_tipo'] =  array('C' => 'CARACTER' ,'N' => 'NUMERICO','D' => 'DATA');

        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($integracao_id = 0)
    {   


        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Layout');
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
                redirect("$this->controller_uri/index/{$integracao_id}");
            }
        }

        //Carrega dados para a página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '1';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['integracao_id'] = $integracao_id;
		
		        
        //Carrega dados de outros models
        $data['tipo'] =  array('H' => 'HEADER','D' => 'DETAIL','T' => 'Trailler');
        $data['campo_tipo'] =  array('C' => 'CARACTER' ,'N' => 'NUMERICO','D' => 'DATA');

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id)
    {


        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Contato');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        $integracao_id =  $data['row']['integracao_id'];
        $data['integracao_id'] = $integracao_id;


        //Carrega dados de outros models
        $data['tipo'] =  array('H' => 'HEADER','D' => 'DETAIL','T' => 'Trailler');
        $data['campo_tipo'] =  array('C' => 'CARACTER' ,'N' => 'NUMERICO','D' => 'DATA');
        
        //Caso não exista registros
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index/".$this->session->userdata('cliente_id'));
        }

        //Caso post
        if($_POST)
        {
            //Valida formulário
            if($this->current_model->validate_form())
            {
                //Realiza update
                $this->current_model->update_form();
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                redirect("$this->controller_uri/index/{$integracao_id}");
            }
        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {

        $row = $this->current_model->get($id);
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index/{$row['integracao_id']}");
    }

}
