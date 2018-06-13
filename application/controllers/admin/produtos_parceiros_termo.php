<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Termo extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produto / Parceiros / Termo");
        $this->template->set_breadcrumb("Produtos / Parceiros / Termo", base_url("$this->controller_uri/edit"));

        //Carrega modelos
        $this->load->model('produto_parceiro_termo_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');

    }


     public function edit($produto_parceiro_id) //Função que edita registro
    {

        $this->auth->check_permission('view', 'produtos_parceiros_termo', 'admin/produtos_parceiros/');
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        $this->load->helper('ckeditor');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produto / Parceiro / Termo");
        $this->template->set_breadcrumb('Produto / Parceiro / Termo', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();
        $row = $this->current_model->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if(count($row) > 0){
            $data['row'] = $row[0];
        }else{
            $data['row'] = NULL;
        }

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
            $data['new_record'] = '1';
        }else{
            $data['new_record'] = '0';
        }

        $data['produto_parceiro'] = $produto_parceiro;


        //Caso post
        if($_POST)
        {
            if($this->current_model->validate_form()) //Valida form
            {

                if($this->input->post('new_record') == '1'){
                    $this->current_model->insert_form();
                }else {
                    //Realiza update
                    $this->current_model->update_form();
                }



                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("$this->controller_uri/edit/{$produto_parceiro_id}");
            }
        }


        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;
        //Configurações ckeditor
        $data['enable_ckeditor'] = true;
        $data['ckeditor_termo'] = array
        (
            'id'   => 'termo',
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




        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }


}
