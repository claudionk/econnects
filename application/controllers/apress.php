<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apress extends Site_Controller {


    public function index()
    {
        $data = array();
        $p = $this->input->get('page', TRUE) ;
        switch ( $p ) {
        	case 'lista1_01-home':
            case 'lista1_01-dados_cadastrais':
            case 'lista1_02-cotacoes':
            case 'lista1_03-pagamento':
            case 'lista1_03-pagamento_1':
            case 'lista1_04-dados_complementares_v2':
            case 'lista1_05-finalizar':

            case 'lista2_02-validacao':
            case 'lista2_03-dados_complementares':
            case 'lista2_04-dados_complementares':
		        $this->template->load('apress/layout/base', "apress/page/" . $p , $data );
      		break;        	
        	default:        
        		$this->template->load('site/layouts/base', "apress/index", $data );
        	break;
        }
    }
}
