<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/PHPExcel.php";

class Excel extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }

    public function getArrayRows($inputFileName, $sheet = 0){

        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);

        $sheet = $objPHPExcel->getSheet($sheet);


       return $this->getSheetRows($sheet);

    }
    public function getSheetNames($inputFileName){

        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);

        return $objPHPExcel->getSheetNames();
    }
    public function getSheetRows($sheet, $min_empty_cols = 30){

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        $data = array();

        for ($row = 1; $row <= $highestRow; $row++){

            $empty_col = 0;
            for ($col = 0; $col < $highestColumnIndex; ++ $col) {

                $cell = $sheet->getCellByColumnAndRow($col, $row);

                $value = $cell->getFormattedValue();

                //formata da data para o padrão br
                if(preg_match('/^\d{2}-\d{2}-\d{2}$/', $value )){


                   $date = ($cell->getValue() - 25569) * 86400;

                   $value = gmdate("d/m/Y", $date);
                }
                $value = trim($value);

                if($value == ''){
                    $empty_col++;
                }

                $data[$row][$col] = $value;

            }
            //deleta as colunas vazias
            if($empty_col > $min_empty_cols){
                unset($data[$row]);
            }

        }

        return $data;
    }
}