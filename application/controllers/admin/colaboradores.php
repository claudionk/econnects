<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Colaborador $current_model
 *
 */
class Colaboradores extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Colaborador');
        $this->template->set_breadcrumb('Colaborador', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('colaborador_model', 'current_model');  
    }
    
    public function index($offset = 0)
    {
        //Carrega models necessários
        $this->load->model('colaborador_cargo_model', 'cargo');
        
        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Colaboradores');
        $this->template->set_breadcrumb('Colaboradores', base_url("$this->controller_uri/index"));
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        
        //Carrega dados
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model
            ->limit($config['per_page'], $offset)
            ->with_colaborador_cargo()
            ->get_all();
        
        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add()
    {   
        //Carrega models necessários
        $this->load->model('colaborador_cargo_model', 'cargo');
        $this->load->model('banco_model', 'banco');
        $this->load->model('usuario_model', 'usuarios');
        $this->load->model('usuario_acl_tipo_model', 'niveis');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('colaborador_parceiro_model', 'colaborador_parceiro');


        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Adicionar Colaborador');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['cargos'] = $this->cargo->with_colaborador_departamento()->order_by('colaborador_departamento.descricao')->get_all();
        $data['new_record'] = '1';
        $data['bancos'] = $this->banco->get_all();
        $data['niveis'] = $this->niveis->get_all();
        $data['parceiro'] = $this->parceiro->get_all();

        //Caso post
        if($_POST)
        {
            //Valida formulário

            if($this->usuarios->validate_form('add_colaborador')) {
                if ($this->current_model->validate_form()) {
                    $this->verificaUpload('foto', 'foto-antiga');
                    $insert_id = $this->current_model->insert_form();
                    $_POST['colaborador_id'] = $insert_id;

                    if ($this->usuarios->validate_form('add')) {
                        //Tenta inserir form
                        $insert_usuario_id = $this->usuarios->insert_form($this->usuarios->get_form_data_colaborador());

                        //Caso inserção ocorra com sucesso
                        if ($insert_id && $insert_usuario_id) {

                            $colaborador_parceiro = $this->input->post("colaborador_parceiro");

                            if (is_array($colaborador_parceiro)) {
                                $this->colaborador_parceiro->updateParceiros($insert_id, $colaborador_parceiro);

                            }

                            $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                        } else {
                            $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                        }
                        //Redireciona para index
                        redirect("$this->controller_uri/index");
                    }
                }
            }

        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id)
    {

        //Carrega models necessários
        $this->load->model('colaborador_cargo_model', 'cargo');
        $this->load->model('colaborador_parceiro_model', 'colaborador_parceiro');
        $this->load->model('banco_model', 'banco');
        $this->load->model('usuario_model', 'usuarios');
        $this->load->model('usuario_acl_tipo_model', 'niveis');
        $this->load->model('parceiro_model', 'parceiro');

        
        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');
        
        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar Colaborador');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $colaborador = $this->current_model->get($id); //Carrega colaboradores
        $user = $this->usuarios->get_by_colaborador_id($id); //Carrega usuários
        $data['row'] = array_merge($colaborador, $user); //Une colaboradores e usuários em um array
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        $data['parceiro'] = $this->colaborador_parceiro->getParceirosPorColaborador($id);

        //Carrega dados do model
        $data['cargos'] = $this->cargo->with_colaborador_departamento()->order_by('colaborador_departamento.descricao')->get_all();
        $data['bancos'] = $this->banco->get_all();
        $data['niveis'] = $this->niveis->get_all();
        $data['base_image_url'] = $this->getBaseUrlImage(); //Resgata url base das imagens deste model

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
            //Valida formulário
            if($this->usuarios->validate_form('edit_colaborador'))
            {
                if ($this->current_model->validate_form())
                {
                    $colaborador_parceiro = $this->input->post("colaborador_parceiro");

                    if(is_array($colaborador_parceiro))
                    {
                        $this->colaborador_parceiro->updateParceiros($data['row']['colaborador_id'], $colaborador_parceiro);
                    }

                    $this->verificaUpload('foto', 'foto-antiga');
                    //Realiza update
                    $this->current_model->update_form();

                    //Realiza update no usuário
                    $this->usuarios->update_usuario($this->input->post('colaborador_id'), $this->usuarios->get_form_data_colaborador());

                    $this->session->set_flashdata('succ_msg', 'Os dados cadastrais foram salvos com sucesso.');
                    redirect("$this->controller_uri/index");
                }
            }
        }
        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {

        $this->load->model('usuario_model', 'usuario');


        $usuario = $this->usuario->get_many_by(array(
            'colaborador_id' => $id

        ));

        foreach ($usuario as $item) {
            $this->usuario->delete($item['usuario_id']);
        }

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

    /**
     * Troca parceiro
     * @param $parceiro_id
     */
    public function trocarParceiro($parceiro_id)
    {
        $this->load->library("response");
        $response = new Response();

        if($parceiro_id)
        {
            $parceiros_permitidos = $this->session->userdata("parceiros_permitidos");

            if(is_array($parceiros_permitidos) && in_array($parceiro_id, $parceiros_permitidos))
            {
                $this->session->set_userdata("parceiro_selecionado", $parceiro_id);
                $this->session->set_userdata("parceiro_id", $parceiro_id);
                $this->_setTheme($parceiro_id);
                $response->setStatus(true);
            }
        }

        echo $response->getJSON();

    }
}
