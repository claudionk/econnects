<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros_Planos
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Capitalizacao_Serie_Titulo extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Capitalização / Série / Título");
        $this->template->set_breadcrumb("Capitalização / Série / Título", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('capitalizacao_serie_titulo_model', 'current_model');
        $this->load->model('capitalizacao_serie_model', 'capitalizacao_serie');


    }



    public function index($capitalizacao_serie_id , $offset = 0)
    {


        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Capitalização / Série / Título");
        $this->template->set_breadcrumb("Capitalização / Série / Título", base_url("$this->controller_uri/index/{$capitalizacao_serie_id}"));


        $capitalizacao_serie = $this->capitalizacao_serie->get($capitalizacao_serie_id);

        //Verifica se registro existe
        if(!$capitalizacao_serie)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/capitalizacao/index");
        }




        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index/{$capitalizacao_serie_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_capitalizacao_serie($capitalizacao_serie_id)->get_total();
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();

        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)
            ->filter_by_capitalizacao_serie($capitalizacao_serie_id)
            ->get_all();

        $data['capitalizacao_serie_id'] = $capitalizacao_serie_id;
        $data['capitalizacao_serie'] = $capitalizacao_serie;


        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($capitalizacao_serie_id){

        //Adicionar Bibliotecas
        $this->load->library('form_validation');


        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Capitalização / Série / Título");
        $this->template->set_breadcrumb("Capitalização / Série / Título", base_url("$this->controller_uri/index"));


        $capitalizacao_serie = $this->capitalizacao_serie->get($capitalizacao_serie_id);

        //Verifica se registro existe
        if(!$capitalizacao_serie)
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
                    //Caso inserido com sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
                }
                else
                {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }
                //Redireciona para index
                redirect("{$this->controller_uri}/index/{$capitalizacao_serie_id}");
            }
        }



        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '1';

        $data['capitalizacao_serie_id'] = $capitalizacao_serie_id;
        $data['capitalizacao_serie'] = $capitalizacao_serie;



        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );

    }

    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar / Série / Título");
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


        $capitalizacao_serie =  $this->capitalizacao_serie->get($data['row']['capitalizacao_serie_id']);


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
                redirect("{$this->controller_uri}/index/{$capitalizacao_serie['capitalizacao_serie_id']}");
            }
        }



        $data['capitalizacao_serie_id'] = $capitalizacao_serie['capitalizacao_serie_id'];
        $data['capitalizacao_serie'] = $capitalizacao_serie;


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

        redirect("{$this->controller_uri}/index/{$row['capitalizacao_serie_id']}");
    }


}
