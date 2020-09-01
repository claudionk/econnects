<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros_Planos
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Campos extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiro / Campos");
        $this->template->set_breadcrumb("Produtos / Parceiro / Campos", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('produto_parceiro_campo_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('campo_model', 'campo');
        $this->load->model('campo_tipo_model', 'campo_tipo');


    }


    public function set_ordem()
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


    public function index($produto_parceiro_id, $campo_tipo_id = 0 )
    {
        $this->auth->check_permission('view', 'produto_parceiro_campo', 'admin/parceiros/');


        $this->template->js(app_assets_url('core/js/jquery.tablednd.js', 'admin'));
        $this->template->js(app_assets_url('modulos/produtos_parceiros_campos/base.js', 'admin'));
        //Carrega bibliotecas

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Campos");
        $this->template->set_breadcrumb("Campos", base_url("$this->controller_uri/view_by_produto_parceiro/{$produto_parceiro_id}"));


        $produto_parceiro = $this->produto_parceiro->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }


        //Carrega dados para a página
        $data = array();
        $data['campo_tipo'] = $this->campo_tipo->get_all();
        $data['campo_tipo_id'] = ($campo_tipo_id == 0) ? $data['campo_tipo'][0]['campo_tipo_id'] : $campo_tipo_id;

        $campo_tipo_id = ($campo_tipo_id == 0) ? $data['campo_tipo'][0]['campo_tipo_id'] : $campo_tipo_id;


        $data['rows'] = $this->current_model
            ->with_campo()
            ->with_campo_tipo()
            ->filter_by_campo_tipo($campo_tipo_id)
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->order_by("ordem", "asc")
            ->get_all();
        
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;



        $data['primary_key'] = $this->current_model->primary_key();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($produto_parceiro_id)
    {

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        $this->load->model("campo_tipo_model", "campo_tipo");
        $this->load->model("campo_model", "campo");
        $this->load->model("campo_validacao_model", "campo_validacao");

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Campos");
        $this->template->set_breadcrumb("Campos", base_url("$this->controller_uri/index"));


        $produto_parceiro =  $this->produto_parceiro->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
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
                redirect("{$this->controller_uri}/index/{$produto_parceiro_id}");
            }
        }



        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '1';

        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;

        $data['campo_tipo_list'] = $this->campo_tipo->get_all();
        $data['campo_list'] = $this->campo->get_all();
        $data['row']['validacoes'] = array();
        $data['row']['classe_css'] = array();


        $data['campo_validacao_list'] = $this->campo_validacao->order_by('nome')->get_all();
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );

    }

    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        $this->load->model("campo_tipo_model", "campo_tipo");
        $this->load->model("campo_model", "campo");
        $this->load->model("campo_validacao_model", "campo_validacao");

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Campo");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);

        if(isset($data['row']['validacoes']))
        {
            $validacoes = explode("|", $data['row']['validacoes']);
            $vals = array();
            if(is_array($validacoes))
            {
                foreach($validacoes as $validacao)
                {
                    $vals[] = array(
                        'slug' => $validacao,
                    );

                }
            }

            $data['row']['validacoes'] = $vals;
        }

        if(isset($data['row']['classe_css']))
        {
            $classe_css = explode("|", $data['row']['classe_css']);
            $val = array();
            if(is_array($classe_css))
            {
                foreach($classe_css as $classe)
                {
                    $val[] = array(
                        'slug' => $classe,
                    );

                }
            }

            $data['row']['classe_css'] = $val;
        }

        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        $data['campo_validacao_list'] = $this->campo_validacao->order_by('nome')->get_all();

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }


        $produto_parceiro =  $this->produto_parceiro->get($data['row']['produto_parceiro_id']);


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
                redirect("{$this->controller_uri}/index/{$produto_parceiro['produto_parceiro_id']}");
            }
        }

        $data['produto_parceiro_id'] = $produto_parceiro['produto_parceiro_id'];
        $data['produto_parceiro'] = $produto_parceiro;

        $data['campo_tipo_list'] = $this->campo_tipo->get_all();
        $data['campo_list'] = $this->campo->get_all();


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

        redirect("{$this->controller_uri}/index/{$row['produto_parceiro_id']}");
    }


}
