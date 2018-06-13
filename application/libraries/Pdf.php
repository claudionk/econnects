<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . '/third_party/tcpdf/config/lang/eng.php');
require_once(APPPATH . '/third_party/tcpdf/tcpdf.php');



class Pdf extends TCPDF {

    protected $pageHeader;

    public function __construct()
    {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->init();

    }
    public function init(){

        $this->setPrintHeader(0);
        $this->setPrintFooter(0);
        $this->SetCreator(PDF_CREATOR);
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->SetMargins(5, 5, 5);
        $this->SetHeaderMargin(10);
        $this->SetFooterMargin(0);
        $this->SetAutoPageBreak(TRUE, 10);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->SetFont('helvetica', '', 10);
    }

    //Page header
    public function Header() {
        $this->writeHTML($this->pageHeader, true, false, true, false, '');
    }

    // Page footer
    public function Footer() {
        $this->SetY(-10);
        // Page number
        $this->Cell(0, 10, 'Página '  .$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

    public function setPageHeader($header)
    {
        $this->setPrintHeader(true);
        $this->pageHeader = $header;
    }


}
