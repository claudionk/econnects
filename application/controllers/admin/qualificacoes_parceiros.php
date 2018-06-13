<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Qualificacoes_Parceiros
 *
 * @property Qualificacao_Parceiro_Model $current_model
 *
 */
class Qualificacoes_Parceiros extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Parceiros");
        $this->template->set_breadcrumb("Parceiros", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('qualificacao_parceiro_model', 'current_model');
        $this->load->model('qualificacao_model', 'qualificacao');
        $this->load->model('parceiro_model', 'parceiro');
    }

    public function view($qualificacao_id, $offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Parceiros");
        $this->template->set_breadcrumb("Parceiros", base_url("$this->controller_uri/index"));

        $qualificacao = $this->qualificacao
            ->set_select()
            ->with_produto()
            ->get($qualificacao_id);

        //Verifica se registro existe
        if(!$qualificacao)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/qualificacoes/index");
        }



        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/view/{$qualificacao_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_qualificacao($qualificacao_id)->with_parceiro()->get_total();
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)
            ->filter_by_qualificacao($qualificacao_id)
            ->with_parceiro()
            ->get_all();

        $data['qualificacao_id'] = $qualificacao['qualificacao_id'];
        $data['qualificacao'] = $qualificacao;


        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($qualificacao_id) //Função que adiciona registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Parceiro");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));


        $qualificacao = $this->qualificacao
            ->set_select()
            ->with_produto()
            ->get($qualificacao_id);

        //Verifica se registro existe
        if(!$qualificacao)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/qualificacoes/index");
        }

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
                redirect("$this->controller_uri/view/{$qualificacao_id}");
            }
        }



        $data['qualificacao_id'] = $qualificacao['qualificacao_id'];
        $data['qualificacao'] = $qualificacao;

        $data['parceiros'] = $this->parceiro->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Parceiro");
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

        $qualificacao = $this->qualificacao->get($data['row']['qualificacao_id']);

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
                redirect("$this->controller_uri/view/{$qualificacao['qualificacao_id']}");
            }
        }



        $data['qualificacao_id'] = $qualificacao['qualificacao_id'];
        $data['qualificacao'] = $qualificacao;

        $data['parceiros'] = $this->parceiro->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }




    public  function delete($id)
    {
        //Deleta registro
        $qualificacao_parceiro = $this->current_model->get($id);

        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/view/{$qualificacao_parceiro['qualificacao_id']}");
    }


}
