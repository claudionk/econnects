<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Enquete extends Admin_Controller
{

    /**
     * Construtor
     */
    public function __construct()
    {
        //Construtor
        parent::__construct();

        //Model padrão
        $model = "Enquete";
        $this->load->model("{$model}_model", 'current_model');

        //Models e Libraries
        $this->load->library('form_validation');

        //Títulos
        $this->template->set("titulo", "Enquetes");
        $this->template->set("titulo_singular", "Enquete");
        $this->template->set("titulo_adicionar", "Adicionar");
        $this->template->set("titulo_editar", "Editar");

        //Model
        $this->template->set("model_name", ucfirst($model));

        //Checa permissão
    }

    /**
     * Listagem de registros
     */
    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Bancos");
        $this->template->set_breadcrumb("Bancos", base_url("$this->controller_uri/index"));



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
     * Adiciona registro
     */
    public function add()
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        $this->template->js(app_assets_url('template/js/libs/toastr/toastr.js', 'admin'));
        $this->template->js(app_assets_url('modulos/enquete/js/add_edit.js', 'admin'));

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Enquete");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        //Carrega dados da página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '1';
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
        $this->template->load("admin/layouts/base", "$this->controller_uri/add_edit", $data );
    }

    /**
     * Editar registro
     * @param $id
     */
    public function dashboard($id)
    {
        $this->load->model("Enquete_pergunta_model", "enquete_pergunta");
        $this->load->model("Enquete_resposta_model", "enquete_resposta");
        $this->load->model("Enquete_resposta_pergunta_model", "enquete_resposta_pergunta");

        $data = array();

        $data['enquete'] = $this->current_model->get($id);

        if(!$data['enquete'])
        {
            redirect(admin_url("Enquete/index"));
        }

        $data['total'] = $this->enquete_resposta
            ->where('enquete_id', '=' , $id)
            ->getTotal();

        $data['respondidos_total'] = $this->enquete_resposta
            ->where('enquete_id', '=' , $id)
            ->where('respondido', '=', 'total')
            ->getTotal();

        $data['respondidos_parcial'] = $this->enquete_resposta
            ->where('enquete_id', '=' , $id)
            ->where('respondido', '=', 'parcial')
            ->getTotal();

        $data['respondidos_nao'] = $this->enquete_resposta
            ->where('enquete_id', '=' , $id)
            ->where('respondido', '=', 'nao')
            ->getTotal();

        $data['enquete_id'] = $id;

        $rel_pergunta_resposta = $this->enquete_resposta_pergunta
            ->relacionamento_pergunta_resposta($id, true);

        $data['relacionamento_pergunta_resposta'] = $rel_pergunta_resposta;

        $this->template->load("admin/layouts/base", "$this->controller_uri/dashboard", $data );
    }

    /**
     * Editar registro
     * @param $id
     */
    public function detalhes($id)
    {
        $this->load->model("Enquete_pergunta_model", "enquete_pergunta");
        $this->load->model("Enquete_resposta_model", "enquete_resposta");
        $this->load->model("Exp_model", "exp");
        $this->load->model("Enquete_resposta_pergunta_model", "enquete_resposta_pergunta");

        $data = array();

        $data['enquete_resposta'] = $this->enquete_resposta->get($id);

        if(!$data['enquete_resposta'])
        {
            redirect(admin_url("Enquete/index"));
        }

        $data['enquete'] = $this->current_model->get($data['enquete_resposta']['enquete_id']);

        $data['exp'] = $this->exp->get($data['enquete_resposta']['id_exp']);

        $data['enquete_resposta_pergunta'] = $this->enquete_resposta_pergunta
            ->with_foreign()
            ->get_many_by(array(
                'enquete_resposta.enquete_resposta_id' => $id
            ));

        $data['enquete_resposta_id'] = $id;

        $this->template->load("admin/layouts/base", "$this->controller_uri/detalhes", $data );
    }

    /**
     * Editar registro
     * @param $id
     */
    public function edit($id)
    {

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Enquete");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        $this->template->js(app_assets_url('template/js/libs/toastr/toastr.js', 'admin'));
        $this->template->js(app_assets_url('modulos/enquete/js/add_edit.js', 'admin'));

        $this->load->model("Enquete_pergunta_model", "enquete_pergunta");

        $this->load->library('form_validation');

        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        if(!$data['row'])
        {
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect(admin_url() . "$this->controller_url/index");
        }

        $data['perguntas'] = $this->enquete_pergunta->get_many_by(array(
            'enquete_id' => $id
        ));

        if($_POST)
        {
            if($this->current_model->validate_form())
            {
                if($this->current_model->update_form())
                {
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                }
                else
                {
                    $this->session->set_flashdata('fail_msg', 'Erro ao salvar os dados.');
                }

                redirect("$this->controller_uri/index");

            }
        }

        $this->template->load("admin/layouts/base", "$this->controller_uri/add_edit", $data );
    }

    /**
     * Deletar
     */
    public  function delete($id)
    {
        if($this->current_model->delete($id))
            $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        else
            $this->session->set_flashdata('fail_msg', 'Falha ao excluir registro.');

        redirect(admin_url("{$this->controller_url}/index"));
    }


}
