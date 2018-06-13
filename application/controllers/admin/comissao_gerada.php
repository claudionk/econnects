<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Parceiros
 *
 * @property Comissao_Gerada_Model $current_model
 *
 */
class Comissao_Gerada extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Comissões");
        $this->template->set_breadcrumb("Comissões", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('comissao_gerada_model', 'current_model');

    }

    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        $this->load->library('form_validation');

        $this->template->js(app_assets_url("modulos/comissao_gerada/base.js", "admin"));

        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('comissao_classe_model', 'comissao_classe');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Comissões");
        $this->template->set_breadcrumb("Comissões", base_url("$this->controller_uri/index"));


        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model
            ->filterFromInput()
            ->get_total();
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model
            ->with_comissao_classe()
            ->with_pedido()
            ->with_parceiro()
            ->filterFromPesquisa()
            ->limit($config['per_page'], $offset)->get_all();


      //  print_r($data['rows']);exit;


        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data["parceiros"] = $this->parceiro->order_by('nome_fantasia')->get_all();
        $data["comissao_classe"] = $this->comissao_classe->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function exportar_excel()
    {
        //Carrega biblioteca do excel
        $this->load->library('ExcelHelper');

        $excel = new ExcelHelper();

        $itens = $this->current_model
            ->with_comissao_classe()
            ->with_pedido()
            ->with_parceiro()
            ->filterFromPesquisa()
            ->get_all();



        $excel->setHeader(array(
            array('nome' => 'PEDIDO', 'tamanho' => 20),
            array('nome' => 'DATA', 'tamanho' => 20),
            array('nome' => 'PARCEIRO', 'tamanho' => 35),
            array('nome' => 'TIPO', 'tamanho' => 20),
            array('nome' => 'DESCRICAO', 'tamanho' => 50),
            array('nome' => 'PRÊMIO', 'tamanho' => 20),
            array('nome' => 'COMISSÃO %', 'tamanho' => 20),
            array('nome' => 'VALOR R$', 'tamanho' => 20),
        ));

        if($itens)
        {
            $coluna = 2;
            foreach ($itens as $item)
            {
                $excel->sheet->setCellValue("A{$coluna}", $item['pedido_codigo']);
                $excel->sheet->setCellValue("B{$coluna}", app_dateonly_mysql_to_mask($item['criacao']));
                $excel->sheet->setCellValue("C{$coluna}", $item['parceiro_nome_fantasia']);
                $excel->sheet->setCellValue("D{$coluna}", $item['comissao_classe_nome']);
                $excel->sheet->setCellValue("E{$coluna}", $item['descricao']);
                $excel->sheet->setCellValue("F{$coluna}", app_format_currency($item['premio_liquido_total']));
                $excel->sheet->setCellValue("G{$coluna}", app_format_currency($item['comissao']));
                $excel->sheet->setCellValue("H{$coluna}", app_format_currency($item['valor']));



                $coluna ++;
            }
        }

        $excel->generate('comissao.xlsx');
    }

}
