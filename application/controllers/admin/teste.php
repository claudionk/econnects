<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Teste extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Tipo de Cobrança");
        $this->template->set_breadcrumb("Tipo de Cobrança", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('log_evento_model', 'current_model');
    }

    /**
     * @property ZGrid $zgrid
     */


    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('ZGrid');


        if($_POST){
            $this->current_model->build_action_grid();
        }

        $this->template->js(app_assets_url("zGrid/js/jquery.dataTables.min.js", "admin"));
        $this->template->js(app_assets_url("zGrid/js/dataTables.bootstrap.min.js", "admin"));
        $this->template->js(app_assets_url("zGrid/js/dataTables.select.min.js", "admin"));
        $this->template->js(app_assets_url("zGrid/js/dataTables.checkboxes.min.js", "admin"));
        $this->template->js(app_assets_url("zGrid/js/dataTables.fixedHeader.min.js", "admin"));
        $this->template->js(app_assets_url("zGrid/js/base.js", "admin"));

        $this->template->css(app_assets_url("zGrid/css/dataTables.bootstrap.min.css", "admin"));
        $this->template->css(app_assets_url("zGrid/css/select.bootstrap.min.css", "admin"));
        $this->template->css(app_assets_url("zGrid/css/dataTables.checkboxes.css", "admin"));

        $config_grid = array(
            'id' => 'zGrid',
            'key' => 'log_evento_id',
            'action_view' => FALSE,
            'action_edit' => TRUE,
            'action_delete' => TRUE,
            'checkbox' => TRUE,
            'pagination' => TRUE,
            'pages' => [2 => '2',200 => '200', 5000 => '5000', -1 => 'Tudo' ],
        );

        $grid = $this->zgrid->grid_config($config_grid);

        $grid = $this->zgrid
                    ->add_grid_column(array('name' => 'log_evento_id', 'label' => 'ID'))
                    ->add_grid_column(array('name' => 'model', 'label' => 'Model'))
                    ->add_grid_column(array('name' => 'controller', 'label' => 'Controller'))
                    ->add_grid_column(array('name' => 'acao', 'label' => 'Ação'))
                    ->add_grid_column(array('name' => 'ip', 'label' => 'IP'))
                    ->add_grid_order([2 => 'asc'])
                    ->render();



        //exit($grid);
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Log de Eventos");
        $this->template->set_breadcrumb("Tipo de Cobrança", base_url("$this->controller_uri/index"));

        

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");

        //Carrega dados para a página
        $data = array('render' => $grid);

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }


    public function datadb(){

        $this->load->library('zGrid');

        $config_grid = array(
            'id' => 'zGrid',
            'key' => 'log_evento_id',
            'action_view' => FALSE,
            'pagination' => TRUE,
        );

        $this->zgrid->grid_config($config_grid)->add_grid_action('Exportar', 'Exportar', '', 'btn-sm btn-primary', array(), "<i class=\"fa  fa-plus\" aria-hidden=\"true\"> Exportar </i>" );

        $this->zgrid
            ->select('log_evento_id, model, controller, acao, ip, tipo_evento')
            ->from('log_evento');
        exit($this->zgrid->generate());
    }

    public function add() //Função que adiciona registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Tipo de Cobrança");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
        
        //Carrega dados da página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        
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
        $this->template->set('page_subtitle', "Editar Tipo de Cobrança");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        
        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        
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


}
