<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Parceiros_Contatos
 *
 * @property Produto_Ramo_Model $current_model
 *
 */
class Parceiros_Cobranca extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Cobrança");
        $this->template->set_breadcrumb("Cobrança", base_url("$this->controller_uri/index"));

        //Carrega modelos

        $this->load->model('parceiro_cobranca_model', 'current_model');
        $this->load->model('parceiro_model', 'parceiro');

        $this->load->model('cobranca_tipo_model', 'cobranca_tipo');
        $this->load->model('cobranca_item_model', 'cobranca_item');
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
        $this->template->set('page_subtitle', "Parceiros Cobrança");
        $this->template->set_breadcrumb("Parceiros Cobrança", base_url("$this->controller_uri/index"));



        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->with_foreign()->filter_by_parceiro($parceiro_id)->get_total();
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->with_foreign()->filter_by_parceiro($parceiro_id)->limit($config['per_page'], $offset)->get_all();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data["parceiro_id"] = $parceiro_id;
        $data["parceiro"] = $parceiro;

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($parceiro_id) //Função que adiciona registro
    {

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Cobrança");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        $parceiro = $this->parceiro->get($parceiro_id);

        //Verifica se registro existe
        if(!$parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
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
                redirect("$this->controller_uri/view/{$parceiro_id}");
            }
        }

        $data['parceiro_id'] = $parceiro_id;
        $data["parceiro"] = $parceiro;
        $data['tipos'] = $this->cobranca_tipo->get_all();
        $data['itens'] = $this->cobranca_item->get_all();
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
        $data['row'] = $this->current_model->get($id);


        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }

        $parceiro = $this->parceiro->get($data['row']['parceiro_id']);

        //Verifica se registro existe
        if(!$parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }


        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");



        $parceiro_id = $data['row']['parceiro_id'];

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
                redirect("$this->controller_uri/view/{$parceiro_id}");
            }
        }

        $data['parceiro_id'] = $parceiro_id;
        $data["parceiro"] = $parceiro;
        $data['tipos'] = $this->cobranca_tipo->get_all();
        $data['itens'] = $this->cobranca_item->get_all();
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
