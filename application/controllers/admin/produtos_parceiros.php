<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * Class Produtos_Parceiros
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiro");
        $this->template->set_breadcrumb("Produtos / Parceiro", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('produto_parceiro_model', 'current_model');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('produto_model', 'produto');
        $this->load->model('produto_ramo_model', 'produto_ramo');

        $this->load->model('parceiro_tipo_model', 'parceiro_tipo');
    }

    public function view_by_parceiro($parceiro_id, $offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiro");

        $this->template->set_breadcrumb("Produtos", base_url("$this->controller_uri/index"));

        $parceiro = $this->parceiro->get($parceiro_id);

        //Verifica se registro existe
        if (!$parceiro) {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("parceiros/index");
        }

        //Inicializa tabela
        $config['base_url']    = base_url("{$this->controller_uri}/view_by_parceiro/{$parceiro_id}");
        $config['uri_segment'] = 5;
        $config['total_rows']  = $this->current_model->with_produto()->filter_by_parceiro($parceiro_id)->get_total();
        $config['per_page']    = 20;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data         = array();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)
            ->with_produto()
            ->filter_by_parceiro($parceiro_id)
            ->get_all();

        $data['parceiro_id'] = $parceiro_id;
        $data['parceiro']    = $parceiro;

        $data['primary_key']      = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        $this->template->set('page_title', "Produtos / Parceiro:");

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/view_by_parceiro", $data);
    }

    public function add_by_parceiro($parceiro_id)
    {

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        $this->template->js(app_assets_url("template/js/libs/jquery.chained/jquery.chained.min.js", "admin"));
        $this->template->js(app_assets_url('modulos/produto_parceiro/base.js', 'admin'));

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiro");
        $this->template->set_breadcrumb("Produtos / Parceiro", base_url("$this->controller_uri/index"));

        $parceiro = $this->parceiro->get($parceiro_id);

        //Verifica se registro existe
        if (!$parceiro) {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("parceiros/index");
        }

        //Caso post
        if ($_POST) {
            //Valida formulário
            if ($this->current_model->validate_form()) {
                //Insere form
                $insert_id = $this->current_model->insert_form();
                if ($insert_id) {
                    //Caso inserido com sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
                } else {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }
                //Redireciona para index
                redirect("admin/produtos_parceiros/view_by_parceiro/{$parceiro_id}");
            }
        }

        $data                = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record']  = '1';

        $data['parceiro_id'] = $parceiro_id;
        $data['parceiro']    = $parceiro;

        $data['produtos']       = $this->produto->get_all();
        $data['produtos_ramos'] = $this->produto_ramo->get_all();

        $data['seguradoras'] = $this->parceiro
            ->filter_by_tipo($this->parceiro_tipo->getIdTipoSeguradora())
            ->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data);

    }

    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiro");
        $this->template->set_breadcrumb('Produtos / Parceiro', base_url("$this->controller_uri/index"));

        $this->template->js(app_assets_url("template/js/libs/jquery.chained/jquery.chained.min.js", "admin"));
        $this->template->js(app_assets_url('modulos/produto_parceiro/base.js', 'admin'));

        //Carrega dados para a página
        $data                = array();
        $data['row']         = $this->current_model->with_produto()->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record']  = '0';
        $data['form_action'] = base_url("$this->controller_uri/edit/{$id}");

        //Verifica se registro existe
        if (!$data['row']) {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

        $parceiro = $this->parceiro->get($data['row']['parceiro_id']);

        //Caso post
        if ($_POST) {
            if ($this->current_model->validate_form()) //Valida form
            {
                //Realiza update
                $this->current_model->update_form();

                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("admin/produtos_parceiros/view_by_parceiro/{$parceiro['parceiro_id']}");
            }
        }

        $parceiro = $this->parceiro->get($data['row']['parceiro_id']);

        $data['parceiro_id']    = $parceiro['parceiro_id'];
        $data['parceiro']       = $parceiro;
        $data['produtos']       = $this->produto->get_all();
        $data['produtos_ramos'] = $this->produto_ramo->get_all();

        $data['seguradoras'] = $this->parceiro
            ->filter_by_tipo($this->parceiro_tipo->getIdTipoSeguradora())
            ->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data);
    }

    public function delete($id)
    {

        $row = $this->current_model->get($id);
        if (!$row) {
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');

        redirect("{$this->controller_uri}/view_by_parceiro/{$row['parceiro_id']}");
    }

}
