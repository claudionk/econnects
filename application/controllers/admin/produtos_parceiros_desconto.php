<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Desconto extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiros / Desconto");
        $this->template->set_breadcrumb("Produtos / Parceiros / Desconto", base_url("$this->controller_uri/edit"));

        //Carrega modelos
        $this->load->model('produto_parceiro_desconto_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');




    }



    public function edit($produto_parceiro_id) //Função que edita registro
    {

        $this->auth->check_permission('view', 'produto_parceiros_desconto', 'admin/produtos_desconto/');

        $this->template->js(app_assets_url('modulos/produtos_parceiros_desconto/base.js', 'admin'));
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiros / Desconto");
        $this->template->set_breadcrumb('Produtos / Parceiros / Desconto', base_url("$this->controller_uri/index"));


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
            $data['row']['habilitado'] = 0;
            $data['row']['data_ini'] = date('d/m/Y');
            $data['row']['data_fim'] = date('d/m/Y');
            $data['row']['valor'] = '000,000';
            $data['row']['utilizado'] = '000,000';
            $data['row']['descricao'] = '';
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
                redirect("admin/produtos_parceiros_desconto/edit/{$produto_parceiro['produto_parceiro_id']}");
            }
        }


        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;




        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function check_desconto_habilitado($desconto_habilitado)
    {

        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');

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
