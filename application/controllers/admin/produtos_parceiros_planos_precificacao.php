<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Planos_Precificacao extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiros / Planos / Precificação");
        $this->template->set_breadcrumb("Produtos / Parceiros / Planos / Precificação", base_url("$this->controller_uri/edit"));


        //Carrega modelos
        $this->load->model('produto_parceiro_plano_precificacao_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('precificacao_tipo_model', 'precificacao_tipo');
        $this->load->model('comissao_tipo_model', 'comissao_tipo');
        




    }



    public function edit($produto_parceiro_plano_id) //Função que edita registro
    {

        $this->auth->check_permission('view', 'produto_parceiro_plano_precificacao', 'admin/produtos_parceiros_planos/');
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiros / Planos / Precificação");
        $this->template->set_breadcrumb('Produtos / Parceiros / Planos / Precificação', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();
        $row = $this->current_model->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)->get_all();

        if(count($row) > 0){
            $data['row'] = $row[0];
        }else{
            $data['row'] = NULL;
        }

        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$produto_parceiro_plano_id}");


        $produto_parceiro_plano =  $this->produto_parceiro_plano->get($produto_parceiro_plano_id);

        if(!$produto_parceiro_plano){
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");

        }
        $produto_parceiro =  $this->produto_parceiro->get($produto_parceiro_plano['produto_parceiro_id']);

        //Verifica se registro existe
        if(!$data['row'])
        {
            $data['row'] = array();
            $data['row']['parceiro_configuracao_id'] = 0;
            $data['row']['venda_habilitada_admin'] = 0;
            $data['row']['venda_habilitada_web'] = 0;
            $data['row']['venda_carrinho_compras'] = 0;
            $data['row']['venda_multiplo_cartao'] = 0;
            $data['new_record'] = '1';
        }else{
            $data['new_record'] = '0';
        }

        $data['produto_parceiro_plano'] = $produto_parceiro_plano;


        //Caso post
        if($_POST)
        {
            if($this->current_model->validate_form()) //Valida form
            {

                if($this->input->post('new_record') == '1'){
                    $this->current_model->insert_form();
                }else {
                    //Realiza update
                    $this->current_model->update_form();
                }



                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("admin/produtos_parceiros_planos/view_by_produto_parceiro/{$produto_parceiro['produto_parceiro_id']}");
            }
        }


        $data['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
        $data['produto_parceiro'] = $produto_parceiro;
        $data['precificacao_tipo'] = $this->precificacao_tipo->get($produto_parceiro_plano['precificacao_tipo_id']);





        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }


}

