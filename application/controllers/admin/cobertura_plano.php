<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Colaborador $current_model
 *
 */
class Cobertura_Plano extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Coberturas');
        $this->template->set_breadcrumb('Coberturas', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('cobertura_plano_model', 'current_model');

        $this->load->model('produto_parceiro_plano_model', 'parceiro_plano');


    }

    public function set_ordem($produto_parceiro_plano_id = 0)
    {

        if(isset($_POST['itens']))
        {
            $i = 1;
            foreach ($_POST['itens'] as $item)
            {
                $data_ordem = array();
                $data_ordem['ordem'] = $i;
                $this->current_model->update($item[0], $data_ordem, TRUE);
                $i++;
            }
            $this->session->set_flashdata('succ_msg', 'A ordem foi salva corretamente.');
        }
        else
        {
            $this->session->set_flashdata('fail_msg', 'Não possuem registros para salvar a ordem.');
            exit('0');
        }
        exit('1');
    }


    public function index($produto_parceiro_plano_id = 0,  $offset = 0)
    {

        $this->template->js(app_assets_url('core/js/jquery.tablednd.js', 'admin'));
        $this->template->js(app_assets_url('modulos/cobertura_plano/ordem.js', 'admin'));
        $parceiro_plano = $this->parceiro_plano->get($produto_parceiro_plano_id);

        //Verifica se registro existe
        if(!$parceiro_plano)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }



        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Coberturas');
        $this->template->set_breadcrumb('Coberturas', base_url("$this->controller_uri/index/{$produto_parceiro_plano_id}"));


        //Carrega dados
        $data = array();
        $data['parceiro_plano'] = $parceiro_plano;
        $data['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
        $data['primary_key'] = $this->current_model->primary_key();
        $data['rows'] = $this->current_model
                                ->with_cobertura()
                                ->with_cobertura_tipo()
                                ->with_parceiro()
                                ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                                ->order_by('cobertura_plano.ordem')
                                ->get_all();

        //print_r($data['rows']);
        //print_r($parceiro_plano);exit;
        $data['total_porcentagem'] = 0;

        foreach ($data['rows'] as $row){
            $data['total_porcentagem'] += $row['porcentagem'];
        }
        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($produto_parceiro_plano_id = 0)
    {   
        //Carrega models necessários
        $this->load->model('cobertura_model', 'coberturas');
        $this->load->model('cobertura_tipo_model', 'cobertura_tipo');


        $this->template->js(app_assets_url("template/js/libs/jquery.chained/jquery.chained.min.js", "admin"));
        $this->template->js(app_assets_url('modulos/cobertura_plano/base.js', 'admin'));

        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Cobertura');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
       

        //Carrega dados para a página
        $data = array();

        $data['coberturas'] = $this->coberturas->order_by('nome')->get_all();
        $data['parceiros'] = $this->parceiro->order_by('nome')->get_all();
        $data['coberturas_tipo'] = $this->cobertura_tipo->get_all();

        $data['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/add");

        //Caso post
        if($_POST)
        {

            if ($this->current_model->validate_form())
            {
                $this->current_model->insert_form();
                redirect("$this->controller_uri/index/{$produto_parceiro_plano_id}");
            }

        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($produto_parceiro_plano_id, $id)
    {
        //Carrega models necessários
        $this->load->model('cobertura_model', 'coberturas');
        $this->load->model('cobertura_tipo_model', 'cobertura_tipo');
        $this->load->model('parceiro_model', 'parceiro');

        $this->template->js(app_assets_url("template/js/libs/jquery.chained/jquery.chained.min.js", "admin"));
        $this->template->js(app_assets_url('modulos/cobertura_plano/base.js', 'admin'));
        
        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Cobertura');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['coberturas'] = $this->coberturas->order_by('nome')->get_all();
        $data['parceiros'] = $this->parceiro->order_by('nome')->get_all();
        $data['coberturas_tipo'] = $this->cobertura_tipo->get_all();
        $data['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
        $data['row'] =  $this->current_model->with_cobertura(array('cobertura_tipo_id'))->get($id); //Carrega Cobertura
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");


        //Caso não exista registros
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index/{$produto_parceiro_plano_id}");
        }
        //Caso post
        if($_POST)
        {
                if ($this->current_model->validate_form())
                {
                    $this->current_model->update_form();

                    $this->session->set_flashdata('succ_msg', 'Os dados cadastrais foram salvos com sucesso.');
                    redirect("$this->controller_uri/index/{$produto_parceiro_plano_id}");
                }
        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($produto_parceiro_plano_id, $id)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("$this->controller_uri/index/{$produto_parceiro_plano_id}");
    }

 }
