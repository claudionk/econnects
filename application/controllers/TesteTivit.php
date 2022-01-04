<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class TesteTivit extends CI_Controller {

    public function __construct() {
        parent::__construct();       
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 
        $this->load->model("integracao_model", "integracao");
    }

    public function testeIntegracao($integracao_id){
        $integracao = $this->integracao->get_by_id($integracao_id);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

        $aFile = $this->integracao->getFile($integracao, null);        
        var_dump($aFile);
    }

}