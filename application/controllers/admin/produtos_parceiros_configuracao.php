<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Configuracao extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiros / Configurações");
        $this->template->set_breadcrumb("Produtos / Parceiros / Configurações", base_url("$this->controller_uri/edit"));

        //Carrega modelos
        $this->load->model('produto_parceiro_configuracao_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('calculo_tipo_model', 'calculo_tipo');

    }

    public function edit($produto_parceiro_id) //Função que edita registro
    {

        $this->load->model("produto_model", "produto");
        $this->load->model('canal_model', 'canal');

        $this->template->js(app_assets_url('modulos/produtos_parceiros_configuracao/base.js', 'admin'));

        $this->auth->check_permission('view', 'produto_parceiros_configuracao', 'admin/produtos_parceiros/');
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiros / Configurações");
        $this->template->set_breadcrumb('Produtos / Parceiros / Configurações', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $row = $this->current_model->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if(count($row) > 0){
            $data['row'] = $row[0];
        }else{
            $data['row'] = NULL;
        }

        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$produto_parceiro_id}");

        $produto_parceiro =  $this->produto_parceiro->get($produto_parceiro_id);

        if(!$produto_parceiro){
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");

        }

        $data['calculo_tipo']        = $this->calculo_tipo->get_all();
        $data['canais_emissao']      = $this->canal->with_produto_parceiro($produto_parceiro_id, 0)->get_all();
        $data['canais_cancelamento'] = $this->canal->with_produto_parceiro($produto_parceiro_id, 1)->get_all();

        //Verifica se registro existe
        if(!$data['row'])
        {
            $data['row'] = array();
            $data['row']['parceiro_configuracao_id'] = 0;
            $data['row']['venda_habilitada_admin'] = 0;
            $data['row']['venda_habilitada_web'] = 0;
            $data['row']['venda_carrinho_compras'] = 0;
            $data['row']['venda_multiplo_cartao'] = 0;
            $data['row']['salvar_cotacao_formulario'] = 0;
            $data['row']['apolice_sequencia'] = 0;
            $data['row']['pagamento_tipo'] = 'UNICO';
            $data['row']['pagmaneto_cobranca'] = 'DATA_COMPRA';
            $data['row']['quantidade_cobertura'] = 10;
            $data['row']['ir_cotacao_salva'] = 0;
            $data['row']['endosso_controle_cliente'] = 0;
            $data['new_record'] = '1';
        }else{
            $data['new_record'] = '0';
        }

        $data['produto_parceiro'] = $produto_parceiro;

        //Dados para gerar URL de venda Online
        $produto = $this->produto->get($produto_parceiro['produto_id']);
        $slug_produto = $produto['slug'];
        $token = $this->auth->get_venda_online_token();

        if($slug_produto == "seguro_viagem")
            $url_produto = "venda/{$slug_produto}/{$produto_parceiro_id}?token={$token}&layout=front";
        else
            $url_produto = "venda_{$slug_produto}/{$slug_produto}/{$produto_parceiro_id}?token={$token}&layout=front";

        if(!$token)
        {
            $data['url_venda_online'] = "Primeiro um usuário para venda online deve ser cadastrado.";
        }
        else
        {
            $data['url_venda_online'] = admin_url($url_produto);
        }

        //Caso post
        if($_POST)
        {

            //check_markup_relacionamento
            if($this->current_model->validate_form('geral')) //Valida form
            {

                if($this->input->post('new_record') == '1'){
                    $this->current_model->insert_config('geral');
                }else {
                    //Realiza update
                    $this->current_model->update_config('geral');
                }

                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("admin/produtos_parceiros_configuracao/edit/{$produto_parceiro['produto_parceiro_id']}");
            }
        }

        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function check_markup_relacionamento($markup)
    {

        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
        $markup = app_unformat_currency($markup);
        $this->load->library('form_validation');


        $produto_parceiro_id = $this->input->post('produto_parceiro_id');

        $soma = $this->relacionamento->get_todas_comissoes($produto_parceiro_id);

        $this->form_validation->set_message('check_markup_relacionamento', 'O Valor do campo markup deve ser inferior ou igual as somas de todas as comissões dos parceiros relacionados para esse produto. Soma total: ' . app_format_currency($soma, false, 2));

        if($soma > $markup){
            return false;
        }else{
            return true;
        }

    }

    public function check_tipo_calculo($tipo_calculo)
    {

        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
        $this->load->model('produto_parceiro_desconto_model', 'produto_parceiro_desconto');

        $produto_parceiro_id = $this->input->post('produto_parceiro_id');

        $result = TRUE;
        if($tipo_calculo != 2){
           if($this->relacionamento->is_desconto_produto_habilitado($produto_parceiro_id) === TRUE){
               $result = FALSE;
           }

            if($this->produto_parceiro_desconto->is_desconto_habilitado($produto_parceiro_id) === TRUE){
                $result = FALSE;
            }

        }

        if($result === FALSE){
            $this->form_validation->set_message('check_tipo_calculo', 'O Tipo de Cálculo selecionado não aceita desconto condiciona, favor desabilite o desconto condicional.');
        }

        return $result;

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

}
