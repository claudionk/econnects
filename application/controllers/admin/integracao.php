<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Integracao extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Integrações");
        $this->template->set_breadcrumb("Integrações", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('integracao_model', 'current_model');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('integracao_comunicacao_model', 'integracao_comunicacao');
    }
    
    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Integração");
        $this->template->set_breadcrumb("Integração", base_url("$this->controller_uri/index"));
        
        

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 10;

        $this->pagination->initialize($config);
        
        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->get_all();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }
    
    public function add() //Função que adiciona registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Integração");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
        
        //Carrega dados da página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['parceiro'] =  $this->parceiro->order_by('nome')->get_all();
        $data['comunicacao'] =  $this->integracao_comunicacao->order_by('nome')->get_all();
        $data['periodicidade_unidade'] =  array('I' => 'MINUTO','H' => 'HORA','D' => 'DIA','M'=> 'MÊS', 'Y' => 'ANO', 'C' => 'CUSTOMIZADO');
        $data['tipo'] =  array('E' => 'ENTRADA','S' => 'SAÍDA','R' => 'RETORNO');
        $data['ambiente'] =  array('H' => 'HOMOLOGAÇÃO','P' => 'PRODUÇÃO');

        //Caso post
        if($_POST)
        {
            //Valida formulário
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
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.'); 
                }
                //Redireciona para index
                redirect("$this->controller_uri/index");
            }
        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Integração");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        
        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        $data['parceiro'] =  $this->parceiro->order_by('nome')->get_all();
        $data['comunicacao'] =  $this->integracao_comunicacao->order_by('nome')->get_all();
        $data['periodicidade_unidade'] =  array('I' => 'MINUTO','H' => 'HORA','D' => 'DIA','M'=> 'MÊS', 'Y' => 'ANO', 'C' => 'CUSTOMIZADO');
        $data['tipo'] =  array('E' => 'ENTRADA','S' => 'SAÍDA','R' => 'RETORNO');
        $data['ambiente'] =  array('H' => 'HOMOLOGAÇÃO','P' => 'PRODUÇÃO');

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
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


    public function copy($id){


        $this->load->model('integracao_layout_model', 'integracao_layout');

        $row = $this->current_model->get($id);


        if(!$row)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

        $new_record = $row;

        unset($new_record['integracao_id']);

        $new_record['nome'] = 'COPIA - ' .$new_record['nome'];
        $integracao_id = $this->current_model->insert($new_record, TRUE);

        $layouts = $this->integracao_layout->filter_by_integracao($row['integracao_id'])->get_all();

        foreach ($layouts as $index => $layout) {
            $new_layout = $layout;
            unset($new_layout['integracao_layout_id']);
            $new_layout['integracao_id'] = $integracao_id;
            $this->integracao_layout->insert($new_layout, TRUE);
        }

        $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

        //Redireciona para index
        redirect("$this->controller_uri/index");



    }
    
    

}
