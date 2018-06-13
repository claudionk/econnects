<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Contato $current_model
 *
 */
class Clientes_Contatos extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Contato');
        $this->template->set_breadcrumb('Contato', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('cliente_contato_model', 'current_model');
    }
    
    public function index($offset = 0)
    {
        //Carrega models necessários
        $this->load->model('cliente_model', 'cliente');
        
        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Contatos');
        $this->template->set_breadcrumb('Contatos', base_url("$this->controller_uri/index"));
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->filter_by_cliente($offset)->get_total();
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        
        //Seta session para botão voltar
        $this->session->set_userdata('cliente_id', $offset);
        
        //Carrega dados

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model->with_contato()->get_by_cliente($offset);
        $data['cliente_id'] = $offset;
        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($cliente_id = 0)
    {
        $this->template->js(app_assets_url('modulos/contato/base.js', 'admin'));

        //Carrega models necessários
        $this->load->model('cliente_contato_cargo_model', 'cargo');
        $this->load->model('cliente_contato_nivel_relacionamento_model', 'nivel_relacionamento');
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_contato_departamento_model', 'cliente_contato_departamento');
        $this->load->model('contato_tipo_model', 'contato_tipo');
        $this->load->model('contato_model', 'contato');

        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Contato');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
        
        //Caso post
        if($_POST)
        {
            //Valida formulário
            if($this->current_model->validate_form())
            {
                //Tenta inserir form
                $data = $_POST;
                $data['cliente_id'] = $cliente_id;

                $insert_id = $this->current_model->insert_contato($data);

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
                redirect("$this->controller_uri/index/{$cliente_id}");
            }
        }

        //Carrega dados para a página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['cliente_id'] = $cliente_id;
		
		        
        //Carrega dados de outros models
        $data['cargos'] = $this->cargo->get_all();
        $data['departamentos'] = $this->cliente_contato_departamento->get_all();
        $data['nivel_relacionamento'] = $this->nivel_relacionamento->get_all();
        $cliente = $this->cliente->get_by_id($data['cliente_id']);
        $data['tipo_cliente'] = $cliente['tipo_cliente'];
        $data['contato_tipo'] = $this->contato_tipo->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id)
    {
        //Carrega JS necessário
        $this->template->js(app_assets_url('modulos/contato/base.js', 'admin'));

        //Carrega models necessários
        $this->load->model('cliente_contato_cargo_model', 'cargo');
        $this->load->model('cliente_contato_departamento_model', 'cliente_contato_departamento');
        $this->load->model('cliente_contato_nivel_relacionamento_model', 'nivel_relacionamento');
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('contato_tipo_model', 'contato_tipo');
        $this->load->model('contato_model', 'contato');
        
        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Contato');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $row = $this->current_model->get($id);

        $contato = $this->contato->get($row['contato_id']);

        $data['row'] = array_merge($contato, $row);


        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        
        //Carrega dados de outros models
        $data['cargos'] = $this->cargo->get_all();
        $data['departamentos'] = $this->cliente_contato_departamento->get_all();
        $data['nivel_relacionamento'] = $this->nivel_relacionamento->get_all();
        $data['cliente_id'] = $this->session->userdata('cliente_id');
        $data['contato_id'] = $data['row']['contato_id'];
        $cliente = $this->cliente->get_by_id($data['cliente_id']);
        $data['tipo_cliente'] = $cliente['tipo_cliente'];
        $data['contato_tipo'] = $this->contato_tipo->get_all();
        
        
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
                $dados_update = $_POST;
                $dados_update['cliente_id'] = $data['row']['cliente_id'];
                $dados_update['contato_id'] = $data['row']['contato_id'];

                $this->current_model->update_contato($dados_update);
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                redirect("$this->controller_uri/index/" . $this->session->userdata('cliente_id'));
            }
        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index/".$this->session->userdata('cliente_id'));
    }

}
