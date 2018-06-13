<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Cobranca extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Cobrança");
        $this->template->set_breadcrumb("Cobrança", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('parceiro_cobranca_model', 'current_model');
        $this->load->model('parceiro_model', 'parceiro');
    }
    
    public function index($offset = 0) //Função padrão (load)
    {

        $this->template->js(app_assets_url('modulos/cobranca/js/base.js', 'admin'));


        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Cobrança");
        $this->template->set_breadcrumb("Cobrança", base_url("$this->controller_uri/index"));

        

        
        //Carrega dados para a página
        $data = array();

        $result = $this->current_model->relatorio();
        if($result['result']) {
            $data['rows'] = $result['rows'];
        }else{
            $data['rows'] = array();
        }
        $data['parceiros'] = $this->parceiro->order_by('nome')->get_all();
        $data['primary_key'] = $this->current_model->primary_key();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function excel()
    {
        //Carrega biblioteca do excel
        $this->load->library('ExcelHelper');

        $excel = new ExcelHelper();

        $data = array();

        $result = $this->current_model->relatorio();
        if($result['result']) {
            $data['rows'] = $result['rows'];
        }else{
            $data['rows'] = array();
        }


        $excel->setHeader(array(
            array('nome' => 'ITEM', 'tamanho' => 120),
            array('nome' => 'TIPO', 'tamanho' => 120),
            array('nome' => 'QUANTIDADE', 'tamanho' => 30),
            array('nome' => 'VALOR ÚNITARIO', 'tamanho' => 30),
            array('nome' => 'VALOR TOTAL', 'tamanho' => 30),
        ));

        if($data['rows'])
        {
            $coluna = 2;
            foreach ($data['rows'] as $item)
            {
                $excel->sheet->setCellValue("A{$coluna}", strip_tags($item['item']));
                $excel->sheet->setCellValue("B{$coluna}", strip_tags($item['tipo']));
                $excel->sheet->setCellValue("C{$coluna}", strip_tags($item['quantidade']));
                $excel->sheet->setCellValue("D{$coluna}", strip_tags($item['valor']));
                $excel->sheet->setCellValue("E{$coluna}", strip_tags($item['valor_total']));
                $coluna ++;
            }
        }

        $excel->generate('cobranca.xlsx');
    }


}
