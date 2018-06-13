<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class comunicacao_template
 */
class comunicacao_template extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Template de comunicação");
        $this->template->set_breadcrumb("Integrações", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('comunicacao_template_model', 'current_model');
        $this->load->model('comunicacao_tipo_model', 'comunicacao_tipos');
        $this->load->model("comunicacao_engine_configuracao_model", "comunicacao_engine_configuracao");
    }
    
    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Template de comunicação");
        $this->template->set_breadcrumb("Template de comunicação", base_url("$this->controller_uri/index"));

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

    /**
     * Adicionar
     */
    public function add()
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');
        $this->load->helper('ckeditor');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Template de comunicação");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
        
        //Carrega dados da página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['comunicacao_tipos'] = $this->comunicacao_tipos->get_all();
        $data['comunicacoes_engines_configuracoes'] = $this->comunicacao_engine_configuracao->get_all();

        //Configurações ckeditor
        $data['enable_ckeditor'] = true;
        $data['ckeditor'] = array
        (
            'id'   => 'mensagem',
            'path' => 'assets/ckeditor/',
            'config' => array
            (
                'toolbar' => "Full",
                'baseHref' => base_url(),
                'width'   => "100%",
                'height'  => "400px",
                'filebrowserBrowseUrl'      => base_url('assets/common/ckfinder/ckfinder.html'),
                'filebrowserImageBrowseUrl' => base_url('assets/common/ckfinder/ckfinder.html?Type=Images'),
                'filebrowserFlashBrowseUrl' => base_url('assets/common/ckfinder/ckfinder.html?Type=Flash'),
                'filebrowserUploadUrl'      => base_url('assets/common/ckfinder/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'),
                'filebrowserImageUploadUrl' => base_url('assets/common/ckfinder/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'),
                'filebrowserFlashUploadUrl' => base_url('assets/common/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash')
            )
        );

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

    /**
     * Editar registro
     * @param $id
     */
    public function edit($id)
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');
        $this->load->helper('ckeditor');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Template de comunicação");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));
        
        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        $data['comunicacao_tipos'] = $this->comunicacao_tipos->get_all();
        $data['comunicacoes_engines_configuracoes'] = $this->comunicacao_engine_configuracao->get_all();

        //Configurações ckeditor
        $data['enable_ckeditor'] = true;
        $data['ckeditor'] = array
        (
            'id'   => 'mensagem',
            'path' => 'assets/ckeditor/',
            'config' => array
            (
                'toolbar' => "Full",
                'baseHref' => base_url(),
                'width'   => "100%",
                'height'  => "400px",
                'filebrowserBrowseUrl'      => base_url('assets/common/ckfinder/ckfinder.html'),
                'filebrowserImageBrowseUrl' => base_url('assets/common/ckfinder/ckfinder.html?Type=Images'),
                'filebrowserFlashBrowseUrl' => base_url('assets/common/ckfinder/ckfinder.html?Type=Flash'),
                'filebrowserUploadUrl'      => base_url('assets/common/ckfinder/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'),
                'filebrowserImageUploadUrl' => base_url('assets/common/ckfinder/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'),
                'filebrowserFlashUploadUrl' => base_url('assets/common/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash')
            )
        );

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

    /**
     * @param $id
     */
    public  function delete($id)
    {
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }

    
    

}
