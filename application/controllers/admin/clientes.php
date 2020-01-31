<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Cliente $current_model
 *
 */
class Clientes extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Cliente');
        $this->template->set_breadcrumb('Cliente', base_url("$this->controller_uri/index"));

        //Carrega modelos necessários
        $this->load->model('cliente_model', 'current_model');  
    }
    
    public function index($offset = 0)
    {
        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Clientes');
        $this->template->set_breadcrumb('Clientes', base_url("$this->controller_uri/index"));

        //Carrega models necessários
        $this->load->model('cliente_evolucao_status_model', 'cliente_evolucao_status');
        $this->load->model('cliente_grupo_empresarial_model', 'grupo_empresarial');
        $this->load->model('colaborador_model', 'colaboradores');
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        $get = $this->input->get();
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->filterFromInput($get)->with_cliente_evolucao_status()->get_total();
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        
        //Carrega dados
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        $data['evolucao_status'] = $this->cliente_evolucao_status->get_all();
        $data['grupos_empresariais'] = $this->grupo_empresarial->get_all();
        $data['colaboradores'] = $this->colaboradores->get_all();
        $get['cnpj_cpf'] = app_retorna_numeros($get['cnpj_cpf']);
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->filterFromInput($get)->with_cliente_evolucao_status()->get_all();
        
        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($tipo)
    {
        //Regra para o tipo nunca ser inválido
        if($tipo != 'cf')
            $tipo = 'co';

        //Carrega JS necessário
        $this->template->js(base_url('assets/admin/modulos/localidade_cidades/base.js'));
        $this->template->js(base_url('assets/admin/modulos/localidade_cidades/cep.js'));
        
        //Carrega models necessários
        $this->load->model('colaborador_cargo_model', 'colaborador_cargo');
        $this->load->model('localidade_estado_model', 'estados');
        $this->load->model('cliente_contato_nivel_relacionamento_model', 'nivel_relacionamento');
        $this->load->model('colaborador_model', 'colaboradores');
        $this->load->model('cliente_grupo_empresarial_model', 'grupo_empresarial');
        $this->load->model('localidade_cidade_model', 'cidades');
        $this->load->model('cliente_evolucao_status_model', 'cliente_evolucao_status');
        $this->load->model('cliente_evolucao_model', 'cliente_evolucao');
        $this->load->model('cliente_codigo_model', 'cliente_codigo');
        
        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Cliente');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
        
        //Carrega dados para a página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        
        //Carrega dados de outros models
        $data['cargos'] = $this->colaborador_cargo->get_all();
        $data['nivel_relacionamento'] = $this->nivel_relacionamento->get_all();
        $data['estados'] = $this->estados->get_all();
        $data['colaboradores'] = $this->colaboradores->get_all();
        $data['gruposEmpresariais'] = $this->grupo_empresarial->get_all();
        $data['novoRegistro'] = 1;
        $data['evolucao_status'] = $this->cliente_evolucao_status->get_all();
        $data['responsavel'] = $this->session->userdata('colaborador_id');
        $data['codigo'] = $this->cliente_codigo->get_codigo_cliente_formatado($tipo);

        //Caso post
        if($_POST)
        {
            //Seta cidade como primeiro do array
            if(isset($_POST['localidade_cidade_id']))
                $_POST['localidade_cidade_id'] = $_POST['localidade_cidade_id'][0];

            //Valida formulário
            if($this->current_model->validate_form())
            {
                $insert_id = $this->current_model->insert_form();

                //Caso inserção ocorra com sucesso
                if ($insert_id)
                {

                    $insert_evolucao_id = $this->cliente_evolucao->insere_data($insert_id);

                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                }
                else
                {
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }


                //Redireciona para index
                redirect("$this->controller_uri/index");
            }
        }
        
        //Carrega template
        if($tipo == 'cf')
            $this->template->load("admin/layouts/base", "$this->controller_uri/edit_cf", $data);
        else
            $this->template->load("admin/layouts/base", "$this->controller_uri/edit_co", $data);
    }
    public function edit($id)
    {
        //Redireciona para a function responsável dependendo do tipo de cliente
        $x = $this->current_model->get_by_id($id);
        $tipo = $x['tipo_cliente'];
        
        //Caso tipo de cliente CO
        if($tipo == 'CO')
            $this->edit_co($id);
        //Caso CF
        else 
            $this->edit_cf($id);
        
    }
    public function edit_cf($id)
    {
        //Carrega JS necessário
        $this->template->js(base_url('assets/admin/modulos/localidade_cidades/base.js'));
        $this->template->js(base_url('assets/admin/modulos/localidade_cidades/cep.js'));
        
        //Carrega models necessários
        $this->load->model('colaborador_cargo_model', 'colaborador_cargo');
        $this->load->model('localidade_estado_model', 'estados');
        $this->load->model('cliente_contato_nivel_relacionamento_model', 'nivel_relacionamento');
        $this->load->model('colaborador_model', 'colaboradores');
        $this->load->model('cliente_grupo_empresarial_model', 'grupo_empresarial');
        $this->load->model('localidade_cidade_model', 'cidades');
        $this->load->model('cliente_evolucao_status_model', 'cliente_evolucao_status');
        $this->load->model('cliente_evolucao_model', 'cliente_evolucao');
        
        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Cliente');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        
        //Carrega dados de outros models
        $data['nivel_relacionamento'] = $this->nivel_relacionamento->get_all();
        $data['estados'] = $this->estados->get_all();
        $data['colaboradores'] = $this->colaboradores->get_all();
        $data['gruposEmpresariais'] = $this->grupo_empresarial->get_all();
        $data['estadoCidade'] = $this->cidades->getEstadoIdByCidadeId($data['row']['localidade_cidade_id']);
        $data['evolucao_status'] = $this->cliente_evolucao_status->get_all();
        $data['responsavel'] = $data['row']['colaborador_id'];
        
        //Caso não exista registros
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index");
        }
        //Caso post
        if($_POST)
        {
            //Posta id
            $_POST['id'] = $id;
            
            //Seta cidade como primeiro do array
            if(isset($_POST['localidade_cidade_id']))
                $_POST['localidade_cidade_id'] = $_POST['localidade_cidade_id'][0];
         
            //Valida formulário
            if($this->current_model->validate_form())
            {
                //Realiza update
                $this->current_model->update_form();
                //Atualiza status se necessário
                $this->cliente_evolucao->checa_update($id);
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                redirect("$this->controller_uri/index");
            }
        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit_cf", $data );
    }
    public function edit_co($id)
    {
        //Carrega JS necessário
        $this->template->js(base_url('assets/admin/modulos/localidade_cidades/base.js'));
        $this->template->js(base_url('assets/admin/modulos/localidade_cidades/cep.js'));

        //Carrega models necessários
        $this->load->model('colaborador_cargo_model', 'colaborador_cargo');
        $this->load->model('localidade_estado_model', 'estados');
        $this->load->model('cliente_contato_nivel_relacionamento_model', 'nivel_relacionamento');
        $this->load->model('colaborador_model', 'colaboradores');
        $this->load->model('cliente_grupo_empresarial_model', 'grupo_empresarial');
        $this->load->model('localidade_cidade_model', 'cidades');
        $this->load->model('cliente_evolucao_status_model', 'cliente_evolucao_status');
        $this->load->model('cliente_evolucao_model', 'cliente_evolucao');
        
        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Cliente');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        
        //Carrega dados de outros models
        $data['cargos'] = $this->colaborador_cargo->get_all();
        $data['nivel_relacionamento'] = $this->nivel_relacionamento->get_all();
        $data['estados'] = $this->estados->get_all();
        $data['colaboradores'] = $this->colaboradores->get_all();
        $data['gruposEmpresariais'] = $this->grupo_empresarial->get_all();
        $data['estadoCidade'] = $this->cidades->getEstadoIdByCidadeId($data['row']['localidade_cidade_id']);
        $data['evolucao_status'] = $this->cliente_evolucao_status->get_all();
		
		$responsavel = $this->colaboradores->get($data['row']['colaborador_id']);
        if($responsavel && isset($responsavel['nome'])) {
            $data['responsavel'] = $responsavel['nome'];
        }else{
           // $data['responsavel'] = '';
        }
        
        //Caso não exista registros
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index");
        }
        //Caso post
        if($_POST)
        {
            //Posta id
            $_POST['id'] = $id;
            //Seta cidade como primeiro do array
            if(isset($_POST['localidade_cidade_id']))
                $_POST['localidade_cidade_id'] = $_POST['localidade_cidade_id'][0];
       
            //Valida formulário
            if($this->current_model->validate_form())
            {
                //Realiza update
                $this->current_model->update_form();
                //Atualiza status se necessário
                $this->cliente_evolucao->checa_update($id);
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                redirect("$this->controller_uri/index");
            }

        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit_co", $data );
    }


    public function view($id)
    {
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Visualizar Cliente');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');

        $this->load->model("localidade_cidade_model", "localidade_cidade");

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model
            ->with_foreign()
            ->get($id);

        $data['cidade'] = $this->localidade_cidade
            ->with_foreign()
            ->get($data['row']['localidade_cidade_id']);

        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        //Caso não exista registros
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/view/$id");
        }

        //Carrega dados para o template
        $this->load->view("$this->controller_uri/view", $data );

    }

    public  function delete($id)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }

    public function get_cliente($cpf = '', $produto_parceiro_id = 0){


        $cpf = empty($cpf) ? $this->input->post('cpf') : $cpf;

        $produto_parceiro_id = ($produto_parceiro_id != 0) ? $produto_parceiro_id : $this->input->post( "produto_parceiro_id" );
        $cliente = $this->current_model->get_cliente( $cpf, $produto_parceiro_id );

        if (!empty($cliente)) {
            $result  = array(
                'sucess' => TRUE,
                'qnt' => $cliente['quantidade'],
                'nome' => $cliente['razao_nome'],
                'email' => $cliente['email'],
                'telefone' => $cliente['telefone'],
                'cliente_id' => $cliente['cliente_id'],
                'estado_civil' => (isset($cliente['estado_civil'])?$cliente['estado_civil']:''),
                'sexo' => (isset($cliente['sexo'])?$cliente['sexo']:''),
                'rg_orgao_expedidor' => (isset($cliente['rg_orgao_expedidor'])?$cliente['rg_orgao_expedidor']:''),
                'rg_uf' => (isset($cliente['rg_uf'])?$cliente['rg_uf']:''),
                'rg_data_expedicao' => (isset($cliente['rg_data_expedicao'])?app_date_mysql_to_mask($cliente['rg_data_expedicao'], 'd/m/Y'):''),
                'rg' => (isset($cliente['ie_rg'])?$cliente['ie_rg']:''),
                'data_nascimento' => (isset($cliente['data_nascimento'])?app_date_mysql_to_mask($cliente['data_nascimento'], 'd/m/Y'):'')
            );
        } else {
            $result = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

}


