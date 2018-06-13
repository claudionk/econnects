<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Relatorios
 */
class Relatorios extends Admin_Controller
{
    /**
     * Relatório de vendas
     */
    public function index()
    {
        //Carrega React e Orb (relatórios)
        $this->template->js(app_assets_url("core/js/react.js", "admin"));
        $this->template->js(app_assets_url("core/js/orb.min.js", "admin"));
        $this->template->js(app_assets_url("modulos/relatorios/vendas/core.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/toastr/toastr.js", "admin"));
        $this->template->css(app_assets_url("template/css/{$this->_theme}/libs/toastr/toastr.css", "admin"));

        $this->template->css(app_assets_url("core/css/orb.min.css", "admin"));

        //Dados para template
        $data = array();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data);
    }

    /**
     * Retorna resultado
     */
    public function getRelatorio ()
    {
        $this->load->model("pedido_model", "pedido");

        $resultado = array();
        $resultado['status'] = false;
        $pedidos = $this->pedido;

        //Dados via GET
        $data_inicio = $this->input->get_post('data_inicio');
        $data_fim = $this->input->get_post('data_fim');

        if(isset($data_inicio) && !empty($data_inicio))
            $pedidos->where("status_data", ">=", app_date_only_numbers_to_mysql($data_inicio));
        if(isset($data_fim) && !empty($data_fim))
            $pedidos->where("status_data", "<=", app_date_only_numbers_to_mysql($data_fim));


        $resultado['data'] = $pedidos->extrairRelatorioVendas();
        $resultado['status'] = true;


        echo json_encode($resultado);
    }
}
