<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Parceiros_Contatos
 *
 * @property Produto_Ramo_Model $current_model
 *
 */
class Parceiros_Contatos extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Contatos");
        $this->template->set_breadcrumb("Contatos", base_url("$this->controller_uri/index"));

        //Carrega modelos

        $this->load->model('parceiro_contato_model', 'current_model');
        $this->load->model('parceiro_model', 'parceiro');

        $this->load->model('parceiro_contato_cargo_model', 'parceiro_contato_cargo');
        $this->load->model('parceiro_contato_departamento_model', 'parceiro_contato_departamento');
        $this->load->model('contato_tipo_model', 'contato_tipo');
        $this->load->model('contato_model', 'contato');
    }

    public function view($parceiro_id , $offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');

        $parceiro = $this->parceiro->get($parceiro_id);

        //Verifica se registro existe
        if(!$parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Parceiros Contatos");
        $this->template->set_breadcrumb("Parceiros Contatos", base_url("$this->controller_uri/index"));



        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_parceiro($parceiro_id)->get_total();
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->with_contato()->with_departamento_cargo()->filter_by_parceiro($parceiro_id)->limit($config['per_page'], $offset)->get_all();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data["parceiro_id"] = $parceiro_id;


        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($parceiro_id) //Função que adiciona registro
    {

        $this->template->js(app_assets_url('modulos/contato/base.js', 'admin'));
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Ramo");
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
                $insert_id = $this->current_model->insert_contato($_POST);
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
                redirect("$this->controller_uri/view/{$parceiro_id}");
            }
        }

        $data['parceiro_id'] = $parceiro_id;
        $data['cargos'] = $this->parceiro_contato_cargo->get_all();
        $data['departamentos'] = $this->parceiro_contato_departamento->get_all();
        $data['contato_tipo'] = $this->contato_tipo->get_all();
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public function edit($id) //Função que edita registro
    {

        $this->template->js(app_assets_url('modulos/contato/base.js', 'admin'));
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Ramo");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();

        //Carrega dados para a página
        $data = array();
        $row = $this->current_model->get($id);

        $contato = $this->contato->get($row['contato_id']);

        $data['row'] = array_merge($contato, $row);

        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }

        $parceiro_id = $data['row']['parceiro_id'];

        //Caso post
        if($_POST)
        {
            if($this->current_model->validate_form()) //Valida form
            {

                $dados_update = $_POST;
                $dados_update['contato_id'] = $data['row']['contato_id'];
                //Realiza update
                $this->current_model->update_contato($dados_update);

                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("$this->controller_uri/view/{$parceiro_id}");
            }
        }

        $data['cargos'] = $this->parceiro_contato_cargo->get_all();
        $data['departamentos'] = $this->parceiro_contato_departamento->get_all();
        $data['parceiro_id'] = $parceiro_id;
        $data['contato_tipo'] = $this->contato_tipo->get_all();
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {

        //Carrega dados para a página
        $data = array();
        $row = $this->current_model->get($id);
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/view/{$row['parceiro_id']}");
    }


}
