<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Usuarios $current_model
 *
 */
class Usuarios_Configuracoes extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Perfil');
        $this->template->set_breadcrumb('Editar Perfil', base_url("$this->controller_uri/index"));

        //Carrega modelos necessários
        $this->load->model('usuario_model', 'current_model');
    }

    public function index()
    {
        //Seta id como colaborador id
        $id = $this->session->userdata('usuario_id');


        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');

        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Perfil');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id); //Carrega colaboradores
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        //$data['base_image_url'] = $this->current_model->getBaseUrlImage(); //Resgata url base das imagens deste model

        //Caso não exista registros
        if(!$id)
        {
            //Mensagem de erro caso não ache ID
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index");
        }
        //Caso post
        if($_POST)
        {
            //Valida formulário
            if($this->current_model->validate_form('config'))
            {
                $this->current_model->update_config($id);
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
                redirect("$this->controller_uri");

            }
           //
        }

        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
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
}
