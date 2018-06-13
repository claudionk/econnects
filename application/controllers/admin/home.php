<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Admin_Controller 
{
	public function index()
	{

        $this->load->library('form_validation');
        //Adiciona bibliotecas necessárias
        $this->template->css(app_assets_url('modulos/venda/equipamento/css/select2.css', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/equipamento/js/base.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/equipamento/js/formulario.js', 'admin'));
            $data = array();

            $this->template->load('admin/layouts/base', "admin/home/index", $data );


	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */