<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class cotacao_aprovacao extends Admin_Controller
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
        $this->load->model('cotacao_seguro_viagem_model', 'seguro_viagem');
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Cotações para aprovação");
        $this->template->set_breadcrumb("Cotações", base_url("$this->controller_uri/index"));



        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model
            ->filterByStatus(4)
            ->filterPesquisa()
            ->with_clientes_contatos()
            ->with_parceiro()
            ->with_produto_parceiro()
            ->get_total();
        $config['per_page'] = 10;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->filterByStatus(4)
            ->limit($config['per_page'], $offset)
            ->filterPesquisa()
            ->with_clientes_contatos()
            ->with_parceiro()
            ->with_produto_parceiro()
            ->get_all();

       // print_r($data['rows']);exit;

        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        if($_POST){
            $sel = $this->input->post('selec_row');
            if(is_array($sel) && count($sel) > 0){
                foreach ($sel as $item) {
                    $dados_cotacao = array();
                    $dados_cotacao['cotacao_status_id'] = 1;

                    $this->current_model->update($item, $dados_cotacao, TRUE);

                    $seguro_viagem = $this->seguro_viagem->getCotacaoAprovacao($item);

                    foreach ($seguro_viagem as $sv_item) {

                        $data_seguro_viagem = array();
                        $data_seguro_viagem['desconto_cond_aprovado'] = 1;
                        $data_seguro_viagem['desconto_cond_aprovado_usuario'] = $this->session->userdata('usuario_id');;
                        $data_seguro_viagem['desconto_cond_aprovado_data'] = date('Y-m-d H:i:s');
                        $this->seguro_viagem->update($sv_item['cotacao_seguro_viagem_id'], $data_seguro_viagem, TRUE);

                    }
                }
                $this->session->set_flashdata('succ_msg', 'Cotações aprovadas com sucesso.');
                redirect("$this->controller_uri/index");
            }else{
                $this->session->set_flashdata('fail_msg', 'Nenhuma cotação foi selecionada.');
                //Redireciona para index

                redirect("$this->controller_uri/index");
            }
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/aprovacao", $data );
    }


    public function view($id)
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Detalhes da Cotação");
        $this->template->set_breadcrumb('Cotação', base_url("$this->controller_uri/view/{$id}"));


        $this->load->model('cotacao_seguro_viagem_model', 'seguro_viagem');
        $this->load->model('produto_parceiro_desconto_model', 'desconto');

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['seguro_viagem'] = $this->seguro_viagem->getCotacaoAprovacao($id);

        $data['desconto'] = $this->desconto->filter_by_produto_parceiro($data['seguro_viagem'][0]['produto_parceiro_id'])->get_all();

        if($data['desconto']){
            $data['desconto'] = $data['desconto'][0];
        }else{
            $data['desconto'] = '';
        }
       // print_r($data['seguro_viagem']);exit;
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


    public  function aprovar($id)
    {
        //Carrega models necessários
        $this->load->model('cotacao_seguro_viagem_model', 'seguro_viagem');

        //Carrega dados para a página
        $row = $this->current_model->get($id);

        //Verifica se registro existe
        if(!$row)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/aprovacao");
        }

        $dados_cotacao = array();
        $dados_cotacao['cotacao_status_id'] = 1;

        $this->current_model->update($id, $dados_cotacao, TRUE);

        $seguro_viagem = $this->seguro_viagem->getCotacaoAprovacao($id);

        foreach ($seguro_viagem as $item) {

            $data_seguro_viagem = array();
            $data_seguro_viagem['desconto_cond_aprovado'] = 1;
            $data_seguro_viagem['desconto_cond_aprovado_usuario'] = $this->session->userdata('usuario_id');;
            $data_seguro_viagem['desconto_cond_aprovado_data'] = date('Y-m-d H:i:s');
            $this->seguro_viagem->update($item['cotacao_seguro_viagem_id'], $data_seguro_viagem, TRUE);

        }

        $this->session->set_flashdata('succ_msg', 'Cotação Aprovada com sucesso.');
        redirect("{$this->controller_uri}/index");
    }

    public  function delete($id)
    {
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }


}
