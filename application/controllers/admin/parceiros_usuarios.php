<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Parceiros_Contatos
 *
 * @property Produto_Ramo_Model $current_model
 *
 */
class Parceiros_Usuarios extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Usuários");
        $this->template->set_breadcrumb("Usuários", base_url("$this->controller_uri/index"));

        //Carrega modelos

        $this->load->model('usuario_model', 'current_model');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('usuario_acl_tipo_model', 'niveis');

    }

    public function view($parceiro_id , $offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');

        $parceiro = $this->parceiro->get($parceiro_id);

        //Verifica se registro existe
        if(!$parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Usuários");
        $this->template->set_breadcrumb("Ramos", base_url("$this->controller_uri/index"));


        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_parceiro($parceiro_id)->get_total();
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['niveis'] = array();
        foreach ( $this->niveis->get_all() as $key => $value) {
            $data['niveis'][ $value["usuario_acl_tipo_id"] ] = $value["nome"] ;
        }
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->filter_by_parceiro($parceiro_id)->get_all();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data["parceiro_id"] = $parceiro_id;

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($parceiro_id) {
      
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        $this->load->model('banco_model', 'banco');
        $this->load->model('colaborador_cargo_model', 'cargo');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Usuário");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        //Carrega dados da página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");
        $data['niveis'] = $this->niveis->get_all();
        $data['cargos'] = $this->cargo->with_colaborador_departamento()->order_by('colaborador_departamento.descricao')->get_all();
        $data['bancos'] = $this->banco->get_all();

        //Caso post
        if($_POST) {
            //Valida formulário
            if($this->current_model->validate_form('add_parceiro')) {
              if( isset( $_POST["usuario_acl_tipo_id"] ) && $_POST["usuario_acl_tipo_id"] != "" && $_POST["usuario_acl_tipo_id"] == "3" ) {
                $usuario_acl_tipo_id = $_POST["usuario_acl_tipo_id"];
                $result = $this->db->query( "SELECT usuario_acl_tipo_id FROM usuario WHERE parceiro_id=$parceiro_id AND usuario_acl_tipo_id=$usuario_acl_tipo_id AND deletado=0" )->result_array();
                if( sizeof( $result ) > 0 ) {
                  $this->session->set_flashdata('fail_msg', 'Já existe um usuário de Acesso Externo cadastrado para esse parceiro.');
                  redirect("$this->controller_uri/view/{$parceiro_id}");
                }
              }

                //Insere form
                $insert_id = $this->current_model->insert_form();
                if($insert_id) {
                    //Caso inserido com sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
                } else {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }
                //Redireciona para index
                redirect("$this->controller_uri/view/{$parceiro_id}");
            }
        }

        $data['parceiro_id'] = $parceiro_id;

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public function edit( $id ) {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        $this->load->model('banco_model', 'banco');
        $this->load->model('colaborador_cargo_model', 'cargo');


        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Usuários");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");
        $data['cargos'] = $this->cargo->with_colaborador_departamento()->order_by('colaborador_departamento.descricao')->get_all();
        $data['bancos'] = $this->banco->get_all();

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }

        $parceiro_id = $data['row']['parceiro_id'];

        //Caso post
        if($_POST) {
            if($this->current_model->validate_form('edit_parceiro')) {
              if( isset( $_POST["usuario_acl_tipo_id"] ) && $_POST["usuario_acl_tipo_id"] != "" && $_POST["usuario_acl_tipo_id"] == "3" ) {
                $usuario_acl_tipo_id = $_POST["usuario_acl_tipo_id"];
                $result = $this->db->query( "SELECT usuario_acl_tipo_id FROM usuario WHERE parceiro_id=$parceiro_id AND usuario_acl_tipo_id=$usuario_acl_tipo_id AND usuario_id <> $id AND deletado=0" )->result_array();
                if( sizeof( $result ) > 0 ) {
                  $this->session->set_flashdata('fail_msg', 'Já existe um usuário de Acesso Externo cadastrado para esse parceiro.');
                  redirect("$this->controller_uri/view/{$parceiro_id}");
                }
              }
              
                //Realiza update
                $this->current_model->update_form();

                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("$this->controller_uri/view/{$parceiro_id}");
            }
        }

        $data['parceiro_id'] = $parceiro_id;
        $data['niveis'] = $this->niveis->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
  
    public function vincular( $id ) {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        // $this->load->model('banco_model', 'banco');
        // $this->load->model('colaborador_cargo_model', 'cargo');


        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Vincular Cobertura/Produtos");
        $this->template->set_breadcrumb('Vincular', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/vincular/{$id}");
        
        $parceiro_id = $data['row']['parceiro_id'];
      
        $data['parceiro_id'] = $parceiro_id;
        
        // echo '<pre>';
        // print_r($data);
        // die;

        //Verifica se registro existe
        

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vincular", $data );
    }
  
    public  function delete($id) {
        $data['row'] = $this->current_model->get($id);

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }

        $parceiro_id = $data['row']['parceiro_id'];

        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/view/{$parceiro_id}");
    }


}


