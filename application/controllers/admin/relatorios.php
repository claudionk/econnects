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
            if (!empty($this->input->get_post('layout'))) {
                $data['layout'] = $this->input->get_post('layout');
            }
            $result = $this->getMapaRepasse(FALSE, $data['layout']);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {
                $this->exportExcelMapaRepasse($data['columns'], $data['result']);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
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
    public function getMapaRepasse ($ajax = TRUE, $layout)
    {
        $this->load->model("pedido_model", "pedido");

        $resultado = array();
        $resultado['status'] = false;
        $where = '';

        //Dados via GET
        $data_inicio = $this->input->get_post('data_inicio');
        $data_fim = $this->input->get_post('data_fim');

        if(isset($data_inicio) && !empty($data_inicio))
            if ($layout=='mapa_analitico')
                $this->pedido->where("status_data", ">=", app_date_only_numbers_to_mysql($data_inicio));
            else
                $where .= " AND pedido.status_data >= '".app_date_only_numbers_to_mysql($data_inicio) ."' ";

        if(isset($data_fim) && !empty($data_fim))
            if ($layout=='mapa_analitico')
                $this->pedido->where("status_data", "<=", app_date_only_numbers_to_mysql($data_fim, FALSE));
            else
                $where .= " AND pedido.status_data <= '".app_date_only_numbers_to_mysql($data_fim, FALSE) ."' ";

        if ($layout=='mapa_analitico') {
            $resultado['data'] = $this->pedido->extrairRelatorioMapaRepasseAnalitico();
        } else {
            $resultado['data'] = $this->preparaMapaRepasse($this->pedido->extrairRelatorioMapaRepasseSintetico($where));
        }
        $resultado['status'] = true;

        if ($ajax)
            echo json_encode($resultado);
        else
            return $resultado;
    }

    private function preparaMapaRepasse($result)
    {
        if (empty($result)) {
            return [];
        }

        $tpas = [
            '007' => 'NOVOS',
            '010' => 'USADOS',
        ];

        $ret = [];
        $quantidade_RF = 0;
        $IOF_RF = 0;
        $PL_RF = 0;
        $PB_RF = 0;
        $pro_labore_RF = 0;
        $valor_comissao_RF = 0;
        $quantidade_QA = 0;
        $PB_QA = 0;
        $IOF_QA = 0;
        $PL_QA = 0;
        $pro_labore_QA = 0;
        $valor_comissao_QA = 0;

        foreach ($tpas as $tpa => $desc) { 
            $find = false;
            foreach ($result as $row) { 
                if ($row['cod_tpa'] == $tpa) {
                    $row['desc'] = $desc;
                    $ret[] = $row;

                    $quantidade_RF += $row['quantidade_RF'];
                    $IOF_RF += $row['IOF_RF'];
                    $PL_RF += $row['PL_RF'];
                    $PB_RF += $row['PB_RF'];
                    $pro_labore_RF += $row['pro_labore_RF'];
                    $valor_comissao_RF += $row['valor_comissao_RF'];
                    $quantidade_QA += $row['quantidade_QA'];
                    $PB_QA += $row['PB_QA'];
                    $IOF_QA += $row['IOF_QA'];
                    $PL_QA += $row['PL_QA'];
                    $pro_labore_QA += $row['pro_labore_QA'];
                    $valor_comissao_QA += $row['valor_comissao_QA'];
                    $find = true;
                    break;
                }
            }

            if (!$find) {
                $ret[] = [
                    'desc' => $desc,
                    'cod_tpa' => 0,
                    'quantidade_RF' => 0,
                    'IOF_RF' => 0,
                    'PL_RF' => 0,
                    'PB_RF' => 0,
                    'pro_labore_RF' => 0,
                    'valor_comissao_RF' => 0,
                    'quantidade_QA' => 0,
                    'PB_QA' => 0,
                    'IOF_QA' => 0,
                    'PL_QA' => 0,
                    'pro_labore_QA' => 0,
                    'valor_comissao_QA' => 0,
                ];
            }
        }

        $ret[] = [
            'desc' => 'TOTAL',
            'quantidade_RF' => $quantidade_RF,
            'IOF_RF' => $IOF_RF,
            'PL_RF' => $PL_RF,
            'PB_RF' => $PB_RF,
            'pro_labore_RF' => $pro_labore_RF,
            'valor_comissao_RF' => $valor_comissao_RF,
            'quantidade_QA' => $quantidade_QA,
            'PB_QA' => $PB_QA,
            'IOF_QA' => $IOF_QA,
            'PL_QA' => $PL_QA,
            'pro_labore_QA' => $pro_labore_QA,
            'valor_comissao_QA' => $valor_comissao_QA,
        ];

        return $ret;
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

    public function exportExcelMapaRepasse($columns, $rows = []) {
        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $letters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $contC = 0;
        $contR = 1;

        $styleCenter = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleCenterVertic = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleRight = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
        );
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:E2')->applyFromArray($styleCenter);
        
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C1:E1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','Vendas');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2','Roubo ou Furto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2','Quebra Acidental');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2','TOTAL');

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:E1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9')
                )
            )
        );

        $contR = 3;
        // Cria as Linhas
        foreach ($rows as $row) {
            $contRFim = $contR + 5;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleCenter);

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$contR.':A'.$contRFim)->getStyle('A'.$contR.':A'.$contR)->applyFromArray($styleCenterVertic);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$contR)->getAlignment()->setTextRotation(90);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'. $contR,$row['desc']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Quantidade de Registros');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, $row['quantidade_RF']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, $row['quantidade_QA']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, $row['quantidade_RF'] + $row['quantidade_QA']);
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Prêmio Bruto');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['PB_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['PB_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['PB_RF'] + $row['PB_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'IOF');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['IOF_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['IOF_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['IOF_RF'] + $row['IOF_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Prêmio Líquido');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['PL_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['PL_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['PL_RF'] + $row['PL_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Pró-labore LASA');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['pro_labore_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['pro_labore_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['pro_labore_RF'] + $row['pro_labore_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Comissão de Corretagem');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['valor_comissao_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['valor_comissao_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['valor_comissao_RF'] + $row['valor_comissao_QA'], true));
            $contR++;
        }

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
