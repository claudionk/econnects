<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Qualificacoes_Questoes
 *
 * @property Qualificacao_Questao_Model $current_model
 *
 */
class Qualificacoes_Questoes extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Questões");
        $this->template->set_breadcrumb("Questões", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('qualificacao_questao_model', 'current_model');
        $this->load->model('qualificacao_categoria_model', 'qualificacao_categoria');
        $this->load->model('qualificacao_criterio_model', 'qualificacao_criterio');
        $this->load->model('qualificacao_model', 'qualificacao');

        $this->load->model('qualificacao_regua_model', 'qualificacao_regua');
    }

    public function view($qualificacao_id, $offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Questões");
        $this->template->set_breadcrumb("Questões", base_url("$this->controller_uri/index"));



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
        $config['total_rows'] =  $this->current_model
                                        ->filter_by_qualificacao($qualificacao_id)
                                        ->filterFromInput()
                                        ->get_total();
        $config['per_page'] = 10;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)
            ->filter_by_qualificacao($qualificacao_id)
            ->filterFromInput()
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
        $this->template->set('page_subtitle', "Adicionar Questão");
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

        $data['criterios'] = $this->qualificacao_criterio->get_all();
        $data['categorias'] = $this->qualificacao_categoria->get_all();

        $data['reguas'] = $this->qualificacao_regua->get_regua_logica_options();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Questão");
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

        $data['criterios'] = $this->qualificacao_criterio->get_all();
        $data['categorias'] = $this->qualificacao_categoria->get_all();

        $data['reguas'] = $this->qualificacao_regua->get_regua_logica_options();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {
        //Deleta registro

        $qualificacao_questao = $this->current_model->get($id);
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("$this->controller_uri/view/{$qualificacao_questao['qualificacao_id']}");
    }


}
