<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Parceiros_Contatos
 *
 * @property Produto_Ramo_Model $current_model
 *
 */
class Parceiros_Relacionamento_Produtos extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Relacionamento / Produtos");
        $this->template->set_breadcrumb("Relacionamento / Produtos", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('parceiro_relacionamento_produto_model', 'current_model');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        $this->load->model('parceiro_tipo_model', 'parceiro_tipo');

    }

    public function index($offset = 0) //Função padrão (load)
    {

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Relacionamento / Produtos");
        $this->template->set_breadcrumb("Relacionamento / Produtos", base_url("{$this->controller_uri}/index"));

        $this->template->js(app_assets_url('template/js/libs/jquery.orgchart/jquery.orgchart.js', 'admin'));
        $this->template->js(app_assets_url('modulos/parceiro_relacionamento_produto/base.js', 'admin'));

        $this->template->css(app_assets_url("template/js/libs/jquery.orgchart/jquery.orgchart.css", "admin"));

        //relacionamentos
        $produtos = $this->produto_parceiro->get_all();

        $relacionamentos = array();
        $i = 0;
        foreach ($produtos as $produto) {
            $produto['parceiro'] = $this->parceiro->get($produto['parceiro_id']);
            $produto['lista'] =  $this->current_model->filter_by_produto_parceiro($produto['produto_parceiro_id'])->with_parceiro()->get_all();

            $produto_configuracao = $this->produto_parceiro_configuracao->filter_by_produto_parceiro($produto['produto_parceiro_id'])->get_all();

            if($produto_configuracao){
                $relacionamentos[$i]['produto_configuracao'] = $produto_configuracao[0];
            }else{
                $relacionamentos[$i]['produto_configuracao'] = array('markup' => 0);
            }

            $relacionamentos[$i]['produto'] = $produto;
            $relacionamentos[$i]['relacionamento'] = array();
            $this->current_model->getRelacionamentoProduto($produto['produto_parceiro_id'], 0, $relacionamentos[$i]['relacionamento']);
            $i++;
        }

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $relacionamentos;
        $data['primary_key'] = $this->current_model->primary_key();
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add() //Função que adiciona registro
    {

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Relacionamento");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        $this->template->js(app_assets_url("template/js/libs/jquery.chained/jquery.chained.min.js", "admin"));
        $this->template->js(app_assets_url('modulos/parceiro_relacionamento_produto/base.js', 'admin'));

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
                redirect("$this->controller_uri/index/");
            }
        }

        $data['produtos'] = $this->produto_parceiro->with_produto()->with_parceiro()->get_all();
        $data['parceiros'] = $this->parceiro->get_all();
        $data['pais'] = $this->current_model->with_produto_parceiro()->with_parceiro()->get_all();
        $data['tipos'] = $this->parceiro_tipo->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Relacionamento");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));
        $this->template->js(app_assets_url("template/js/libs/jquery.chained/jquery.chained.min.js", "admin"));
        $this->template->js(app_assets_url('modulos/parceiro_relacionamento_produto/base.js', 'admin'));

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
                redirect("$this->controller_uri/index");
            }
        }

        $data['produtos'] = $this->produto_parceiro->with_produto()->with_parceiro()->get_all();
        $data['parceiros'] = $this->parceiro->get_all();
        $data['pais'] = $this->current_model->with_produto_parceiro()->with_parceiro()->get_all();
        $data['tipos'] = $this->parceiro_tipo->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {
        //Deleta registro
        $row =  $this->current_model->get($id);

        //Verifica se registro existe
        if(!$row)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("$this->controller_uri/index");
    }

    public function check_markup_relacionamento($comissao)
    {

        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
        $comissao = app_unformat_currency($comissao);
        $this->load->library('form_validation');

        $produto_parceiro_id = $this->input->post('produto_parceiro_id');
        $parceiro_relacionamento_produto_id = $this->input->post('parceiro_relacionamento_produto_id');
        $parceiro_id = $this->input->post('parceiro_id');

        $soma = $this->relacionamento->get_todas_comissoes($produto_parceiro_id, $parceiro_relacionamento_produto_id, $parceiro_id);

        $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if($configuracao) {

            $configuracao = $configuracao[0];

            $markup = $configuracao['markup'];
            $soma = $soma + $comissao;

            $this->form_validation->set_message('check_markup_relacionamento', 'A soma de todas as comissões dos parceiros relacionados deve ser inferior ou igual ao MARKUP configurado apara esse produto. Soma total: ' . app_format_currency($soma, false, 2) . ' - MARKUP: ' . app_format_currency($markup, false, 2));

            if ($soma > $markup) {
                return false;
            } else {
                return true;
            }

        }else{
            $this->form_validation->set_message('check_markup_relacionamento', 'É necessário realizar a configuração do produto antes do relacionamento.');
            return false;
        }

    }

    public function check_repasse_maximo($repasse)
    {

        $this->load->library('form_validation');

        $this->form_validation->set_message('check_repasse_maximo', 'O Campo Repasse máximo deve ser inferior ou igual do que o campo Comissão');

        $comissao = $this->input->post('comissao');

        $repasse = app_unformat_currency($repasse);
        $comissao = app_unformat_currency($comissao);

        if($repasse > $comissao){
            return false;

        }else{
            return true;
        }

    }

    public function check_desconto_habilitado($desconto_habilitado)
    {
        $produto_parceiro_id = $this->input->post('produto_parceiro_id');
        $configuracao = $this->produto_parceiro_configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        $result = TRUE;
        if($configuracao && $desconto_habilitado == 1){
            $configuracao = $configuracao[0];
            if($configuracao['calculo_tipo_id'] != 2){
                $result = FALSE;
            }

        }

        if($result === FALSE){
            $this->form_validation->set_message('check_desconto_habilitado', 'Para que o desconto seja habilitado é necessário que o campo "Tipo de cálculo" localizado nas configurações do produto seja alteado para "CÁLCULO BRUTO"');
        }

        return $result;
    }

}
