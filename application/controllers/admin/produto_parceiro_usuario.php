<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros_Planos
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produto_Parceiro_Usuario extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiro / Usuários");
        $this->template->set_breadcrumb("Produtos / Parceiro / Usuários", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('produto_parceiro_usuario_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('usuario_model', 'usuario');
        $this->load->model('comissao_model', 'comissao');


    }



    public function index($usuario_id, $parceiro_id)
    {


        $this->load->library('form_validation');
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiro / Usuários");
        $this->template->set_breadcrumb("Produtos / Parceiro / Usuários", base_url("$this->controller_uri/index/{$usuario_id}"));


        $usuario = $this->usuario->get($usuario_id);

        //Verifica se registro existe
        if(!$usuario)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros_usuarios/view/{$parceiro_id}");
        }




        //Carrega dados para a página
        $data = array();




        if($_POST){
            foreach ($this->input->post('produto_parceiro_id') as $item) {
                $this->current_model->delete_by(array(
                    'produto_parceiro_id' => $item,
                    'usuario_id' => $this->input->post('usuario_id')
                ));

                $data_usuario = array();
                $data_usuario['produto_parceiro_id'] = $item;
                $data_usuario['usuario_id'] = $this->input->post('usuario_id');
                $data_usuario['comissao_id'] = $this->input->post("comissao_".$item);
                $this->current_model->insert($data_usuario, TRUE);
            }
            $this->session->set_flashdata('succ_msg', 'Os dados foram alterados com sucesso.'); //Mensagem de sucesso
            redirect("$this->controller_uri/index/{$usuario_id}/{$parceiro_id}");
            
        }


      ///  $produtos = $this->produto_parceiro->get_produtos_venda_admin($parceiro_id);
      //  $relacionamento = $this->produto_parceiro->get_produtos_venda_admin_parceiros($parceiro_id);


        //$data['rows'] = array_merge($produtos, $relacionamento);

        $data['rows'] = $this->current_model->getProdutosRelacionamento($usuario_id, $parceiro_id);

        $data['usuario_id'] = $usuario_id;
        $data['parceiro_id'] = $parceiro_id;

        $data['primary_key'] = $this->current_model->primary_key();
        $data['comissoes'] = $this->comissao->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }



}
