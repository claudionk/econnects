<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TestarModels extends Site_Controller {

    private $models = array();
    private $testado = array(
        'apolice_cobertura_model.php',
        'apolice_documento_model.php',
        'apolice_endosso_model.php',
        'apolice_equipamento_model',
        'apolice_generico_model',

    );

    public function __construct(){
        parent::__construct();
        $this->load->model('Apolice_Endosso_Model', 'model');    
    }

	public function index() {
        $this->importarModels();
        echo '<pre>';
        print_r($this->models);
        
    }
    

    private function importarCamada($dirPath, $execao = [], $apenas = []){        
        $aFileName = array_diff(scandir($dirPath), [".", ".."]);
        foreach($aFileName as $fileName){
            $filePath = $dirPath.$fileName;
            if(mb_strpos($filePath, ".php") && !mb_strpos($filePath, ".bak")){
                if(!empty($apenas)){
                    if(in_array($fileName, $apenas)){
                        require_once($filePath);

                        if(!in_array($fileName, $this->testado)){
                            $this->models[] = $fileName;
                        }
                    }
                }else {
                    if(!in_array($fileName, $execao)){
                        require_once($filePath);

                        if(!in_array($fileName, $this->testado)){
                            $this->models[] = $fileName;
                        }
                    }
                }
                
                
            }
        }
    }

       
    private function importarModels(){
        $this->importarCamada("application/models/");
    }

}
