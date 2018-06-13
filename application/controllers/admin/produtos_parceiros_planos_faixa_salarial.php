<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_parceiros_planos_origem_destino
 */
class Produtos_parceiros_planos_faixa_salarial extends Admin_Controller
{
    /**
     * Produtos_parceiros_planos_origem_destino constructor.
     */
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Planos - Faixa Salarial");
        $this->template->set_breadcrumb("Planos - Faixa Salarial", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('faixa_salarial_model', 'faixa_salarial');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('produto_parceiro_plano_faixa_salarial_model', 'produto_parceiro_plano_faixa_salarial');
    }

    /**
     * Edita
     * @param $produto_parceiro_plano_id
     */
    public function edit($produto_parceiro_plano_id)
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        $this->template->js(app_assets_url("template/js/libs/multi-select/jquery.multi-select.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/jquery.quicksearch/jquery.quicksearch.js", "admin"));
        $this->template->js(app_assets_url("modulos/produtos_parceiros_planos_faixa_salarial/js/base.js", "admin"));
        $this->template->css(app_assets_url("template/css/{$this->_theme}/libs/multi-select/multi-select.css", "admin"));
        //$this->template->js(app_assets_url("coral/components/forms_elements_multiselect/multiselect.init.js?v=v2.0.0-rc1&amp;sv=v0.0.1.2", "admin"));

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Parametrização");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();

        $produto_parceiro_plano = $this->produto_parceiro_plano->get($produto_parceiro_plano_id);

        $data['faixa_salarial'] = $this->faixa_salarial->order_by('ordem')->get_all();
        $data['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
        $data['row_faixa_salarial'] = $this->produto_parceiro_plano_faixa_salarial->get_many_by(
            array('produto_parceiro_plano_id' => $produto_parceiro_plano_id)
        );

        $data['row'] = $this->produto_parceiro_plano->get($produto_parceiro_plano_id);
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$produto_parceiro_plano_id}");
        
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
            $rows_fs = $this->input->post("faixa_salarial");


            $atualizaFaixaSalarial = $this->produto_parceiro_plano_faixa_salarial->atualiza_faixa_salarial($rows_fs, $produto_parceiro_plano_id);


            if($atualizaFaixaSalarial)
            {
                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
            }
            else
            {
                //Mensagem de sucesso
                $this->session->set_flashdata('fail_msg', 'Não foi possível salvar os dados.');
            }


            //Redireciona para index
            //produtos_parceiros_planos/view_by_produto_parceiro/44
            redirect("admin/produtos_parceiros_planos/view_by_produto_parceiro/{$produto_parceiro_plano['produto_parceiro_id']}");

        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

}
