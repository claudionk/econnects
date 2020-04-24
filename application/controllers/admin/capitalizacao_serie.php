<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros_Planos
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Capitalizacao_Serie extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Capitalização / Série");
        $this->template->set_breadcrumb("Capitalização / Série", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('capitalizacao_serie_model', 'current_model');
        $this->load->model('capitalizacao_model', 'capitalizacao');
    }

    public function index($capitalizacao_id , $offset = 0)
    {

        $this->auth->check_permission('view', 'capitalizacao_serie', 'admin/capitalizacao/');

        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Campos");
        $this->template->set_breadcrumb("Campos", base_url("$this->controller_uri/index/{$capitalizacao_id}"));

        $capitalizacao = $this->capitalizacao->get($capitalizacao_id);

        //Verifica se registro existe
        if(!$capitalizacao)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/capitalizacao/index");
        }

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index/{$capitalizacao_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_capitalizacao($capitalizacao_id)->filter_by_ativo(1)->get_total();
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();

        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)
            ->filter_by_capitalizacao($capitalizacao_id)
            ->filter_by_ativo(1)
            ->get_all();

        $data['capitalizacao_id'] = $capitalizacao_id;
        $data['capitalizacao'] = $capitalizacao_id;
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($capitalizacao_id){

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Capitalização / Série");
        $this->template->set_breadcrumb("Capitalização / Série", base_url("$this->controller_uri/index"));

        $capitalizacao = $this->capitalizacao->get($capitalizacao_id);

        //Verifica se registro existe
        if(!$capitalizacao)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/capitalizacao/index");
        }

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

                    //Insere os títulos
                    $this->load->model('capitalizacao_serie_titulo_model', 'titulo');

                    $len = strlen($this->input->post('numero_inicio'));
                    $inicio = (int)$this->input->post('numero_inicio');
                    $quantidade = (int)$this->input->post('quantidade');
                    $responsavel_num_sorte = 0;

                    $capitalizacao = $this->capitalizacao->get($this->input->post('capitalizacao_id'));
                    if ($capitalizacao)
                    {
                        /*
                        0 - Integração
                        1 - Parceiro
                        2 - Manual
                        */
                        $responsavel_num_sorte = $capitalizacao['responsavel_num_sorte'];
                    }

                    // Manual
                    if ($responsavel_num_sorte == 2)
                    {
                        for ($i = $inicio; $i < ($inicio + $quantidade); $i++) {
                            $data = array();
                            $data['capitalizacao_serie_id'] = $insert_id;
                            $data['contemplado'] = 0;
                            $data['numero'] = str_pad($i, $len, "0", STR_PAD_LEFT);//(int)$i; //str_pad($i, $len, "0", STR_PAD_LEFT);
                            $data['utilizado'] = 0;
                            $data['ativo'] = 1;
                            $this->titulo->insert($data, FALSE);

                        }
                    }

                    //Caso inserido com sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
                }
                else
                {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }
                //Redireciona para index
                redirect("{$this->controller_uri}/index/{$capitalizacao_id}");
            }
        }

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '1';
        $data['capitalizacao_id'] = $capitalizacao_id;
        $data['capitalizacao'] = $capitalizacao;

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );

    }

    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Série");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

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

        $capitalizacao =  $this->capitalizacao->get($data['row']['capitalizacao_id']);

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
                redirect("{$this->controller_uri}/index/{$capitalizacao['capitalizacao_id']}");
            }
        }

        $data['capitalizacao'] = $capitalizacao;
        $data['capitalizacao_id'] = $capitalizacao['capitalizacao_id'];
        $data['responsavel_num_sorte'] = !empty($capitalizacao['responsavel_num_sorte']);

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public  function delete($id)
    {
        $row = $this->current_model->get($id);
        if(!$row){
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');

        redirect("{$this->controller_uri}/index/{$row['capitalizacao_id']}");
    }

    public function required_if($data, $j)
    {
        $this->load->library('form_validation');

        $values = explode(",", $j);
        $valueField = $this->input->post($values[0]);
        $valueExpected = $values[1];
        $valueDesc = $values[2];

        $this->form_validation->set_message('required_if', "O campo {$valueDesc} é obrigatório");

        if ($valueField == $valueExpected)
        {
            return !empty($data);
        }

        return TRUE;
    }

}
