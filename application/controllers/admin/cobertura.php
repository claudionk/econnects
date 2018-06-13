<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Colaborador $current_model
 *
 */
class Cobertura extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Coberturas');
        $this->template->set_breadcrumb('Coberturas', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('cobertura_model', 'current_model');
        $this->load->model('produto_model', 'produto');
        $this->load->model('produto_ramo_model', 'produto_ramo');
        $this->load->model('cobertura_tipo_model', 'tipo');
    }
    
    public function index($offset = 0)
    {
        //Carrega models necessários
        $this->load->model('cobertura_tipo_model', 'tipo');
        
        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Coberturas');
        $this->template->set_breadcrumb('Coberturas', base_url("$this->controller_uri/index"));
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        
        //Carrega dados
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->with_cobertura_tipo()->get_all();


        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add()
    {


        $this->template->js(app_assets_url("template/js/libs/jquery.chained/jquery.chained.min.js", "admin"));
        $this->template->js(app_assets_url('modulos/cobertura/base.js', 'admin'));

        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Cobertura');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));
       

        //Carrega dados para a página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['tipo'] = $this->tipo->get_all();
        $data['new_record'] = '1';

        $data['produtos'] = $this->produto->get_all();
        $data['produtos_ramos'] = $this->produto_ramo->get_all();

        
        //Caso post
        if($_POST)
        {

            if ($this->current_model->validate_form())
            {
                $this->current_model->insert_form();
                redirect("$this->controller_uri/index");
            }

        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id)
    {

        $this->template->js(app_assets_url("template/js/libs/jquery.chained/jquery.chained.min.js", "admin"));
        $this->template->js(app_assets_url('modulos/cobertura/base.js', 'admin'));

        //Carrega models necessários

        
        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Cobertura');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['row'] =  $this->current_model->with_cobertura_produto(array('nome', 'produto_ramo_id'))->get($id); //Carrega Cobertura


        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        //Carrega dados do model
        $data['tipo'] = $this->tipo->get_all();
        $data['produtos'] = $this->produto->get_all();
        $data['produtos_ramos'] = $this->produto_ramo->get_all();

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
                if ($this->current_model->validate_form())
                {
                    $this->current_model->update_form();

                    $this->session->set_flashdata('succ_msg', 'Os dados cadastrais foram salvos com sucesso.');
                    redirect("$this->controller_uri/index");
                }
        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }

    //Verifica se existe novo arquivo para upload e realiza-o
    public function verificaUpload ($nomeCampo, $nomeCampoAntigo)
    {
        $_POST[$nomeCampo] = $_POST[$nomeCampoAntigo]; // Seta como campo antigo

        if($_FILES[$nomeCampo]['name'] != "")
            $_POST[$nomeCampo] = $this->doUpload ();

        if($_POST[$nomeCampo] != null)
            return true;
        return false;
    }
    protected function doUpload()
    {
        $pasta = './assets/admin/upload/colaboradores';

        //Caso diretório não exista ele cria
        if(!file_exists($pasta))
        {
            mkdir($pasta, 0777, true);
        }

        //Carrega configurações
        $config['upload_path'] = $pasta;
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['max_size'] = 0;
        $config['encrypt_name'] = true;

        //Carrega biblioteca de upload
        $this->load->library('upload', $config);

        //Realiza upload
        $this->upload->do_upload('foto');

        //Realiza upload da imagem
        $foto = $this->upload->data();

        //Caso retorne erros
        if ($this->upload->display_errors())
        {
            return null; //Seta nulo
        }
        else
        {
            return $foto['file_name']; //Retorna nome da imagem
        }
    }

    public function getBaseUrlImage ()
    {
        return base_url("assets/admin/upload/colaboradores").'/';
    }
}
