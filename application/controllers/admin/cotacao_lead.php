<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class cotacao_Lead extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Cotações");
        $this->template->set_breadcrumb("Cotações", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('cotacao_model', 'current_model');


    }




    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Cotações");
        $this->template->set_breadcrumb("Cotações", base_url("$this->controller_uri/index"));



        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model
            ->filterPesquisa()
            ->filterByStatus(5)
            ->with_produto_parceiro()
            ->with_lead_clientes_contatos()
            ->get_total();

        $config['per_page'] = 10;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model
            ->filterByStatus(5)
            ->with_produto_parceiro()
            ->filterPesquisa()
            ->limit($config['per_page'], $offset)
            ->with_lead_clientes_contatos()
            ->order_by('cotacao.criacao', 'DESC')
            ->get_all();


        //print_r($data['rows']);
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/lead", $data );
    }

    public function view($id)
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Detalhes do Lead");
        $this->template->set_breadcrumb('Cotação', base_url("$this->controller_uri/view/{$id}"));


        $this->load->model('cotacao_seguro_viagem_model', 'seguro_viagem');

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);

        $data['seguro_viagem'] = $this->seguro_viagem->getCotacaoLead($id);


         //print_r($data['seguro_viagem']);exit;
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/view/{$id}");

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/view", $data );
    }

    public  function delete($id)
    {
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }


}
