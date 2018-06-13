<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Evolução $current_model
 *
 */
class Clientes_Evolucoes extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Evolução');
        $this->template->set_breadcrumb('Evolução', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('cliente_evolucao_model', 'current_model');
    }
    
    public function index($cliente_id = 0, $offset = 0)
    {
        //Carrega models necessários
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('colaborador_model', 'colaboradores');
        $this->load->model('cliente_evolucao_status_model', 'status');
        
        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Evoluçãos');
        $this->template->set_breadcrumb('Evoluçãos', base_url("$this->controller_uri/index"));
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->getTotalCondicao('cliente_id', $cliente_id);
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        
        //Seta session para botão voltar
        $this->session->set_userdata('cliente_id', $offset);
        
        //Carrega dados
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->with_colaborador()->with_cliente_evolucao_status()->get_by_cliente($cliente_id);
        $data['cliente_id'] = $cliente_id;

        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    //Deleta registro
    public  function delete($id)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index/".$this->session->userdata('cliente_id'));
    }

    //Exportar Relatório PDF
    public function export_pdf($id = 0)
    {
        //Carrega Models Necessários
        $this->load->model('cliente_evolucao_model', 'evolucao');
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_evolucao_status_model', 'cliente_evolucao_status');

        //Carrega biblioteca
        $this->load->library('pdf');

        //Orientação
        $this->pdf->setPageOrientation('L');


        $data['row'] = $this->cliente->get($id);


        $header = $this->load->view('admin/clientes_evolucoes/pdf/header', $data, true);

        $this->pdf->setPageHeader($header);
        $this->pdf->SetTopMargin(24);
        $this->pdf->setPrintFooter(true);

        $data = array();
        $rows = $this->evolucao->with_cliente_evolucao_status()->with_colaborador()->get_by_cliente($id);

        $this->pdf->AddPage();
        $i = 1;
        foreach($rows as $row)
        {
            $data['row']  = $row;
            $data['row']['id'] = $i;
            $data['row']['data'] = app_dateonly_mysql_to_mask($data['row']['data']);
            $content = $this->load->view('admin/clientes_evolucoes/pdf/evolucoes', $data, true);
            $this->pdf->writeHTML($content, false, false, true, false, '');

            $i++;
        }

        $footer = $this->load->view('admin/clientes_evolucoes/pdf/footer', $data, true);
        $this->pdf->writeHTML($footer, true, false, true, false, '');

        $this->pdf->Output('relatorio-evolucao-'.date('Y_m_d').'.pdf', 'D');
        exit;
    }

    //Exporta relatório para excel
    function export_excel($id)
    {
        //Carrega Models Necessários
        $this->load->model('cliente_evolucao_model', 'evolucao');
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_evolucao_status_model', 'cliente_evolucao_status');

        //Carrega biblioteca do excel
        $this->load->library('excel');

        //Seta configs
        $this->excel->setActiveSheetIndex(0);
        $sheet = $this->excel->getActiveSheet();

        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setWorksheet($sheet);
        $objDrawing->setName("Corcovado");
        $objDrawing->setDescription($this->config->item('app_name'));
        $objDrawing->setPath('assets/admin/core/images/logo.png');
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(10);
        $objDrawing->setOffsetY(20);

        //Formata linhas
        $styleArrayTitle = array(

            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font' => array(
                'size' => '14'
            ),
        );
        $styleArrayHeader = array(

            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => '20a7e7',
                ),
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FFFFFF')
            )
        );

        $styleArrayCenter = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $styleArrayLinha = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
        );


        //header
        $cliente = $this->cliente->get($id);
        $sheet->setCellValue('A2', 'Relatório de evolução de status - '.$cliente['razao_nome']);

        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2:C2')->applyFromArray($styleArrayTitle);
        $sheet->getRowDimension('1')->setRowHeight(100);

        //cabeçalho
        $sheet->setCellValue('A3', 'Data');
        $sheet->setCellValue('B3', 'Comercial Responsável');
        $sheet->setCellValue('C3', 'Status');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getStyle('A3:C3')->applyFromArray($styleArrayHeader);
        $linha = 4;

        //Retorna todos os registros
        $rows = $this->evolucao->with_cliente_evolucao_status()->with_colaborador()->get_by_cliente($id);

        foreach($rows as $row)
        {
            $sheet->setCellValue("A{$linha}", app_date_mysql_to_mask($row['data']));
            $sheet->setCellValue("B{$linha}", $row['colaborador_nome']);
            $sheet->setCellValue("C{$linha}", $row['cliente_evolucao_status_descricao']);
            $linha++;
        }

        $sheet->getStyle("A2:A{$linha}")->applyFromArray($styleArrayCenter);
        $sheet->getStyle("A2:A{$linha}")->applyFromArray($styleArrayCenter);
        $sheet->getStyle("C2:C{$linha}")->applyFromArray($styleArrayCenter);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"Evolução - ".$cliente['razao_nome']." - ".date("d-m-Y").".xlsx\"");
        header("Cache-Control: max-age=0");

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
        $objWriter->setIncludeCharts(false);
        $objWriter->save("php://output");

        exit;
    }
}
