<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Segurado_Controller
{
	public function index()
	{
        $this->load->model('apolice_model', 'apolice');


        $cliente_id = $this->session->userdata("cliente_id");

        $data = array();


        $data['rows'] = $this->apolice
            ->with_cliente($cliente_id)
            ->get_all();


        foreach ($data['rows'] as $index => $row) {
            $code = $this->apolice->get_codigo_apolice($row['apolice_id']);
            $data['rows'][$index]['pdf'] = base_url() . 'impressao/certificado?apolice_id='.$code;
        }
        $this->template->load('segurado/layouts/base', "segurado/home/index", $data );
	}
}
