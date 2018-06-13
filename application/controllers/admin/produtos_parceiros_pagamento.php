<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Pagamento extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produto / Parceiro / Forma Pagamento");
        $this->template->set_breadcrumb("Produto / Produtos / Parceiro", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('produto_parceiro_pagamento_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('forma_pagamento_model', 'forma_pagamento');


    }



    public function index($produto_parceiro_id , $offset = 0) //Função padrão (load)
    {



        $this->auth->check_permission('view', 'produto_parceiro_pagamento', 'admin/produto_parceiros/');

        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "produto / Parceiro / Forma Pagamento");


        $this->template->set_breadcrumb("Produto / Parceiro / Forma Pagamento", base_url("$this->controller_uri/index"));


        $produto_parceiro = $this->produto_parceiro->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("parceiros/index");
        }


        //Inicializa tabela
        $config['base_url'] = base_url("{$this->controller_uri}/index/{$produto_parceiro_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->with_forma_pagamento()->filter_by_produto_parceiro($produto_parceiro_id)->get_total();
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)
            ->with_forma_pagamento()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;


        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        $this->template->set('page_title', "Produto / Parceiro / Forma Pagamento");

        //Carrega template

        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($produto_parceiro_id){

        //Adicionar Bibliotecas
        $this->load->library('form_validation');


        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produto / Parceiro / Forma Pagamento");
        $this->template->set_breadcrumb("Produto / Parceiro / Forma Pagamento", base_url("$this->controller_uri/index"));

        $this->template->js(app_assets_url('modulos/produtos_parceiros_pagamento/base.js', 'admin'));


        $produto_parceiro = $this->produto_parceiro->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("parceiros/index");
        }

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
                redirect("admin/produtos_parceiros_pagamento/index/{$produto_parceiro_id}");
            }
        }



        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();

        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;

        $data['forma_pagamento'] = $this->forma_pagamento->get_all();


        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );

    }

    public function edit($produto_parceiro_id, $id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Parceiro");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        $this->template->js(app_assets_url('modulos/produtos_parceiros_pagamento/base.js', 'admin'));

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/$produto_parceiro_id/{$id}");

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
                redirect("admin/produtos_parceiros_pagamento/index/{$produto_parceiro_id}");
            }
        }

        $produto_parceiro =  $this->produto_parceiro->get($produto_parceiro_id);

        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;
        $data['forma_pagamento'] = $this->forma_pagamento->get_all();

        //Carrega template
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
