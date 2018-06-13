<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Cancelamento extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiros / Cancelamento");
        $this->template->set_breadcrumb("Produtos / Parceiros / Cancelamento", base_url("$this->controller_uri/edit"));

        //Carrega modelos
        $this->load->model('produto_parceiro_cancelamento_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');




    }



    public function edit($produto_parceiro_id) //Função que edita registro
    {

        $this->auth->check_permission('view', 'produto_parceiros_cancelamento', 'admin/produtos_parceiros/');
        $this->template->js(app_assets_url('modulos/produtos_parceiros_cancelamento/base.js', 'admin'));
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiros / Cancelamento");
        $this->template->set_breadcrumb('Produtos / Parceiros / Cancelamento', base_url("$this->controller_uri/index"));


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

        //Verifica se registro existe
        if(!$data['row'])
        {
            $data['row'] = array();
            $data['row']['calculo_tipo'] = 'T';
            $data['row']['seg_antes_hab'] = 0;
            $data['row']['seg_antes_dias'] = 0;
            $data['row']['seg_antes_valor'] = '000,000';
            $data['row']['seg_depois_hab'] = 0;
            $data['row']['seg_depois_dias'] = 0;
            $data['row']['seg_depois_valor'] = '000,000';
            $data['row']['inad_hab'] = 0;
            $data['row']['inad_max_dias'] = 0;
            $data['row']['inad_max_parcela'] = 0;
            $data['row']['inad_reativacao_hab'] = 0;
            $data['row']['inad_reativacao_max_dias'] = 0;
            $data['row']['inad_reativacao_max_parcela'] = 0;
            $data['row']['inad_reativacao_valor'] = '000,000';
            $data['row']['seg_antes_calculo'] = 'PORCENTAGEM';
            $data['row']['seg_depois_calculo'] = 'PORCENTAGEM';
            $data['row']['inad_reativacao_calculo'] = 'PORCENTAGEM';
            $data['row']['indenizacao_hab'] = 1;
            $data['new_record'] = '1';
        }else{
            $data['new_record'] = '0';
        }

        $data['produto_parceiro'] = $produto_parceiro;


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
                redirect("admin/produtos_parceiros_cancelamento/edit/{$produto_parceiro['produto_parceiro_id']}");
            }
        }


        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['parceiro_id'] = $produto_parceiro['parceiro_id'];
        $data['produto_parceiro'] = $produto_parceiro;




        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }


}
