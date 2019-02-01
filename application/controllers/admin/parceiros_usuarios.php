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
        $this->load->model('usuario_cobertura_produto_model', 'usuario_cobertura_produto');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('usuario_acl_tipo_model', 'niveis');

        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model( "produto_parceiro_plano_model", "produto_parceiro_plano" );
        $this->load->model( "parceiro_plano_model", "parceiro_plano" );
        $this->load->model( "parceiro_produto_model", "parceiro_produto" );

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
  
    public function vincular( $id_usuario ) {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        // $this->load->model('banco_model', 'banco');
        // $this->load->model('colaborador_cargo_model', 'cargo');


        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id_usuario);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/vincular/{$id_usuario}");
        
        $id = $data['row']['parceiro_id'];
        $parceiro = $this->parceiro->get($id);


        //Caso post
        if($_POST)
        {
            if($this->usuario_cobertura_produto->validate_form()) //Valida form
            {              

                // //Realiza update
                // $this->usuario_cobertura_produto->update_form();

                // Deletando
                $this->db->where('usuario_id', $_POST['usuario_id']);
                $this->db->delete('usuario_cobertura_produto');

                $arrDados = [];

                if(isset($_POST['cobertura']))
                {
                    foreach ($_POST['cobertura'] as $k => $vl) {
                        foreach ($vl as $cob => $v) {
                            $arrDados  = [
                                'usuario_id' => $_POST['usuario_id'],
                                'cobertura_plano_id' => $k,
                                'cobertura_id' => $cob,
                                'criacao' => date('Y-m-d H:i:s'),
                                'alteracao' => date('Y-m-d H:i:s'),
                                'deletado' => 0
                            ];

                            $this->db->set($arrDados);
                            $this->db->insert('usuario_cobertura_produto');
                        }
                    }
                }
                
                // Atualizando deletado 
                // $this->db->set('deletado', 0, FALSE);
                // $this->db->where('usuario_id', $_POST['usuario_id']);
                // $this->db->update('usuario_cobertura_produto');
                

                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("{$this->controller_uri}/view/{$id}");
            }
        }


        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Vincular Cobertura/Produtos");
        $this->template->set_breadcrumb('Vincular', base_url("$this->controller_uri/index"));



        //Verifica se registro existe
        if(!$parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Parceiro Inválido.');
            redirect("parceiros/index");
        }

        $prod_plan = [];
        $prod_plan_cobertura = [];
        $result = $this->produto_parceiro->getProdutosByParceiro( $id, null, false );
        $planos_parc = $this->parceiro_plano->get_by_parceiro($id)->get_all();
        $produtos_parc = $this->parceiro_produto->get_by_parceiro($id)->get_all();

        if( !empty($result) ) {
            foreach ($result as $r) {

                $r['ok'] = false;

                // verifica se tem permissão
                foreach ($produtos_parc as $pp) {
                    if ($pp['produto_parceiro_id'] == $r['produto_parceiro_id']) {
                        $r['ok'] = true;
                        break;
                    }
                }

                $planosProd = $this->produto_parceiro_plano->coreSelectPlanosProdutoParceiro($r['produto_parceiro_id'])->get_all_select();

                $plansProdutos = [];
                foreach ($planosProd as $key => $value) {

                    $cobertura_itens = $this->db->query("SELECT 
                    cp.cobertura_plano_id, cp.cobertura_id, cp.produto_parceiro_plano_id, c.nome, ifnull(usuario_cobertura_produto_id,0) as selecionado
                    FROM cobertura_plano cp
                    INNER JOIN cobertura c ON c.cobertura_id = cp.cobertura_id
                    LEFT JOIN usuario_cobertura_produto ucp ON  ucp.cobertura_id = c.cobertura_id AND ucp.cobertura_plano_id = cp.cobertura_plano_id
                    AND ucp.usuario_id = '".$id_usuario."'
                    WHERE cp.produto_parceiro_plano_id = ".$value['produto_parceiro_plano_id']."
                    AND cp.deletado = 0")->result_array();

                    $value['cobertura'] = $cobertura_itens;
                    $prod_plan_cobertura[] = $value;
                    
                    $value['ok'] = false;

                    // verifica se tem permissão
                    foreach ($planos_parc as $pp) {
                        if ($pp['produto_parceiro_plano_id'] == $value['produto_parceiro_plano_id']) {
                            $value['ok'] = true;
                            break;
                        }
                    }

                    $plansProdutos[$key] = $value;
                }

                $r['planos'] = $plansProdutos;
                $prod_plan[] = $r;
            }
        }

        $data = array();
        $data['page_title'] = 'Planos/Produtos do Parceiro';
        $data['produtos'] = $prod_plan;
        $data['parceiro_id'] = $id;
        $data['parceiro'] = $parceiro;
        $data['id_usuario'] = $id_usuario;        
        

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vincular", $data );
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
    public function delete($id) {
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