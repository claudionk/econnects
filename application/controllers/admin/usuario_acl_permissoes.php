<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_Acl_Permissoes extends Admin_Controller {

    /**
     * @var Acl_Acao
     */
    protected $current_model;

    function __construct()
    {
        parent::__construct();
        $this->load->model('usuario_acl_recurso_model', 'usuario_acl_recurso');
        $this->load->model('usuario_acl_acao_model', 'usuario_acl_acao');
        $this->load->model('usuario_acl_tipo_model', 'usuario_acl_tipo');
        $this->load->model('usuario_acl_permissao_model', 'usuario_acl_permissao');
        $this->load->model('usuario_acl_recurso_acao_model', 'usuario_acl_recurso_acao');
    }

    public function edit($id)
    {
        $this->template->js(app_assets_url('modulos/usuarios_acl_permissoes/base.js', 'admin'));

        if(! ($row = $this->usuario_acl_tipo->get($id)) ){

            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect('admin/usuario_acl_tipos/');
        }

        $data = array();
        $data['page_title'] = 'Permissões de Acesso';
        $data['usuario_tipo']  = $row;
        $data['recursos'] = $this->usuario_acl_recurso->retornarRecursivamente($id);
        //print_r($data['recursos']);exit;
        $data['current_acl'] = $this->usuario_acl_permissao->get_all_by_tipo($id);
        $data['usuario_acl_tipo_id'] = $id;
        $data['parceiro_id'] = $row['parceiro_id'];
        $data['row'] = $row;

        if($_POST)
        {
            if($this->usuario_acl_permissao->update_permissoes($id, $this->input->post("recurso_acao")))
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
            redirect("admin/usuarios_acl_tipos/index/0/{$row['parceiro_id']}");

        }

        $this->template->load('admin/layouts/base', 'admin/usuario_acl_permissoes/edit', $data );
    }


}
