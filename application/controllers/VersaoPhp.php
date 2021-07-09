<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class VersaoPhp extends CI_Controller
{
	public function __construct(){		
        parent::__construct();
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    private function importarCamada($dirPath, $execao = [], $apenas = []){        
        $aFileName = array_diff(scandir($dirPath), [".", ".."]);
        foreach($aFileName as $fileName){
            $filePath = $dirPath.$fileName;
            if(mb_strpos($filePath, ".php") && !mb_strpos($filePath, ".bak")){
                if(!empty($apenas)){
                    if(in_array($fileName, $apenas)){
                        require_once($filePath);
                    }
                }else {
                    if(!in_array($fileName, $execao)){
                        require_once($filePath);
                    }
                }
                
                
            }
        }
    }

    private function importarCore(){
        $this->importarCamada("system/core/");
        $this->importarCamada("application/core/");
    }
    
    private function importarModels(){
        $this->importarCamada("application/models/");
    }

    private function importarLibs(){
        $this->importarCamada("system/libraries/");
        $this->importarCamada("application/libraries/");
        //$this->importarCamada("application/libraries/nusoap/");
        //$this->importarCamada("application/libraries/Sendpulse/");
        //$this->importarCamada("application/libraries/cimogo/");
        $this->importarCamada("application/libraries/Twilio/");
    }

    private function importarHelpers(){
        $this->importarCamada("system/helpers/");
        $this->importarCamada("application/helpers/", ['app_helper.php.orig']);
    }

    private function importarClasses(){
        $this->importarHelpers();
        $this->importarCore();
        $this->importarModels();
        $this->importarLibs();
        
    }

    private function classesMVC(){
        $this->importarClasses();
        $aClassNames = get_declared_classes();
        
        foreach($aClassNames as $className){
            $oReflectionClass = new ReflectionClass($className);
            $constructor = $oReflectionClass->getConstructor();
            if($constructor){
                if($constructor->name == $className){
                    //$this->printBreak($oReflectionClass->getFileName()." - ".$className);
                }
            }
        }  
    }

	/**
	 * Index
	 * @param null $slug
	 */
    public function index() {
        $this->classesMVC();
    }

    private function printExit($content){
        echo '<pre>';
        print_r($content);
        exit();
    }

    private function printBreak($content){
        var_dump($content);
        echo '<br>';
    }

}
