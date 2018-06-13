<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Resumo extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiros / Configurações / Resumo");
        $this->template->set_breadcrumb("Produtos / Parceiros / Resumo", base_url("$this->controller_uri/edit"));

        //Carrega modelos
        $this->load->model('produto_parceiro_configuracao_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('calculo_tipo_model', 'calculo_tipo');
        $this->load->model('produto_parceiro_regra_preco_model', 'produto_parceiro_regra_preco');
        $this->load->model('produto_parceiro_desconto_model', 'produto_parceiro_desconto');
        $this->load->model('produto_parceiro_cancelamento_model', 'produto_parceiro_cancelamento');




    }



    public function index($produto_parceiro_id) //Função que edita registro
    {

        //$this->template->js(app_assets_url('modulos/produtos_parceiros_configuracao/base.js', 'admin'));

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiros / Configurações / Resumo");
        $this->template->set_breadcrumb('Produtos / Parceiros / Configurações / Resumo', base_url("$this->controller_uri/index"));


        //Carrega dados para a página


        $data = array();


      $row = $this->current_model->filter_by_produto_parceiro($produto_parceiro_id)->get_all();


      if(count($row) > 0){
          $data['row'] = $row[0];
          $data['row']['calculo_tipo'] = $this->calculo_tipo->get($data['row']['calculo_tipo_id']);
      }else{
          $data['row'] = NULL;
      }

//        print_r( $data['row']);exit;

      $data['primary_key'] = $this->current_model->primary_key();
      $data['form_action'] =  base_url("$this->controller_uri/edit/{$produto_parceiro_id}");


      $produto_parceiro =  $this->produto_parceiro->get($produto_parceiro_id);


      if(!$produto_parceiro){
          //Mensagem de erro caso registro não exista
          $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
          //Redireciona para index
          redirect("admin/parceiros/index");

      }



      //Verifica se registro existe
      if(!$data['row'])
      {
          $data['row'] = array();
          $data['row']['comissao_config'] = FALSE;
          $data['row']['geral_config'] = FALSE;
      }else{
          $data['row']['comissao_config'] = TRUE;
          $data['row']['geral_config'] = TRUE;
      }



      //regra Preço

        $regra_preco = $this->produto_parceiro_regra_preco
            ->with_regra_preco()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();


        if($regra_preco){
            $data['row']['regrapreco_config'] = TRUE;
            $data['row']['regrapreco'] = $regra_preco;
        }else{
            $data['row']['regrapreco_config'] = FALSE;
            $data['row']['regrapreco'] = array();
        }


      $desconto = $this->produto_parceiro_desconto->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
            
        if($desconto ){
            $data['row']['desconto_config'] = TRUE;
            $data['row']['desconto'] = $desconto[0];

        }else{
            $data['row']['desconto_config'] = FALSE;
            $data['row']['desconto'] = array();
        }


      $cancelamento = $row = $this->produto_parceiro_cancelamento->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if($cancelamento ){
            $data['row']['cancelamento_config'] = TRUE;
            $data['row']['cancelamento'] = $cancelamento[0];

        }else{
            $data['row']['cancelamento_config'] = FALSE;
            $data['row']['cancelamento'] = array();
        }


      $data['produto_parceiro'] = $produto_parceiro;



      $data['produto_parceiro_id'] = $produto_parceiro_id;
      $data['produto_parceiro'] = $produto_parceiro;




        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/index", $data );

    }

}
