<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Enquete_configuracao extends Admin_Controller
{

    /**
     * Construtor
     */
    public function __construct()
    {
        //Construtor
        parent::__construct();

        //Model padrão
        $model = "Enquete_configuracao";
        $this->load->model("{$model}_model", 'current_model');

        //Models e Libraries
        $this->load->library('form_validation');

        //Títulos
        $this->template->set("titulo", "Configuração de Enquetes");
        $this->template->set("titulo_singular", "Configuração de Enquete");
        $this->template->set("titulo_adicionar", "Adicionar");
        $this->template->set("titulo_editar", "Editar");

        //Model
        $this->template->set("model_name", ucfirst($model));

        //Checa permissão
        permite_acesso('administrador');
    }

    /**
     * Listagem de registros
     */
    public function index()
    {
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();

        $this->template->load($this->layout, "$this->controller_uri/list", $data );
    }

    /**
     * Adiciona registro
     */
    public function add()
    {
        $this->load->model("Enquete_model", "enquete");
        $this->load->model("Sis_clientes_model", "clientes");
        $this->load->model("Sis_clientes_estipulantes_model", "clientes_estipulantes");
        $this->load->model("Sis_contratos_model", "contrato");
        $this->load->model("Sis_prestacoes_model", "prestacao");
        $this->load->model("Enquete_gatilho_model", "enquete_gatilho");

        //Dados para template
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = true;

        $data['enquete_list'] = $this->enquete->get_all();
        $data['cliente_list'] = $this->clientes->get_all();
        $data['cliente_estipulante_list'] = $this->clientes_estipulantes->get_all();
        $data['prestacao_list'] = $this->prestacao->get_all();
        $data['contrato_list'] = $this->contrato->get_all();
        $data['enquete_gatilho_list'] = $this->enquete_gatilho->get_all();


        //Caso efetua POST
        if($_POST)
        {
            //Executa validação
            if($this->current_model->validate_form())
            {
                if($this->current_model->insert_form())
                {
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                    redirect(admin_url() . "$this->controller_url/index");
                }
                else
                {
                    $this->session->set_flashdata('fail_msg', 'Erro ao salvar os dados.');
                    redirect(admin_url() . "$this->controller_url/index");
                }
            }
        }

        $this->template->load($this->layout, "$this->controller_uri/add_edit", $data );
    }

    /**
     * Editar registro
     * @param $id
     */
    public function edit($id)
    {
        $this->load->model("Enquete_model", "enquete");
        $this->load->model("Sis_clientes_model", "clientes");
        $this->load->model("Sis_clientes_estipulantes_model", "clientes_estipulantes");
        $this->load->model("Sis_contratos_model", "contrato");
        $this->load->model("Sis_prestacoes_model", "prestacao");
        $this->load->model("Enquete_gatilho_model", "enquete_gatilho");
        $this->load->model("Enquete_gatilho_configuracao_model", "enquete_gatilho_configuracao");


        $this->load->library('form_validation');

        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        $data['enquete_list'] = $this->enquete->get_all();

        $data['cliente_list'] = $this->clientes
            ->order_by('nome_fantasia', 'asc')
            ->with_contratos_ativos()
            ->get_all();

        $data['cliente_estipulante_list'] = $this->clientes_estipulantes->get_all();
        $data['prestacao_list'] = $this->prestacao->get_all();
        $data['contrato_list'] = $this->contrato->get_all();
        $data['enquete_gatilho_list'] = $this->enquete_gatilho->get_all();

        $data['enquete_gatilho_configuracao'] = $this->enquete_gatilho_configuracao->get_many_by(array(
            'enquete_configuracao_id' => $id
        ));

        if(isset($data['row']['estipulantes']))
        {
            $estipulantes = explode(",",$data['row']['estipulantes']);
            if($estipulantes)
            {
                foreach($estipulantes as $cod_estipulante)
                {
                    $est = $this->clientes_estipulantes->get_by(array(
                        'cod_estipulante' => $cod_estipulante
                    ));
                    if($est)
                        $estipulantes_selecionas[] = $est;
                }
            }
        }

        if(isset($estipulantes_selecionas))
            $data['estipulantes_selecionados'] = $estipulantes_selecionas;

        if(isset($data['row']['contratos']))
        {
            $rows = explode(",",$data['row']['contratos']);
            if($rows)
            {
                foreach($rows as $row)
                {
                    $est = $this->contrato->get_by(array(
                        'id_contrato' => $row
                    ));
                    if($est)
                        $contratos_selecionados[] = $est;
                }
            }
        }

        if(isset($contratos_selecionados))
            $data['contratos_selecionados'] = $contratos_selecionados;
        else
            $data['contratos_selecionados'] = array();

        if(!$data['row'])
        {
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect(admin_url() . "$this->controller_url/index");
        }

        if($_POST)
        {
            if($this->current_model->validate_form())
            {
                if($this->current_model->update_form())
                {
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                    redirect(admin_url() . "$this->controller_url/index");
                }
                else
                {
                    $this->session->set_flashdata('fail_msg', 'Erro ao salvar os dados.');
                    redirect(admin_url() . "$this->controller_url/index");
                }

            }
        }

        $this->template->load($this->layout, "$this->controller_uri/add_edit", $data );
    }

    /**
     * Deletar
     */
    public  function delete($id)
    {
        if($this->current_model->delete($id))
            $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        else
            $this->session->set_flashdata('fail_msg', 'Falha ao excluir registro.');

        redirect(admin_url("{$this->controller_url}/index"));
    }


}
