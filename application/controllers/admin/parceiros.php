<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Parceiros
 *
 * @property Parceiro_Model $current_model
 *
 */
class Parceiros extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Parceiros");
        $this->template->set_breadcrumb("Parceiros", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('parceiro_model', 'current_model');
        $this->load->model('parceiro_status_model', 'parceiro_status');
        $this->load->model('localidade_estado_model', 'estado');
        $this->load->model('localidade_cidade_model', 'cidade');
        $this->load->model('parceiro_tipo_model', 'parceiro_tipo');

    }

    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        $this->load->library('form_validation');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Parceiros");
        $this->template->set_breadcrumb("Parceiros", base_url("$this->controller_uri/index"));


        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model
            ->filterFromInput()
            ->get_total();
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model
            ->with_parceiro_tipo()
            ->filterFromInput()
            ->limit($config['per_page'], $offset)->get_all();


      //  print_r($data['rows']);exit;


        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add() //Função que adiciona registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');
        $this->load->helper('ckeditor');

        $this->template->js(base_url('assets/admin/modulos/parceiros/js/base.js'));

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Parceiro");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        //Carrega dados da página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");

        //Configurações ckeditor
        $data['enable_ckeditor'] = true;
        $data['ckeditor'] = array
        (
            'id'   => 'termo_aceite_usuario',
            'path' => 'assets/ckeditor/',
            'config' => array
            (
                'toolbar' => "Full",
                'baseHref' => base_url(),
                'width'   => "100%",
                'height'  => "400px",
                'filebrowserBrowseUrl'      => base_url('assets/common/ckfinder/ckfinder.html'),
                'filebrowserImageBrowseUrl' => base_url('assets/common/ckfinder/ckfinder.html?Type=Images'),
                'filebrowserFlashBrowseUrl' => base_url('assets/common/ckfinder/ckfinder.html?Type=Flash'),
                'filebrowserUploadUrl'      => base_url('assets/common/ckfinder/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'),
                'filebrowserImageUploadUrl' => base_url('assets/common/ckfinder/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'),
                'filebrowserFlashUploadUrl' => base_url('assets/common/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash')
            )
        );



        //Caso post
        if($_POST)
        {
            //Valida formulário
            if(!$this->verificaUpload('logo', 'logo-antiga'))
            {
                $this->session->set_flashdata('fail_msg', 'Imagem não compatível.');
            }


            if($this->current_model->validate_form('add'))
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
                redirect("$this->controller_uri/index");
            }
        }

        $data['status'] = $this->parceiro_status->get_all();
        $data['estados'] = $this->estado->get_all();
        $data['matriz'] = $this->current_model->filter_matriz(0)->get_all();

        $data['cidades'] = array();

        $data['tipos_parceiros'] = $this->parceiro_tipo->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');
        $this->load->helper('ckeditor');

        $this->template->js(base_url('assets/admin/modulos/parceiros/js/base.js'));

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Parceiro");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        $data['base_image_url'] = $this->getBaseUrlImage(); //Resgata url base das imagens deste model


        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

        //Configurações ckeditor
        $data['enable_ckeditor'] = true;
        $data['ckeditor'] = array
        (
            'id'   => 'termo_aceite_usuario',
            'path' => 'assets/ckeditor/',
            'config' => array
            (
                'toolbar' => "Full",
                'baseHref' => base_url(),
                'width'   => "100%",
                'height'  => "400px",
                'filebrowserBrowseUrl'      => base_url('assets/common/ckfinder/ckfinder.html'),
                'filebrowserImageBrowseUrl' => base_url('assets/common/ckfinder/ckfinder.html?Type=Images'),
                'filebrowserFlashBrowseUrl' => base_url('assets/common/ckfinder/ckfinder.html?Type=Flash'),
                'filebrowserUploadUrl'      => base_url('assets/common/ckfinder/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'),
                'filebrowserImageUploadUrl' => base_url('assets/common/ckfinder/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'),
                'filebrowserFlashUploadUrl' => base_url('assets/common/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash')
            )
        );
        //Caso post
        if($_POST)
        {


            if($this->current_model->validate_form('edit')) //Valida form
            {
                if(!$this->verificaUpload('logo', 'logo-antiga'))
                {
                    $this->session->set_flashdata('warn_msg', 'Foto não compatível.');
                }

                //Realiza update
                $this->current_model->update_form();

                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("$this->controller_uri/index");
            }
        }

        $data['status'] = $this->parceiro_status->get_all();
        $data['estados'] = $this->estado->get_all();

        if( $data['row']['localidade_estado_id']){

            $data['cidades'] = $this->cidade->getCidadesPorEstado($data['row']['localidade_estado_id']);

        }else {

            $data['cidades'] = array();
        }


        $data['matriz'] = $this->current_model->filter_matriz($id)->get_all();

        $data['tipos_parceiros'] = $this->parceiro_tipo->get_all();


        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {
        //Deleta registro
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('parceiro_relacionamento_produto_model', 'parceiro_relacionamento_produto');


        $this->produto_parceiro->delete_by(array('parceiro_id' => $id));
        $this->parceiro_relacionamento_produto->delete_by(array('parceiro_id' => $id));
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
        $diretorio = './assets/admin/upload/parceiros';

        //Caso diretório não exista ele cria
        if(!file_exists($diretorio))
        {
            mkdir($diretorio, 0777, true);
        }

        //Carrega configurações
        $config['upload_path'] = $diretorio;
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['max_size'] = 0;
        $config['encrypt_name'] = true;

        //Carrega biblioteca de upload
        $this->load->library('upload', $config);

        //Realiza upload
        $this->upload->do_upload('logo');

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
        return base_url("assets/admin/upload/parceiros").'/';
    }

}
