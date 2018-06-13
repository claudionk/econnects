<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Qualificacoes_Parceiros_Respostas
 *
 * @property Qualificacao_Parceiro_Resposta_Model $current_model
 *
 */
class Qualificacoes_Parceiros_Respostas extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Questões");
        $this->template->set_breadcrumb("Questões", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('qualificacao_parceiro_resposta_model', 'current_model');
        $this->load->model('qualificacao_parceiro_model', 'qualificacao_parceiro');
        $this->load->model('qualificacao_model', 'qualificacao');
        $this->load->model('parceiro_model', 'parceiro');
    }



    public function form($qualificacao_parceiro_id){


        //Adicionar Bibliotecas
        $this->load->library('form_validation');
        $this->load->model('qualificacao_questao_model', 'qualificacao_questao');
        $this->load->model('qualificacao_questao_opcao_model', 'qualificacao_questao_opcao');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Questão");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();

        $qualificacao_parceiro = $this->qualificacao_parceiro->get($qualificacao_parceiro_id);

        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$qualificacao_parceiro_id}");

        //Verifica se registro existe
        if(!$qualificacao_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/qualificacaoes/index");
        }

        //Caso post
        if($_POST)
        {
            if($this->current_model->validate_form()) //Valida form
            {
                //Realiza update
                $this->current_model->insert_form();

                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');


                //Redireciona para index
                redirect("$this->controller_uri/form/{$qualificacao_parceiro['qualificacao_parceiro_id']}");
            }
        }


        $questoes = $this->qualificacao_questao->filter_by_qualificacao($qualificacao_parceiro['qualificacao_id'])->get_all();

        foreach($questoes as $index => $questao){

            $questoes[$index]['opcoes'] = $this->qualificacao_questao_opcao->filter_by_questao($questao['qualificacao_questao_id'])->get_all();
        }

        $data['questoes'] = $questoes;
        $data['qualificacao_parceiro_id'] = $qualificacao_parceiro_id;
        $data['qualificacao_parceiro'] = $qualificacao_parceiro;
        $data['parceiro_id'] = $qualificacao_parceiro['parceiro_id'];
        $data['qualificacao_id'] = $qualificacao_parceiro['qualificacao_id'];

        $data['current_respostas'] =  $this->current_model->get_all_by_qualificacao_parceiro($qualificacao_parceiro_id);

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/form", $data );

    }

    public function extrato_by_form($qualificacao_parceiro_id){

        $this->load->model('qualificacao_model', 'qualificacao');

        $this->template->set('page_title', "Extrato");

        $data = array();
        $rows = $this->qualificacao->coreRanking()
            ->coreRakingByQuestaoFields()
            ->coreRakingFilterByQualificacaoParceiro($qualificacao_parceiro_id)
            ->get_all();

        $data['rows'] = $rows;

        $qualificacao_parceiro = $this->qualificacao_parceiro->get($qualificacao_parceiro_id);

        $data['qualificacao_parceiro_id'] = $qualificacao_parceiro_id;
        $data['qualificacao_id'] = $qualificacao_parceiro['qualificacao_id'];

        $this->template->load("admin/layouts/base", "$this->controller_uri/extrato", $data );

    }
}
