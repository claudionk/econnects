<?php

/**
 * Created by PhpStorm.
 * User: Leonardo Lazarini
 * Date: 03/05/2016
 * Time: 16:29
 */
class ExcelHelper
{
    protected $CI;
    protected $config = array();
    public $sheet;
    public $excelLibrary;

    public function __construct($config = array())
    {
        $this->config = $config;
        $this->CI = &get_instance();
        $this->initialize();
    }

    public function initialize()
    {
        //Carrega biblioteca do excel
        $this->CI->load->library('excel');

        $this->excelLibrary = $this->CI->excel;

        //Seta configs
        $this->CI->excel->setActiveSheetIndex(0);
        $this->sheet = $this->CI->excel->getActiveSheet();

    }
    public function getHeaderStyle()
    {
        $styleArrayHeader = array(

            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => '294981',
                ),
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => '12'
            )
        );
        return $styleArrayHeader;
    }

    /**
     * Seta headers
     * @param array $array
     */
    public function setHeader($array = array())
    {
        $i = 0;
        foreach($array as $data)
        {
            $alpha = app_num2alpha($i);

            $this->sheet->setCellValue("{$alpha}1", $data['nome']);
            $this->sheet->getColumnDimension("{$alpha}")->setWidth($data['tamanho']);
            $i++;
        }

        $alpha = app_num2alpha($i-1);
        $this->sheet->getStyle("A1:{$alpha}1")->applyFromArray($this->getHeaderStyle());
    }


    public function generate($name_file = "preficicacao.xlsx")
    {
        //Cria header
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"{$name_file}\"");
        header("Cache-Control: max-age=0");

        //Cria excel
        $objWriter = PHPExcel_IOFactory::createWriter($this->CI->excel, "Excel2007");
        //  $objWriter->setIncludeCharts(false);
        $objWriter->save("php://output");
    }

}