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
        $this->loadLibraries();
        $this->template->js(app_assets_url("modulos/relatorios/vendas/venda.js", "admin"));

        //Dados para template
        $data = [];

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data);
    }

    public function vendas1()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 01 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Desc. do Produto',
            'Seguro Contratado',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'), 
                        $row['segurado'], 
                        $row['documento'], 
                        $row['plano_nome'], 
                        $row['nome_produto_parceiro'], 
                        app_format_currency($row['nota_fiscal_valor'], true), 
                        app_format_currency($row['premio_liquido_total'], true), 
                        $row['num_apolice'], 
                    ];
                }
                $this->exportExcel($data['columns'], $rows);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas1", $data);
    }

    public function vendas4()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 04 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Seguro Contratado',
            'Desc. do Produto',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
            'Varejista',
            'CNPJ Varejista',
            'UF Varejista',
            'Vendedor',
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'), 
                        $row['segurado'], 
                        $row['documento'], 
                        $row['plano_nome'], 
                        $row['nome_produto_parceiro'], 
                        app_format_currency($row['nota_fiscal_valor'], true), 
                        app_format_currency($row['premio_liquido_total'], true), 
                        $row['num_apolice'], 
                        $row['nome_fantasia'], 
                        $row['cnpj'], 
                        $row['UF'], 
                        $row['vendedor'], 
                    ];
                }
                $this->exportExcel($data['columns'], $rows);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas4", $data);
    }

    public function vendas5()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 05 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Seguro Contratado',
            'Desc. do Produto',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
            'Varejista',
            'CNPJ Varejista',
            'UF Varejista',
            'Valor a Receber',
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'), 
                        $row['segurado'], 
                        $row['documento'], 
                        $row['plano_nome'], 
                        $row['nome_produto_parceiro'], 
                        app_format_currency($row['nota_fiscal_valor'], true), 
                        app_format_currency($row['premio_liquido_total'], true), 
                        $row['num_apolice'], 
                        $row['nome_fantasia'], 
                        $row['cnpj'], 
                        $row['UF'], 
                        app_format_currency($row['comissao_parceiro'], true), 
                    ];
                }
                $this->exportExcel($data['columns'], $rows);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas5", $data);
    }

    public function mapa_repasse_lasa()
    {
        //Carrega React e Orb (relatórios)
        // $this->loadLibraries();
        // $this->template->js(app_assets_url("modulos/relatorios/vendas/mapa_repasse_lasa.js", "admin"));

        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['action'] = $this->uri->segment(3);
        $data['src'] = $this->controller_uri;
        $data['title'] = 'Relatório de Mapa de Repasse LASA';
        $data['layout'] = 'mapa_analitico';
        $data['columns'] = [
            'Operacao',
            'Grupo',
            'Data da Venda',
            'Inicio Vigencia',
            'Fim Vigencia',
            'Num Bilhete',
            'Segurado',
            'Documento',
            'Equipamento',
            'Marca',
            'Modelo',
            'IMEI',
            'Produto',
            'Importancia Segurada',
            'Num Endosso',
            'Vigencia Parcela',
            'Parcela',
            'Status Parcela',
            'Data Cancelamento',
            'Valor Parcela',
            'Premio Bruto Roubo Furto',
            'Premio Liquido Roubo Furto',
            'Premio Bruto Quebra',
            'Premio Liquido Quebra',
            'Pro Labore',
            'Comissao Corretagem',

        ];

        if ($_POST) {
            $result = $this->getMapaRepasse(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        $row['operacao'],
                        $row['grupo'],
                        $row['data_emissao'],
                        $row['ini_vigencia'],
                        $row['fim_vigencia'],
                        $row['num_apolice'],
                        $row['segurado_nome'],
                        $row['documento'],
                        $row['equipamento'],
                        $row['marca'],
                        $row['modelo'],
                        $row['imei'],
                        $row['nome_produto_parceiro'],
                        $row['importancia_segurada'],
                        $row['num_endosso'],
                        $row['vigencia_parcela'],
                        $row['parcela'],
                        $row['status_parcela'],
                        $row['data_cancelamento'],
                        app_format_currency($row['valor_parcela'], true),
                        app_format_currency($row['PB_RF'], true),
                        app_format_currency($row['PL_RF'], true),
                        app_format_currency($row['PB_QA'], true),
                        app_format_currency($row['PL_QA'], true),
                        app_format_currency($row['pro_labore'], true),
                        app_format_currency($row['valor_comissao'], true),
                    ];
                }
                $this->exportExcel($data['columns'], $rows);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
            if (!empty($this->input->get_post('layout'))) {
                $data['layout'] = $this->input->get_post('layout');
            }
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "{$this->controller_uri}/{$data['action']}" , $data);
    }

    /**
     * Retorna resultado
     */
    public function getRelatorio ($ajax = TRUE)
    {
        $this->load->model("pedido_model", "pedido");

        $resultado = array();
        $resultado['status'] = false;
        // $pedidos = $this->pedido;

        //Dados via GET
        $data_inicio = $this->input->get_post('data_inicio');
        $data_fim = $this->input->get_post('data_fim');

        if(isset($data_inicio) && !empty($data_inicio))
            $this->pedido->where("status_data", ">=", app_date_only_numbers_to_mysql($data_inicio));
        if(isset($data_fim) && !empty($data_fim))
            $this->pedido->where("status_data", "<=", app_date_only_numbers_to_mysql($data_fim, FALSE));


        $resultado['data'] = $this->pedido->extrairRelatorioVendas();
        $resultado['status'] = true;

        if ($ajax)
            echo json_encode($resultado);
        else
            return $resultado;
    }

    /**
     * Retorna resultado
     */
    public function getMapaRepasse ($ajax = TRUE)
    {
        $this->load->model("pedido_model", "pedido");

        $resultado = array();
        $resultado['status'] = false;
        // $pedidos = $this->pedido;

        //Dados via GET
        $data_inicio = $this->input->get_post('data_inicio');
        $data_fim = $this->input->get_post('data_fim');

        if(isset($data_inicio) && !empty($data_inicio))
            $this->pedido->where("status_data", ">=", app_date_only_numbers_to_mysql($data_inicio));
        if(isset($data_fim) && !empty($data_fim))
            $this->pedido->where("status_data", "<=", app_date_only_numbers_to_mysql($data_fim, FALSE));


        $resultado['data'] = $this->pedido->extrairRelatorioMapaRepasse();
        $resultado['status'] = true;

        if ($ajax)
            echo json_encode($resultado);
        else
            return $resultado;
    }

    public function exportExcel($columns, $rows = []) {
        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $letters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $contC = 0;
        $contR = 1;

        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        // Cria as colunas
        foreach ($columns as $column) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$contC] . $contR, $column);
            $contC++;
        }

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:'. $letters[count($columns)-1].'1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9')
                )
            )
        );

        // Cria as Linhas
        foreach ($rows as $row) {
            $contR++;
            $contC = 0;

            foreach ($columns as $column) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$contC] . $contR, $row[$contC]);
                $contC++;
            }
            
        }

        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // $objWriter->save(str_replace('.php', '.xlsx', app_assets_dir('temp', 'uploads'). basename(__FILE__)));

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="relatorio.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    private function loadLibraries() {
        //Carrega React e Orb (relatórios)
        $this->template->js(app_assets_url("core/js/react.js", "admin"));
        $this->template->js(app_assets_url("core/js/orb.min.js", "admin"));
        $this->template->js(app_assets_url("modulos/relatorios/vendas/core.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/toastr/toastr.js", "admin"));

        $this->template->css(app_assets_url("template/css/{$this->_theme}/libs/toastr/toastr.css", "admin"));
        $this->template->css(app_assets_url("core/css/orb.min.css", "admin"));
    }
}
