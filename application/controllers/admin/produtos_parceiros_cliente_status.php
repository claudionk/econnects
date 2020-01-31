<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros_Planos
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Cliente_Status extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiro / Status");
        $this->template->set_breadcrumb("Produtos / Parceiro / Status", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('produto_parceiro_cliente_status_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('cliente_evolucao_status_model', 'cliente_evolucao_status');

    }

    public function index($produto_parceiro_id , $offset = 0)
    {

        $this->auth->check_permission('view', 'produto_parceiro_cliente_status', 'admin/parceiros/');

        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Status");
        $this->template->set_breadcrumb("Status", base_url("$this->controller_uri/view_by_produto_parceiro/{$produto_parceiro_id}"));

        $produto_parceiro = $this->produto_parceiro->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index/{$produto_parceiro_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_produto_parceiro($produto_parceiro_id)->get_total();
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();

        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;

        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($produto_parceiro_id){

        //Adicionar Bibliotecas
        $this->load->library('form_validation');


        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Status");
        $this->template->set_breadcrumb("Status", base_url("$this->controller_uri/index"));


        $produto_parceiro =  $this->produto_parceiro->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }

        //Caso post
        if($_POST)
        {
            //Se criar novo status, insere e guarda id
            if( !empty($_POST['descricao']) )
            {

                if($this->cliente_evolucao_status->validate_form()) //Valida form
                {
                    $cliente_evolucao_status_id = $this->cliente_evolucao_status->insert_form();
                    $_POST['cliente_evolucao_status_id'] = $cliente_evolucao_status_id;
                }
            }

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
                redirect("{$this->controller_uri}/index/{$produto_parceiro_id}");
            }
        }

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '1';
        $data['evolucao_status'] = $this->cliente_evolucao_status->filter_by_uso_interno(0)->get_all();
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );

    }

    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Status");
        $this->template->set_breadcrumb('Status', base_url("$this->controller_uri/index"));


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

        $produto_parceiro =  $this->produto_parceiro->get($data['row']['produto_parceiro_id']);

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
                redirect("{$this->controller_uri}/index/{$produto_parceiro['produto_parceiro_id']}");
            }
        }

        $data['produto_parceiro_id'] = $produto_parceiro['produto_parceiro_id'];
        $data['produto_parceiro'] = $produto_parceiro;
        $data['evolucao_status'] = $this->cliente_evolucao_status->filter_by_uso_interno(0)->get_all();;

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public  function delete($id)
    {
        $row = $this->current_model->get($id);
        if(!$row){
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');

        redirect("{$this->controller_uri}/index/{$row['produto_parceiro_id']}");
    }


}
